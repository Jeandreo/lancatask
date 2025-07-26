<div class="modal-body p-0">
	<div class="row m-0">
		<div class="col-12 col-md-4 bg-dark h-md-600px rounded-task-left p-0">
			<div class="h-50px h-md-75px p-3 d-flex align-items-center justify-content-center" style="border-bottom: solid 1px rgba(0, 0, 0, 0.1)">
				<h2 class="text-white fw-bold text-uppercase m-0">Detalhes da Tarefa</h2>
			</div>
			<div class="px-8 h-md-500px pb-4 mb-md-0">
				<div class="h-md-300px">
					<div class="d-flex mb-4 mt-7">
						<span class="badge text-white" style="background-color: {{ $task->module->color }};">{{ $task->module->name }}</span>
						@if ($task->status == 0)
						<span class="badge badge-danger text-white ms-2">Arquivada</span>
						@endif
						@if ($task->subtasks->count())
						<div class="form-check form-switch form-check-custom form-check-solid ms-4">
							<input class="form-check-input h-20px w-30px cursor-pointer" name="challenge" data-task="{{ $task->id }}" type="checkbox" @if($task->challenge) checked @endif id="challenge_task"/>
							<label class="form-check-label fw-bold cursor-pointer" for="challenge_task">
								Subtarefas
							</label>
						</div>
						@endif
					</div>
                    <textarea class="form-control form-control-flush fs-2x my-4 px-0 text-white py-0 lh-1 auto-height input-name" name="name" style="resize: none; height: auto; overflow: hidden;" rows="1" data-task="{{ $task->id }}">{{ $task->name }}</textarea>
					{{-- <textarea class="text-gray-200 fs-6 bg-transparent p-0 border-0 w-100 task-description mh-100px mh-md-300px" @if ($task->status == 0) disabled @endif placeholder="anotações aqui..." style="resize: none;" name="description" rows="8" data-task="{{ $task->id }}">@if($task->description){{ $task->description }}@endif</textarea> --}}
				</div>
				<div class="h-md-50px d-flex align-items-center mb-2">
                    <span class="fw-bold text-white cursor-pointer">Participantes:</span>
                    <div class="symbol-group symbol-hover flex-nowrap ms-5 list-participants-{{ $task->id }}">
                        @include('pages.tasks._participants')
                    </div>
                </div>
				{{-- ALINHAR EM BAIXO  --}}
				<div class="d-flex align-items-end h-md-150px pb-5">
					<div class="w-100">
						<div class="row pb-3 mb-2" style="border-bottom: solid 1px rgba(0, 0, 0, 0.1)">
							<div class="col-4">
								<p class="text-white fw-bolder m-0 text-uppercase fs-8">Autor</p>
							</div>
							<div class="col-8">
								<p class="text-white text-end m-0">{{ $task->author->name }}</p>
							</div>
						</div>
						<div class="row pb-3 mb-2" style="border-bottom: solid 1px rgba(0, 0, 0, 0.1)">
							<div class="col-4">
								<p class="text-white fw-bolder m-0 text-uppercase fs-8">Projeto</p>
							</div>
							<div class="col-8">
								<p class="text-white text-end m-0">{{ $task->module->project->name }}</p>
							</div>
						</div>
						<div class="row pb-3 mb-2">
							<div class="col-4">
								<p class="text-white fw-bolder m-0 text-uppercase fs-8">Criado as</p>
							</div>
							<div class="col-8">
								<p class="text-white text-end m-0">{{ $task->created_at->format('d/m/Y H:i') }}</p>
							</div>
						</div>
						<div class="row" style="border-bottom: solid 1px rgba(0, 0, 0, 0.1)">
                            <p class="btn btn-sm btn-info fw-bold text-uppercase mb-0" id="see-historic" data-task="{{ $task->id }}">Ver Histórico</p>
                            <p class="btn btn-sm btn-success fw-bold text-uppercase mb-0" id="see-details" style="display: none;">Ver Detalhes</p>
                        </div>
					</div>
				</div>
				{{-- ALINHAR EM BAIXO  --}}
			</div>
		</div>
		<div class="col-12 col-md-8 h-md-600px bg-gray-200 rounded-task-right p-0">
			<div id="task-details">
                <div class="h-75px p-3 d-flex align-items-center justify-content-center position-relative opacity-1" style="border-bottom: solid 1px rgba(0, 0, 0, 0.05);">
                    <p class="text-gray-600 text-uppercase m-0 text-center"><i>Tudo começa com uma ideia.</i></p>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2 position-absolute opacity-0" style="right: 20px;" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark text-white text-gray-400 fs-5"></i>
                    </div>
                </div>
                <div class="rounded scroll-y scroll-dark h-md-375px p-3 show-image-div" id="results-comments">
                    {{-- COMMENTS HERE --}}
                    {{-- COMMENTS HERE --}}
                    {{-- COMMENTS HERE --}}
                </div>
                <div class="h-150px p-3">
                    @if ($task->status != 0)
                    <form action="" method="POST" class="position-relative ck-tiny">
                        @csrf
                        <div class="pt-0" data-bs-theme="dark">
                            <textarea name="text" placeholder="Algum comentário sobre essa tarefa?" class="load-editor"></textarea>
                        </div>
                        <div class="text-end position-absolute z-index-3" style="bottom: 5px; right: 5px;">
                            <button class="btn btn-sm btn-icon" type="button" id="attach-file">
                                <i class="fa-solid fa-paperclip"></i>
                            </button>
                            <button class="btn btn-sm btn-info btn-active-success fw-bold text-uppercase" id="send-comment" data-task="{{ $task->id }}">
                                Enviar
                                <i class="fa-regular fa-paper-plane fs-5 pe-0"></i>
                            </button>
                            <input type="file" accept="image/*" id="file-textarea" style="display: none;" />
                        </div>
                    </form>
                    @endif
                </div>
            </div>
            <div class="p-5 scroll-y h-100" id="task-historic" style="display: none">
                <div class="w-100 bg-light border border-dashed border-1 border-gray-200 h-200px rounded d-flex justify-content-center align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border text-primary me-4" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div>
                            <h2 class="fw-bold text-gray-900 mb-0">Carregando...</h2>
                            <p class="m-0 text-gray-600">Aguarde enquanto nosso sistema busca o histórico dessa tarefa.</p>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
