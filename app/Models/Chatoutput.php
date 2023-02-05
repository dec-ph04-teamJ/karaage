<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatoutput extends Model
{
    use HasFactory;


    protected $fillable = [
        'score',
        "user_id",
        "input_id"
    ];
/*
    public static function Get_Chat_Score_from_Inputid($input_id_lis){
        return self::whereIn("input_id",$input_id_lis)->get();
        #指定したidが入ったリストを渡してそれと一致する物を抽出する。
        #whereInは渡された配列の中にある要素と$input_idのなかで一致するものを全件取得する。
    }
*/
   public static function getAllOrderByUpdated_at($user_id)
    {
        return self::where("user_id",$user_id)
        ->orderBy('updated_at', 'desc')->get();
    }
}
