<!-- Nav tabs -->
<ul class="nav nav-tabs md-tabs " role="tablist">
    <li class="nav-item">
        <a class="nav-link {{ $current_tab=='setting_buzzer' ? 'active' : '' }}" href="{{ route('machines.settings.buzzer',encrypt_str($machine->id)) }}" ><i class="fa fa-lg fa-bell"></i> Buzzer</a>
        <div class="slide"></div>
    </li>
</ul>
