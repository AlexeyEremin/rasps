<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function openDor(Timetable $timetable) {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'tgts' => $this->hasMany(TGTs::class, 'group_id', 'id')
                ->where('timetable_id', $timetable->id)
                ->orderBy('numeric', 'ASC')
                ->get(),
            'ts_group' => $this->hasMany(RelatedItem::class, 'group_id', 'id')->get()
        ];
    }

    public function getRelatedItem() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'teacher_subjects' => TeacherSubject::whereIn('id', RelatedItem::where('group_id', $this->id)->get()->pluck('ts_id'))->get(),
            'not_teacher_subjects' => TeacherSubject::whereNotIn('id', RelatedItem::where('group_id', $this->id)->get()->pluck('ts_id'))->get()
        ];
    }
}
