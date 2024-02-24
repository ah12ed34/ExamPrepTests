<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use App\Models\Exam;

class Test extends Component
{
    public $question ;

    public $questions ;
    public $userAnswer = [];
    public $number = 0;
    public function mount(Exam $Exam)
    {
        $file = Storage::path($Exam->file);
        $xml = simplexml_load_file($file);
            $y = 0;
        // foreach ($xml->children() as $child) {
        //     $this->questions[$y]['text'] =  $child->text->__tostring();
        //     $i = 0;
        //     foreach ($child->options->children() as $child2) {
        //         $this->questions[$y]['options'][$i] = $child2->__tostring();
        //         $i++;
        //     }
        //     $y++;
        //     // foreach ($child->children() as $child2) {

        //     // }
        // }


            if (session('Exam')!=$Exam->id|| session('qus')==null || !session('qus')){
                foreach ($xml->children() as $child) {
            $this->questions[$y]['text'] = $child->text->__tostring();
            $options = [];
            foreach ($child->options->children() as $option) {
                if (isset($option['opt']))
                    $options[(string) $option['opt']] = $option->__tostring();
                else
                    $options[] = $option->__tostring();
                // $options[isset($option['opt'])?$option['opt']:] = $option->__tostring();
            }
            // $keys = array_keys($options);
            // shuffle($keys);

            // $options = array_merge(array_flip($keys), $options);

              // Separate text options from numerical keys
    // Shuffle the keys of the options array
            $keys = array_keys($options);
            shuffle($keys);

    // Reconstruct the options array using shuffled keys
            $shuffledOptions = [];
            foreach ($keys as $key) {
                $shuffledOptions[$key] = $options[$key];
            }
            $options = $shuffledOptions;
            // $options = array_values($options);
            // dd($options);
            $this->questions[$y]['options'] = $options;
            $y++;
        }
            session(['qus' => $this->questions]);
            session(['Exam' => $Exam->id]);

            session()->forget('userAnswer');
            session()->forget('number');

            }else{
                $this->questions = session('qus');
            }

        // dd($this->questions);

        $this->userAnswer = session('userAnswer', []);
        $this->number = session('number', 0);
        $this->question = $this->questions[$this->number];
        // dd($this->question);
    }

    public function next()
    {
        if ($this->number < count($this->questions) - 1){

            $this->number++;
            $this->question = $this->questions[$this->number];
            $this->saveUserAnswer();
        }
    }
    public function prev()
    {
        if ($this->number > 0){
            $this->number--;
            $this->question = $this->questions[$this->number];
            $this->saveUserAnswer();
        }

    }
    public function updatedUserAnswer()
    {
        $this->saveUserAnswer();
    }
    public function res()
    {
        $this->userAnswer = [];
        $this->number = 0;
        $this->question = $this->questions[0];
        $this->saveUserAnswer();
    }
    public function saveUserAnswer()
    {
        session(['userAnswer' => $this->userAnswer]);
        session(['number' => $this->number]);
    }
    public function resetData()
    {
        session()->forget('userAnswer');
        session()->forget('number');
        session()->forget('qus');
        session()->forget('Exam');

    }

    public function save()
    {
        $Exam = Exam::find(session('Exam'));
        if ($Exam->answers()->where('user_id', auth()->id())->exists()){
            $Exam->answers()->where('user_id', auth()->id())->delete();
        }
        $Exam->answers()->createMany(array_map(function ($answer, $question_id) {
            return [
                'question_id' => $question_id,
                'answer' => $answer,
                'user_id' => auth()->id()
            ];
        }, $this->userAnswer, array_keys($this->userAnswer)));
        $this->resetData();
        return redirect()->route('home');

    }

    public function render()
    {
        return optional(view('livewire.test'))->layout('components.layouts.app');
        // slot('content');
    }
}
