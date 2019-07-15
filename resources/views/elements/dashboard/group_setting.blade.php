{{-- Buzzer Group Modal Pop-up --}}
<div class="md-modal md-effect-11" id="group-setting">
    <div class="md-content">
        <h3 class="dsh-group">Groups Setting</h3>
        <div class="dsh-group">
            <div class="row">
                <div class="col-md-12">
                    <label class="text-info"> <i class="fa fa-info-circle"></i> <span id="group_change_text">Group will auto change at interval of 10 Seconds</span></label>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {{ Form::label('auto_change_group','Auto Change') }}
                        {{Form::checkbox('auto_change_group',1,false,['id'=>'auto_change_group','class'=>'js-primary'])}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {{ Form::label('machine_group','Select Group') }}
                        {{ Form::select('machine_group',$groups,[],['id'=>'machine_group','class'=>'form-control']) }}
                    </div>
                </div>
            </div>
            {{-- <button id="save_group_setting" class="btn hor-grd btn-grd-info btn-round">Save Changes</button> --}}
        </div>
    </div>
</div>
<div class="md-overlay"></div>
{{-- Group Setting Modal Pop-up --}}


<script type="text/javascript">
    var auto_change_group;
    var count_down;
    var counter = 14;
    jQuery(document).ready(function($) {
        $("#machine_group").change(function(event) {
            $(".machine-element").hide();
            var group_id = $(this).val();
            var group_name = $("#machine_group option:selected").text();
            console.log(group_name);
            if(group_id!='all'){
                $('*[data-group="'+ group_id +'"]').show();
                $("#current_group").html(group_name);
            }else{
                $(".machine-element").show();
                $("#current_group").html("All Groups");
            }

        });

        $("#auto_change_group").change(function(event) {
            var _checked = $(this).is(':checked');
            if(_checked==true){

                auto_change_group = setInterval( function () {
                    if($('#machine_group option:selected').next().text() == "all" || $('#machine_group option:selected').next().text() == "")
                    {
                        $("#machine_group").find('option').prop("selected",false) ;
                        $("#machine_group option:eq(1)").prop("selected", true);
                    }
                    else
                    {
                        $('#machine_group option:selected').next().prop("selected", true);
                    }
                    $("#machine_group").trigger('change');
                    counter = 15;
                }, 15000 );

                count_down = setInterval( function () {
                    $("#group_change_text").html("Group will change in " + counter-- + " Seconds");
                }, 1000 );

            }else{
                clearInterval(auto_change_group);
                clearInterval(count_down);
                $("#group_change_text").html("Group will auto change at interval of 10 Seconds");
                counter = 14;
            }
        });


    });
</script>