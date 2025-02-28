<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Kabupaten extends Model
{
    protected static $filePath = 'assets/json/kabupaten.json';

    public static function getAll(): Collection
    {
        if (!File::exists(self::$filePath)) {
            return collect([]);
        }

        $json = File::get(self::$filePath);
        return collect(json_decode($json, false));
    }
    public function getAllByCode($code): Collection
    {
        return self::getAll()->where('provinsi_id', $code);
    }

    public static function getByCode($code)
    {
        return self::getAll()->where('id', $code)->first();
    }

    public static function getDuplicateCodes(): array
    {
        $data = self::getAll();
        $grouped = $data->groupBy('id')->filter(fn($items) => $items->count() > 1);
        return $grouped->toArray();
    }
}
