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
            <div id="center">WORKER TOTAL REPORT</div>
        </div>

    </div>
<?php endif ?>

<?php 

// Hide Columns
$thred_break = !empty($column_settings['columns']['thred_break']) ? 1 : 0;
$working_head = !empty($column_settings['columns']['working_head']) ? 1 : 0;

$colspan = 5;

if (!empty($working_head)) $colspan++;

?>


<table class="table table-striped" width="100%" style="padding: 3px;border-collapse: collapse;">
    <thead>
        <tr>
            <th class="tbl_th">Sr No</th>
            <th class="tbl_th">Machine No</th>
            <th class="tbl_th">Worker Name</th>
            <th class="tbl_th">Shift</th>
            <th class="tbl_th">Stitch</th>
            <?php if ($thred_break==1): ?>
                <th class="tbl_th">TB</th>
            <?php endif ?>            
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

    ?>
        <tr>
            <td class="tbl_td"><?php echo ($key+1) ?></td>
            <td class="tbl_td"><?php echo $value['machine_number']?></td>
            <td class="tbl_td"><?php echo $value['first_name'] .' '. $value['last_name'].' - '. $value['contact_number_1']?></td>
            <td class="tbl_td"><?php echo $value['shift']?></td>
            <td class="tbl_td"><?php echo $value['stitches']?></td>
            <?php if ($thred_break==1): ?>
                <td class="tbl_td"><?php echo $value['thred_break'] ?></td>
            <?php endif ?>            
            <td class="tbl_td"><?php echo $stop_time?></td>
            <td class="tbl_td"><?php echo $value['worker_bonus']?></td>
        </tr>
    <?php
        }
    ?>
    <!-- Summary -->
        <tr>
            <th colspan="4" class="tbl_th">Summary</th>
            <th class="tbl_th">
                <?php
                    echo array_sum($total_stitches);
                ?>
            </th>
            <?php if ($thred_break==1): ?>
                <th class="tbl_th">
                    <?php
                        echo array_sum($tot_thred_break);
                    ?>
                </th>
            <?php endif ?>            
            <th class="tbl_th">
                <?php
                    echo secondsToTime(array_sum($tot_stop_time));
                ?>
            </th>
            <th class="tbl_th">
                <?php
                    echo array_sum($worker_bonus);
                ?>
            </th>
        </tr>
    </tbody>
</table>