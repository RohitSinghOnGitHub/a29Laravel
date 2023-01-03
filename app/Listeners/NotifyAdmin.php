<?php

namespace App\Listeners;

use App\Events\BetCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\MyBettingRecords;
use App\Models\Result_Parity;
use App\Models\Result_Spare;
use App\Models\Result_Becon;
use App\Models\Result_Emerd;

class NotifyAdmin
{
    
  
    public function __construct()
    {
        
    }

  
    public function handle(BetCreated $event)
    {
        // dd($event->period);
        // ,'Spare'=>new Result_Spare,'Becon'=>new Result_Becon,'Emerd'=>new Result_Emerd
        $modelNameArray=['Parity'=>new Result_Parity,'Spare'=>new Result_Spare,'Becon'=>new Result_Becon,'Emerd'=>new Result_Emerd];
        $finalResult=$this->makeResult($event->period,$event->category);
        $result = $modelNameArray[$event->category]->where('Period','=',$event->period)->first();
        // dd($modelNameArray[$event->category]::where('Period','=',$event->period)->first());
        
        if(!is_null($result)){
            $result->Color=$finalResult["Color"];
            $result->number=$finalResult["Number"];
            $result->update();
        }
        else{
            $result=$modelNameArray[$event->category];
            $result->Period=$event->period;
            $result->Color=$finalResult["Color"];
            $result->number=$finalResult["Number"];
            $result->save();
        }
        $this->calcWin($event->period,$event->category);
        
    }
    // -------------------------------------------------------------
    // TODO: Can be delete after Checking....
    // protected function calcColor($period,$color){
    //     $sumOnlyColor=MybettingRecords:: where('Period','=',$period)->where('Select',$color)->sum('Delivery')*2;
    //     $colorArray=$color==='Red'?['2','4','6','8']:['1','3','7','9'];
    //     $numArray=[];
    //     $numArray=array_map(function($num) use($period){
    //        return MybettingRecords:: where('Period','=',$period)->where('Select',$num)->sum('Delivery')*9;
    //     },$colorArray);
    //     $colorKeys=$color==='Red'?['Red+2','Red+4','Red+6','Red+8']:['Green+1','Green+3','Green+7','Green+9'];
    //     $colorPno=[$sumOnlyColor+$numArray[0],$sumOnlyColor+$numArray[1],$sumOnlyColor+$numArray[2],$sumOnlyColor+$numArray[3]];
    //     $colorReultValues=array_combine($colorKeys,$colorPno);
    //     return $colorReultValues;
    // }


    // -------------------------------------------------------------
    
    protected function calcVilot($period,$category){
        $sumOnlyVilot=MybettingRecords::where('Period','=',$period)->where('category',$category)->where('Select','Vilot')->sum('Delivery')*4.5;
        $sumOnlyRed=MybettingRecords::where('Period','=',$period)->where('category',$category)->where('Select','Red')->sum('Delivery')*1.5;
        $sumOnlyGreen=MybettingRecords::where('Period','=',$period)->where('category',$category)->where('Select','Green')->sum('Delivery')*1.5;
        $numArray=[];
        $numArray=array_map(function($num) use($period,$category){
            // global $category;
           return MybettingRecords::where('Period','=',$period)->where('category',$category)->where('Select',(string)$num)->sum('Delivery')*9;
        },['0','5']);
        
        $vilotKeys=['Vilot+0','Vilot+5'];
        $vilotPno=[$sumOnlyVilot+$numArray[0]+$sumOnlyRed,$sumOnlyVilot+$numArray[1]+$sumOnlyGreen];
        $VilotResultValues=array_combine($vilotKeys,$vilotPno);
        return $VilotResultValues;
    }
    protected function makeResult($period,$category){
        $bet=new MybettingRecords();
        $combineResult=array_merge($bet->calcColor($period,'Red',$category),$bet->calcColor($period,'Green',$category),$this->calcVilot($period,$category));
        $minimumResult=min($combineResult);
        $minimumValues=array_filter($combineResult,function($val) use($minimumResult)
        { 
            return ($val===$minimumResult);
        });
        $resultKey=array_rand($minimumValues); //example Red+1 desc :array_rand() provide any random key of array.💕✔
        $explodedKeyArray=explode('+',$resultKey); // Array([0]=>Red [1]=>1)
        $resultColor=$explodedKeyArray[0];
        $resultNumber=$explodedKeyArray[1];
        return ["Color"=>$resultColor,"Number"=>$resultNumber];
    }

/* Calculating Win Or Lose */
    protected function calcWin($period,$category){
        $bet=new MybettingRecords();
        $modelNameArray=['Parity'=>new Result_Parity,'Spare'=>new Result_Spare,'Becon'=>new Result_Becon,'Emerd'=>new Result_Emerd];
        $result = $modelNameArray[$category]->where('Period','=',$period)->first();
       
        $bets=MybettingRecords::where('Period','=',$period)->where('category',$category)->get();

        foreach ($bets as $bet) {
            $flag=false;
            if($bet['Select']===$result['Color'] || $result['Color']==='Vilot' || $bet['Select']===$result['number']){
                // dd('I m in ');
                // $bet['Status']="Success";
                // print_r($bet['Select'].'and'.$result['number']);
                    if($bet['Select']==='Vilot'){
                        $bet['win_amount']=$bet['Delivery']*4.5;
                        $bet['Status']="Success";
                        $flag=true;
                    }
                    if(($bet['Select']==='Red' && ($result['Color']==='Red' && $result['number']!==0)) || ($bet['Select']==='Green'  && ($result['Color']==='Green' && $result['number']!==5))) {
                        $bet['win_amount']=$bet['Delivery']*2.0;
                        $bet['Status']="Success";
                        $flag=true;
                        print_r($result['Color']."and".$result['number']);
                    }
                    if(($bet['Select']==='Red' && $result['number']==='0') || ($bet['Select']==='Green'  && $result['number']==='5')){
                        $bet['win_amount']=$bet['Delivery']*1.5;
                        $bet['Status']="Success";
                        $flag=true;
                        print_r("Color =>".$result['Color']."and no is =>".$result['number']);
                    }
                    if($bet['Select']===$result['number']){
                        $bet['win_amount']=$bet['Delivery']*9.0;
                        $bet['Status']="Success";
                        $flag=true;
                    }
                    if($flag===false){
                        $bet['Status']="Fail";
                        $bet['win_amount']=0.0;
                    }
                    $bet->save();

            }
            else{
                $bet['Status']="Fail";
                $bet['win_amount']=0.0;
                $bet->save();
            }
            // print_r($bet->Select);
            
        }
        // dd();
     }

}
