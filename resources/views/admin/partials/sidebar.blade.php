<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">
            Welcome
{{--            <sup>2</sup>--}}
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <li class="nav-item active">
        <a class="nav-link" href="{{ route('categories.index') }}">
            <i class="fas fa-fw fa-list"></i>
            <span>Categories</span></a>
    </li>

    <li class="nav-item active">
        <a class="nav-link" href="{{ route('airlines.index') }}">
            <i class="fas fa-fw fa-route"></i>
            <span>Airlines</span></a>
    </li>

    <li class="nav-item active">
        <a class="nav-link" href="{{ route('airports.index') }}">
            <i class="fas fa-fw fa-plane"></i>
            <span>Airports</span></a>
    </li>

    <li class="nav-item active">
        <a class="nav-link" href="{{ route('places.index') }}">
            <i class="fas fa-fw fa-building"></i>
            <span>Places</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
