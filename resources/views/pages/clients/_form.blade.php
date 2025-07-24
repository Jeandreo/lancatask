<div class="row">
    <div class="col-md-6 mb-4">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" name="name" placeholder="Nome completo / Fantasia" value="{{ $content->name ?? old('name') }}" required>
    </div>
    <div class="col-md-3 mb-4">
        <label class="form-label fw-bold required">Tipo:</label>
        <select class="form-select form-select-solid"
                name="person_type" data-control="select2" data-hide-search="true"
                data-placeholder="Selecione" required>
            <option value=""></option>
            <option value="PF" {{ old('person_type', $content->person_type ?? 'PF') === 'PF' ? 'selected' : '' }}>Pessoa Física</option>
            <option value="PJ" {{ old('person_type', $content->person_type ?? '') === 'PJ' ? 'selected' : '' }}>Pessoa Jurídica</option>
        </select>
    </div>
    <div class="col-md-3 mb-4">
        <label class="form-label fw-bold">Documento:</label>
        <input type="text" class="form-control form-control-solid input-document" name="document" placeholder="CPF/CNPJ" value="{{ $content->document ?? old('document') }}">
    </div>
    <div class="col-md-4 mb-4 div-pj" style="display:none;">
        <label class="form-label fw-bold">Razão Social:</label>
        <input type="text" class="form-control form-control-solid" name="company_name" placeholder="Razão social" value="{{ $content->company_name ?? old('company_name') }}">
    </div>
    <div class="col-md-3 mb-4 div-pj" style="display:none;">
        <label class="form-label fw-bold">Inscrição Estadual:</label>
        <input type="text" class="form-control form-control-solid" name="state_registration" placeholder="IE" value="{{ $content->state_registration ?? old('state_registration') }}">
    </div>
    <div class="col-md-5 mb-4 div-pj" style="display:none;">
        <label class="form-label fw-bold">Contato (Nome):</label>
        <input type="text" class="form-control form-control-solid" name="contact_name" placeholder="Nome do responsável" value="{{ $content->contact_name ?? old('contact_name') }}">
    </div>
    <div class="col-md-3 mb-4">
        <label class="form-label fw-bold">E-mail:</label>
        <input type="email" class="form-control form-control-solid" name="email" placeholder="email@dominio.com" value="{{ $content->email ?? old('email') }}">
    </div>
    <div class="col-md-3 mb-4">
        <label class="form-label fw-bold">Telefone:</label>
        <input type="text" class="form-control form-control-solid input-phone" name="phone" placeholder="(00) 00000-0000" value="{{ $content->phone ?? old('phone') }}">
    </div>
    <div class="col-md-3 mb-4">
        <label class="form-label fw-bold">Entrada:</label>
        <input type="text" class="form-control form-control-solid flatpickr-date" name="start_date" placeholder="dd/mm/yyyy" value="{{ $content->start_date ?? old('start_date') }}">
    </div>
    <div class="col-md-3 mb-4">
        <label class="form-label fw-bold">Saída:</label>
        <input type="text" class="form-control form-control-solid flatpickr-date" name="end_date" placeholder="dd/mm/yyyy" value="{{ $content->end_date ?? old('end_date') }}">
    </div>

    <div class="col-12"><h6 class="fw-bold mb-3">Endereço</h6></div>

    <div class="col-md-3 mb-4">
        <label class="form-label fw-bold">CEP:</label>
        <input type="text" class="form-control form-control-solid input-cep" name="zip" placeholder="00000-000" value="{{ $content->zip ?? old('zip') }}">
    </div>
    <div class="col-md-5 mb-4">
        <label class="form-label fw-bold">Rua:</label>
        <input type="text" class="form-control form-control-solid" name="street" placeholder="Rua/Avenida" value="{{ $content->street ?? old('street') }}">
    </div>
    <div class="col-md-2 mb-4">
        <label class="form-label fw-bold">Número:</label>
        <input type="text" class="form-control form-control-solid" name="number" placeholder="Nº" value="{{ $content->number ?? old('number') }}">
    </div>
    <div class="col-md-2 mb-4">
        <label class="form-label fw-bold">Bairro:</label>
        <input type="text" class="form-control form-control-solid" name="neighborhood" placeholder="Bairro" value="{{ $content->neighborhood ?? old('neighborhood') }}">
    </div>
    <div class="col-md-5 mb-4">
        <label class="form-label fw-bold">Cidade:</label>
        <input type="text" class="form-control form-control-solid" name="city" placeholder="Cidade" value="{{ $content->city ?? old('city') }}">
    </div>
    <div class="col-md-2 mb-4">
        <label class="form-label fw-bold">Estado:</label>
        <select name="state" class="form-select form-select-solid">
            <option value="">Selecione</option>
            <option value="AC" {{ isset($content) && $content->state === 'AC' ? 'selected' : '' }}>Acre</option>
            <option value="AL" {{ isset($content) && $content->state === 'AL' ? 'selected' : '' }}>Alagoas</option>
            <option value="AP" {{ isset($content) && $content->state === 'AP' ? 'selected' : '' }}>Amapá</option>
            <option value="AM" {{ isset($content) && $content->state === 'AM' ? 'selected' : '' }}>Amazonas</option>
            <option value="BA" {{ isset($content) && $content->state === 'BA' ? 'selected' : '' }}>Bahia</option>
            <option value="CE" {{ isset($content) && $content->state === 'CE' ? 'selected' : '' }}>Ceará</option>
            <option value="DF" {{ isset($content) && $content->state === 'DF' ? 'selected' : '' }}>Distrito Federal</option>
            <option value="ES" {{ isset($content) && $content->state === 'ES' ? 'selected' : '' }}>Espírito Santo</option>
            <option value="GO" {{ isset($content) && $content->state === 'GO' ? 'selected' : '' }}>Goiás</option>
            <option value="MA" {{ isset($content) && $content->state === 'MA' ? 'selected' : '' }}>Maranhão</option>
            <option value="MT" {{ isset($content) && $content->state === 'MT' ? 'selected' : '' }}>Mato Grosso</option>
            <option value="MS" {{ isset($content) && $content->state === 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
            <option value="MG" {{ isset($content) && $content->state === 'MG' ? 'selected' : '' }}>Minas Gerais</option>
            <option value="PA" {{ isset($content) && $content->state === 'PA' ? 'selected' : '' }}>Pará</option>
            <option value="PB" {{ isset($content) && $content->state === 'PB' ? 'selected' : '' }}>Paraíba</option>
            <option value="PR" {{ isset($content) && $content->state === 'PR' ? 'selected' : '' }}>Paraná</option>
            <option value="PE" {{ isset($content) && $content->state === 'PE' ? 'selected' : '' }}>Pernambuco</option>
            <option value="PI" {{ isset($content) && $content->state === 'PI' ? 'selected' : '' }}>Piauí</option>
            <option value="RJ" {{ isset($content) && $content->state === 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
            <option value="RN" {{ isset($content) && $content->state === 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
            <option value="RS" {{ isset($content) && $content->state === 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
            <option value="RO" {{ isset($content) && $content->state === 'RO' ? 'selected' : '' }}>Rondônia</option>
            <option value="RR" {{ isset($content) && $content->state === 'RR' ? 'selected' : '' }}>Roraima</option>
            <option value="SC" {{ isset($content) && $content->state === 'SC' ? 'selected' : '' }}>Santa Catarina</option>
            <option value="SP" {{ isset($content) && $content->state === 'SP' ? 'selected' : '' }}>São Paulo</option>
            <option value="SE" {{ isset($content) && $content->state === 'SE' ? 'selected' : '' }}>Sergipe</option>
            <option value="TO" {{ isset($content) && $content->state === 'TO' ? 'selected' : '' }}>Tocantins</option>
        </select>
    </div>
    <div class="col-md-5 mb-4">
        <label class="form-label fw-bold">Complemento:</label>
        <input type="text" class="form-control form-control-solid" name="complement" placeholder="Ap, bloco, referência..." value="{{ $content->complement ?? old('complement') }}">
    </div>

    <div class="col-12 mb-4">
        <label class="form-label fw-bold">Observações:</label>
        <textarea name="observations" class="form-control form-control-solid" rows="3" placeholder="Alguma observação?">{{ $content->observations ?? old('observations') }}</textarea>
    </div>
</div>

@section('custom-footer')
<script>
    function maskDocument(){

        // Obtém o tipo de pessoa
        var personType = $('[name="person_type"]').val();

        // Aplica a máscara de CPF ou CNPJ
        if (personType === 'PF') {
            Inputmask(["999.999.999-99"], {
                "clearIncomplete": true,
            }).mask(".input-document");
        } else {
            Inputmask(["99.999.999/9999-99"], {
                "clearIncomplete": true,
            }).mask(".input-document");
        }
        
    }

    $(document).ready(function() {
        maskDocument();
    });
</script>
@endsection
