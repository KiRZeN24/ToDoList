<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todotask extends Model
{
    use HasFactory;
    protected $filleable= [
        'texto',
        'status',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
