<div class="modal-header py-3 bg-dark border-0">
    <div class="d-flex w-100">
        <h5 class="modal-title text-gray-700">{{ $content->name }}</h5>
    </div>
    <div class="btn btn-icon bg-pure-darker ms-2" data-bs-dismiss="modal" aria-label="Close">
        <span class="svg-icon svg-icon-2x fw-bolder">X</span>
    </div>
</div>
<div class="modal-body">
    <p class="text-gray-700 fw-bolder mb-0">Data:
        <span class="text-gray-600 fw-normal">
                 <span class="text-success fw-bold">{{ date('d/m/Y', strtotime($content->date_start)) }}</span>
             das <span class="text-primary fw-bold">{{ date('H:i', strtotime($content->hour_start)) }}</span>
             até <span class="text-primary fw-bold">{{ date('H:i', strtotime($content->hour_end)) }}</span>
        </span>.
        </p>
    <p class="text-gray-700 fw-bolder mb-0">Descrição do compromisso:</p>
    <p class="text-gray-600">{{ $content->description ?? 'Sem descrição' }}</p>
    <div>
        @if ($content->members->count() > 0)
            <p class="text-gray-700 fw-bolder mb-0">Usuários:</p>
            @foreach ($content->members as $member)
                <span class="text-gray-600">{{ $member->information->name }}</span>
                @if (isset($googleAttendees[$member->information->email]))
                    @if($googleAttendees[$member->information->email] == 'needsAction')
                        <span class="text-warning">(Pendente)</span>
                    @elseif ($googleAttendees[$member->information->email] == 'accepted')
                        <span class="text-success">(Confirmado)</span>
                    @elseif ($googleAttendees[$member->information->email] == 'tentative')
                        <span class="text-warning">(Talvez)</span>
                    @elseif ($googleAttendees[$member->information->email] == 'declined')
                        <span class="text-danger">(Recusado)</span>
                    @endif
                @endif
                @if($loop->last)
                    @break
                @endif
                <span class="text-gray-600">,</span>
            @endforeach
        @endif
    </div>
</div>
<div class="modal-footer py-3 bg-light d-flex justify-content-between">
    <div>
        @if ($content->created_by == Auth::id())
        <a href="{{ route('agenda.destroy', $content->id) }}" class="btn btn-danger me-2">Cancelar evento</a>
        @endif
    </div>
    <div class="d-flex">
        <button type="button" class="btn text-gray-600 bg-gray-300 me-2" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-info edit-agenda" data-bs-dismiss="modal" data-id="{{ $content->id }}">Editar</button>
    </div>
</div>
