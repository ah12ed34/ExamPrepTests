<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="container">
        <div class="row">
            <a class="btn btn-primary" href="{{ route('new-test') }}">Create Exam</a>

                    @forelse ($Exams as $Exam)
                    <div class="card">
                        <div class="card-body">
                            <!-- Card content -->
                            <h5 class="card-title">{{ $Exam->name }}</h5>
                            <p class="card-text">{{ $Exam->user->name }}</p>

                            <a href="{{ route('test', $Exam->id) }}" class="btn btn-primary">Start Test</a>
                            @if ($Exam->isExam())
                            <a  class="btn btn-primary" href="{{ route('result',$Exam->id) }}" >Show Result</a>
                            @endif
                        </div>
                    </div>

                    @empty

                    <p>No Exams</p>
                    @endforelse
                    {{-- <div class="card">
                        <div class="card-body">
                            <!-- Card content -->
                            <h5 class="card-title">Test 1</h5>
                            <p class="card-text">Description of Test 1</p>
                        </div>
                    </div> --}}

                </div>
            </div>


        </div>
    </div>


</div>
