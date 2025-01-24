<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = ['name', 'logo', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function majors()
    {
        return $this->hasMany(Major::class);
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
