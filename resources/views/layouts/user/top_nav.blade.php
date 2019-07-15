<nav class="navbar header-navbar pcoded-header">
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
                    <a href="#!">
                        <i class="ti-bell"></i>
                        <span class="badge bg-c-pink"></span>
                    </a>
                    <ul class="show-notification">
                        <li>
                            <h6>Notifications</h6>
                            <label class="label label-danger">New</label>
                        </li>
                        <li>
                            <div class="media">
                                {{ HTML::image('images/avatar-2.jpg', 'alt text', array('class' => 'd-flex align-self-center img-radius','alt'=>'Generic placeholder image')) }}
                                <div class="media-body">
                                    <h5 class="notification-user">Account Created</h5>
                                    <p class="notification-msg">Your Account has been created.</p>
                                    <span class="notification-time">30 minutes ago</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="user-profile header-notification">
                    <a href="#!">
                        {{ HTML::image('images/avatar-4.jpg', 'alt text', array('class' => 'img-radius','alt'=>'User-Profile-Image')) }}
                        <span>{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</span>
                        <i class="ti-angle-down"></i>
                    </a>
                    <ul class="show-notification profile-notification">
                        <li>
                            <a href="{{ route('customers.profile.about') }}">
                                <i class="ti-user"></i> Profile
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('password.change_password') }}">
                                <i class="ti-user"></i> Change Password
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ti-layout-sidebar-left"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</nav>