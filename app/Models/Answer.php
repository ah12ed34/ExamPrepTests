<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    // protected $fillable = ['exam_id', 'question_id', 'answer', 'user_id'];
    protected $fillable = ['exam_id', 'file', 'user_id'];

    public function Exam()
    {
        return $this->belongsTo(Exam::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }



}
