<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinasiUser extends Model
{
    use HasFactory;

    protected $table = 'destinasi_user';

    protected $fillable = ['user_id', 'destinasi_id', 'is_penanggungjawab'];

    public $timestamps = true;

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with Destinasi
    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class);
    }
}
