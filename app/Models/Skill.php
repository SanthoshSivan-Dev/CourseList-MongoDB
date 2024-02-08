<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Skill extends Eloquent
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'skills';

    protected $fillable = [
        'skillName'
    ];

    public function capability(){
        return $this->belongsTo(Capability::class);
    }
}
