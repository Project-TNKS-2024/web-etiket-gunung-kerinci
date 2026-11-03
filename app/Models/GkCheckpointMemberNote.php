<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GkCheckpointMemberNote extends Model
{
    use \App\Models\Concerns\UsesWibDates;

    protected $table = 'gk_checkpoint_member_notes';

    protected $fillable = ['id_booking', 'id_pendaki', 'id_post', 'phase', 'note', 'updated_by'];
}
