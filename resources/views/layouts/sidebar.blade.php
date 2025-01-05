<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <a href="{{ route('dashboard.index') }}">
            <img alt="Logo" src="{{ asset('assets/media/images/logo-white.png') }}" class="h-40px app-sidebar-logo-default" />
            <img alt="Logo" src="{{ asset('assets/media/images/favicon.png') }}" class="h-30px app-sidebar-logo-minimize mb-2" />
        </a>
        <div id="kt_app_sidebar_toggle" class="toggle-sidebar app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate @if(Auth::user()->sidebar == false){{ 'active' }}@endif" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
    </div>
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
                    <div class="menu-item">
                        <a class="menu-link active" href="{{ route('dashboard.index') }}">
                            <span class="menu-icon">
                                <i class="fa-solid fa-house fs-4"></i>
                            </span>
                            <span class="menu-title">Minhas Tarefas</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="{{ route('agenda.index') }}">
                            <span class="menu-icon">
                                <i class="fa-solid fa-calendar-days fs-4"></i>
                            </span>
                            <span class="menu-title">Agenda
                            </span>
                        </a>
                    </div>
                    <div  data-kt-menu-trigger="click"  class="menu-item menu-accordion" >
                        <span class="menu-link"  >
                            <span  class="menu-icon" >
                                <i class="ki-duotone ki-element-11 fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span  class="menu-title" >
                                Projetos e Tarefas
                            </span>
                            <span  class="menu-arrow" ></span>
                        </span>
                        <div  class="menu-sub menu-sub-accordion">
                           <div  class="menu-item">
                              <a class="menu-link" href="{{ route('projects.index') }}"><span class="menu-bullet"><span class="bullet bullet-dot"></span></span><span class="menu-title">Gestão de Projetos</span></a>
                           </div>
                           <div  class="menu-item">
                              <a class="menu-link" href="{{ route('modules.index') }}"><span class="menu-bullet"><span class="bullet bullet-dot"></span></span><span class="menu-title">Gestão de módulos</span></a>
                           </div>
                           <div  class="menu-item" >
                            <a class="menu-link" href="{{ route('tasks.index') }}"><span class="menu-bullet"><span class="bullet bullet-dot"></span></span><span class="menu-title">Gestão de tarefas</span></a>
                           </div>
                           <div  class="menu-item" >
                              <a class="menu-link" href="{{ route('projects.types.index') }}"><span class="menu-bullet"><span class="bullet bullet-dot"></span></span><span class="menu-title">Tipos de projetos</span></a>
                           </div>
                        </div>
                     </div>
                    <div  data-kt-menu-trigger="click"  class="menu-item menu-accordion" >
                        <span class="menu-link"  >
                            <span  class="menu-icon" >
                                <i class="ki-duotone ki-profile-user fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span  class="menu-title" >
                                Membros
                            </span>
                            <span  class="menu-arrow" ></span>
                        </span>
                        <div  class="menu-sub menu-sub-accordion">
                           <div  class="menu-item" >
                              <a class="menu-link" href="{{ route('users.index') }}"><span class="menu-bullet"><span class="bullet bullet-dot"></span></span><span class="menu-title">Usuários</span></a>
                           </div>
                           <div  class="menu-item" >
                              <a class="menu-link" href="{{ route('positions.index') }}"><span class="menu-bullet"><span class="bullet bullet-dot"></span></span><span class="menu-title">Cargos</span></a>
                           </div>
                        </div>
                     </div>
                     @if (Auth::user()->groupProjects()->count())
                     <div class="p-2 rounded draggable-sidebar-zone" style="background: rgba(0, 0, 0, 0.2)">
                        @foreach (Auth::user()->groupProjects() as $key => $group)
                        <div class="menu-item opacity-1 draggable-sidebar group-list" data-group="{{ $key }}">
                            <div class="menu-content pb-2 pt-2 pe-0 d-flex justify-content-between align-items-center position-relative">
                                <span class="menu-section text-muted text-uppercase fw-bold fs-8 ls-1">
                                    {{ $group['name'] }}
                                    <i class="fa-solid fa-arrows-to-dot text-white opacity-0 fs-9 draggable-sidebar-handle"></i>
                                </span>
                                <div class="d-flex align-items-center justify-content-end me-2">
                                    <a href="{{ route('projects.create') }}" class="w-20px h-20px text-white cursor-pointer rounded-circle bg-success fw-bolder d-flex align-items-center justify-content-center opacity-0">
                                        +
                                    </a>
                                </div>
                            </div>
                            <div class="draggable-projects-zone">
                                @foreach ($group['items'] as $project)
                                <div data-kt-menu-trigger="click" class="menu-item menu-accordion draggable-project group-project position-relative opacity-sub-1" data-project="{{ $project->id }}">
                                    <a class="menu-link position-relative" href="{{ route('projects.show', $project->id) }}">
                                        @if (count($group['items']) > 1)
                                        <span class="w-20px h-20px cursor-move fw-bolder d-flex align-items-center justify-content-center opacity-0 draggable-project-handle position-absolute" style="left: -3px;">
                                            <i class="fa-solid fa-arrows-to-dot text-white opacity-25 fs-8"></i>
                                        </span>
                                        @endif
                                        <span class="menu-icon">
                                            <i class="ki-duotone ki-element-7 fs-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <span class="menu-title">{{ Str::limit($project->name, 18) }}</span>
                                    </a>
                                    <div class="position-absolute" style="right: 0px; top: 50%; transform: translateY(-50%);">
                                        <div class="d-flex opacity-sub-0">
                                            <a href="{{ route('projects.edit', $project->id) }}" class="w-20px h-20px text-white cursor-pointer">
                                                <i class="fa-solid fa-pen-to-square text-white fs-8"></i>
                                            </a>
                                            <a href="{{ route('projects.destroy', $project->id) }}" class="w-20px h-20px text-white cursor-pointer">
                                                <i class="fa-solid fa-trash-alt text-white fs-8"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="separator separator-dashed mx-6" style="opacity: 0.15;"></div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                     @endforeach
                     </div>
                     @else
                     <div class="menu-item opacity-1">
                        <div class="menu-content pt-8 pb-2 pe-0 d-flex justify-content-between align-items-center">
                            <span class="menu-section text-muted text-uppercase fw-bold fs-8 ls-1">Projetos</span>
                            <a href="{{ route('projects.create') }}" class="w-20px h-20px text-white cursor-pointer rounded-circle bg-primary bg-hover-success fw-bolder d-flex align-items-center justify-content-center opacity-0">
                                +
                            </a>
                        </div>
                    </div>
                     <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                        <a href="{{ route('projects.create') }}" class="menu-link" style="background: #0000000f;">
                            <span class="menu-title justify-content-center">Adicionar Projeto</span>
                        </a>
                    </div>
                     @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('custom-footer')
