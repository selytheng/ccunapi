<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'title',
        'image',
        'gallery',
        'description',
        'partner_id',
        'co_host',
        'sponsor',
        'location',
        'status',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'gallery' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
