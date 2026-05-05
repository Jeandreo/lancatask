<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class BudgeProcessingController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function processing(Request $request)
    {

        // Extrai dados
        $data = $request->all();

        // Inicia consulta
        $query = $this->loadTables();

        // Filtra dados relevantes
        $query = $this->filters($query, $data);

        // Filtra pela busca
        $query = $this->search($query, $data);

        // Ordena resultados
        $query = $this->ordering($query, $data);

        // Retorna dados
        return $this->formatResults($query, $request);

    }

     /**
     * Inicializa a consulta com junções e seleção de colunas.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function loadTables(){

        // Tabela principal
        $query = DB::table('budges');

        // Une com a tabela de usuários para extrair quem criou o registro
        $query->leftJoin('clients', 'budges.client_id', '=', 'clients.id');
        $query->leftJoin('budge_items', 'budge_items.budge_id', '=', 'budges.id');
        $query->leftJoin('orders', 'orders.budge_id', '=', 'budges.id');

        // Retorna consulta
        return $query;

    }


    /**
     * Inicializa a consulta com junções e seleção de colunas.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function filters($query, $data)
    {

        // Obtém dados da consulta
        $filters = $data;

        // Filtra por status
        if (isset($filters['status'])) {
            $query->whereIn('budges.status', $filters['status']);
        }

        // Filtra por data
        if (isset($filters['dates'])) {

            // Separa datas
            $dates = explode(' - ', $filters['dates']);

            // Formata para o banco de dados
            $dateStart = Carbon::createFromFormat('d/m/Y', $dates[0]);
            $dateEnd = Carbon::createFromFormat('d/m/Y', $dates[1]);

            // Filtra por data
            $query->whereBetween('budges.created_at', [$dateStart, $dateEnd]);

        }

        return $query;

    }

    /**
     * Aplica filtros de pesquisa à consulta.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Query\Builder
     */
    public function search($query, $data)
    {
        // Obtém dados da consulta
        $searchBy = $data['searchBy'];

        // Realiza filtro na busca
        if ($searchBy != '') {
            // Realiza busca no nome do fornecedor
            $query->where('budges.name', 'like', "%$searchBy%");
        }

        // Retorna a query
        return $query;
    }

    /**
     * Aplica a ordenação à consulta.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Query\Builder
     */
    public function ordering($query, $data)
    {

        // Ordena de acordo com a coluna desejada
        if ($data['order']) {

            // Direção e coluna
            $direction = $data['order'][0]['dir'];
            $orderThis = $data['order_by'];

            // Define qual a lógica de ordenação
            switch ($orderThis) {
                case 'name':
                    $column = 'budges.name';
                    break;

                case 'created_at':
                    $column = 'budges.created_at';
                    break;

                case 'status':
                    $column = 'budges.status';
                    break;

                default:
                    $column = 'budges.id';
                    break;
            }

            // Ordena a coluna
            return $query->orderBy($column, $direction);

        }
    }

    /**
     * Aplica a ordenação à consulta.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Query\Builder
     */
    public function formatResults($query)
    {
        // SELECT final
        $query->select(
            'budges.id               as id',
            'budges.name             as name',
            'orders.id               as order_id',
            'budges.created_at       as created_at',
            'budges.date_completion  as date_completion',
            'budges.status           as status',
            'clients.name            as client_name',
            DB::raw('COALESCE(SUM(budge_items.price_total), 0) as total_items')
        );

        // GROUP BY final
        $query->groupBy(
            'budges.id',
            'budges.name',
            'orders.id',
            'budges.date_completion',
            'budges.created_at',
            'budges.status',
            'clients.name'
        );

        return DataTables::of($query)
            ->addColumn('id', function ($row) {
                return '<a href="' . route('core.orders.edit', $row->id) . '" class="text-gray-700 text-hover-primary fw-bold fs-6">
                            <p class="mb-0 lh-1 mt-1">#' . str_pad($row->id, 4, '0', STR_PAD_LEFT) . '</p>' . '
                            <span class="text-gray-500 fw-normal fs-8">' . date('d/m/Y H:i', strtotime($row->created_at)) . '</span>
                        </a>';
            })
            ->addColumn('name', function ($row) {
                // Verifica o cliente
                $client = $row->client_name ? "<span class='text-gray-700'>{$row->client_name}</span>" : "<span class='badge badge-light'>-</span>";
                return "<a href='" . route('core.budges.edit', $row->id) . "'
                        class='text-gray-700 fw-bold text-hover-primary fs-6'>
                            {$row->name}
                        </a><br>
                       {$client}";
            })
            ->addColumn('price_total', function ($row) {
                $formatted = number_format($row->total_items, 2, ',', '.');
                return "<span class='text-primary fw-bolder fs-7'>R$ {$formatted}</span>";
            })

            ->addColumn('price_total', function ($row) {
                $formatted = number_format($row->total_items, 2, ',', '.');
                return "<span class='text-primary fw-bolder fs-7'>R$ {$formatted}</span>";
            })

            ->addColumn('status_date', function ($row) {
                $status = $row->date_completion ? "<span class='text-gray-600'>" . date('d/m/Y H:i', strtotime($row->date_completion)) . "</span>" : "";
                return $status;
            })

            ->addColumn('order_id', function ($row) {

                if(empty($row->order_id)) {
                    return "-";
                } else {
                    return "<a href='" . route('core.orders.edit', $row->order_id) . "' class='btn btn-sm btn-success btn-active-primary fw-bolder text-uppercase'>Pedido #" . str_pad($row->order_id, 4, '0', STR_PAD_LEFT) . "</a>";
                }

            })

            ->addColumn('status', function ($row) {
                $status = match ($row->status) {
                    'pendente' => "<span class='badge badge-light-warning'>Pendente</span>",
                    'aprovado' => "<span class='badge badge-light-success'>Aprovado</span>",
                    default => "<span class='badge badge-light-danger'>Recusado</span>",
                };
                return $status;
            })

           ->addColumn('actions', function ($row) {

                $html = "<div class='d-flex align-items-center icons-edit gap-2'>";

                // Editar
                $html .= "<a href='" . route('core.budges.edit', $row->id) . "'>
                            <i class='fas fa-edit fs-4 text-hover-primary' title='Editar'></i>
                        </a>";

                // Pendente → Aprovar / Rejeitar
                if ($row->status === 'pendente') {

                    $html .= "<a href='" . route('core.budges.status', ['id' => $row->id, 'status' => 'aprovado']) . "'>
                                <i class='fa-solid fs-4 fa-circle-check text-hover-success' title='Aprovado'></i>
                            </a>";

                    $html .= "<a href='" . route('core.budges.status', ['id' => $row->id, 'status' => 'recusado']) . "'>
                                <i class='fa-solid fs-4 fa-circle-xmark text-hover-danger' title='Rejeitar'></i>
                            </a>";
                }

                // Recusado → Reverter
                elseif ($row->status === 'recusado') {

                    $html .= "<a href='" . route('core.budges.status', ['id' => $row->id, 'status' => 'reverter']) . "'>
                                <i class='fa-solid fs-4 fa-rotate-right text-hover-danger' title='Reverter para Pendente'></i>
                            </a>";
                }

                $html .= "</div>";

                return $html;
            })

            ->rawColumns(['id', 'name', 'price_total', 'client_name', 'created_at', 'status', 'status_date', 'actions', 'order_id'])
            ->make(true);
    }

}
