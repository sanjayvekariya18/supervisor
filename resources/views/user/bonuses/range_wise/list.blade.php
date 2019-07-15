@extends('layouts.user.index')
@section('page_title','List of all Range wise Bonuses')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">List of all Range wise Bonuses</h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('bonuses.range.wise.create') }}" class="text-danger"><strong>Create a new Bonus</strong></a>
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
                        {{ Form::model($range_wise_bonus_search,array('route' => 'bonuses.range.wise.list')) }}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('machine_id','Machine Number')}}
                                        {{Form::text('machine_id',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-8 p-t-25">
                                    {{ Form::submit('Search',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('bonuses.range.wise.list',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
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
                                        <th>Range</th>
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
                                                    @foreach ($machine_numbers as $machine_number)
                                                        <label class="label label-primary font-per-100">{{ $machine_number }}</label>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>{!! display_range_braker($bonus->stitches_range) !!}</td>
                                            <td>
                                                <a href="{{ route('bonuses.range.wise.update',$bonus->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Edit Bonus"><i class="fa fa-lg fa-edit text-primary"></i></a>
                                                <a href="{{ route('bonuses.range.wise.delete',$bonus->id) }}" class="m-l-5 delete-range-wise-bonus" data-toggle="tooltip" data-placement="top" data-original-title="Delete Bonus"><i class="fa fa-lg fa-trash text-danger"></i></a>
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
        $('a.delete-range-wise-bonus').confirm({
            title: 'Heads-up!',
            content: 'Are you sure want to <strong class="text-danger">Delete</strong> this Range wise Bonus?',
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
