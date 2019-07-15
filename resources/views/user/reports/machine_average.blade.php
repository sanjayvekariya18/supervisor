
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
            <div id="center">MACHINE AVERAGE REPORT</div>
        </div>

    </div>
<?php endif ?>

<?php 

// Hide Columns
$thred_break = !empty($column_settings['columns']['thred_break']) ? 1 : 0;
$working_head = !empty($column_settings['columns']['working_head']) ? 1 : 0;

$colspan = 6;

if (!empty($thred_break)) $colspan++;

?>


<table class="table table-striped" width="100%" style="padding: 3px;border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="<?php echo $colspan?>" style="text-align: center;">
                DAY
            </th>
        </tr>
        <tr>
            <th class="tbl_th">Machine Number</th>
            @if(hasAccess('machine_name'))
                <th class="tbl_th">Machine Name</th>
            @endif
            
                <th class="tbl_th">Stitch</th>
            
            @if(hasAccess('tb'))
                <?php if ($thred_break==1): ?>
                    <th class="tbl_th">TB</th>
                <?php endif ?>
            @endif
            @if(hasAccess('live_rpm'))
                <th class="tbl_th">RPM</th>
            @endif
            @if(hasAccess('stop_time'))
                <th class="tbl_th">Stop Time</th>
            @endif
            @if(hasAccess('bonus'))
                <th class="tbl_th">Bonus</th>
            @endif
        </tr>
    </thead>
    <tbody>

    <?php

        $total_stitches=[];
        $tot_thred_break=[];
        $tot_stop_time=[];
        $rpm_avg = [];
        $worker_bonus=[];
        foreach ($report_data as $key => $value) {
        
        // Only Day Shift
        if ($value['shift']==2) continue;

        $stop_time = secondsToTime($value['stop_time']);

        $total_stitches[] = $value['stitches'];
        $tot_thred_break[] = $value['thred_break'];
        $tot_stop_time[] = $value['stop_time'];
        $rpm_avg[] = $value['rpm'];
        $worker_bonus[] = $value['worker_bonus'];
    ?>
        <tr>
            <td class="tbl_td"><?php echo $value['machine_number']?></td>            
            @if(hasAccess('machine_name'))
                <td class="tbl_td"><?php echo $value['machine_name']?></td>                
            @endif
           
                <td class="tbl_td"><?php echo $value['stitches']?></td>                
            
            @if(hasAccess('tb'))
                <?php if ($thred_break==1): ?>
                    <td class="tbl_td"><?php echo $value['thred_break'] ?></td>
                <?php endif ?>
            @endif
            @if(hasAccess('live_rpm'))
                <td class="tbl_td"><?php echo (int)$value['rpm']?></td>                
            @endif
            @if(hasAccess('stop_time'))
                <td class="tbl_td"><?php echo $stop_time?></td>
            @endif
            @if(hasAccess('bonus'))
                <td class="tbl_td"><?php echo $value['worker_bonus']?></td>                
            @endif
        </tr>
    <?php
        }
    ?>
    <!-- Summary -->
        <tr>
            <th class="tbl_th">Summary</th>
            @if(hasAccess('machine_name'))
            <th></th>
            @endif
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
            
            @if(hasAccess('tb'))
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
            @endif
            @if(hasAccess('live_rpm'))
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
            @endif
            @if(hasAccess('stop_time'))
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
            @endif
            @if(hasAccess('bonus'))
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
            @endif
        </tr>
    </tbody>
</table>
<div class="page-break"></div>
<table class="table table-striped" width="100%" style="padding: 3px;border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="<?php echo $colspan?>" style="text-align: center;">
                NIGHT
            </th>
        </tr>
        <tr>
            <th class="tbl_th">Machine Number</th>
            @if(hasAccess('machine_name'))
                <th class="tbl_th">Machine Name</th>
            @endif
            
                <th class="tbl_th">Stitch</th>
            
            @if(hasAccess('tb'))
                <?php if ($thred_break==1): ?>
                    <th class="tbl_th">TB</th>
                <?php endif ?>
            @endif
            @if(hasAccess('live_rpm'))
                <th class="tbl_th">RPM</th>
            @endif
            @if(hasAccess('stop_time'))
                <th class="tbl_th">Stop Time</th>
            @endif
            @if(hasAccess('bonus'))
                <th class="tbl_th">Bonus</th>
            @endif
        </tr>
    </thead>
    <tbody>

    <?php

        $total_stitches= [];
        $tot_thred_break= [];
        $tot_stop_time= [];
        $rpm_avg= [];
        $worker_bonus= [];
        foreach ($report_data as $key => $value) {
        
        // Only Night Shift
        if ($value['shift']==1) continue;

        $stop_time = secondsToTime($value['stop_time']);

        $total_stitches[] = $value['stitches'];
        $tot_thred_break[] = $value['thred_break'];
        $rpm_avg[] = $value['rpm'];
        $tot_stop_time[] = $value['stop_time'];
        $worker_bonus[] = $value['worker_bonus'];
    ?>
        <tr>
            <td class="tbl_td"><?php echo $value['machine_number']?></td>            
            @if(hasAccess('machine_name'))
                <td class="tbl_td"><?php echo $value['machine_name']?></td>                
            @endif
            
                <td class="tbl_td"><?php echo $value['stitches']?></td>                
            
            @if(hasAccess('tb'))
                <?php if ($thred_break==1): ?>
                    <td class="tbl_td"><?php echo $value['thred_break'] ?></td>
                <?php endif ?>
            @endif
            @if(hasAccess('live_rpm'))
                <td class="tbl_td"><?php echo $value['rpm']?></td>                
            @endif
            @if(hasAccess('stop_time'))
                <td class="tbl_td"><?php echo $stop_time?></td>
            @endif
            @if(hasAccess('bonus'))
                <td class="tbl_td"><?php echo $value['worker_bonus']?></td>                
            @endif
        </tr>
    <?php
        }
    ?>
    <!-- Summary -->
    <tr>
            <th class="tbl_th">Summary</th>
            @if(hasAccess('machine_name'))
            <th></th>
            @endif
            
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
            
            @if(hasAccess('tb'))
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
            @endif
            @if(hasAccess('live_rpm'))
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
            @endif
            @if(hasAccess('stop_time'))
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
            @endif
            @if(hasAccess('bonus'))
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
            @endif
        </tr>
    </tbody>
</table>