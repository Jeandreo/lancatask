<div class="row">
    <div class="col-4 mb-5">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" placeholder="Nome" name="name" value="{{ $content->name ?? old('name') }}" required/>
    </div>
    <div class="col-4 mb-5">
        <label class="required form-label fw-bold">Email:</label>
        <input type="email" class="form-control form-control-solid" placeholder="nome@gmail.com" autocomplete="off" name="email" value="{{ $content->email ?? old('email') }}" required/>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold required">Nível de acesso:</label>
        <select class="form-select form-select-solid" name="role" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            <option value="Administrador" @if(isset($content) && $content->role == 'Administrador') selected @endif>Administrador</option>
            <option value="Gerente" @if(isset($content) && $content->role == 'Gerente') selected @endif>Gerente</option>
            <option value="Usuário" @if(isset($content) && $content->role == 'Usuário') selected @endif>Usuário comum</option>
        </select>
    </div>
    <div class="col-4 mb-5">
        <label class="@if(!isset($content)) required @endif form-label fw-bold">Senha:</label>
        <input type="password" class="form-control form-control-solid" placeholder="****" name="password" autocomplete="new-password" autocorrect="off" value="" @if(!isset($content)) required @endif/>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold required">Cargo:</label>
        <select class="form-select form-select-solid" name="position_id" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            @foreach ($positions as $position)
            <option value="{{ $position->id }}" @if(isset($content) && $content->position_id == $position->id) selected @endif>{{ $position->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold">Imagem do usuário:</label>
        <input type="file" class="form-control form-control-solid" name="photo" value="{{ $content->photo ?? old('photo') }}" accept="image/*"/>
    </div>
</div>

<div class="rounded bg-gray-200 p-4">
    <h3 class="fw-bolder">Níveis de permissão</h3>
    <ul class="mb-0">
        <li><b>Administrador:</b> herda permissões de gerente e pode visualizar quadros privados.</li>
        <li><b>Gerente:</b> mantém permissões de gestão, sem acesso a quadros privados.</li>
        <li><b>Usuário comum:</b> mantém acesso padrão atual.</li>
    </ul>
</div>
