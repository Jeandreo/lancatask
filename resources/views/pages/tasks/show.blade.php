<div class="modal-body p-0">
	<div class="row m-0">
		<div class="col-12 col-md-4 bg-dark h-md-600px rounded-task-left p-0">
			<div class="h-50px h-md-75px p-3 d-flex align-items-center justify-content-center" style="border-bottom: solid 1px rgba(0, 0, 0, 0.3)">
				<h2 class="text-white fw-bold text-uppercase m-0">Detalhes da missão</h2>
			</div>
			<div class="px-8 h-md-500px pb-4 mb-md-0">
				<div class="h-md-350px">
					<div class="d-flex mb-4 mt-7">
						<span class="badge text-white" style="background-color: {{ $contents->statusInfo->color }};">{{ $contents->statusInfo->name }}</span>
						@if ($contents->status == 0)
						<span class="badge badge-danger text-white">Arquivada</span>
						@endif
						@if ($contents->subtasks->count())
						<div class="form-check form-switch form-check-custom form-check-solid ms-4">
							<input class="form-check-input h-20px w-30px cursor-pointer" name="challenge" data-task="{{ $contents->id }}" type="checkbox" @if($contents->challenge) checked @endif id="challenge_task"/>
							<label class="form-check-label fw-bold cursor-pointer" for="challenge_task">
								DESAFIO
							</label>
						</div>
						@endif
					</div>
					<h2 class="text-white fs-2x my-4">{{ $contents->name }}</h2>
					<textarea class="text-gray-200 fs-6 bg-transparent p-0 border-0 w-100 task-description mh-100px mh-md-300px" @if ($contents->status == 0) disabled @endif placeholder="anotações aqui..." style="resize: none;" name="description" rows="8" data-task="{{ $contents->id }}">@if($contents->description){{ $contents->description }}@endif</textarea>
				</div>
				{{-- ALINHAR EM BAIXO  --}}
				<div class="d-flex align-items-end h-md-150px pb-5">
					<div class="w-100">
						<div class="row pb-3 mb-2" style="border-bottom: solid 1px rgba(0, 0, 0, 0.1)">
							<div class="col-4">
								<p class="text-white fw-bolder m-0 text-uppercase fs-8">Autor</p>
							</div>
							<div class="col-8">
								<p class="text-white text-end m-0">{{ $contents->author->name }}</p>
							</div>
						</div>
						<div class="row pb-3 mb-2" style="border-bottom: solid 1px rgba(0, 0, 0, 0.1)">
							<div class="col-4">
								<p class="text-white fw-bolder m-0 text-uppercase fs-8">Projeto</p>
							</div>
							<div class="col-8">
								<p class="text-white text-end m-0">{{ $contents->project->name }}</p>
							</div>
						</div>
						<div class="row">
							<div class="col-4">
								<p class="text-white fw-bolder m-0 text-uppercase fs-8">Criado as</p>
							</div>
							<div class="col-8">
								<p class="text-white text-end m-0">{{ $contents->created_at->format('d/m/Y H:i:s') }}</p>
							</div>
						</div>
					</div>
				</div>
				{{-- ALINHAR EM BAIXO  --}}
			</div>
		</div>
		<div class="col-12 col-md-8 h-md-600px bg-white rounded-task-right p-0">
			<div class="h-75px p-3 d-flex align-items-center justify-content-center position-relative opacity-1" style="border-bottom: solid 1px rgba(0, 0, 0, 0.05);">
				<p class="text-gray-600 text-uppercase m-0 text-center"><i>“Não é sobre ideias. É sobre fazer as ideias acontecerem.” – Scott Belsky</i></p>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2 position-absolute opacity-0" style="right: 20px;" data-bs-dismiss="modal" aria-label="Close">
					<i class="fa-solid fa-xmark text-white text-gray-400 fs-5"></i>
                </div>
			</div>
			<div class="rounded scroll-y scroll-dark h-md-425px p-3 show-image-div" id="results-comments">
				{{-- COMMENTS HERE --}}
				{{-- COMMENTS HERE --}}
				{{-- COMMENTS HERE --}}
			</div>
			<div class="h-100px p-3">
				@if ($contents->status != 0)
				<form action="" method="POST" class="position-relative ck-tiny" id="send-comment" data-task="{{ $contents->id }}">
					@csrf
					<div class="pt-0" data-bs-theme="light">
						<textarea name="text" placeholder="Algum comentário sobre essa tarefa?" class="load-editor"></textarea>
					</div>
					<div class="text-end position-absolute" style="bottom: 5px; right: 5px;">
						<button class="btn btn-sm btn-icon btn-primary btn-active-danger fw-bold text-uppercase mt-2">
							<i class="fa-regular fa-paper-plane fs-5"></i>
						</button>
					</div>
				</form>
				@endif
			</div>
		</div>
	</div>
</div>