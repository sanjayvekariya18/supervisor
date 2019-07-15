<style>
    .table td, .table th {
       padding: 0.35rem;
        width: 150px;   
    }
</style>
<form action="{{url('reading/update_reading')}}" method="post">
    @csrf
    <input type="hidden" name="reading_date" value="{{$reading_date}}" />
    <?php
    use App\Models\Worker;
    use App\Models\Machine;
    $machine_data = get_all_machines();
    
    foreach ($report_data as $shift_name => $rows): ?>
        <?php 
            $cust_id = Auth::id();
            $shift = ($shift_name == "day")?1:2;
            $rows = make_m_no_as_key($rows);
            if ($options['header_data']!=true) {
                $rows = array_merge_recursive_distinct($machine_list,$rows);
            } 
        ?>
        <!-- Header of Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th colspan="4" style="text-align:center;">
                        <?php echo strtoupper($shift_name) ?>
                    </th>
                </tr>
                <tr>
                    <th class="tbl_th">M. No.</th>
                    @if(hasAccess('machine_name'))
                        <th class="tbl_th">M. Name</th>
                    @endif
                    @if(hasAccess('worker_account'))
                        <th class="tbl_th">W. Name</th>
                    @endif
                        <th class="tbl_th">Stitch</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($rows as $machine_number => $row): ?>
                    <!-- Data of table -->
                    <?php                        
                        $machine = Machine::where('cust_id',$cust_id)->where('machine_number',$machine_number)->first();
                        $worker_id = ($shift == 1)?$machine->day_worker_id:$machine->night_worker_id;
                        if(!empty($worker_id)){
                            $worker = Worker::findOrFail($worker_id);
                            $first_name = !empty($row['first_name']) ? $row['first_name'] : $worker->first_name;
                            $last_name = !empty($row['last_name']) ? " " . $row['last_name'] : $worker->last_name;
                            $worker_name = $first_name . $last_name;
                        }else{
                            $worker_name = "No Worker Assign";
                        }
                        
                        $stitches = !empty($row['stitches']) ? $row['stitches'] : 0;
                    ?>
                    <tr>
                        <td class="tbl_td">{{$machine_number}}</td>
                        @if(hasAccess('machine_name'))
                        <td class="tbl_td">{{$machine_data[$machine_number]}}</td>
                        @endif
                        @if(hasAccess('worker_account'))
                        <td class="tbl_td">{{$worker_name}}</td>
                        @endif
                        <td class="tbl_td">
                            <input type="text" name="readingData[{{$shift}}][{{$machine_number}}]" value="{{$stitches}}"/>
                        </td>
                    </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach;?>    
    {{ Form::submit("Update Reading",["class"=>"btn hor-grd btn-grd-info btn-round text-center"]) }}  
</form>      