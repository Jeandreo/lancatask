<div class="row">
    <div class="col-4 mb-5">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" placeholder="Nome do projeto" name="name" value="{{ $content->name ?? old('name') }}" required/>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold required">Tipo:</label>
        <select class="form-select form-select-solid" name="type" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            <option value="Lançamento" @if(isset($content) && $content->type == 'Lançamento') selected @endif>Lançamento</option>
            <option value="Time" @if(isset($content) && $content->type == 'Time') selected @endif>Time</option>
        </select>
    </div>
    <div class="col-4 mb-5">
        <label class="required form-label fw-bold">Cor:</label>
        <input type="color" class="form-control form-control-solid" placeholder="Selecione uma cor" name="color" value="{{ $content->color ?? '#009ef7' }}" required style="height: 43px;"/>
    </div>
    <div class="col-6 mb-5">
        <label class="form-label fw-bold">Início:</label>
        <input type="text" class="form-control form-control-solid flatpickr" placeholder="Definir data de inicio" name="start" value="{{ $content->start ?? '' }}" required/>
    </div>
    <div class="col-6 mb-5">
        <label class="form-label fw-bold">Fim:</label>
        <input type="text" class="form-control form-control-solid flatpickr" placeholder="Definir data de encerramento" name="end" value="{{ $content->end ?? '' }}" required/>
    </div>
    <div class="col-12 mb-5">
        <label class="form-label fw-bold">Descrição:</label>
        <textarea name="description" class="form-control form-control-solid" placeholder="Alguma observação sobre?">@if(isset($content->description)){{$content->description}}@endif</textarea>
    </div>
</div>