<?php

namespace App\Livewire;

use App\Models\exam;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Psy\Command\HistoryCommand;

class ResultExam extends Component
{
    public $exam;
    public $questions;
    public $question;
    public $number = 0;
    public $currect = 0;
    public $wrong = 0;
    public $total = 0;


    public function mount(exam $exam)
    {
        if (!$exam->isExam()) {
            return abort(404, 'This exam is not found');

        }
        if(session('resultId')==$exam->id){
            $this->number = session('resultNumber', 0);

        }else{
           session(['resultId'=>$exam->id,'resultNumber'=>0]);
        }
        $this->exam = $exam;


        // $this->questions = $exam->answers->mapWithKeys(function ($item) {
        //     return [$item->question_id => $item->answer];
        // });

        $file = Storage::path($exam->file);
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
            $this->questions[$y]['userAnswer'] = $exam->answers->where('question_id', $y)->first()->answer;
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
            return redirect()->route('result',[$exam,'q'=>1],);
        }else{
            $this->number = $number;
        }
    }

    public function next()
    {

        if ($this->number >= count($this->questions) -1) {
            return;
        }$this->number++;
        $this->question = $this->questions[$this->number];
        session(['resultNumber' => $this->number]);
        // $this->redirect(route('result', [$this->exam->id,'q' => $this->number + 1]));

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
