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
                        <p class="text-gray-600 mb-0">
                            <span class="mb-0 fw-bold text-gray-700">
                                Para:
                            </span> {{ $historic->key }}
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
                @case('status')
                    <div class="bg-light rounded p-2">
                        <p class="text-gray-700 mb-0 d-flex justify-content-between align-items-center mb-0">
                            <b>Status alterado:</b>
                            <span class="fs-8 text-gray-600">
                                {{ $historic->created_at->format('d/m/Y') }} às {{ $historic->created_at->format('H:i') }}
                            </span>
                        </p>
                        <p class="fw-bold">
                            <span class="badge" style="background: {{ hex2rgb($historic->statusOld->color, 12) }}; color: {{ $historic->statusOld->color }}">{{ $historic->statusOld->name }}</span> para <span class="badge" style="background: {{ hex2rgb($historic->status->color, 12) }}; color: {{ $historic->status->color }}">{{ $historic->status->name }}</span>.
                        </p>
                    </div>
                    @break
                @case('designado')
                    <div class="bg-light rounded p-2">
                        <p class="text-gray-700 mb-0 d-flex justify-content-between align-items-center mb-0">
                            <b>Designou um novo responsável:</b>
                            <span class="fs-8 text-gray-600">
                                {{ $historic->created_at->format('d/m/Y') }} às {{ $historic->created_at->format('H:i') }}
                            </span>
                        </p>
                        <p class="fw-bold">
                            <span class="badge badge-light-danger">{{ $historic->designatedOld->name }}</span> para <span class="badge badge-light-primary">{{ $historic->designated->name }}</span>.
                        </p>
                    </div>
                    @break
                @case('estado')
                    <div class="bg-light rounded p-2">
                        <p class="text-gray-700 mb-0 d-flex justify-content-between align-items-center mb-0">
                            <span>
                                <b>Mudou o estado dessa tarefa para:</b>
                                @if ($historic->key == true)
                                    <span class="badge badge-success">Ativa</span>
                                @else
                                    <span class="badge badge-danger">Arquivada</span>
                                @endif
                            </span>
                            <span class="fs-8 text-gray-600">
                                {{ $historic->created_at->format('d/m/Y') }} às {{ $historic->created_at->format('H:i') }}
                            </span>
                        </p>
                    </div>
                    @break
                @case('prioridade')
                    <div class="bg-light rounded p-2">
                        <p class="text-gray-700 mb-0 d-flex justify-content-between align-items-center mb-0">
                            <span>
                                <b>Alterou de :</b>
                                <i class="fa-solid fa-font-awesome p-2
                                @if ($historic->previous_key == 0)
                                text-gray-300
                                @elseif($historic->previous_key == 1)
                                text-warning
                                @elseif($historic->previous_key == 2)
                                text-info
                                @elseif($historic->previous_key == 3)
                                text-danger
                                @endif"></i>
                                Para
                                <i class="fa-solid fa-font-awesome p-2
                                @if ($historic->key == 0)
                                text-gray-300
                                @elseif($historic->key == 1)
                                text-warning
                                @elseif($historic->key == 2)
                                text-info
                                @elseif($historic->key == 3)
                                text-danger
                                @endif"></i>
                            </span>
                            <span class="fs-8 text-gray-600">
                                {{ $historic->created_at->format('d/m/Y') }} às {{ $historic->created_at->format('H:i') }}
                            </span>
                        </p>
                    </div>
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
        <i class="fa-solid fa-clock-rotate-left text-success fs-3x me-4"></i>
        <div>
            <h2 class="fw-bold text-gray-700 mb-0">Tarefa sem histórico</h2>
            <p class="m-0 text-gray-600">Não existe registro de atividades no momento para essa tarefa.</p>
        </div>
    </div>
</div>
@endif
