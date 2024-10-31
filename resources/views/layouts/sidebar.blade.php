<div class="sidebar-menu">
    <ul class="menu">
        <li class="sidebar-title">Menu</li>

        <li class="sidebar-item  ">
            <a href="index.html" class='sidebar-link'>
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @can('acl_main_index')
            <li
                class="sidebar-item  has-sub {{ Request::is('*user*') ? 'active' : '' }} || {{ Request::routeIs('*role*') ? 'active' : '' }}|| {{ Request::routeIs('*log*') ? 'active' : '' }}">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-stack"></i>
                    <span>Accest Control List</span>
                </a>
                <ul class="submenu ">
                    @can('acl_user_index')
                        <li class="submenu-item {{ Request::is('*user*') ? 'active' : '' }}">
                            <a href="{{ route('user.index') }}" class="submenu-link">Users</a>
                        </li>
                    @endcan
                    @can('acl_role_index')
                        <li class="submenu-item  {{ Request::routeIs('*role*') ? 'active' : '' }}">
                            <a href="{{ route('role.index') }}" class="submenu-link">Roles&Permissions</a>
                        </li>
                    @endcan

                    @can('acl_log_index')
                        <li class="submenu-item  {{ Request::routeIs('*log*') ? 'active' : '' }}">
                            <a href="{{ route('log.index') }}" class="submenu-link">Log-Active</a>
                        </li>
                    @endcan

                </ul>
            </li>
        @endcan

        <li class="sidebar-item  has-sub">
            <a href="#" class='sidebar-link'>
                <i class="bi bi-collection-fill"></i>
                <span>Servers</span>
            </a>
            <ul class="submenu ">
                <li class="submenu-item  ">
                    <a href="extra-component-avatar.html" class="submenu-link">Routers</a>
                </li>
                <li class="submenu-item  ">
                    <a href="extra-component-divider.html" class="submenu-link">OLT</a>
                </li>
            </ul>
        </li>
        {{-- <li class="sidebar-title">Forms &amp; Tables</li> --}}
        <li class="sidebar-item  ">
            <a href="form-layout.html" class='sidebar-link'>
                <i class="bi bi-file-earmark-medical-fill"></i>
                <span>Customers</span>
            </a>
        </li>
        <li class="sidebar-item  ">
            <a href="form-layout.html" class='sidebar-link'>
                <i class="bi bi-file-earmark-medical-fill"></i>
                <span>ODP</span>
            </a>
        </li>
        <li class="sidebar-item">
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class='sidebar-link' style="background: none; border: none; ">
                    <i class="bi bi-file-earmark-medical-fill"></i>
                    <span>LOGOUT</span>
                </button>
            </form>
        </li>

    </ul>
</div>
