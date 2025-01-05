<div class="modal-header py-3 bg-dark border-0">
    <div class="d-flex w-100">
        <h5 class="modal-title text-gray-300">{{ $content->name }}</h5>
    </div>
    <div class="btn btn-icon bg-pure-darker ms-2" data-bs-dismiss="modal" aria-label="Close">
        <span class="svg-icon svg-icon-2x fw-bolder">X</span>
    </div>
</div>
<form action="{{ route('agenda.update', $content->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-body">
        @include('pages.agenda._form')
    </div>
    <div class="modal-footer py-3 bg-light">
        <button class="btn btn-primary btn-active-danger mb-3">
            Atualizar
        </button>  
    </div>
</form> 