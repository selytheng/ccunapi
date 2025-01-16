<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
   
    protected $fillable = [
        'major_id', 'year_id', 'name', 'description', 'image'
    ];


    public function major()
    {
        return $this->belongsTo(Major::class);
    }

  
    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
