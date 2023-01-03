<?php

namespace App\Models;

use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MybettingRecords extends Model
{
    use HasFactory, HasApiTokens;
    public $timestamps = false;

    protected $fillable = [
        // 'Contract_Money',
        // 'Contract_Count',
        // 'Delivery',
        // 'Fee',
        // 'Result',
        // 'Amount',
        // 'Category',
        // 'Create_Time',
    ];
    protected $gaurded = [
        // 'Period',
        // 'User_id',
        // 'Open_Price',
        // 'Select',
        // 'Status'
    ];
    public function calcPeriod()
    {
        $dateString = Carbon::parse(now('Asia/kolkata')->toDateTimeString());
        $year = $dateString->format('Y');
        $month = $dateString->format('m');
        $day = $dateString->format('d');
        $Hour = $dateString->format('H');
        $Min = $dateString->format('i');
        $period = ($Hour * 20) + (((int) floor($Min / 3)) + 1);
        return $year . $month . $day . substr("000{$period}", -3);
    }

    public function calcColor($period, $color, $category)
    {
        $sumOnlyColor = MybettingRecords::where('Period', '=', $period)->where('category', $category)->where('Select', $color)->sum('Delivery') * 2.0;
        $colorArray = $color === 'Red' ? ['2', '4', '6', '8'] : ['1', '3', '7', '9'];
        $numArray = [];
        $numArray = array_map(function ($num) use ($period, $category) {
            // global $category;
            return MybettingRecords::where('Period', '=', $period)->where('category', $category)->where('Select', $num)->sum('Delivery') * 9.0;
        }, $colorArray);
        $colorKeys = $color === 'Red' ? ['Red+2', 'Red+4', 'Red+6', 'Red+8'] : ['Green+1', 'Green+3', 'Green+7', 'Green+9'];
        $colorPno = [$sumOnlyColor + $numArray[0], $sumOnlyColor + $numArray[1], $sumOnlyColor + $numArray[2], $sumOnlyColor + $numArray[3]];
        $colorReultValues = array_combine($colorKeys, $colorPno);
        return $colorReultValues;
    }
}
