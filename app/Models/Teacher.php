<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'fullname', 'color'];

    public function subjects() {
        return [
            'fullname' => $this->fullname,
            'subjects' => $this->hasMany(TeacherSubject::class)->get()->toArray(),
            'not_subjects' => Subject::whereNotIn('id', $this->hasMany(TeacherSubject::class)->get()->pluck('subject_id'))->orderBy('name', 'ASC')->get()
        ];
    }

    public function timetable($date) {
        $subjects = $this->hasMany(TeacherSubject::class)->get()->pluck('id');
        $timetable = Timetable::where('timetable', $date)->first();
        return [
            'fullname' => $this->fullname,
            'timetable' => TGTs::select(['group_id', 'numeric', 'lesson_type'])->
                whereIn('teacher_subject_id', $subjects)->
                where('timetable_id', $timetable->id)->
                get()
        ];
    }
}
