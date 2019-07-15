<?php if (!empty($view_mode) && $view_mode=='download'): ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="{{ URL::asset('css/report.css') }}">
    <div class="report-wrapper">
        <div class="report-title">
            <?php echo $company_name ?>
        </div>
        <div class="container">
            <div id="left"><strong>Dates</strong> : <?php echo $from_date .' to '. $from_date  ?></div>
            <div id="right"></div>
            <div id="center">PRODUCTION REPORT PER 5 MINUTES</div>
        </div>
        <div class="container">
            <?php 
                $group_name = !empty($options['group_name']) ? $options['group_name'] : '-';
                $machine_no = !empty($options['machine_no']) ? $options['machine_no'] : '-';
                $worker_name = !empty($options['worker_name']) ? $options['worker_name'] : '-';
            ?>
            <div id="left"><strong>Group</strong> : <?php echo $group_name?> </div>
            <div id="right"><strong>Machine No</strong> : <?php echo $machine_no?> </div>
            <div id="center"><strong>Worker</strong> : <?php echo $worker_name?> </div>
        </div>

    </div>
<?php endif ?>

<?php 

// Hide Columns
$thred_break = !empty($column_settings['columns']['thred_break']) ? 1 : 0;
$working_head = !empty($column_settings['columns']['working_head']) ? 1 : 0;

$colspan = 5;
$headpan = 8;

if (!empty($working_head)) {
    $colspan++;
    $headpan++;
};

if (!empty($working_head)) {
    $colspan++;
    $headpan++;
}

// SELECT `date`,m_no,st FROM tbl_hourse_report_test GROUP BY DATE( `date` ), hour( `date` ) DIV 3 ORDER BY `date`
$total_stitches = [];
$avg_stitches = [];
$total_thred_break = 0;
$total_stop_time = 0;
$avg_rpm = [];
$avg_max_rpm = [];


?>

