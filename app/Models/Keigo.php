<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keigo extends Model
{
    use HasFactory;

    protected $fillable = [
        'output_id',
        'keigo',
    ];

     public static function Get_Keigo_from_Outputid($output_id){
        return self::where("output_id",$output_id)->get();

        #指定したidが入ったリストを渡してそれと一致する物を抽出する。
        #whereInは渡された配列の中にある要素と$input_idのなかで一致するものを全件取得する。
    }
    
}
