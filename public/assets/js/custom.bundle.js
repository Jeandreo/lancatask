/**
 * Função responsável por gerar as tabelas do website.
 * Obs.: Tabelas server site são configuradas na própria página.
 *
 * Metronic:  https://preview.keenthemes.com/html/metronic/docs/forms/inputmask
 * Website:   https://datatables.net/
 *
 * Esta função também possui um extensão para o input da paginação.
 * GitHub: https://datatables.net/plug-ins/pagination/input
 *
 * ATENÇÃO: Baixamos o arquivo da extenção e realizamos alguns ajustes
 * de tradução e usabilidade que a versão nativa não possuia.
 * Local: "/public/assets/js/datatable-input.js"
 */
function loadTables(seletor = '.datatables', items = 25, order = undefined) {
    const table = $(seletor);
    const dataTableOptions = {
        pageLength: items,
        order: order,
        aaSorting: [],
        language: {
            search: 'Pesquisar:',
            lengthMenu: 'Mostrando _MENU_ registros por página',
            zeroRecords: 'Ops, não encontramos nenhum resultado :(',
            info: 'Mostrando _START_ até _END_ de _TOTAL_ registros',
            infoEmpty: 'Nenhum registro disponível',
            infoFiltered: '(Filtrando _MAX_ registros)',
            processing: 'Filtrando dados',
            paginate: {
                previous: 'Anterior',
                next: 'Próximo',
                first: '<i class="fa-solid fa-angles-left text-gray-300 text-hover-primary cursor-pointer"></i>',
                last: '<i class="fa-solid fa-angles-right text-gray-300 text-hover-primary cursor-pointer"></i>',
            },
        },
        dom:
            "<'row'" +
            "<'col-sm-6 d-flex align-items-center justify-content-start'l>" +
            "<'col-sm-6 d-flex align-items-center justify-content-end'f>" +
            '>' +
            "<'table-responsive'tr>" +
            "<'row'" +
            "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
            "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
            '>',
    };
    table.DataTable(dataTableOptions);
}

/**
 * Função responsável por gerar calendários no Core.
 *
 * Metronic:  https://preview.keenthemes.com/html/metronic/docs/forms/flatpickr
 * Website:   https://flatpickr.js.org/examples/
 */
function generateFlatpickr(options = null, calendarSelector = '.flatpickr', time = false) {

    /**
     * Define opções padrões para o calendário.
     */
    var defaultOptions = {
        locale: "pt",
        timePicker: time,
        startDate: moment().startOf("hour"),
        endDate: moment().startOf("hour").add(32, "hour"),
        autoUpdateInput: false,
        locale: {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "fromLabel": "de",
            "toLabel": "até",
            "customRangeLabel": "Personalizado",
            "daysOfWeek": [
                "Dom",
                "Seg",
                "Ter",
                "Qua",
                "Qui",
                "Sex",
                "Sáb"
            ],
            "monthNames": [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abril",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro",
            ],
            "firstDay": 0
        },
    };

    // Combina as opções personalizadas com as padrões
    var finalOptions = { ...defaultOptions, ...options };

    // Inicializa o calendário para cada input de forma independente
    $(calendarSelector).each(function() {
        var inputElement = $(this); // Elemento específico do input
        var inputValue = inputElement.val(); // Obtém o valor atual do input

        // Se o valor do input existir, defina o startDate e endDate com base nele
        if (inputValue) {
            var dates = inputValue.split(' até ');
            if (dates.length === 2) {
                var startDate = moment(dates[0], 'DD/MM/YYYY');
                var endDate = moment(dates[1], 'DD/MM/YYYY');
                finalOptions.startDate = startDate;
                finalOptions.endDate = endDate;
            }
        }

        // Configura o daterangepicker para o input específico
        inputElement.daterangepicker(finalOptions, function(start, end, label) {
            // Se o usuário não selecionar nenhuma data, deixa o input vazio
            if (!start || !end) {
                inputElement.val('');
            } else {
                // Caso contrário, preenche com o intervalo de datas selecionado
                inputElement.val(start.format('DD/MM') + ' até ' + end.format('DD/MM'));
            }
        });

        // Evento de aplicar a data
        inputElement.on('apply.daterangepicker', function(ev, picker) {
            // Verifica se as datas estão no mesmo dia
            var formattedValue = picker.startDate.isSame(picker.endDate, 'day')
                ? picker.startDate.format('DD/MM/YYYY')
                : picker.startDate.format('DD/MM') + ' até ' + picker.endDate.format('DD/MM');

            inputElement.val(formattedValue); // Preenche o valor do input

            // Função de callback opcional
            if (typeof onChange !== 'undefined' && onChange === true) {
                flatpickrRangeFunction();
            }
        });
    });

    // Evento de aplicar a data no daterangepicker
    $(calendarSelector).on('apply.daterangepicker', function(ev, picker) {
        var dateStart = picker.startDate.format('YYYY-MM-DD');
        var dateEnd = picker.endDate.format('YYYY-MM-DD');
        var taskId = $(this).data('task');
        changeDate(taskId, dateStart, dateEnd);
    });
}




