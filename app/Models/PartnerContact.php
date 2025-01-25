<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'phone_number',
        'email',
        'location_link',
        'address',
        'website',
        'moodle_link',
    ];

    protected $casts = [
        'phone_number' => 'array',
        'email' => 'array',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
