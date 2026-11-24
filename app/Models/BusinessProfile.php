<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    protected $table = 'business_profile';

    protected $fillable = [
        'business_name', 'description', 'address', 'image', 'hero_image',
        'hero_title', 'hero_subtitle', 'open_hours', 'instagram', 'map_query',
        'bank_name', 'bank_account', 'bank_holder', 'qris_image', 'shipping_policy', 'logo',
    ];

    /** URL logo: hasil upload admin, atau logo bawaan di public/images/logo.png. */
    public function logoUrl(): string
    {
        return $this->logo ? asset('storage/'.$this->logo) : asset('images/logo.png');
    }

    /** Profil usaha hanya satu baris. */
    public static function current(): self
    {
        return static::first() ?? new static(['business_name' => 'Izam Jaya Roster']);
    }
}
