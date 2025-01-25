<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'name',
        'company',
        'email',
        'phone_number',
        'message',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