@parent
<script>
    $(document).ready(function () {
        var containers = document.querySelectorAll(".group-list");
        if (containers.length === 0) return false;
        containers.forEach(function (group) {
            var projectsZone = group.querySelector(".draggable-projects-zone");

            // Inicializar Sortable para cada grupo
            var sortable = new Sortable.default(projectsZone, {
                draggable: ".draggable-project",
                handle: ".draggable-project-handle",
                group: {
                    name: $(group).data('group'),
                    pull: false,
                    put: false
                },
            });

            // Evento ao parar o arraste
            sortable.on("drag:stopped", function (event) {
                // Obter os projetos no grupo atual
                var movedDiv = event.originalSource;
                var draggableDropped = $(movedDiv).closest(".draggable-sidebar-zone");

                var projectList = [];
                draggableDropped.find(".group-project").each(function () {
                    var item = $(this).data("project");
                    projectList.push(item);
                });

                // Enviar a nova ordem via AJAX
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content") },
                    type: "PUT",
                    url: "{{ route('account.sidebar.order', 'sidebarProjectsOrder') }}",
                    data: {
                        _token: @json(csrf_token()),
                        list: projectList
                    },
                    success: function (response) {
                        console.log(projectList);
                    }
                });
            });
        });
    });

    $(document).ready(function(){
        var containers = document.querySelectorAll(".draggable-sidebar-zone");
        if (containers.length === 0) return false;
        var swappableMenu = new Sortable.default(containers, {
            draggable: ".draggable-sidebar",
            handle: ".draggable-sidebar .draggable-sidebar-handle",
            mirror: {
                constrainDimensions: true,
            },
        });

        // ON STOP DRAG
        swappableMenu.on('drag:stopped', function(event) {

            // GET DIV OF ELEMENT
            var movedDiv = event.originalSource;

            // GET PROJECT
            var draggableDropped = $(movedDiv).closest('.draggable-sidebar-zone');

            // START
            var groupList = [];

            // GET IDS OF TASKS ONLY DRAGGABLE-ZONE
            draggableDropped.find('.group-list').each(function() {
                // OBTEM ITEM
                var item = $(this).data('group');
                groupList.push(item);
            });

            // AJAX
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type:'PUT',
                url: "{{ route('account.sidebar.order', 'sidebarGroupOrder') }}",
                data: {
                    _token: @json(csrf_token()),
                    list: groupList
                },
                success: function(response){
                    console.log(groupList);
                }
            });

        });
    });
</script>
@endsection
