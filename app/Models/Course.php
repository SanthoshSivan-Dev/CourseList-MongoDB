<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Course extends Eloquent
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'courses';

    protected $fillable = [
        'courseName', 'startDate', 'endDate', 'courseImage'
    ];

    public function capabilities(){
        return $this->hasMany(Capability::class);
    }
}
