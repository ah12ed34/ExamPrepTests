<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use App\Models\Exam;

class CreateExam extends Component
{
    public $name;
    public $questions = [];
    public $number = 0;
    public $total = 0;
    public $question;
    public $xml;
    private $nameSessionNumber = 'CreateExam q';
    private $nameSessionQuestions = 'CreateExam questions';
    public $char = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    public $changeId = [];


    public function mount()
    {
        if (session($this->nameSessionQuestions)) {
            $this->questions = session($this->nameSessionQuestions);
            $this->total = count($this->questions);

        }else{
            $this->questions = [
                [
                    'text' => '',
                    'answer' => '',
                    'options' => [
                        'A' => '',
                        'B' => '',
                        'C' => '',
                        'D' => '',
                    ],
                ]
            ];
            $this->total = count($this->questions);
        }
        if (session($this->nameSessionNumber)) {
            $this->goTo(session($this->nameSessionNumber));
        } else {
            $this->goTo(0);
        }
    }

    public function next()
    {
        if ($this->number < $this->total - 1) {
            $this->goTo($this->number + 1);
        }
    }
    public function prev()
    {
        if ($this->number > 0) {
            $this->goTo($this->number - 1);
        }
    }

    public function goTo($number)
    {
        $this->number = $number;
        $this->question = $this->questions[$this->number];
        session([$this->nameSessionNumber => $this->number]);
    }

    public function updatedQuestion()
    {
        $this->checkdate();
    }

    public function checkdate()
    {
        if (!empty($this->question) && (!isset($this->questions[$this->number]) || $this->question != $this->questions[$this->number])) {
            $this->questions[$this->number] = $this->question;
            $this->changeId[$this->number] = $this->number;
            session([$this->nameSessionQuestions => $this->questions]);
        }
    }

    public function addQuestion()
    {
        $this->questions[] = [
            'text' => '',
            'answer' => '',
            'options' => [
                'A' => '',
                'B' => '',
                'C' => '',
                'D' => '',
            ],
        ];
        $this->total = count($this->questions);
        $this->goTo($this->total - 1);
    }

    public function removeQuestion()
    {
        unset($this->questions[$this->number]);
        $this->total = count($this->questions);
        session([$this->nameSessionQuestions => $this->questions]);
        if ($this->number >= $this->total) {
            $this->goTo($this->total - 1);
        }
    }

    public function addOption()
    {
        $this->questions[$this->number]['options'][$this->char[count($this->questions[$this->number]['options'])]] = '';
        session([$this->nameSessionQuestions => $this->questions]);
        $this->goTo($this->number);
    }

    public function removeOption()
    {

        unset($this->questions[$this->number]['options'][$this->char[count($this->questions[$this->number]['options']) - 1]]);
        session([$this->nameSessionQuestions => $this->questions]);
        $this->goTo($this->number);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'questions.*.text' => 'required',
            'questions.*.answer' => 'required',
        ]);



        // $xml = new \SimpleXMLElement('<questions><questions/>');
        $xml = new \SimpleXMLElement('<questions></questions>');


        foreach ($this->questions as $key => $question) {

            if($this->checkQuestion($question)){
                $child = $xml->addChild('question');
                $child->addChild('text', $question['text']);
                $options = $child->addChild('options');
                foreach ($question['options'] as $key => $option) {
                    if(!empty($option)){
                        $option = $options->addChild('option', $option);
                        $option->addAttribute('opt', $key);
                    }
                }
                $child->addChild('answer', $question['answer']);
            }else{
                session()->flash('message', 'Question ' . ($key + 1) . ' is not valid.');
                dd('error in question ' . ($key + 1) . ' is not valid.');
                return;
            }

        }
        // create file hash name and save it to storage

        $file = $xml->asXML();

        do {
            $hash = md5($file . time());
        } while (Exam::where('file', 'public/test/' . $hash . '.xml')->exists());

        $path = 'public/test/' . $hash . '.xml';
        if (Storage::put($path, $file)) {
            $exam = optional(auth()->user())->exams()->create([
                'name' => $this->name,
                'file' => $path,
            ]);
            session()->flash('message', 'Exam Created.');
            $this->clearSession();
            return redirect()->route('home');
        }else{
            session()->flash('message', 'Error creating exam.');
            dd('error');
        }


    }

    public function clearSession()
    {
        session()->forget($this->nameSessionNumber);
        session()->forget($this->nameSessionQuestions);

    }
    public function checkQuestion($question)
    {
        if(empty($question['text']) || count($question['options']) < 2){
            return false;
        }else{
            foreach ($question['options'] as $key => $option) {
                if(!empty($option)){
                    if($question['answer'] == $key){
                        return true;
                    }
                }else{
                    unset($question['options'][$key]);
                }
            }
            return false;
        }
        return true;
    }

    public function render()
    {
        return view('livewire.create-exam');
    }
}
