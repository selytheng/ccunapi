<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'partner_id'
    ];
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
