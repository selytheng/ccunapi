<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryDetail extends Model
{
    protected $fillable = ['gallery_id', 'picture'];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }
}
