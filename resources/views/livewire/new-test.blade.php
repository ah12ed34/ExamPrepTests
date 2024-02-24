<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="container">
        <div class="row">
            <a class="btn btn-primary" href="{{ route('home') }}">Back</a>
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            <form>
                <div class="form-group">
                    <label for="nameExam">Name</label>
                    <input type="text" class="form-control" id="nameExam" wire:model="name">
                    @error('name') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="file">File</label>
                    <input type="file" class="form-control" id="file" wire:model="file" accept="text/xml" >
                    @error('file') <span class="error">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-primary" wire:click.prevent="save()">Submit</button>
            </form>
</div>
