<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GkSosChat extends Model
{
    protected $table = 'gk_sos_chats';

    protected $fillable = [
        'id_sos', 'sender_id', 'sender_type', 'type', 'content', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function sos()
    {
        return $this->belongsTo(GkSos::class, 'id_sos');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
