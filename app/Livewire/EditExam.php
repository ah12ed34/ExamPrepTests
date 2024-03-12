<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam;
use Exception;
use Hamcrest\Type\IsBoolean;
use Illuminate\Support\Facades\Storage;

class EditExam extends Component
{

    public $Exam;
    public $name;
    public $file;
    public $questions ;
    public $number = 0;
    public $total = 0;
    public $chenageId =[];
    public $question;
    public $xml ;
    private $nameSessionNumber = 'EditExam q' ;
    public function mount(Exam $Exam)
    {
        $this->Exam = $Exam;
        $this->name = $Exam->name;
        $this->file = $Exam->file;
        $file = Storage::path($Exam->file);
        $xml = simplexml_load_file($file);
        $this->xml = file_get_contents($file) ;
        $y = 0;
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
            $y++;
        }
        $this->total = $y;

        $this->question = $this->questions[$this->number];

        if(request('q')){
            if(request('q')>$this->total){
                redirect()->route('edit-exam',[$this->Exam->id,'q'=>$this->total-1]);
            }else{
                $this->goTo(request('q')-1);
            }
        }elseif(session($this->nameSessionNumber)){
            $this->goTo(session($this->nameSessionNumber));
        }else{
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

    public function goTo($number){

        $this->number = $number;

        $this->question = $this->questions[$this->number];

        session(
            [
                $this->nameSessionNumber => $this->number
                ]
            );
    }

    public function updatedQuestion(){
        $this->checkdate();
    }
    public function checkdate(){
        if(!Empty($this->question)&&$this->question != $this->questions[$this->number]){
            $this->questions[$this->number] = $this->question;
            $this->chenageId[$this->number] = $this->number;
            // dd($this->questions[$this->number],$this->question,$this->chenageId,count($this->chenageId));
        }
    }

     // foreach ($this->xml->children() as $key => $child) {
        //     $child->text = $this->questions[$key]['text'];
        //     $child->answer = $this->questions[$key]['answer'];
        //     $options = $child->options;
        //     foreach ($options->children() as $key => $option) {
        //         if (isset($option['opt'])) {
        //             $option->__set('opt', $key);
        //             $option->__set('__toString', $this->questions[$key]['options'][$key]);
        //         } else {
        //             $option->__set('__toString', $this->questions[$key]['options'][$key]);
        //         }
        //     }
        // }
    public $chare = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N'];
    public function addOption()
    {

        if(count($this->question['options'])<count($this->chare)){
            $this->question['options'][$this->chare[count($this->question['options'])]] = '';
        }
    }

    public function removeOption()
    {
        unset($this->question['options'][array_key_last($this->question['options'])]);
        $this->checkdate();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
        ]);
        // try{
            $this->Exam->update([
            'name' => $this->name,
        ]);


        if(Empty($this->chenageId)){
            // dd(count($this->chenageId));
            session()->flash('message', 'Exam Updated.');
            return redirect()->route('home');
        }
        $xml = simplexml_load_string($this->xml);
        $i = 0;

        foreach ($xml->children() as $key => $child) {

            if(!isset($this->chenageId[$i])){
                $i++;
                continue;
            }
            optional($child)->text[0] = $this->questions[$i]['text'];
            optional($child)->answer[0] = $this->questions[$i]['answer'];

            unset($child->options->option);
            foreach($this->questions[$i]['options'] as $key => $option){
                $opt = $child->options->addChild('option',$option);
                $opt->addAttribute('opt',$key);
            }
            // dd($child,$this->questions[$i]);
            $i++;


        }
        // dd($this->questions,$xml);
        // dd($xml->asXML(Storage::path($this->file)));
        $xml->asXML(Storage::path($this->file));
        $this->res();
        session()->flash('message', 'Exam Updated.');


        return redirect()->route('home');
        // }catch(Exception $i){
        //     dd('error'.$i);
        //     return back()->with('error', 'Error');
        // }


    }

    public function res(){
        session()->forget($this->nameSessionNumber);
    }


    public function render()
    {
        return optional(view('livewire.edit-exam'))->layout('components.layouts.app') ;
    }
}
