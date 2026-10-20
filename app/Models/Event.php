<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use \App\Models\Concerns\UsesWibDates;

    protected $fillable = [
        'judul',
        'tanggal',
        'libur',
    ];
}
