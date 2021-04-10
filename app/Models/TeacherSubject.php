<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    use HasFactory;
    protected $table = 'teacher_subject';

    protected $fillable = ['teacher_id', 'user_id', 'subject_id'];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_fullname' => $this->hasOne(User::class, 'id', 'user_id')->first()->fullname,
            'teacher_id' => $this->teacher_id,
            'teacher_fullname' => $this->hasOne(Teacher::class, 'id', 'teacher_id')->first()->fullname,
            'color' => $this->hasOne(Teacher::class, 'id', 'teacher_id')->first()->color,
            'subject_id' => $this->subject_id,
            'subject_name' => $this->hasOne(Subject::class, 'id', 'subject_id')->first()->name
        ];
    }
}
