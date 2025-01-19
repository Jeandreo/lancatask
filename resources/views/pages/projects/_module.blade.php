<div class="card mb-6">
    <div class="card-header min-h-50px ps-2 pe-6 position-relative opacity-1">
        <div class="d-flex align-items-center ps-3 pe-5 w-50">
            <input type="color" class="form-control form-control-flush p-0 w-20px h-20px border-0 min-h-10px rounded module-colors" data-module="{{ $module->id }}" value="{{ $module->color }}">
            <input class="form-control form-control-flush bg-hover-light rounded py-2 px-3 text-gray-700 fs-5 fw-bold p-0 module-title" data-module="{{ $module->id }}" value="{{ $module->name }}" placeholder="Digite o nome do módulo">
        </div>
        <div class="d-none d-md-flex align-items-center">
            <div class="w-125px text-center text-gray-700 fs-7 text-uppercase fw-bold">
                Responsáveis
            </div>
            <div class="d-flex align-items-center justify-content-center w-150px text-gray-700 fs-7 text-uppercase fw-bold">
                Status
            </div>
            <div class="d-flex align-items-center justify-content-center w-200px text-gray-700 fs-7 text-uppercase fw-bold">
                Data
            </div>
            <div>
                <i class="fa-solid fa-arrows-to-dot py-2 px-3 mx-3 fs-7 text-gray-700"></i>
            </div>
        </div>
        <div class="position-absolute cursor-pointer module-remove" style="top: -10px; right: -10px;" data-module="{{ $module->id }}">
            <span class="d-flex align-items-center justify-content-center opacity-0 text-white fw-bold bg-danger h-25px w-25px rounded-circle">
                X
            </span>
        </div>
    </div>
    <div class="card-body p-5">
        <div class="draggable-zone load-tasks-project" data-type="project" style="min-height: 50px;" data-module="{{ $module->id }}" id="project-tasks-{{ $module->id }}">
            {{-- RESULTS HERE --}}
            {{-- RESULTS HERE --}}
            {{-- RESULTS HERE --}}
            <div class="d-flex justify-content-center mt-2">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            {{-- RESULTS HERE --}}
            {{-- RESULTS HERE --}}
            {{-- RESULTS HERE --}}
        </div>
        <form action="#" method="POST" class="send-tasks">
            @csrf
            <input type="hidden" name="module_id" value="{{ $module->id }}">
            <div class="position-relative">
                <input
                type="text"
                name="name"
                class="form-control form-control-solid w-100 rounded mt-4"
                placeholder="Inserir nova tarefa"
                required
                autocomplete="off"
                autocorrect="off"
                spellcheck="false">
                <button class="btn btn-sm btn-icon btn-success fw-bold text-uppercase position-absolute" style="top: 5px; right: 5px;">
                    <i class="fa-solid fa-rocket fs-5 pe-0 mt-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>
