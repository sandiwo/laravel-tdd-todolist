@extends('layouts.app')

@section('content')
    <div class="row justify-content-between px-3 py-3">
        <div class="col-lg-3 col-sm-4">
            <h4>My Projects</h4>
        </div>
        <div class="col-auto">
            <a href="{{ url('projects/create') }}" class="btn btn-primary">Add New</a>
        </div>
    </div>
    <div class="row mx-1">
        @forelse ($projects as $project)
            <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                <div class="card h-100">
                    <div class="card-body my-3">
                        <h3 class="mt-1">
                        <a href="{{ $project->path() }}" style="text-decoration:none; color:black">{{ $project->title }}</a>
                        </h3>
                        <div class="card-title text-muted mt-4">{{ $project->description }}</div>
                    </div>
                </div>
            </div>
        @empty
        @endforelse
    </div>
@endsection