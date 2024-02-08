<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Capability extends Eloquent
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'capabilities';

    protected $fillable = [
        'capabilityName'
    ];

    public function course(){
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function skills(){
        return $this->hasMany(Skill::class);
    }
}
