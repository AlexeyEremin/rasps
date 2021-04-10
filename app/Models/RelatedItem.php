<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedItem extends Model
{
    use HasFactory;

    protected $table = 'related_items';
    protected $fillable = ['ts_id', 'group_id', 'user_id'];
}
