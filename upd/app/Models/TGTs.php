<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TGTs extends Model
{
    use HasFactory;
    protected $table = 't_g_ts';

    protected $fillable = ['timetable_id', 'group_id', 'numeric', 'sub'];

    public function toArray()
    {
        return [
            "id" => $this->id,
            "timetable_id" => $this->timetable_id,
            "group_id" => $this->group_id,
            "teacher_subject_id" => $this->teacher_subject_id,
            "numeric" => $this->numeric,
            "sub" => $this->sub,
            "color" => "#ffffff",
            "cabinet" => $this->cabinet,
            "cabinet_color" => false,
            'lesson_type' => $this->lesson_type
        ];
    }
}

