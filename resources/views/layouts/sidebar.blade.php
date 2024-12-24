<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <a href="{{ route('dashboard.index') }}">
            <img alt="Logo" src="{{ asset('assets/media/images/logo-white.png') }}" class="h-40px app-sidebar-logo-default" />
            <img alt="Logo" src="{{ asset('assets/media/images/icon.png') }}" class="h-25px app-sidebar-logo-minimize mb-2" />
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
                            <span class="menu-title">Início</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="{{ route('projects.index') }}">
                            <span class="menu-icon">
                                <i class="fa-solid fa-rocket fs-4"></i>
                            </span>
                            <span class="menu-title">Lançamentos</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="{{ route('users.index') }}">
                            <span class="menu-icon">
                                <i class="fa-solid fa-users fs-4"></i>
                            </span>
                            <span class="menu-title">Usuários</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-icon">
                                <i class="fa-solid fa-calendar-days fs-4"></i>
                            </span>
                            <span class="menu-title">Agenda
                                <span class="badge badge-light-danger ms-2">Em Breve</span>
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <div class="menu-content pt-8 pb-2 pe-0 d-flex justify-content-between align-items-center">
                            <span class="menu-section text-muted text-uppercase fw-bold fs-8 ls-1">Lançamentos</span>
                            <a href="{{ route('projects.create') }}" class="w-20px h-20px text-white cursor-pointer rounded-circle bg-primary bg-hover-success fw-bolder d-flex align-items-center justify-content-center" data-kt-menu-trigger="click" data-kt-menu-placement="right-start">
                                +
                            </a>
                        </div>
                    </div>
                    @foreach (projects() as $project)
                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                            <a class="menu-link" href="{{ route('projects.show', $project->id) }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-element-7 fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">{{ Str::limit($project->name, 18) }}</span>
                            </a>
                        </div>
                        @if (!$loop->last)
                        <div class="separator separator-dashed mx-6" style="opacity: 0.15;"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-6" id="kt_app_sidebar_footer">
        <a href="#" class="btn btn-flex flex-center btn-custom btn-primary overflow-hidden text-nowrap px-0 h-40px w-100">
            <span class="btn-label">Lançamentos</span>
            <i class="ki-duotone ki-document btn-icon fs-2 m-0">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </a>
    </div>
</div>
