<div class="sidebar-menu">
    <ul class="menu">
        <li class="sidebar-title">Menu</li>

        <li class="sidebar-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class='sidebar-link'>
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @can('main_acl_index')
            <li
                class="sidebar-item has-sub {{ Request::routeIs('user.*', 'role.*', 'log.*', 'audit.*') ? 'active' : '' }}">
                <a href="#" class='sidebar-link'>
                    <i class="bi bi-stack"></i>
                    <span>Access Control List</span>
                </a>
                <ul class="submenu ">
                    @can('acl_user_index')
                        <li class="submenu-item {{ Request::routeIs('user.*') ? 'active' : '' }}">
                            <a href="{{ route('user.index') }}" class="submenu-link">Users</a>
                        </li>
                    @endcan
                    @can('acl_role_index')
                        <li class="submenu-item {{ Request::routeIs('role.*') ? 'active' : '' }}">
                            <a href="{{ route('role.index') }}" class="submenu-link">Roles & Permissions</a>
                        </li>
                    @endcan

                    @can('acl_log_index')
                        <li class="submenu-item {{ Request::routeIs('log.*') ? 'active' : '' }}">
                            <a href="{{ route('log.index') }}" class="submenu-link">Log Aktif</a>
                        </li>
                    @endcan
                    @can('acl_audit_index')
                        <li class="submenu-item {{ Request::routeIs('audit.*') ? 'active' : '' }}">
                            <a href="{{ route('audit.index') }}" class="submenu-link">Activity Log</a>
                        </li>
                    @endcan

                </ul>
            </li>
        @endcan

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
