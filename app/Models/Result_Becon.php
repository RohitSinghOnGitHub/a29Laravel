<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result_Becon extends Model
{
    use HasFactory, HasApiTokens;
    protected $fillable=[
        'Period',
        'Color',
        'number'
    ];
}
