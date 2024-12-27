@foreach ($users as $user)
    <div class="d-flex justify-content-between mb-2">
        <h2 class="text-gray-800 fs-4 fw-bold">{{ $user->name }}</h2>
        @if ($user->id == $contents->designated_id)
        <button class="btn btn-sm btn-primary fw-bolder text-uppercase" disabled>Proprietário</button>
        @else
            @if (in_array($user->id, $contents->participants->pluck('id')->toArray()))
            <button class="btn btn-sm btn-danger fw-bolder text-uppercase add-user" data-task="{{ $contents->id }}" data-user="{{ $user->id }}">Remover</button>
            @else
            <button class="btn btn-sm btn-success fw-bolder text-uppercase add-user" data-task="{{ $contents->id }}" data-user="{{ $user->id }}">Adicionar</button>
            @endif
        @endif
    </div>
@endforeach
