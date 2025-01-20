<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'image',
        'gallery_id',
        'description',
        'partner_id',
        'location',
        'status',
        'start_date',
        'end_date'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }
}
