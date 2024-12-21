<div class="row">
    <div class="col-4 mb-5">
        <label class="required form-label fw-bold">Nome:</label>
        <input type="text" class="form-control form-control-solid" placeholder="Nome da categoria" name="name" value="{{ $content->name ?? old('name') }}" required/>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold required">Tipo:</label>
        <select class="form-select form-select-solid" name="type" data-control="select2" data-placeholder="Selecione" required>
            <option value=""></option>
            <option value="expense" @if(isset($content) && $content->type == 'expense') selected @endif>Despesa</option>
            <option value="revenue" @if(isset($content) && $content->type == 'revenue') selected @endif>Receita</option>
        </select>
    </div>
    <div class="col-4 mb-5">
        <label class="form-label fw-bold">Categoria:</label>
        <select class="form-select form-select-solid select-categories" name="father_id" data-placeholder="Selecione">
            <option></option>
            @foreach ($categories as $category)
            <option value="{{ $category->id }}" @if(isset($content) && $content->father_id == $category->id) selected @endif data-color="@if($category->father){{ $category->father->color }}@else{{ $category->color }}@endif" data-icon="@if($category->father){{ str_replace(' ', ',', $category->father->icon) }}@else{{ str_replace(' ', ',', $category->icon) }}@endif">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 mb-5 has-father" style="@if(isset($content) && $content->father_id) display:none; @endif">
        <label class="required form-label fw-bold">Cor:</label>
        <input type="color" class="form-control form-control-solid" placeholder="Selecione uma cor" name="color" value="{{ $content->color ?? '#009ef7' }}" required/>
    </div>
    <div class="col-6 mb-5 has-father" style="@if(isset($content) && $content->father_id) display:none; @endif">
        <label class="form-label fw-bold">Ícone:</label> <a href="https://fontawesome.com/search?o=r&m=free" class="fs-7 fw-normal" target="_blank">Font Awesome</a>
        <input type="text" class="form-control form-control-solid" placeholder="fa-solid fa-pen-to-square" name="icon" value="{{ $content->icon ?? old('icon')}}"/>
    </div>
    <div class="col-12 mb-5">
        <label class="form-label fw-bold">Descrição:</label>
        <textarea name="description" class="form-control form-control-solid" placeholder="Alguma observação sobre esta categoria?">@if(isset($content->description)){{$content->description}}@endif</textarea>
    </div>
</div>


@section('custom-footer')
<script>

    // SELECTOR CATEGORIES
    select2Categories();

    // IF HAS CATEGORY FATHER
    $('[name="father_id"]').change(function(){

        // GET NAME
        var father = $(this).val();

        // IF HAS FATHER
        if(father){
            $('.has-father').hide();
            $('.has-father').find('input').prop('required', false);
        } else {
            $('.has-father').show();
            $('.has-father').find('input').prop('required', true);
        }

    });
</script>
@endsection