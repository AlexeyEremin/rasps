<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;
    protected $table = 'timetable';
    protected $fillable = ['timetable', 'user_id'];

    public function firstOpen() {
        $groups = [];
        foreach(Group::where('user_id', $this->user_id)->orderBy('position', 'ASC')->get() as $item) {
            $groups[] = $item->openDor($this);
        }
        return [
            'id' => $this->id,
            'timetable' => $this->timetable,
            'timetable_format' => Carbon::parse($this->timetable)->format('d.m.Y'),
            'dayName' => (int) Carbon::parse($this->timetable)->format('N'),
            'user_id' => $this->user_id,
            'groups' => $groups,
            'subjects' => TeacherSubject::all()
        ];
    }

    public function  generatePDFFile() {

    }
}
