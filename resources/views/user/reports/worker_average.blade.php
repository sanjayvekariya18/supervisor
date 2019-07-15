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
            <div id="center">WORKER AVERAGE REPORT</div>
        </div>

    </div>
<?php endif ?>

<?php 

// Hide Columns
$thred_break = !empty($column_settings['columns']['thred_break']) ? 1 : 0;
$working_head = !empty($column_settings['columns']['working_head']) ? 1 : 0;

$colspan = 6;

if (!empty($working_head)) $colspan++;

?>


<table class="table table-striped" width="100%" style="padding: 3px;border-collapse: collapse;">
    <thead>
        <tr>
            <th class="tbl_th">Worker ID</th>
            <th class="tbl_th">Worker Name</th>
            <th class="tbl_th">Stitch</th>
            <?php if ($thred_break==1): ?>
                <th class="tbl_th">TB</th>
            <?php endif ?>
            <th class="tbl_th">RPM</th>
            <th class="tbl_th">Max RPM</th>
            <th class="tbl_th">Stop Time</th>
            <th class="tbl_th">Bonus</th>
        </tr>
    </thead>
    <tbody>

    <?php

        $total_stitches=[];
        $tot_thred_break=[] ;
        $rpm_avg=[] ;
        $max_rpm_avg=[] ;
        $tot_stop_time=[];
        $worker_bonus=[];
        foreach ($report_data as $key => $value) {

        $stop_time = secondsToTime($value['stop_time']);

        $total_stitches[] = $value['stitches'];
        $tot_thred_break[] = $value['thred_break'];
        $tot_stop_time[] = $value['stop_time'];
        $worker_bonus[] = $value['worker_bonus'];
        $rpm_avg[] = $value['rpm'];
        $max_rpm_avg[] = $value['max_rpm'];

    ?>
        <tr>
            <td class="tbl_td"><?php echo $value['worker_id']?></td>
            <td class="tbl_td"><?php echo $value['first_name'] .' '. $value['last_name'].' - '. $value['contact_number_1']?></td>
            <td class="tbl_td"><?php echo $value['stitches']?></td>
            <?php if ($thred_break==1): ?>
                <td class="tbl_td"><?php echo $value['thred_break'] ?></td>
            <?php endif ?>
            <td class="tbl_td"><?php echo (int)$value['rpm']?></td>
            <td class="tbl_td"><?php echo $value['max_rpm']?></td>

            <td class="tbl_td"><?php echo $stop_time?></td>
            <td class="tbl_td"><?php echo $value['worker_bonus']?></td>
        </tr>
    <?php
        }
    ?>
    <!-- Summary -->
        <tr>
            <th class="tbl_th"></th>
            <th class="tbl_th">Summary</th>
            <th class="tbl_th">
                <?php
                    $total_stitches_sum = 0; 
                    $total_stitches = array_filter($total_stitches);
                    if (count($total_stitches)>0) {
                        echo $total_stitches_sum = round(array_sum($total_stitches) / count($total_stitches)); 
                    }else{
                        echo $total_stitches_sum = 0 ;
                    }
                ?>
            </th>
            <?php if ($thred_break==1): ?>
                <th class="tbl_th">
                    <?php
                        $tot_thred_break_sum = 0; 
                        $tot_thred_break = array_filter($tot_thred_break);
                        if (count($tot_thred_break)>0) {
                            echo $tot_thred_break_sum = round(array_sum($tot_thred_break) / count($tot_thred_break)); 
                        }else{
                            echo $tot_thred_break_sum = 0 ;
                        }
                    ?>
                </th>
            <?php endif ?>
            <th class="tbl_th">
                <?php
                    $rpm_avg_sum = 0; 
                    $rpm_avg = array_filter($rpm_avg);
                    if (count($rpm_avg)>0) {
                        echo $rpm_avg_sum = round(array_sum($rpm_avg) / count($rpm_avg)); 
                    }else{
                        echo $rpm_avg_sum = 0 ;
                    }
                ?>
            </th>
            <th class="tbl_th">
                <?php
                    $max_rpm_avg_sum = 0; 
                    $max_rpm_avg = array_filter($max_rpm_avg);
                    if (count($max_rpm_avg)>0) {
                        echo $max_rpm_avg_sum = round(array_sum($max_rpm_avg) / count($max_rpm_avg)); 
                    }else{
                        echo $max_rpm_avg_sum = 0 ;
                    }
                ?>
            </th>
            <th class="tbl_th">
                <?php
                    $tot_stop_time_sum = 0; 
                    $tot_stop_time = array_filter($tot_stop_time);
                    if (count($tot_stop_time)>0) {
                        echo $tot_stop_time_sum = secondsToTime(round(array_sum($tot_stop_time) / count($tot_stop_time))); 
                    }else{
                        echo $tot_stop_time_sum = secondsToTime(0) ;
                    }
                ?>
            </th>
            <th class="tbl_th">
                <?php
                    $worker_bonus_sum = 0; 
                    $worker_bonus = array_filter($worker_bonus);
                    if (count($worker_bonus)>0) {
                        echo $worker_bonus_sum = round(array_sum($worker_bonus) / count($worker_bonus)); 
                    }else{
                        echo $worker_bonus_sum = 0 ;
                    }
                ?>
            </th>
        </tr>
    </tbody>
</table>