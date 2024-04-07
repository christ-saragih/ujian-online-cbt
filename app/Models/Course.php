<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    public function category() {
        // belongsTo = FK dari tabel 'course'
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function questions() {
        // hasMany = FK dari tabel 'course_question' dan PK dari tabel 'course'
        return $this->hasMany(CourseQuestion::class, 'course_id', 'id');
    }

    public function students() {
        return $this->belongsToMany(User::class, 'course_students', 'course_id', 'user_id');
    }
}
 