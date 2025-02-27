<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Negara extends Model
{
    protected static $filePath = 'assets/json/negara.json';

    // ✅ Ambil semua data dari JSON
    public static function getAll(): Collection
    {
        if (!File::exists(self::$filePath)) {
            return collect([]);
        }

        $json = File::get(self::$filePath);
        return collect(json_decode($json, false));
    }

    // ✅ Filter negara berdasarkan code
    public static function getByCode($code)
    {
        return self::getAll()->where('code', $code)->first();
    }

    // ✅ Ambil semua code yang duplikat
    public static function getDuplicateCodes(): array
    {
        $data = self::getAll();
        $grouped = $data->groupBy('code')->filter(fn($items) => $items->count() > 1);
        return $grouped->toArray();
    }
}
