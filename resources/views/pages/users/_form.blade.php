<div class="row">
    <div class="col-4 mb-5">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" placeholder="Nome" name="name" value="{{ $content->name ?? old('name') }}" required/>
    </div>
    <div class="col-4 mb-5">
        <label class="required form-label fw-bold">Email:</label>
        <input type="email" class="form-control form-control-solid" placeholder="nome@gmail.com" name="email" value="{{ $content->email ?? old('email') }}" required/>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold required">Grupo de usuário:</label>
        <select class="form-select form-select-solid" name="role" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            <option value="Administrador" @if(isset($content) && $content->role == 'Administrador') selected @endif>Administrador</option>
            <option value="Gerente" @if(isset($content) && $content->role == 'Gerente') selected @endif>Gerente</option>
            <option value="Colaborador" @if(isset($content) && $content->role == 'Colaborador') selected @endif>Colaborador</option>
        </select>
    </div>
    <div class="col-4 mb-5">
        <label class="@if(!isset($content)) required @endif form-label fw-bold">Senha:</label>
        <input type="password" class="form-control form-control-solid" placeholder="****" name="password" value="" @if(!isset($content)) required @endif/>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold required">Cargo:</label>
        <select class="form-select form-select-solid" name="position_id" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            @foreach ($positions as $position)
            <option value="{{ $position->id }}" @if(isset($content) && $content->id == $position->id) selected @endif>{{ $position->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold">Imagem do usuário:</label>
        <input type="file" class="form-control form-control-solid" name="photo" value="{{ $content->photo ?? old('photo') }}" accept="image/*"/>
    </div>
</div>
