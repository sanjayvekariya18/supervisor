<nav class="navbar header-navbar pcoded-header dashboard-nav">
    <div class="navbar-wrapper">
        <div class="navbar-logo">
            <a class="mobile-menu" id="mobile-collapse" href="#!">
                <i class="ti-menu"></i>
            </a>
            <a href="#">
                {{ HTML::image('images/logo.png', 'alt text', array('class' => 'img-fluid brand-logo','alt'=>'Logo')) }}
            </a>
            <a class="mobile-options">
                <i class="ti-more"></i>
            </a>
        </div>

        <div class="navbar-container container-fluid">
            <ul class="nav-left">
                <li>
                    <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                </li>
                <li>
                    <a href="#!" onclick="javascript:toggleFullScreen()">
                        <i class="ti-fullscreen"></i>
                    </a>
                </li>
            </ul>
            <ul class="nav-right">
                <li class="header-notification">
                    <a href="{{ route('machines.list') }}">
                        <i class="fa fa-lg fa-mail-reply"></i> Go Back
                    </a>
                </li>
                @if(hasAccess('machine_group'))
                    <li class="header-notification m-t-5">
                        <a id="change_group" href="javascript:void(0);" class="btn-sm md-trigger" data-modal="group-setting">
                            <i class="fa fa-lg fa-server"></i> <span id="current_group">All Groups</span>
                        </a>
                    </li>
                @endif
                <li class="header-notification">
                    <a href="{{ route('reports.production') }}">
                        <i class="fa fa-lg fa-bar-chart-o"></i> Reports
                    </a>
                </li>                
                <li class="header-notification">
                    <a href="{{ route('logout') }}" class="text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-lg fa-power-off"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>