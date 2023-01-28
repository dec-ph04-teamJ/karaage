<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatoutput extends Model
{
    use HasFactory;

    protected $fillable = [
        'score',
    ];

    public static function Get_Chat_Score($id_lis){
        return self::whereIn("input_id",$id_lis)->get();
        #指定したidが入ったリストを渡してそれと一致する物を抽出する。
    }
}
