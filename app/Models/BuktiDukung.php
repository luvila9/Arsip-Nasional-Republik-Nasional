<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiDukung extends Model
{
    protected $fillable = [
        'user_id',
        'aspek_id',
        'sub_aspek_id',
        'sub_sub_aspek_id',
        'nama_dokumen',
        'file_path',
        'keterangan',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aspek()
    {
        return $this->belongsTo(Aspek::class);
    }

    public function subAspek()
    {
        return $this->belongsTo(SubAspek::class);
    }

    public function subSubAspek()
    {
        return $this->belongsTo(SubSubAspek::class);
    }
}