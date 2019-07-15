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
            <div id="center">WORKER SALARY REPORT</div>
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

<div class="m-t-5"></div>
<table class="table table-striped report-table" >
    <thead>
        <tr>
            <th class="tbl_th">Sr No</th>
            <th class="tbl_th">Worker Name</th>
            <th class="tbl_th">Stitch</th>
            <th class="tbl_th">Avg Stitches</th>
            <?php if ($thred_break==1): ?>
                <th class="tbl_th">TB</th>
            <?php endif ?>
            <th class="tbl_th">Stop Time</th>
            <th class="tbl_th">Bonus</th>
            <th class="tbl_th">Salary</th>
            <th class="tbl_th">Total Salary</th>
        </tr>
    </thead>
    <tbody>

    <?php

        $total_stitches=[];
        $avg_stitches=[];
        $tot_thred_break=[] ;
        $tot_stop_time=[];
        $worker_bonus=[];
        $working_salary=[];
        $total_salary=[];
        foreach ($report_data as $key => $value) {

        $stop_time = secondsToTime($value['stop_time']);

        $total_stitches[] = $value['stitches'];
        $avg_stitches[] = $value['avg_stitches'];
        $tot_thred_break[] = $value['thred_break'];
        $tot_stop_time[] = $value['stop_time'];
        $worker_bonus[] = $value['worker_bonus'];
        $working_salary[] = $value['working_salary'];
        $total_salary[] = $value['total_salary'];

    ?>
        <tr>
            <td class="tbl_td"><?php echo ($key+1) ?></td>
            <td class="tbl_td"><?php echo $value['first_name'] .' '. $value['last_name'].' - '. $value['contact_number_1']?></td>
            <td class="tbl_td"><?php echo $value['stitches']?></td>
            <td class="tbl_td"><?php echo $value['avg_stitches']?></td>
            <?php if ($thred_break==1): ?>
                <td class="tbl_td"><?php echo $value['thred_break'] ?></td>
            <?php endif ?>
            <td class="tbl_td"><?php echo $stop_time?></td>
            <td class="tbl_td"><?php echo $value['worker_bonus']?></td>
            <td class="tbl_td"><?php echo $value['working_salary']?></td>
            <td class="tbl_td"><?php echo $value['total_salary']?></td>
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
                    echo array_sum($total_stitches);
                ?>
            </th>
            <th class="tbl_th">
                <?php
                    echo array_sum($avg_stitches);
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
            <th class="tbl_th">
                <?php
                    echo array_sum($working_salary);
                ?>
            </th>
            <th class="tbl_th">
                <?php
                    echo array_sum($total_salary);
                ?>
            </th>
        </tr>
    </tbody>
</table>