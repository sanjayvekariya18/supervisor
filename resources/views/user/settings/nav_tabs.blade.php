<!-- Nav tabs -->
<ul class="nav nav-tabs md-tabs " role="tablist">
    @if(hasAccess('range_color'))
    <li class="nav-item">
        <a class="nav-link {{ $current_tab=='color_range' ? 'active' : '' }}" href="{{ route('settings.color.range') }}" ><i class="fa fa-lg fa-tint"></i> Color Ranges</a>
        <div class="slide"></div>
    </li>
    @endif
    
    @if(hasAccess('supervisor'))
    {{-- <li class="nav-item">
        <a class="nav-link {{ $current_tab=='supervisor_permissions' ? 'active' : '' }}" href="{{ route('settings.supervisor.permissions') }}" ><i class="fa fa-lg fa-user"></i> Supervisor Permissions</a>
        <div class="slide"></div>
    </li> --}}
    @endif
</ul>
