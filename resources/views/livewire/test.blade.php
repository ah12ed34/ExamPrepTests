<div>

@push('styles')
<style>
    /* Custom CSS for highlighting selected option */
    .custom-radio input[type="radio"] {
        display: none; /* Hide the default radio input */
    }
    .custom-radio label {
        cursor: pointer;
        width: 100%;
        padding: 10px 20px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        margin: 5px;
    }
    .custom-radio label:hover {
        background-color: #f8f9fa; /* Change background color on hover */
    }
    .custom-radio input[type="radio"]:checked + label {
        background-color: #007bff; /* Change background color of selected label */
        color: #fff; /* Change text color of selected label */
        border-color: #007bff; /* Change border color of selected label */
    }
    .error label {
        background-color: red;
        font-size: 80%;
        margin-top: 5px;
    }
    .form-group label{
        margin: 5px;
    }
</style>

@endpush
<div class="container">
    <div class="row">
        <form >
            <div class="form-group">
                {{-- @dump($userAnswer) --}}
                <p class="text-center"> totle question: {{ count($userAnswer) }} / {{ count($questions) }} , {{ $number +1 }} </p>
                <label for="question">Question: {{ $question['text'] }}</label>
                @forelse ($question['options'] as $key => $option)
                    {{-- @dump($option['opt']) --}}
                    <div class="custom-radio">
                        <input type="radio" id="{{ $number.$key }}" wire:model.lazy='userAnswer.{{ $number }}' value="{{ $key }}">
                        <label for="{{ $number.$key }}">{{ $option }}</label>
                    </div>

                @empty

                @endforelse
                {{-- <div class="custom-radio">
                    <input type="radio" id="option1" name="options" value="option1">
                    <label for="option1">Option 1</label>
                </div>
                <div class="custom-radio">
                    <input type="radio" id="option2" name="options" value="option2">
                    <label for="option2">Option 2</label>
                </div>
                <div class="custom-radio">
                    <input type="radio" id="option3" name="options" value="option3">
                    <label for="option3">Option 3</label>
                </div>
                <div class="custom-radio">
                    <input type="radio" id="option4" name="options" value="option4">
                    <label for="option4">Option 4</label>
                </div> --}}
            </div>

        </form>
            <div class="text-center mt-5">
                <button class="btn btn-primary" @if ($number == 0) disabled @endif wire:click='prev' id="prevBtn">السابق</button>
                {{-- @if(count($userAnswer) < $number )
                    @dump($number-count($userAnswer))
                    @if($number-count($userAnswer) == 1 && isset($userAnswer[$number]))
                        <button class="btn btn-primary" @if ($number == 0) disabled @endif wire:click='prevNotAnswered' id="prevBtn">السابق</button>
                    @elseif ($number-count($userAnswer) > 1)
                        <button class="btn btn-primary" @if ($number == 0) disabled @endif wire:click='prevNotAnswered' id="prevBtn">السابق</button>
                    @endif
                @endif --}}
                <button class="btn btn-primary" wire:click='res' id="resetBtn" @if (count($userAnswer)==0)
                    disabled
                @endif >إعادة</button>
                {{-- <button class="btn btn-primary" wire:click='submit' id="submitBtn">تسليم</button> --}}
                <button class="btn btn-primary" wire:click='save' id="submitBtn" @if (count($userAnswer) != count($questions) ) disabled @endif>تسليم</button>
                <button class="btn btn-primary" wire:click='next' id="nextBtn" @if ($number == count($questions) -1 ) disabled @endif>التالي</button>
            </div>
    </div>
</div>

</div>
