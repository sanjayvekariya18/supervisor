<script type="text/javascript" src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/modernizr.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/css-scrollbars.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/pcoded.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/horizontal-layout.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.slimscroll.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/notify.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery-confirm.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/switchery.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap-editable.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.knob.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/knob-custom-chart.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ url('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
<script  src="{{asset('sweetalert/js/sweetalert.min.js')}}"></script>

<script type="text/javascript">
	//buttons
    $.fn.editableform.buttons = 
      '<button type="submit" class="btn hor-grd btn-grd-info btn-round btn-sm editable-submit">'+
        '&nbsp;&nbsp;<i class="fa fa-lg fa-check" aria-hidden="true"></i>'+
      '</button>'+
      '<button type="button" class="btn hor-grd btn-grd-inverse btn-round hover-white btn-sm editable-cancel">'+
        '&nbsp;&nbsp;<i class="fa fa-lg fa-times" aria-hidden="true"></i>'+
      '</button>'; 

      $( function() {
      	$(".date-picker").attr('readonly', true);
	    	$(".date-picker").datepicker({
	    		dateFormat: 'dd-mm-yy'
	    	});
	  } );
</script>