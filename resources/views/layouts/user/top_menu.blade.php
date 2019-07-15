<?php
    /* echo "<pre>";
    print_r(auth()->user()->permission);
    die; */
?>
<nav class="pcoded-navbar">
    <div class="pcoded-inner-navbar">
        <ul class="pcoded-item pcoded-left-item">
           
                <li class="">
                    <a href="{{ route('customer.dashboard') }}">
                        <span class="pcoded-micon"><i class="fa fa-lg fa-dashboard"></i><b>D</b></span>
                        <span class="pcoded-mtext">Dashboard</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
            @if(hasAccess('worker_account') || hasAccess('supervisor') || hasAccess('machine_group') || hasAccess('machine_name'))
                <li class="pcoded-hasmenu">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="fa fa-lg fa-group"></i><b>M</b></span>
                        <span class="pcoded-mtext">Masters</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    <ul class="pcoded-submenu">
                        @if(hasAccess('worker_account'))
                            <li>
                                <a href="{{ route('workers.list') }}">
                                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                                    <span class="pcoded-mtext">Workers</span>
                                    <span class="pcoded-mcaret"></span>
                                </a>
                            </li>
                        @endif
                            @if(hasAccess('supervisor'))
                            <li>
                                <a href="{{ route('supervisors.list') }}">
                                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                                    <span class="pcoded-mtext">Supervisor</span>
                                    <span class="pcoded-mcaret"></span>
                                </a>
                            </li>
                            @endif
                            @if(hasAccess('machine_group'))
                            <li>
                                <a href="{{ route('machine.groups.list') }}">
                                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                                    <span class="pcoded-mtext">Machine Groups</span>
                                    <span class="pcoded-mcaret"></span>
                                </a>
                            </li>
                            @endif
                        @if(hasAccess('machine_name'))
                                <li>
                                    <a href="{{ route('machines.list') }}">
                                        <span class="pcoded-micon"><i class="ti-home"></i></span>
                                        <span class="pcoded-mtext">Machines</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                </li>
                        @endif
                    </ul>
                </li>
            @endif
            
                <li class="pcoded-hasmenu">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="fa fa-lg fa fa-bar-chart-o"></i><b>C</b></span>
                        <span class="pcoded-mtext">Reports</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li>
                            <a href="{{ route('reports.production') }}">
                                <span class="pcoded-micon"><i class="ti-bar-chart-alt"></i></span>
                                <span class="pcoded-mtext">Production</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        @if(hasAccess('report_avg'))
                        <li>
                            <a href="{{ route('reports.average') }}">
                                <span class="pcoded-micon"><i class="ti-bar-chart-alt"></i></span>
                                <span class="pcoded-mtext">Average</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        @endif
                        @if(hasAccess('report_avg_weekly'))
                        <li>
                            <a href="{{ route('reports.average_weekly') }}">
                                <span class="pcoded-micon"><i class="ti-bar-chart-alt"></i></span>
                                <span class="pcoded-mtext">Average Weekly</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        @endif
                        @if(hasAccess('report_total'))
                        <li>
                            <a href="{{ route('reports.worker.total') }}">
                                <span class="pcoded-micon"><i class="ti-bar-chart-alt"></i></span>
                                <span class="pcoded-mtext">Worker Total</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        @endif
                        @if(hasAccess('report_salary'))
                        <li>
                            <a href="{{ route('reports.worker.salary') }}">
                                <span class="pcoded-micon"><i class="ti-bar-chart-alt"></i></span>
                                <span class="pcoded-mtext">Worker Salary</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                <li class="pcoded-hasmenu">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="fa fa-lg fa fa-bar-chart-o"></i><b>C</b></span>
                        <span class="pcoded-mtext">Graphs</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li>
                            <a href="{{ route('graphs.production') }}">
                                <span class="pcoded-micon"><i class="ti-bar-chart-alt"></i></span>
                                <span class="pcoded-mtext">Production</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li> 
                        @if(hasAccess('report_avg'))
                        <li>
                            <a href="{{ route('graphs.average') }}">
                                <span class="pcoded-micon"><i class="ti-bar-chart-alt"></i></span>
                                <span class="pcoded-mtext">Average</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>  
                        @endif
                        @if(hasAccess('report_avg_weekly'))
                        <li>
                            <a href="{{ route('graphs.average_weekly') }}">
                                <span class="pcoded-micon"><i class="ti-bar-chart-alt"></i></span>
                                <span class="pcoded-mtext">Average Weekly</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        @endif                     
                    </ul>
                </li>
            @if(hasAccess('bonus'))
                <li class="pcoded-hasmenu">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="fa fa-lg fa-life-ring"></i><b>B</b></span>
                        <span class="pcoded-mtext">Bonus</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li>
                            <a href="{{ route('bonuses.fixed.list') }}">
                                <span class="pcoded-micon"><i class="ti-receipt"></i></span>
                                <span class="pcoded-mtext">Fixed</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('bonuses.range.wise.list') }}">
                                <span class="pcoded-micon"><i class="ti-receipt"></i></span>
                                <span class="pcoded-mtext">Range Wise</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            @if(hasAccess('disconnect_machine'))
                    <li>
                        <a href="{{ route('machines.list.disconnected') }}">
                            <span class="pcoded-micon"><i class="fa fa-lg fa-minus-circle"></i><b>O</b></span>
                            <span class="pcoded-mtext">Disconnected Machines</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
            @endif
            @if(hasAccess('range_color') || hasAccess('supervisor'))
            <li>
                <a href="{{ route('settings.color.range') }}">
                    <span class="pcoded-micon"><i class="fa fa-lg fa-gears"></i><b>O</b></span>
                    <span class="pcoded-mtext">App Settings</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            @endif
            <!--<li>
                <a href="{{ route('settings.machine') }}">
                    <span class="pcoded-micon"><i class="fa fa-lg fa-gears"></i><b>O</b></span>
                    <span class="pcoded-mtext">Machine Settings</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>-->
        </ul>
    </div>
</nav>