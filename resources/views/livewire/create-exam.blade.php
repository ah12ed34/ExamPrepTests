<div>
    {{-- The Master doesn't talk, he acts. --}}
    <div class="container">
        {{-- show massege  --}}
        @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
        @endif
        {{-- error valdiet qustaions --}}
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <h1>Create Exam</h1>
                <a class="btn btn-primary" href="{{ route('home') }}">Back</a>
                <form wire:submit.prevent="save">
                    <div class="form-group">
                        <label for="ExamName">Exam Name</label>
                        <input type="text" class="form-control" id="ExamName" placeholder="Enter Exam Name" wire:model="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="ExamQuestion">Exam Question</label>
                        <input type="text" class="form-control" id="ExamQuestion" placeholder="Enter Exam Question" wire:model="question.text">
                        @error('question') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    @foreach ($question["options"] as $key => $value)
                    {{-- @dump($question["options"]) --}}
                        <div class="form-group">
                            <label for="o{{ $number }}{{ $key }}" >Option {{ $key  }}</label>
                            <input type="radio" id="r{{ $number }}{{ $key }}" wire:model="question.answer" value="{{ $key }}"/>
                            <input type="text" id="o{{ $number }}{{ $key }}" class="form-control"  placeholder="Enter Option" wire:model="question.options.{{ $key }}">
                            @error('question.options') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-primary" wire:click="addOption">Add Option</button>
                    <button type="button" class="btn btn-primary" wire:click="removeOption">Remove Option</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <br>
                <div class="text-center">
                    <button class="btn btn-primary" wire:click="prev" @if($number<=0) disabled @endif >Previous</button>
                    <button class="btn btn-primary" wire:click="addQuestion">Add Question</button>
                    <button class="btn btn-primary" wire:click="removeQuestion">Remove Question</button>
                    <button class="btn btn-primary" wire:click="next" @if ($this->number+1 >= count($questions)) disabled @endif href="?q={{ $number+1 }}" >Next</button>

                </div>


</div>
