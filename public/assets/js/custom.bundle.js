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
function generateFlatpickr(options = null, calendarSelector = '.flatpickr') {

    /**
     * Define opções padrões para o calendário.
     */
    var defaultOptions = {
        // allowInput:true,
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        locale: "pt",
        minDate: "today",
    };

    // Sobrescreve as opções personalizadas nas padrões
    var options = { ...defaultOptions, ...options };

    // Inicia calendário
    $(calendarSelector).flatpickr(options);

}

// Chama funções necessárias
$(document).ready(function() {
    loadTables();
    generateFlatpickr();
});
