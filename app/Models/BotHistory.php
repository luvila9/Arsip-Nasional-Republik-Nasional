<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotHistory extends Model
{
    use HasFactory;
    protected $fillable = ['pertanyaan', 'jawaban_bot', 'ip_address'];
}