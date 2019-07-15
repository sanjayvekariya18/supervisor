@extends('layouts.user.index')
@section('page_title','List of all Fixed Bonuses')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">List of all Fixed Bonuses</h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('bonuses.fixed.create') }}" class="text-danger"><strong>Create a new Bonus</strong></a>
            </div>
        </div>
    </div>
    <!-- Page header end -->

    <!-- Page body start -->
    <div class="page-body">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header p-b-5">
                        {{ Form::model($fixed_bonus_search,array('route' => 'bonuses.fixed.list')) }}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('machine_id','Machine Number')}}
                                        {{Form::text('machine_id',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-8 p-t-25">
                                    {{ Form::submit('Search',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('bonuses.fixed.list',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
                                </div>

                            </div>
                        {{ Form::close() }}
                        <div class="card-header-right">
                            <i class="fa fa-window-maximize full-card"></i>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>@sortablelink('machine_id','Machine')</th>
                                        <th>@sortablelink('min_stitches','Minimum Stitches')</th>
                                        <th>@sortablelink('min_stitches_bonus','Minimum Stitches Bonus')</th>
                                        <th>@sortablelink('after_min_per_stitches','After Minimum Per Stitches')</th>
                                        <th>@sortablelink('after_min_per_stitches_bonus','After Minimum Per Stitches Bonus')</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bonuses as $bonus)
                                        <tr>
                                            <td>
                                                @php ($machine_numbers = json_decode($bonus->machine_id))
                                                @php ($machine_numbers = get_machine_number_by_ids($machine_numbers))

                                                @if (!empty($machine_numbers))
                                                    @foreach ($machine_numbers as $element)
                                                        <label class="label label-primary font-per-100">{{ $element }}</label>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>{{ $bonus->min_stitches }}</td>
                                            <td>{{ $bonus->min_stitches_bonus }}</td>
                                            <td>{{ $bonus->after_min_per_stitches }}</td>
                                            <td>{{ $bonus->after_min_per_stitches_bonus }}</td>
                                            <td>
                                                <a href="{{ route('bonuses.fixed.update',$bonus->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Edit Bonus"><i class="fa fa-lg fa-edit text-primary"></i></a>
                                                <a href="{{ route('bonuses.fixed.delete',$bonus->id) }}" class="m-l-5 delete-fixed-bonus" data-toggle="tooltip" data-placement="top" data-original-title="Delete Bonus"><i class="fa fa-lg fa-trash text-danger"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! $bonuses->appends(\Request::except('page'))->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
    <script type="text/javascript">
        $('a.delete-fixed-bonus').confirm({
            title: 'Heads-up!',
            content: 'Are you sure want to <strong class="text-danger">Delete</strong> this Fixed Bonus?',
            buttons: {
                confirm:{
                    btnClass: 'btn-warning',
                    action: function () {
                        location.href = this.$target.attr('href');
                    }
                },
                cancel: function () {
                    
                }
            }
        });
    </script>
@endsection
