@extends('layouts.user.index')
@section('page_title','Supervisor Permissions')
@section('content')

<!-- Page body start -->
<div class="page-body m-t-40">

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-right"><i class="fa fa-window-maximize full-card"></i></div>
                </div>
                <div class="card-block">
                    <div class="bs-example grid-layout">
                        <div class="row">
                            <div class="width-100">
                                <!-- Nav tabs -->
                                @include('user.settings.nav_tabs', ['current_tab' => $current_tab])
                                <!-- Tab panes -->
                                <div class="tab-content card-block">
                                    {{ Form::model($data,array('route' => ['settings.supervisor.permissions'],'id'=>'frm_supervisor_permissions')) }}
                                    <div class="tab-pane {{ $current_tab=='supervisor_permissions' ? 'active' : '' }}" id="supervisor_permissions" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {{ Form::label('workers','Workers') }}
                                                            {{ Form::checkbox('workers',1,$data['workers'],['class'=>'js-primary']) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {{ Form::label('dashboard','Dashboard') }}
                                                            {{ Form::checkbox('dashboard',1,$data['dashboard'],['class'=>'js-primary']) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {{ Form::label('machines','Machines') }}
                                                            {{ Form::checkbox('machines',1,$data['machines'],['class'=>'js-primary']) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {{ Form::label('reports','Reports') }}
                                                            {{ Form::checkbox('reports',1,$data['reports'],['class'=>'js-primary']) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {{ Form::label('bonus','Bonus') }}
                                                            {{ Form::checkbox('bonus',1,$data['bonus'],['class'=>'js-primary']) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {{ Form::label('disconnected_machines','Disconnected Machines') }}
                                                            {{ Form::checkbox('disconnected_machines',1,$data['disconnected_machines'],['class'=>'js-primary']) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                {{ Form::submit("Save changes",['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Page body end -->
<script type="text/javascript">
    
</script>

@endsection