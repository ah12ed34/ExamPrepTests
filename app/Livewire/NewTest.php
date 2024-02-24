<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;


class NewTest extends Component
{
    use WithFileUploads;
    #[Validate(['name' => 'required'])]
    public $name = '';
    #[Validate(['file' => 'required|mimes:xml'])]
    public $file;

    public function save()
    {
        $this->validate();



        // if ($this->file) {
        //     $this->file = $this->file->store('Exams');
        // }
        $file   =  $this->file->store('public/test');

        if(Storage::exists($file)){
            $this->file = $file;

            $xml = simplexml_load_file(Storage::path($file));
            $boo = true;
            // $questions = [];
            foreach ($xml->children() as $child) {

                if (!isset($child->text) || !isset($child->options) || !isset($child->answer) || !isset($child->options->option['opt'])|| count($child->options->children()) < 2){
                    $boo = false;
                    break;
                }
                // $options = [];
                // foreach ($child->options->children() as $option) {
                //     if (isset($option['opt']))
                //         $options[(string) $option['opt']] = $option->__tostring();
                //     else
                //         $options[] = $option->__tostring();
                // }
            }

            if ($boo) {
            $Exam = \App\Models\Exam::create([
                'name' => $this->name,
                'file' => $this->file,
                'user_id' => auth()->id()
            ]);
            return redirect()->route('home');
        }else{
            Storage::delete($file);
            $this->file = null;
            session()->flash('message', 'Invalid XML file');
        }

    }
        // $Exam = new \App\Models\Exam;
        // $Exam->name = $this->name;
        // $Exam->file = $this->file;
        // $Exam->user_id = auth()->id();
        // $Exam->save();
        // return redirect()->route('home');
    }
    public function render()
    {
        return optional(view('livewire.new-test'))->layout('components.layouts.app');
    }
}
