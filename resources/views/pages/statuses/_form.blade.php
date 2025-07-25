<div class="row">
    <div class="col-12 mb-4">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" placeholder="Nome" name="name" value="{{ $content->name ?? old('name') }}" required/>
    </div>
    <div class="col-12 mb-4">
        <label class="required form-label fw-bold">Cor:</label>
        <input type="color" class="form-control form-control-solid" placeholder="Cor" name="color" value="{{ $content->color ?? randomColor() }}" required/>
    </div>
    <div class="col-12 mb-4">
        <label class="required form-label fw-bold">Projeto:</label>
        <select class="form-select form-select-solid" name="project_id" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            @foreach ($projects as $project)
            <option value="{{ $project->id }}" @if(isset($content) && $content->project_id == $project->id) selected @endif>{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 mb-4">
        <div class="form-check form-check-custom form-check-solid">
            <input type="hidden" name="done" value="0"/>
            <input class="form-check-input" name="done" @if(isset($content) && $content->done) checked @endif type="checkbox" value="1" id="taskDone"/>
            <label class="form-check-label" for="taskDone">
                Marcar Tarefa como Concluída?
            </label>
        </div>
    </div>
</div>
