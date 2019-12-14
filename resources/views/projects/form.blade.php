@csrf
<div class="card">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }} </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-group">
            <label for="exampleInputEmail1">Title</label>
            <input type="text" name="title" value="{{ $project->title }}" class="form-control" aria-describedby="emailHelp" placeholder="Enter title">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Description</label>
            <textarea name="description" class="form-control" placeholder="Description">{{ $project->description }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</div>