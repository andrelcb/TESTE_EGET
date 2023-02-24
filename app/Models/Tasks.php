<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'conclusion_date',
        'status'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsToMany(User::class, 'users_tasks_assigned', 'tasks_idtasks', 'users_idusers');
    }
}
