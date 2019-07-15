<?php
    $machine_data = get_all_machines();
?>
<?php if (!empty($view_mode) && $view_mode=='download'): ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="{{ URL::asset('css/report.css') }}">
    <div class="report-wrapper">
        <div class="report-title">
            <?php echo $company_name ?>
        </div>
        <div class="container">
            <div id="left"><strong>Dates</strong> : <?php echo $from_date .' to '. $to_date  ?></div>
            <div id="right"></div>
            <div id="center">3 HRS PRODUCTION REPORT</div>
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

$colspan = 2;
$head_colspan = 11;

if (!empty($working_head)) $colspan++;
if (!empty($working_head)) $head_colspan++;

// SELECT `date`,m_no,st FROM tbl_hourse_report_test GROUP BY DATE( `date` ), hour( `date` ) DIV 3 ORDER BY `date`

$total_stitches_day = [];
$total_stitches_night = [];
$total_tb_day = [];
$total_tb_night = [];
$bonus_day = [];
$bonus_night = [];

$total_pages = count($report_data) * 2 ;
$total_pg = 0;
?>

<?php foreach ($report_data as $current_date => $day_night_data): ?>
    
    <?php foreach ($day_night_data as $shift_name => $rows): ?>

        <?php 
            $live_datetime = (int)date('U'); 
            $shiftEnd_datetime = (int)date('U',strtotime($rows['endDateTime']));

            $shiftStartTime = date('h:i A',strtotime($rows['startDateTime']));
            $shiftEndTime   = date('h:i A',strtotime($rows['endDateTime']));
            $shiftName      = explode("_",$shift_name)[0];

            unset($rows['startDateTime']);
            unset($rows['endDateTime']);
        
            $total_pg++;
            $rows = make_m_no_as_key($rows);
            // Merge with Machine list
            if ($options['header_data']!=true) {
                $rows = array_merge_recursive_distinct($machine_list,$rows);
            }
            $rows = make_m_no_as_key($rows);        

            if($shiftEnd_datetime <= $live_datetime){ 
        ?>
        
        <!-- Header of Table -->
        <table class="table table-striped report-table">
            <thead>
                <tr>
                    <th colspan="<?php echo $head_colspan?>" style="text-align: center;">
                        <?php echo date("d-m-Y",strtotime($current_date)) . ' (' . strtoupper($shiftName) .' '.$shiftStartTime.' - '.$shiftEndTime.')' ?>
                    </th>
                </tr>
                <tr>
                    <th class="tbl_th">M. No.</th>
                    <th class="tbl_th">M. Name</th>
                    <th class="tbl_th">W. Name</th>
                    <th class="tbl_th">Stitch</th>
                    <?php if ($working_head==1): ?>
                        <th class="tbl_th">Head</th>
                    <?php endif ?>
                    <?php if ($thred_break==1): ?>
                        <th class="tbl_th">TB</th>
                    <?php endif ?>
                    <th class="tbl_th">MAX RPM</th>
                    <th class="tbl_th">RPM</th>
                    <th class="tbl_th">Stop Time</th>
                    <th class="tbl_th">Shift Ends</th>
                    <th class="tbl_th">Total Stitch</th>
                    <th class="tbl_th">Sign.</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($rows as $machine_number => $row): ?>
                    
                    <!-- Data of table -->
                    <?php
                        $first_name = !empty($row['wrk_first_name']) ? $row['wrk_first_name'] : '';
                        $last_name = !empty($row['wrk_last_name']) ? " " . $row['wrk_last_name'] : '';
                        $worker_name = $first_name . $last_name;
                        $st = !empty($row['stop_time']) ? $row['stop_time'] : 0;
                        $stop_time = secondsToTime($st);
                        $shift_date_change = !empty($row['created_at']) ? date("h:i A",strtotime($row['created_at'])) : '';

                        $stitches = !empty($row['stitches']) ? $row['stitches'] : 0;
                        $color_code = "#000";

                        foreach ($color_range as $key => $range){
                            if ($stitches >= $range['to']) {
                                $color_code = $range['color_code'];
                            }
                            if (($stitches >= $range['from']) && ($stitches <= $range['to'])) {
                                $color_code = $range['color_code'];
                            }
                        }

                        if (strtolower($shiftName) == 'day') {
                            if(!isset($total_stitches_day[$machine_number])){
                                $total_stitches_day[$machine_number] = 0;    
                            }
                            $total_stitches_day[$machine_number] += $stitches;
                            $total_tb_day[] = !empty($row['thred_break']) ? $row['thred_break'] : 0;
                            $bonus_day[] = !empty($row['bonus_amount']) ? $row['bonus_amount'] : 0;
                        }else{
                            if(!isset($total_stitches_night[$machine_number])){
                                $total_stitches_night[$machine_number] = 0;    
                            }
                            $total_stitches_night[$machine_number] += $stitches;
                            $total_tb_night[] = !empty($row['thred_break']) ? $row['thred_break'] : 0;
                            $bonus_night[] = !empty($row['bonus_amount']) ? $row['bonus_amount'] : 0;

                        }
                    ?>
                        <tr>
                            <td class="tbl_td"><?php echo $machine_number?></td>
                            <td class="tbl_td"><?php echo $machine_data[$machine_number] ?></td>
                            <td class="tbl_td"><?php echo $worker_name?></td>
                            <td class="tbl_td" style="color: <?php echo $color_code;?>"><?php echo $stitches ?></td>
                            <?php if ($working_head==1): ?>
                                <td class="tbl_td">
                                    <?php
                                        if(isset($row['working_head'])){
                                            if($setting->head->type=="on_off"){
                                                echo ($row['working_head'] > $setting->head->min_head) ? "ON" : "OFF";
                                            }else{
                                                echo $row['working_head']; 
                                            }
                                        }else{
                                            echo 0;
                                        } 
                                    ?>
                                </td>
                            <?php endif ?>
                            <?php if ($thred_break==1): ?>
                                <td class="tbl_td"><?php echo !empty($row['thred_break']) ? $row['thred_break'] : 0 ?></td>
                            <?php endif ?>
                            <td class="tbl_td"><?php echo !empty($row['max_rpm']) ? $row['max_rpm'] : 0 ?></td>
                            <td class="tbl_td"><?php echo !empty($row['avg_rpm']) ? (int)$row['avg_rpm'] : 0 ?></td>
                            <td class="tbl_td"><?php echo $stop_time?></td>
                            <td class="tbl_td"><?php echo $shiftEndTime?></td>
                            <td class="tbl_td"><?php echo isset($row['stitches_total']) ? $row['stitches_total'] : $stitches ?></td>
                            <td class="tbl_td"></td>
                        </tr>

                <?php endforeach ?>    

            </tbody>
        </table>
        <?php if ($total_pg<$total_pages): ?>
            <div class="page-break"></div>
        <?php endif ?>
        <?php } endforeach ?>    

