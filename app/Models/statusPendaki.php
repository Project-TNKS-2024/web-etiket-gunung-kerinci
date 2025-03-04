<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\isEmpty;

class statusPendaki extends Model
{
    use HasFactory;
    protected $table = 'gk_status_pendaki';
    protected $fillable = [
        'id_pendaki',
        'status',
        'detail'
    ];

    public function statusName($id = null)
    {
        if (isEmpty($id)) {
            $id = $this->status;
        }
        switch ($id) {
            case 1:
                return 'Batal Mendaki';
            case 2:
                return 'Cek In';
            case 3:
                return 'Cek Out';
            default:
                return 'Tidak Diketahui';
        }
    }
}