<?php foreach ($report_data as $machine_number => $report_data): ?>
        
        <!-- Each Machine through -->
        <table class="table table-striped" width="100%" style="padding: 3px;border-collapse: collapse;">
            <thead>
                <tr>
                    <th class="tbl_th" colspan="<?php echo $headpan?>"><?php echo "Machine No - " . $machine_number?></th>
                </tr>
                <tr>
                    <th class="tbl_th">Sr No</th>
                    @if(hasAccess('worker_account'))
                        <th class="tbl_th">Worker</th>
                    @endif
                    <th class="tbl_th">Date</th>
                    @if(hasAccess('head'))
                    <?php if ($working_head==1): ?>
                        <th class="tbl_th">Head</th>
                    <?php endif ?>
                    @endif
                    <th class="tbl_th">Shift</th>
                    <th class="tbl_th">Stitch</th>
                    
                    @if(hasAccess('tb'))
                    <?php if ($thred_break==1): ?>
                        <th class="tbl_th">TB</th>
                    <?php endif ?>
                    @endif
                    @if(hasAccess('max_rpm'))
                    <th class="tbl_th">MAX RPM</th>
                    @endif
                    @if(hasAccess('live_rpm'))
                    <th class="tbl_th">RPM</th>
                    @endif
                    @if(hasAccess('stop_time'))
                    <th class="tbl_th">Stop Time</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            
            <?php

                foreach ($report_data as $key => $value){

                    $prev_stitch = !empty($report_data[$key-1]['stitches']) ? $report_data[$key-1]['stitches'] : 0;
                    $prev_stop_time = !empty($report_data[$key-1]['stop_time']) ? $report_data[$key-1]['stop_time'] : 0;
                    $prev_tb = !empty($report_data[$key-1]['thred_break']) ? $report_data[$key-1]['thred_break'] : 0;

                    $stop_time = $value['stop_time'] - $prev_stop_time;

                    $stop_time = secondsToTime($stop_time);

                    $final_stitches = abs($value['stitches'] - $prev_stitch);
                    $total_stitches = $value['stitches'];
                    $total_thred_break = $value['thred_break'];
                    $avg_rpm[] = $value['rpm'];
                    $avg_max_rpm[] = $value['max_rpm'];
                    $total_stop_time = $value['stop_time'];

            ?>
                <tr>
                    <td class="tbl_td"><?php echo $key+1 ?></td>
                    @if(hasAccess('worker_account'))
                        <td class="tbl_td"><?php echo $value['first_name'] . ' ' . $value['last_name'] ?></td>
                    @endif
                    
                    <td class="tbl_td"><?php echo date('d-m-Y h:i A',strtotime($value['created_at']))?></td>
                    @if(hasAccess('head'))
                        <?php if ($working_head==1): ?>
                            <td class="tbl_td">
                                <?php 
                                    if(isset($value['working_head'])){
                                            if($setting->head->type=="on_off"){
                                                echo ($value['working_head'] > $setting->head->min_head) ? "ON" : "OFF";
                                            }else{
                                                echo $value['working_head']; 
                                            }
                                        }else{
                                            echo 0;
                                        }
                                ?>
                            </td>
                        <?php endif ?>
                    @endif
                        <td class="tbl_td"><?php echo $value['shift'];?></td>  
                        <td class="tbl_td"><?php echo $final_stitches;?></td>
                    
                    @if(hasAccess('tb'))
                        <?php if ($thred_break==1): ?>
                            <td class="tbl_td"><?php echo $value['thred_break'] - $prev_tb?></td>
                        <?php endif ?>
                    @endif
                    @if(hasAccess('max_rpm'))
                        <td class="tbl_td"><?php echo $value['max_rpm']?></td>
                    @endif

                    @if(hasAccess('live_rpm'))
                        <td class="tbl_td"><?php echo $value['rpm']?></td>
                    @endif
                    @if(hasAccess('stop_time'))
                        <td class="tbl_td"><?php echo $stop_time?></td>
                    @endif
                </tr>

            <?php } ?>
            
            <tr>
                    @if(hasAccess('worker_account'))
                        <td class="tbl_td"></td>
                    @endif
                    <td class="tbl_td"></td>
                    <td class="tbl_td"></td>
                    @if(hasAccess('head'))
                        <?php if ($working_head==1): ?>
                            <td class="tbl_td"></td>
                        <?php endif ?>
                    @endif
                        <td class="tbl_td"></td>
                        <td class="tbl_td"><?php echo $total_stitches;?></td>
                    
                    @if(hasAccess('tb'))
                        <?php if ($thred_break==1): ?>
                            <td class="tbl_td"><?php echo $total_thred_break ?></td>
                        <?php endif ?>
                    @endif
                    @if(hasAccess('max_rpm'))
                        <td class="tbl_td">
                            <?php 
                                $avg_max_rpm = array_filter($avg_max_rpm);
                                if (count($avg_max_rpm) > 0) {
                                    $avg_max_rpm = round(array_sum($avg_max_rpm) / count($avg_max_rpm));
                                }else{
                                    $avg_max_rpm = 0;
                                }
                                echo $avg_max_rpm;
                            ?>
                        </td>
                    @endif
                    @if(hasAccess('live_rpm'))
                        <td class="tbl_td">
                            <?php 
                                $avg_rpm = array_filter($avg_rpm);
                                if (count($avg_rpm) > 0) {
                                    $avg_rpm = round(array_sum($avg_rpm) / count($avg_rpm));
                                }else{
                                    $avg_rpm = 0;
                                }
                                echo $avg_rpm;
                            ?>
                        </td>
                    @endif
                    @if(hasAccess('stop_time'))
                        <td class="tbl_td"><?php echo secondsToTime($total_stop_time)?></td>
                    @endif
                    <td class="tbl_td"></td>
                    <td class="tbl_td"></td>
                    <td class="tbl_td"></td>
                </tr>
            </tbody>

        </table>

<?php endforeach ?>
