<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','title','desc','is_done'];
    protected $casts = ['is_done' => 'boolean'];

}