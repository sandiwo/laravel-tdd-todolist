@if(count($activity->changes['before']) == 1)
    {{ $activity->user->name }} update {{ key($activity->changes['before']) }}
@else
    {{ $activity->user->name }} update project 
@endif