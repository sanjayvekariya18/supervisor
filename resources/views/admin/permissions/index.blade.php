@extends('layouts.admin.index')
@section('page_title','List of Permission')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">List of all Models</h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ url('admin/permission/create') }}" class="text-danger"><strong>Create a new Model</strong></a>
            </div>
        </div>
    </div>
    <!-- Page header end -->

    <!-- Page body start -->
    <div class="page-body">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Model Id</th>
                                        <th>Model Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions as $permission)
                                        <tr>
                                            <th>{{ $permission->id }}</th>
                                            <td>{{ $permission->name }}</td>
                                            <td>
                                                <a href="{{ url('admin/permission/'.$permission->id.'/edit') }}" data-toggle="tooltip" data-placement="top" data-original-title="Edit Customer"><i class="fa fa-lg fa-edit text-primary"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