<?php endforeach ?>
<div class="m-t-15">&nbsp;</div>
<table class="table table-striped report-table">
    <thead>
        <tr>
            <th colspan="4" class="tbl_th">Summary</th>
        </tr>
        <tr>
            <th class="tbl_th">Description</th>
            <th class="tbl_th">Day</th>
            <th class="tbl_th">Night</th>
            <th class="tbl_th">Total</th>
        </tr>
    </thead>
    <tbody>
    <tr>
            <td class="tbl_td">Total Stitch</td>
            <td class="tbl_td">
                <?php 
                    echo $total_stitches_day_count = array_sum(array_filter($total_stitches_day));
                ?>
            </td>
            <td class="tbl_td">
                <?php 
                    echo $total_stitches_night_count = array_sum(array_filter($total_stitches_night));
                ?>
            </td>
            <td class="tbl_td">
                <?php 
                    echo $total_stitches_day_count + $total_stitches_night_count;
                ?>
            </td>
        </tr>
        <tr>
            <td class="tbl_td">Avg. Stitch</td>
            <td class="tbl_td">
                <?php 
                    $total_stitches_day = array_filter($total_stitches_day);
                    if (count($total_stitches_day) > 0) {
                        $avg_stitches_day = round(array_sum($total_stitches_day) / count($total_stitches_day));
                    }else{
                        $avg_stitches_day = 0;
                    }
                    echo $avg_stitches_day;
                ?>
            </td>
            <td class="tbl_td">
                <?php 
                    $total_stitches_night = array_filter($total_stitches_night);
                    if (count($total_stitches_night) > 0) {
                        $avg_stitches_night = round(array_sum($total_stitches_night) / count($total_stitches_night));
                    }else{
                        $avg_stitches_night = 0;
                    }
                    echo $avg_stitches_night;
                ?>
            </td>
            <td class="tbl_td">
                <?php 
                    echo $avg_stitches_day + $avg_stitches_night;
                ?>
            </td>
        </tr>
        <tr>
            <td class="tbl_td">Total TB</td>
            <td class="tbl_td">
                <?php 
                    echo $total_tb_day = array_sum(array_filter($total_tb_day));
                ?>
            </td>
            <td class="tbl_td">
                <?php 
                    echo $total_tb_night = array_sum(array_filter($total_tb_night));
                ?>
            </td>
            <td class="tbl_td">
                <?php 
                    echo $total_tb_day + $total_tb_night;
                ?>
            </td>
        </tr>
    </tbody>
</table>