<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GkCheckpointMemberNote extends Model
{
    use \App\Models\Concerns\UsesWibDates;

    protected $table = 'gk_checkpoint_member_notes';

    protected $fillable = ['booking_id', 'pendaki_id', 'post_id', 'phase', 'note', 'updated_by'];
}
