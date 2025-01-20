<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['partner_id'];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function galleryDetails()
    {
        return $this->hasMany(GalleryDetail::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function workshops()
    {
        return $this->hasMany(Workshop::class);
    }

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }
}
