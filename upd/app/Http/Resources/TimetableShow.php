<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TimetableShow extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'timetable' => $this->timetable,
            'timetable_format' => Carbon::parse($this->timetable)->format('d.m.Y'),
            'dayName' => (int) Carbon::parse($this->timetable)->format('N'),
            'user_id' => $this->user_id
        ];
    }
}