// CUSTOM UPLOAD
class MyUploadCKE {
    constructor(loader) {
        // INSTANCE TO BE USED
        this.loader = loader;
    }

    // STARTS THE UPLOAD PROCESS
    upload() {
        return this.loader.file
            .then(file => new Promise((resolve, reject) => {
                this._initRequest();
                this._initListeners(resolve, reject, file);
                this._sendRequest(file);
            }));
    }

    // ABORTS THE UPLOAD PROCESS
    abort() {
        if (this.xhr) {
            this.xhr.abort();
        }
    }

    //  INITIALIZE THE OBJECT USING URL PASSED
    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();
        xhr.open('POST', globalUrl + '/configuracoes/cke-upload', true);
        xhr.setRequestHeader('x-csrf-token', csrf);
        xhr.responseType = 'json';
    }

    // INIT LISTENERS
    _initListeners(resolve, reject, file) {
        const xhr = this.xhr;
        const loader = this.loader;
        const genericErrorText = `Couldn't upload file: ${file.name}.`;

        xhr.addEventListener('error', () => reject(genericErrorText));
        xhr.addEventListener('abort', () => reject());
        xhr.addEventListener('load', () => {

            // ERROR
            const response = xhr.response;
            if (!response || response.error) {
                return reject(response && response.error ? response.error.message : genericErrorText);
            }

            // SUCCESS
            resolve({
                default: response.url
            });

        });

        // UPLOAD PROGRESS
        if (xhr.upload) {
            xhr.upload.addEventListener('progress', evt => {
                if (evt.lengthComputable) {
                    loader.uploadTotal = evt.total;
                    loader.uploaded = evt.loaded;
                }
            });
        }
    }

    // PREPARE DATA AND SENDS REQUEST
    _sendRequest(file) {

        // CREATE FORMDATA
        const data = new FormData();

        // APPEND FILE
        data.append('upload', file);

        // SEND REQUEST.
        this.xhr.send(data);
    }
}

function UploadPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        // URL TO UPLOAD CKE
        return new MyUploadCKE(loader);
    };
}


// FUNCTION CKE EDITOR
function loadEditorText(selector = '.load-editor') {

    ClassicEditor.create(document.querySelector(selector), {
        extraPlugins: [UploadPlugin],
        removePlugins: ["MediaEmbedToolbar"],
    }).then(function (editor) {
        // ALOW ACCESS TO CLEAR
        textarea = editor;

        // Detectar seleção de arquivo
        const fileInput = document.querySelector('#file-textarea');
        fileInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                // Criar um link ou uma imagem dependendo do tipo de arquivo
                const reader = new FileReader();
                reader.onload = function (e) {
                    const fileContent = e.target.result;

                    // Criar um elemento de imagem a partir da URL de dados
                    const imageElement = `<img src="${fileContent}" alt="Uploaded Image" />`;

                    // Inserir a imagem no editor
                    editor.model.change(writer => {
                        const imageElementNode = writer.createElement('imageBlock', {
                            src: fileContent
                        });
                        const insertPosition = editor.model.document.selection.getFirstPosition();
                        editor.model.insertContent(imageElementNode, insertPosition);
                    });
                };
                reader.readAsDataURL(file); // Lê o arquivo como URL de dados
            }
        });
    });

}

