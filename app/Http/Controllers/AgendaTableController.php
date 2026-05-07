<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AgendaTableController extends Controller
{
    public function processing(Request $request)
    {
        $query = Agenda::query()->select(['id', 'name', 'date_start', 'date_end', 'hour_start', 'hour_end', 'status']);

        $orderColumnIndex = filter_var($request->input('order.0.column'), FILTER_VALIDATE_INT);
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        if ($orderColumnIndex !== false) {
            $orderColumnKey = $request->input('columns.' . $orderColumnIndex . '.data');
            $column = match ($orderColumnKey) {
                'name' => 'name',
                'date' => 'date_start',
                'status' => 'status',
                default => 'id',
            };

            $query->orderBy($column, $orderDirection);
        } else {
            $query->orderBy('status', 'desc')->orderBy('id', 'desc');
        }

        return DataTables::of($query)
            ->addColumn('name', fn ($row) => '<span class="text-gray-900 fw-bold fs-6">' . e($row->name) . '</span>')
            ->addColumn('date', function ($row) {
                if ($row->date_start != $row->date_end) {
                    return '<span class="fw-bold text-gray-900">' . date('d/m/Y H:i', strtotime($row->date_start . ' ' . $row->hour_start)) . ' até ' . date('d/m/Y H:i', strtotime($row->date_end . ' ' . $row->hour_end)) . '</span>';
                }
                return '<span class="fw-bold text-gray-900">' . date('d/m/Y', strtotime($row->date_start)) . ' das ' . date('H:i', strtotime($row->hour_start)) . ' até ' . date('H:i', strtotime($row->date_end . ' ' . $row->hour_end)) . '</span>';
            })
            ->addColumn('status', fn ($row) => $row->status ? '<span class="badge badge-light-success">Ativo</span>' : '<span class="badge badge-light-danger">Inativo</span>')
            ->addColumn('actions', function ($row) {
                $toggleIcon = $row->status ? '<i class="fas fa-times-circle" title="Desativar"></i>' : '<i class="fas fa-redo" title="Reativar"></i>';
                return '<div class="d-flex align-items-center icons-table">'
                    . '<a href="' . route('agenda.destroy', $row->id) . '">' . $toggleIcon . '</a>'
                    . '<a href="#" class="js-confirm-delete" data-url="' . route('agenda.delete', $row->id) . '" data-label="' . e($row->name) . '" data-entity="evento"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>'
                    . '</div>';
            })
            ->rawColumns(['name', 'date', 'status', 'actions'])
            ->toJson();
    }
}
