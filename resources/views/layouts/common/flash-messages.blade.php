@if(Session::has('error'))
    <script type="text/javascript">
        $.notify("{{Session::get('error')}}", "error");
    </script>
@endif
@if(Session::has('success'))
    <script type="text/javascript">
        $.notify("{{Session::get('success')}}",'success');
    </script>
@endif