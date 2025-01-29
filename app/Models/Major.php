<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = ['name', 'partner_id', 'logo', 'description'];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
