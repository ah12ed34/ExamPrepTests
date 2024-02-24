<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="container">
        <div class="row">
            <a class="btn btn-primary" href="{{ route('new-test') }}">Create Exam</a>

                    @forelse ($exams as $exam)
                    <div class="card">
                        <div class="card-body">
                            <!-- Card content -->
                            <h5 class="card-title">{{ $exam->name }}</h5>
                            <p class="card-text">{{ $exam->user->name }}</p>

                            <a href="{{ route('test', $exam->id) }}" class="btn btn-primary">Start Test</a>
                            @if ($exam->isExam())
                            <a  class="btn btn-primary" href="{{ route('result',$exam->id) }}" >Show Result</a>
                            @endif
                        </div>
                    </div>

                    @empty

                    <p>No exams</p>
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
