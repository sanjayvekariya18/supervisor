<script type="text/javascript">
	jQuery(document).ready(function($) {

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
    });
	
	$("#report_types").change(function(event) {
		$("#groups_list").trigger('change');
	});

	$("#groups_list").change(function(event) {

		var report_type = $("#report_types").val();
		
		var group_id = $(this).val();

		$.ajax({
			url: '{{ route('machines.list.by.group') }}',
			type: 'POST',
			async:false,
			data: { group_id : group_id },
		})
		.done(function(result) {
			$('#machines_list').find('option').remove();

			// Remove temporary
			if (report_type != '5_min_diff') {
				$('#machines_list').append($("<option></option>").attr("value",'all').text('All')); 
			}

			$.each(result.data, function(key, value) {   
			     $('#machines_list').append($("<option></option>").attr("value",key).text(value)); 
			});


		})
		.fail(function(err) {
			$.notify("Unable to get Machines list, please try again later","error");
		});
	
	});

	$("#groups_list").trigger('change');
	$("#report_types").trigger('change');
	if (search_data != null && typeof search_data.machine_no != null && typeof search_data.machine_no != 'undefined') {
		$('#machines_list').val(search_data.machine_no);
	}

	if(search_data != null && typeof search_data.machine_no != null){
		$('#interval').val(search_data.interval);
	}
	
});
</script>