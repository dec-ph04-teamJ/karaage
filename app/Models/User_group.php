<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_group extends Model
{
    use HasFactory;
    
    // デフォルトだとクラス名を複数形のスネークケースにしたもの(user_groups)がテーブル名になる？
    protected $table = 'user_group';
}
