<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class exam extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'file', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function answers()
    {
        return $this->hasMany(Answer::class, 'exam_id', 'id')->where('user_id', auth()->id());
    }
    public function isExam() : bool{
        return $this->answers->where('user_id', auth()->id())->count() > 0;
    }
}