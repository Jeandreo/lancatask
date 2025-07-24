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
        @if ($content->membersUsers->count() > 0)
            <p class="text-gray-700 fw-bolder mb-0">Usuários:</p>
            @foreach ($content->membersUsers as $member)
                <span class="text-gray-600">{{ $member->user->name }}</span>
                @if($loop->last)
                    @break
                @endif
                <span class="text-gray-600">,</span>
            @endforeach
        @endif
        @if ($content->membersClients->count() > 0)
            <p class="text-gray-700 fw-bolder mb-0">Clientes:</p>
            @foreach ($content->membersClients as $member)
                <span class="text-gray-600">{{ $member->client->name }}</span>
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
