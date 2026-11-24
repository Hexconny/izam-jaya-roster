<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['whatsapp'];

    /** Nomor WhatsApp admin dalam format internasional tanpa tanda, contoh 6281234567890. */
    public static function whatsapp(): string
    {
        $number = preg_replace('/\D/', '', (string) optional(static::first())->whatsapp);

        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        return $number;
    }
}
