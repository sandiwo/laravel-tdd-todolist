@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h3>Edit Project</h3>
        </div>
    </div>
    <form action="{{ url($project->path()) }}" method="POST">
        @method('PATCH')

        @include('projects.form', ['buttonText' => 'Update Project'])
    </form>
</div>
@endsection