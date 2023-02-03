<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatinput extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sentence',

    ];

    public static function getAllOrderByUpdated_at($user_id)
    {
        return self::where("user_id",$user_id)
        ->orderBy('updated_at', 'desc')->get();
    }

    public static function Get_input_id_from_sentence($sentence){
        return self::where("sentence",$sentence)->get();

        #指定したidが入ったリストを渡してそれと一致する物を抽出する。
        #whereInは渡された配列の中にある要素と$input_idのなかで一致するものを全件取得する。
    }


}
