<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubAspek extends Model
{
    protected $fillable = ['aspek_id', 'nama_sub_aspek', 'link'];

    public function aspek()
    {
        return $this->belongsTo(Aspek::class);
    }

    public function subSubAspeks()
    {
        return $this->hasMany(SubSubAspek::class);
    }
}