<?php

namespace App\Livewire;

use App\Models\Exam;
use Livewire\Component;

class Home extends Component
{
    public $exams;
    public function mount()
    {
        $this->exams = Exam::all();
    }
    public function render()
    {
        return optional(view('livewire.home'))->layout('components.layouts.app');
    }
}
