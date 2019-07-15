<!-- Nav tabs -->
<div class="card social-tabs">
    <ul class="nav nav-tabs md-tabs tab-timeline" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $current_tab=='about' ? 'active' : '' }}" href="{{ route('admin.profile.about') }}"> <i class="fa fa-user"></i> About</a>
            <div class="slide"></div>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $current_tab=='change_password' ? 'active' : '' }}" href="{{ route('admin.profile.change.password') }}"><i class="fa fa-magic"></i> Change Password</a>
            <div class="slide"></div>
        </li>
    </ul>
</div>
