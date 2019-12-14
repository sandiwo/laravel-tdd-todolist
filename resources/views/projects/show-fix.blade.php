@extends('layouts.app')

@section('content')
    <header class="row justify-content-between px-3 py-3">
        <div class="col-md-11">
            <h4>My Projects / {{ $project->title }}</h4>
        </div>
        <div class="col">
            <a href="{{ url($project->path() . '/edit') }}" class="btn btn-primary">Edit Task</a>
        </div>
    </header>

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }} </li>
                @endforeach
            </ul>
        </div>
    @endif

    <h3 class="ml-3">Tasks</h3>
    <div class="row mx-1">
        <div class="col-lg-8 col-md-4 col-sm-12">
            @foreach ($project->tasks as $task)
            <div class="card mb-2">
                <div class="card-body {{ $task->completed ? 'bg-success' : '' }}">
                    <form action="{{ url($task->path()) }}" method="POST">
                        @csrf
                        @method('patch')
                        <div class="d-flex justify-content-between">
                            <input type="text" name="body" class="form-control {{ $task->completed ? 'bg-success' : '' }}" style="border:0;" value="{{ $task->body }}">
                            <input type="checkbox" name="completed" class="ml-3 mt-3" style="transform:scale(2); background-color:red;" onChange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
            <div class="card mb-2">
                <div class="card-body">
                    <div class="text-muted">
                        <form action="{{ url($project->path() . '/tasks') }}" method="POST">
                            @csrf
                            <input type="text" name="body" class="form-control" placeholder="Create a task..." style="width:100%; border:0" autocomplete="off">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body my-3">
                    <h3 class="mt-1">
                    <a href="{{ $project->path() }}" style="text-decoration:none; color:black">{{ Str::limit($project->title, 35) }}</a>
                    </h3>
                    <div class="card-title text-muted mt-4">{{ Str::limit($project->description, 250) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mx-1">
        <div class="col-md-8">
            <h3 class="mt-3">General Notes</h3>
            <form action="{{ url($project->path()) }}" method="POST">
            @csrf
            @method('patch')
                <div class="card">
                    <div class="card-body">
                        <textarea name="notes" style="width: 100%; border:0" rows="6">{{ $project->notes }}</textarea>
                    </div>
                </div>
                <button class="btn btn-primary btn-md mt-2">Update Note</button>
            </form>
        </div>
    </div>
@endsection