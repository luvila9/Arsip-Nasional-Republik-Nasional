<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspek extends Model
{
    protected $fillable = ['nama_aspek','link'];

    public function subAspeks()
    {
        return $this->hasMany(SubAspek::class);
    }
}