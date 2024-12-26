@foreach ($users as $user)
    <div class="d-flex justify-content-between mb-2">
        <h2 class="text-gray-800 fs-4 fw-bold">{{ $user->name }}</h2>
        @if (in_array($user->id, $contents->participants->pluck('id')->toArray()))
        <button class="btn btn-sm btn-success fw-bolder text-uppercase add-user" data-task="{{ $contents->id }}" data-user="{{ $user->id }}">Adicionar</button>
        @else
        <button class="btn btn-sm btn-success fw-bolder text-uppercase add-user" data-task="{{ $contents->id }}" data-user="{{ $user->id }}">Remover</button>
        @endif
    </div>
@endforeach
