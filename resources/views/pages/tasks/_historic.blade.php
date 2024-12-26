@if ($contents->historic->count())
    @foreach ($contents->historic()->orderBy('id', 'DESC')->get() as $historic)
       <div class="mb-3 text-gray-700">
            @switch($historic->action)
                @case('nome')
                    <div class="bg-light rounded p-2">
                        <p class="text-gray-700 mb-0 d-flex justify-content-between align-items-center mb-0">
                            <b>Nome alterado:</b>
                            <span class="fs-8 text-gray-600">
                                {{ $historic->created_at->format('d/m/Y') }} às {{ $historic->created_at->format('H:i') }}
                            </span>
                        </p>
                        <p class="fw-bold">
                            <span class="badge badge-light-danger">{{ $historic->previous_key }}</span> para <span class="badge badge-light-primary">{{ $historic->key }}</span>.
                        </p>
                    </div>
                    @break
                @case('descrição')
                    <div class="bg-light rounded p-2">
                        <p class="text-gray-700 mb-0 d-flex justify-content-between align-items-center mb-0">
                            <b>Descrição alterada:</b>
                            <span class="fs-8 text-gray-600">
                                {{ $historic->created_at->format('d/m/Y') }} às {{ $historic->created_at->format('H:i') }}
                            </span>
                        </p>
                        <p class="text-gray-600 mb-0">
                            {{ $historic->previous_key }}
                        </p>
                        <p class="mb-0 fw-bold">
                            Para:
                        </p>
                        <p class="text-gray-600 mb-0">
                            {{ $historic->key }}
                        </p>
                    </div>
                    @break
                @case('data')
                    <div class="bg-light rounded p-2">
                        @if ($historic->previous_key)
                        <p class="text-gray-700 mb-0 d-flex justify-content-between align-items-center mb-0">
                            <b>Data alterada:</b>
                            <span class="fs-8 text-gray-600">
                                {{ $historic->created_at->format('d/m/Y') }} às {{ $historic->created_at->format('H:i') }}
                            </span>
                        </p>
                        <p class="fw-bold">
                            <span class="badge badge-light-danger">{{ date('d/m/Y', strtotime($historic->previous_key))  }}</span> para <span class="badge badge-light-primary">{{ date('d/m/Y', strtotime($historic->key)) }}</span>.
                        </p>
                        @else
                        <p class="text-gray-700 mb-0 d-flex justify-content-between align-items-center mb-0">
                            <b>Data inserida: <span class="badge badge-light-primary">{{ date('d/m/Y', strtotime($historic->key)) }}</span></b>
                            <span class="fs-8 text-gray-600">
                                {{ $historic->created_at->format('d/m/Y') }} às {{ $historic->created_at->format('H:i') }}
                            </span>
                        </p>
                        @endif
                    </div>
                    @break
                @case('nome')

                    @break
                @case('nome')

                    @break

                @default
            @endswitch
       </div>
    @endforeach
    <p class="text-center m-0 text-gray-700 fw-bolder text-uppercase fs-8">
        Tarefa Criada
        {{ $historic->task->created_at->format('d/m/Y') }} às {{ $historic->task->created_at->format('H:i') }}
    </p>
@else
<div class="w-100 bg-light border border-dashed border-1 border-gray-200 h-200px rounded d-flex justify-content-center align-items-center">
    <div class="d-flex">
        <i class="fa-regular fa-face-smile-wink text-success fs-3x me-4"></i>
        <div>
            <h2 class="fw-bold text-gray-700 mb-0">Sem tarefas no momento</h2>
            <p class="m-0 text-gray-600">Nenhuma tarefa atribuida a você no momento, volte em breve.</p>
        </div>
    </div>
</div>
@endif
