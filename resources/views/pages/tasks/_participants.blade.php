@foreach ($task->participants as $users)
<div class="symbol symbol-25px symbol-circle">
    <img alt="Pic" src="{{ findImage('users/photos/' . $users->id . '.jpg') }}">
</div>
@endforeach
<div class="symbol symbol-25px symbol-circle" data-bs-toggle="tooltip" data-bs-original-title="Adicionar participante" data-task="{{ $task->id }}" id="add-participants"  @if(isset($opacity0) && $task->participants->count()) style="margin-right: -10px;" @endif>
    <span class="symbol-label bg-primary bg-hover-success text-inverse-primary fw-bold @if(isset($opacity0) && $task->participants->count()) opacity-0 @endif" >+</span>
</div>
