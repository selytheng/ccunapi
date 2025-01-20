<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'logo',
        'description',
    ];
    public function majors()
    {
        return $this->hasMany(Major::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
