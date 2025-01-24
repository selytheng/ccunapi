<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'major_id',
        'year_id',
        'name',
        'description',
        'image',
        'link',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }
}
