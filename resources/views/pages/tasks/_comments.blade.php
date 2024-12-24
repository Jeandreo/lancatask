@if ($contents->count())
   @php $previousCreatedBy = null @endphp
   @foreach ($contents as $content)
      @if ($content->created_by != $previousCreatedBy)
         <div class="d-flex align-items-center mb-2">
               <div class="symbol symbol-35px symbol-rounded">
                  <img alt="Pic" src="{{ findImage('users/' . $content->created_by . '/' . 'perfil-35px.jpg') }}">
               </div>
               <div class="ms-3">
                  <p class="fs-6 fw-bold text-gray-700 me-1 mb-0">{{ $content->author->name }}</p>
                  <p class="text-muted fs-7 mb-0">{{ $content->created_at->format('d/m/Y') }} ás {{ $content->created_at->format('H:i') }}</p>
               </div>
         </div>
         @php $previousCreatedBy = $content->created_by @endphp
      @endif
      <div class="d-flex justify-content-start mb-3" title="{{ $content->created_at->format('d/m/Y') }} ás {{ $content->created_at->format('H:i') }}">
         <div class="d-flex flex-column align-items-start opacity-1">
            <div class="p-5 rounded bg-light-primary text-gray-700 fw-semibold mw-lg-400px text-start comment-ajax position-relative" data-kt-element="message-text">
               <a href="{{ route('comments.destroy', $content->id) }}" class="destroy-comment position-absolute btn btn-sm btn-danger btn-active-primary rounded-circle btn-icon opacity-0 h-20px w-20px" data-task="{{ $content->task_id }}" style="right: -10px; top: -10px;">
                  <i class="fa-solid fa-trash-can text-white fs-9"></i>
               </a>
               {!! $content->text !!}
            </div>
         </div>
      </div>
   @endforeach
@else
<div class="no-tasks" @if ($contents->count()) style="display: none;" @endif>
   <div class="rounded bg-light d-flex align-items-center justify-content-center h-200px h-md-400px">
      <div class="text-center">
         <p class="m-0 text-gray-600 fw-bold text-uppercase">Sem comentários ainda nessa tarefa!</p>
      </div>
   </div>
</div>
@endif
