<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
 
    protected $fillable = [
        'major_id', 'year_id', 'name', 'description', 'image', 'link_registration'
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
