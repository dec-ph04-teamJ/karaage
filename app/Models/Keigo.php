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
}
