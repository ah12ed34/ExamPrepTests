<?php

namespace App\Livewire;

use App\Models\Exam;
use Livewire\Component;

class Home extends Component
{
    public $Exams;
    public function mount()
    {
        $this->Exams = Exam::all();
    }
    public function render()
    {
        return optional(view('livewire.home'))->layout('components.layouts.app');
    }
}
