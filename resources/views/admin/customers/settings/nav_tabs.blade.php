<!-- Nav tabs -->
<ul class="nav nav-tabs md-tabs " role="tablist">
    <li class="nav-item">
        <a class="nav-link {{ $current_tab=='general_settings' ? 'active' : '' }}" href="{{ route('admin.customers.settings.general_settings',$customer_id) }}" ><i class="fa fa-lg fa-cogs"></i> General Settings</a>
        <div class="slide"></div>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $current_tab=='shift_change' ? 'active' : '' }}" href="{{ route('admin.customers.settings.shift.change',$customer_id) }}" ><i class="fa fa-lg fa-hourglass-half"></i> Shift Change</a>
        <div class="slide"></div>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $current_tab=='sms_recharge' ? 'active' : '' }}" href="{{ route('admin.customers.settings.sms_recharge',$customer_id) }}" ><i class="fa fa-lg fa-envelope"></i> SMS Recharge</a>
        <div class="slide"></div>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $current_tab=='reports_settings' ? 'active' : '' }}" href="{{ route('admin.customers.settings.reports_settings',$customer_id) }}" ><i class="fa fa-lg fa-file"></i> Reports Settings</a>
        <div class="slide"></div>
    </li>
</ul>
