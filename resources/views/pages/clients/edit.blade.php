@extends('layouts.app')

@section('Page Title', 'Editar Cliente')

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="card mb-7">
				<div class="card-body">
					<form action="{{ route('clients.update', $content->id) }}" method="POST" enctype="multipart/form-data" id="client-form">
						@csrf
						@method('PUT')
						@include('pages.clients._form')
						<div class="d-flex justify-content-between">
							<a href="{{ route('clients.index') }}" class="btn btn-light mt-2">Voltar</a>
							<button type="submit" class="btn btn-info btn-active-success mt-2" id="submit-client-form">Atualizar</button>
						</div>
					</form>
				</div>
			</div>

            <div class="card mb-7">
                <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold">Contratos do cliente</h3>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addClientContractModal">Adicionar contrato</button>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-bordered gy-3 align-middle">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th>Contrato</th>
                                    <th>Início</th>
                                    <th>Fim</th>
                                    <th>Valor</th>
                                    <th>Duração</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clientContracts as $clientContract)
                                    <tr>
                                        <td>{{ $clientContract->contract->name ?? '-' }}</td>
                                        <td>{{ optional($clientContract->start_date)->format('d/m/Y') }}</td>
                                        <td>{{ optional($clientContract->end_date)->format('d/m/Y') ?: '-' }}</td>
                                        <td>R$ {{ number_format($clientContract->amount, 2, ',', '.') }}</td>
                                        <td>
                                            @if($clientContract->contract && $clientContract->contract->is_open_ended)
                                                <span class="badge badge-light-primary">Sem fim</span>
                                            @else
                                                {{ $clientContract->duration_in_months }} mês(es)
                                            @endif
                                        </td>
                                        <td>
                                            @if($clientContract->status)
                                                <span class="badge badge-light-success">Ativo</span>
                                            @else
                                                <span class="badge badge-light-secondary">Encerrado</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($clientContract->status)
                                                <form action="{{ route('clients.contract.close', [$content->id, $clientContract->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-light-danger">Encerrar</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-10">Nenhum contrato vinculado para este cliente.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold">Cobranças do cliente</h3>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAdhocBillingModal">Lançar avulsa</button>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-bordered gy-3 align-middle">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th>Vencimento</th>
                                    <th>Competência</th>
                                    <th>Origem</th>
                                    <th>Nome</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($billings as $billing)
                                    @php
                                        $isPaid = $billing->billing_status === 'pago';
                                        $isVirtual = !empty($billing->is_virtual);
                                        $badge = 'badge-light-warning';

                                        if ($billing->billing_status === 'pago') {
                                            $badge = 'badge-light-success';
                                        }

                                        if ($billing->billing_status === 'vencido') {
                                            $badge = 'badge-light-danger';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ optional($billing->due_date)->format('d/m/Y') ?: optional($billing->date)->format('d/m/Y') }}</td>
                                        <td>{{ $billing->reference_period ?: '-' }}</td>
                                        <td>
                                            {{ ucfirst($billing->origin_type ?: 'avulsa') }}
                                            @if($isVirtual)
                                                <span class="badge badge-light-primary ms-2">Projetada</span>
                                            @endif
                                        </td>
                                        <td>{{ $billing->name }}</td>
                                        <td>R$ {{ number_format($billing->amount, 2, ',', '.') }}</td>
                                        <td><span class="badge {{ $badge }}">{{ ucfirst($billing->billing_status ?: 'pendente') }}</span></td>
                                        <td class="text-end">
                                            <form action="{{ route('clients.billing.status', $billing->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $isPaid ? 'pendente' : 'pago' }}">
                                                @if($isVirtual)
                                                    <input type="hidden" name="is_virtual" value="1">
                                                    <input type="hidden" name="client_contract_id" value="{{ $billing->client_contract_id }}">
                                                    <input type="hidden" name="reference_period" value="{{ $billing->reference_period }}">
                                                @endif
                                                <button type="submit" class="btn btn-sm {{ $isPaid ? 'btn-light-warning' : 'btn-light-success' }}">
                                                    {{ $isPaid ? 'Marcar como pendente' : 'Marcar como pago' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-10">Nenhuma cobrança cadastrada para este cliente.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
		</div>
	</div>

    <div class="modal fade" id="addClientContractModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar contrato ao cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clients.contract.store', $content->id) }}" method="POST" id="client-contract-form">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="required form-label fw-bold">Contrato:</label>
                                <select class="form-select form-select-solid" name="contract_id" id="modal_contract_id" required>
                                    <option value=""></option>
                                    @foreach($contracts as $contract)
                                        <option value="{{ $contract->id }}">{{ $contract->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="required form-label fw-bold">Valor:</label>
                                <input type="text" class="form-control form-control-solid input-money" name="amount" id="modal_contract_amount" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="required form-label fw-bold">Data de início:</label>
                                <input type="date" class="form-control form-control-solid" name="start_date" id="modal_contract_start_date" required>
                            </div>
                        </div>
                        <div class="alert alert-light-info mb-0 d-none" id="contract-preview-content"></div>
                        <input type="hidden" name="confirm_contract_generation" id="modal_confirm_contract_generation" value="0">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-info" id="preview-contract-generation">Visualizar geração</button>
                        <button type="submit" class="btn btn-success d-none" id="confirm-contract-generation">Confirmar e gerar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addAdhocBillingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova cobrança avulsa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clients.billing.adhoc', $content->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="required form-label fw-bold">Nome da cobrança:</label>
                                <input type="text" class="form-control form-control-solid" name="name" required>
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="required form-label fw-bold">Data:</label>
                                <input type="date" class="form-control form-control-solid" name="date" required>
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="required form-label fw-bold">Valor:</label>
                                <input type="text" class="form-control form-control-solid input-money" name="amount" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="required form-label fw-bold">Carteira:</label>
                                <select class="form-select form-select-solid" name="wallet_id" data-control="select2" data-placeholder="Selecione" required>
                                    <option value=""></option>
                                    @foreach($wallets as $wallet)
                                        <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="required form-label fw-bold">Categoria:</label>
                                <select class="form-select form-select-solid" name="category_id" data-control="select2" data-placeholder="Selecione" required>
                                    <option value=""></option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-0">
                                <label class="form-label fw-bold">Descrição:</label>
                                <textarea class="form-control form-control-solid" rows="3" name="description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Cadastrar cobrança</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custom-footer')
@parent
<script>
    $(document).ready(function () {
        $(document).on('click', '#preview-contract-generation', function () {
            const contractId = $('#modal_contract_id').val();
            const amount = $('#modal_contract_amount').val();
            const startDate = $('#modal_contract_start_date').val();

            if (!contractId || !amount || !startDate) {
                $('#contract-preview-content')
                    .removeClass('d-none alert-light-info')
                    .addClass('alert-light-danger')
                    .html('<p class="mb-0">Preencha contrato, valor e data de início para visualizar a geração.</p>');
                return;
            }

            $.post("{{ route('clients.contract.preview') }}", {
                _token: "{{ csrf_token() }}",
                contract_id: contractId,
                amount: amount,
                start_date: startDate,
            }).done(function (response) {
                let durationText = response.is_open_ended ? 'Sem fim' : (response.duration_in_months + ' mês(es)');
                let projectionText = '';
                if (response.is_open_ended) {
                    projectionText = `<p class=\"mb-0\"><strong>Visualização inicial:</strong> ${response.projection_window} competências projetadas</p>`;
                }

                $('#contract-preview-content').html(`
                    <p><strong>Contrato:</strong> ${response.contract_name}</p>
                    <p><strong>Duração:</strong> ${durationText}</p>
                    <p><strong>Geração:</strong> ${response.generation_text}</p>
                    <p><strong>Data de início:</strong> ${response.start_date}</p>
                    <p><strong>Valor:</strong> ${response.amount}</p>
                    <p><strong>Transações que serão geradas:</strong> ${response.total_transactions}</p>
                    ${projectionText}
                `);
                $('#contract-preview-content')
                    .removeClass('d-none alert-light-danger')
                    .addClass('alert-light-info');
                $('#modal_confirm_contract_generation').val('1');
                $('#confirm-contract-generation').removeClass('d-none');
            }).fail(function (xhr) {
                let message = 'Não foi possível gerar a prévia.';

                if (xhr.status === 419) {
                    message = 'Sessão expirada. Atualize a página e tente novamente.';
                }

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                $('#contract-preview-content')
                    .removeClass('d-none alert-light-info')
                    .addClass('alert-light-danger')
                    .html('<p class="mb-0">' + message + '</p>');
                $('#modal_confirm_contract_generation').val('0');
                $('#confirm-contract-generation').addClass('d-none');
            });
        });

        $('#addClientContractModal').on('hidden.bs.modal', function () {
            $('#modal_confirm_contract_generation').val('0');
            $('#contract-preview-content').addClass('d-none').html('');
            $('#confirm-contract-generation').addClass('d-none');
        });

        $('#addClientContractModal').on('shown.bs.modal', function () {
            if (!$('#modal_contract_start_date').val()) {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                $('#modal_contract_start_date').val(year + '-' + month + '-' + day);
            }
        });
    });
</script>
@endsection
