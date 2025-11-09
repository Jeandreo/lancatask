<div class="modal fade" tabindex="-1" id="modal_meeting">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-absolute">
            <div class="modal-header py-3 bg-dark border-0">
                <div class="d-flex w-100">
                    <h5 class="modal-title text-gray-900">Cadastrar na agenda</h5>
                </div>
                <div class="btn btn-icon bg-pure-darker ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-2x fw-bolder">X</span>
                </div>
            </div>
            <form action="{{ route('agenda.store') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    @include('pages.agenda._form')
                </div>
                <div class="modal-footer py-3 bg-light">
                    <button class="btn btn-info btn-active-danger mb-3">
                        Cadastrar na agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="show_metting">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content position-absolute" id="show-meeting">
            {{-- RESULTS HERE --}}
            {{-- RESULTS HERE --}}
            {{-- RESULTS HERE --}}
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="edit_metting">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-absolute" id="edit-agenda">
            {{-- RESULTS HERE --}}
            {{-- RESULTS HERE --}}
            {{-- RESULTS HERE --}}
        </div>
    </div>
</div>

@section('custom-footer')
@parent
<script>
    // Função para abrir uma reunião
    function openMetting(id){
        var url = "{{ route('agenda.show', '') }}/" + id;
        $.ajax({
            type: 'GET',
            url: url,
            success: function(response){
                $('#show-meeting').html(response);
                $('#show_metting').modal('show');
            }
        });
    }

    // Ao clicar para editar
    $(document).on('click', '.edit-agenda', function(){
        var id = $(this).data('id');
        editMetting(id);
    });


    // Função para editar uma reunião
    function editMetting(id){
        var url = "{{ route('agenda.edit', '') }}/" + id;
        $.ajax({
            type: 'GET',
            url: url,
            success: function(response){
                $('#edit-agenda').html(response);
                $('#edit_metting select').select2({
                    dropdownParent: $('#edit_metting')
                });
                generateFlatpickrDate({
                    dropdownParent: $('#edit_metting'),
                }, '.flatpickr-date');
                generateFlatpickrBase(
                    {
                        altFormat: 'H:i',
                        dateFormat: 'H:i',
                        enableTime: true,
                        noCalendar: true,
                        dropdownParent: $('#edit_metting'),
                    },
                    '.flatpickr-time-custom'
                );

  
            // Configuração do Tagify com verificação de instância existente
            const tagInput = document.querySelector("#edit-agenda .tagify");
            
            // Destruir instância existente se houver
            if (tagInput && tagInput.tagify) {
                tagInput.tagify.destroy();
            }
            
            // Criar nova instância apenas se o elemento existir
            if (tagInput) {
                const tagify = new Tagify(tagInput, {
                    // Suas configurações personalizadas aqui, se necessário
                    duplicates: false,
                    placeholder: "Digite e-mails...",
                    pattern: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
                });
                
                tagInput.tagify = tagify;
            }
            

                KTMenu.createInstances();
                $('#edit_metting').modal('show');
            }
        });
    }
</script>
@endsection
