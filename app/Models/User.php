<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_name',
        'email',
        'password',
        'character',
        'level',
    ];

    public function completion()
    {
        return $this->hasMany(Completion::class);
    }
    
    public function accuracy()
    {
        return $this->hasMany(Accuracy::class);
    }
}
