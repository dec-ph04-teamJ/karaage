<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function Get_Group_Users(){
        return $this->belongsToMany(User::class,"user_groups","group_id","user_id");
        //特定のグループに所属しているuserを取得
        //引数の一つ目はどのテーブルの情報を取得するか,二つ目は中間テーブルはなにかを表す。
    }

}
