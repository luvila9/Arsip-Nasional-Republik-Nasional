<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSubAspek extends Model
{
    protected $fillable = [
        'sub_aspek_id', 
        'nama_sub_sub_aspek',
        'link'
    ];
}