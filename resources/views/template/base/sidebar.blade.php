<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset($headerData['user_image']) }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#"
                    class="d-block">{{ Auth::user()->name }}<br><small>{{ Auth::user()->app_role->role_name }}</small></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        {{-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> --}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                @foreach ($rs_heading as $heading)
                    <li class="nav-item {{ $heading->app_heading_id == $currentInduk ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ $heading->app_heading_id == $currentInduk ? 'nav-link active' : '' }}">
                            <i class="nav-icon {{ $heading->app_heading_icon }}"></i>
                            <p>
                                {{ $heading->app_heading_name }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @foreach ($heading->data_menu as $dt_menu)
                                <li class="nav-item">
                                    <a href="{{ route($dt_menu->menu->menu_url) }}"
                                        class="nav-link {{ $dt_menu->menu->menu_url == $currentChild ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ $dt_menu->menu->menu_name }}</p>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
