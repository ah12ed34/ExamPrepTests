<?php

namespace App\Livewire;

use App\Models\Exam;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Psy\Command\HistoryCommand;

class ResultExam extends Component
{
    public $Exam;
    public $questions;
    public $question;
    public $number = 0;
    public $currect = 0;
    public $wrong = 0;
    public $total = 0;


    public function mount(Exam $Exam)
    {
        if (!$Exam->isExam()) {
            return abort(404, 'This Exam is not found');

        }
        if(session('resultId')==$Exam->id){
            $this->number = session('resultNumber', 0);

        }else{
           session(['resultId'=>$Exam->id,'resultNumber'=>0]);
        }
        $this->Exam = $Exam;


        // $this->questions = $Exam->answers->mapWithKeys(function ($item) {
        //     return [$item->question_id => $item->answer];
        // });

        $file = Storage::path($Exam->file);
        $xml = simplexml_load_file($file);
        $y = 0;
        $currect = 0;
        $wrong = 0;

        foreach ($xml->children() as $child) {
            $this->questions[$y]['text'] = $child->text->__tostring();
            $options = [];
            foreach ($child->options->children() as $option) {
                if (isset($option['opt']))
                    $options[(string) $option['opt']] = $option->__tostring();
                else
                    $options[] = $option->__tostring();
            }
            $this->questions[$y]['options'] = $options;
            $this->questions[$y]['answer'] = $child->answer->__tostring();
            $this->questions[$y]['userAnswer'] = $Exam->answers->where('question_id', $y)->first()->answer;
            $this->questions[$y]['correct'] = $this->questions[$y]['answer'] == $this->questions[$y]['userAnswer'];
            if ($this->questions[$y]['correct']) {
                $currect++;
            } else {
                $wrong++;
            }
            $y++;
        }

        $this->question = $this->questions[$this->number];
        $this->currect = $currect;
        $this->wrong = $wrong;
        $this->total = count($this->questions);

        $number =  request('q') != null ? request('q')-1 : $this->number;
        // dd($number);
        if ($number < 0 || $number >= $this->total) {
            return redirect()->route('result',[$Exam,'q'=>1],);
        }else{
            $this->number = $number;
        }
    }

    public function next()
    {

        if ($this->number >= count($this->questions) -1) {
            return redirect()->route('home');
        }$this->number++;
        $this->question = $this->questions[$this->number];
        session(['resultNumber' => $this->number]);
        // $this->redirect(route('result', [$this->Exam->id,'q' => $this->number + 1]));

    }
    public function goTo($number)
    {
        if ($number < 0 || $number >= $this->total) {
            return;
        }$this->number = $number;
        $this->question = $this->questions[$this->number];
        session(['resultNumber' => $this->number]);
    }

    public function nextError()
    {
        if($this->wrong==0){
            return;
        }
        $next = $this->number;
        do {
            $next++;
            if ($next >= count($this->questions)) {
                if($this->wrong==1)
                return;
                $next = 0;
            }
        } while ($this->questions[$next]['correct']);
        $this->goTo($next);
    }

    public function prev()
    {

        if ($this->number <= 0) {
            return;
        } $this->number--;
        $this->question = $this->questions[$this->number];
        session(['resultNumber' => $this->number]);
    }

    public function render()
    {

        return optional(view('livewire.result-exam'))->layout('components.layouts.app');
    }
}
