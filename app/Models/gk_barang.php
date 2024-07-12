<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gk_barang extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'detail',
    ];
}