$(document).on('click', '.show-image, .show-image-div img, figure img', function(){

    // GET LINK IMAGE
    var url = $(this).attr('src');

    // REPLACE IN MODAL
    $('#preview-image-modal').attr('src', url);

    // OPEN MODAL
    $('#preview_image_modal').modal('show');

});

function select2Images(selector = '.select-with-images'){

    // FORMAT OPTIONS SELECT2 WITH IMAGES
    var optionFormat = function(item) {
        if ( !item.id ) {
            return item.text;
        }

        var span = document.createElement('span');
        var imgUrl = item.element.getAttribute('data-kt-select2-user');
        var template = '';

        template += '<img src="' + imgUrl + '" class="rounded-circle h-20px me-2" alt="image"/>';
        template += item.text;

        span.innerHTML = template;

        return $(span);
    }

    // INIT SELECT2 IMAGES
    $(selector).select2({
        templateSelection: optionFormat,
        templateResult: optionFormat
    });

}

function autoHeight(){
    $('.auto-height').on('input', function() {
        var input = $(this);
        input.css('height', 'auto'); // Reseta a altura
        input.css('height', input[0].scrollHeight + 'px'); // Ajusta a altura conforme o conteúdo
    });
}

// Chama funções necessárias
$(document).ready(function() {
    autoHeight();
    loadTables();
    select2Images();
    generateFlatpickr();
});


function loadFlatpickrRange(selector = '.flatpickr-ranges', startAndEnd = 'default', onChange = false) {

    $(selector).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        if(onChange == true){
            flatpickrRangeFunction();
        }

    });

    $(selector).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // Definir as datas de início e fim com base no parâmetro startAndEnd
    let startDate = null;
    let endDate = null;

    if (startAndEnd === 'month' || startAndEnd === 'default') {
        startDate = moment().startOf('month');
        endDate = moment().endOf('month');
    } else if (startAndEnd === 'week') {
        startDate = moment().startOf('week');
        endDate = moment().endOf('week');
    }

    // Inicializar o daterangepicker com as datas configuradas, se houver
    $(selector).daterangepicker({
        autoUpdateInput: false,
        startDate: startDate,
        endDate: endDate,
        ranges: {
            "Hoje": [moment(), moment()],
            "Últimos 7 Dias": [moment().subtract(6, "days"), moment()],
            "Mês passado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
            "Este mês": [moment().startOf("month"), moment().endOf("month")],
            "Próximo mês": [moment().startOf("month").add(1, 'months'), moment().endOf("month").add(1, 'months')],
            "Este ano": [moment().startOf('year'), moment().endOf('year')],
        },
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "fromLabel": "de",
            "toLabel": "até",
            "customRangeLabel": "Personalizado",
            "daysOfWeek": [
                "Dom",
                "Seg",
                "Ter",
                "Qua",
                "Qui",
                "Sex",
                "Sáb"
            ],
            "monthNames": [
                "Janeiro",
                "Fevereiro",
                "Março",
                "Abril",
                "Maio",
                "Junho",
                "Julho",
                "Agosto",
                "Setembro",
                "Outubro",
                "Novembro",
                "Dezembro",
            ],
            "firstDay": 0
        },
    });
}


/**
 * Função responsável por gerar calendários no Core.
 *
 * Metronic:  https://preview.keenthemes.com/html/metronic/docs/forms/flatpickr
 * Website:   https://flatpickr.js.org/examples/
 */
function generateFlatpickrBase(options = null, calendarSelector = '.flatpickr') {

    /**
     * Define opções padrões para o calendário.
     */
    var defaultOptions = {
        allowInput: false,
        altInput: true,
        altFormat: "d/m/Y H:i",
        dateFormat: "Y-m-d H:i:s",
        locale: "pt",
        enableTime: false,
        dateFormat: "Y-m-d H:i",
        // minDate: "today",
    };

    // Sobrescreve as opções personalizadas nas padrões
    var options = { ...defaultOptions, ...options };

    // Inicia calendário
    $(calendarSelector).flatpickr(options);

}

loadFlatpickrRange();
generateFlatpickrBase({enableTime: true}, '.flatpickr-with-time');
