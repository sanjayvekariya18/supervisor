<?php
    $machine_data = get_all_machines();
    $colspan = 7;
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
            <div id="center">WORKER SALARY REPORT</div>
        </div>
        <div class="container">
            <?php 
                $group_name = !empty($options['group_name']) ? $options['group_name'] : '-';
                $machine_no = !empty($options['machine_no']) ? $options['machine_no'] : '-';
                $worker_name = !empty($options['worker_name']) ? $options['worker_name'] : '-';
            ?>            
            <div id="center"><strong>Worker</strong> : <?php echo isset($report_data[0]['first_name'])?$report_data[0]['first_name']:''?> </div>
        </div>
    </div>
<?php endif ?>
<?php 
$srno = 0;
$totalStitch = [];
$totalBonus = [];
$totalSalary = [];
$totalTotal = [];

foreach ($report_data as $worker_salary){
    $srno += 1;
    $totalStitch[]  = $worker_salary['stitches'];
    $totalBonus[]   = $worker_salary['bonus_amount'];
    $totalSalary[]  = $worker_salary['working_salary'];
    $totalTotal[]   = $worker_salary['total_salary'];
?>

<table class="table table-striped report-table">
    <thead>
        <tr>
            <th colspan="<?php echo $colspan?>" style="text-align: center;">
                <?php echo date("d-m-Y",strtotime($worker_salary['report_date'])) ?>
            </th>
        </tr>
        <tr>
            <th class="tbl_th">SR.NO</th>
            <th class="tbl_th">Shift</th>
            <th class="tbl_th">Stitch</th>
            <th class="tbl_th">Bonus</th>
            <th class="tbl_th">Salary</th>
            <th class="tbl_th">Total</th>
            <th class="tbl_th">Sign.</th>
        </tr>
    </thead>
    <tbody>
            <!-- Data of table -->
            <tr>
                <td class="tbl_td"><?php echo $srno ?></td>
                <td class="tbl_td"><?php echo $worker_salary['shiftname'] ?></td>
                <td class="tbl_td"><?php echo $worker_salary['stitches'] ?></td>
                <td class="tbl_td"><?php echo !empty($worker_salary['bonus_amount']) ? $worker_salary['bonus_amount'] : 0?></td>
                <td class="tbl_td"><?php echo !empty($worker_salary['working_salary']) ? $worker_salary['working_salary'] : 0?></td>
                <td class="tbl_td"><?php echo !empty($worker_salary['total_salary']) ? $worker_salary['total_salary'] : 0?></td>
                <td class="tbl_td"></td>
            </tr>
    </tbody>
</table> 
<?php } ?>
<table class="table table-striped report-table">
    <thead>
        <tr>
            <th colspan="2" class="tbl_th">Summary</th>
        </tr>
        <tr>
            <th class="tbl_th">Description</th>
            <th class="tbl_th">Total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tbl_td">Total Stitch</td>
            <td class="tbl_td">{{array_sum($totalStitch)}}</td>
        </tr>
        <tr>
            <td class="tbl_td">Average Stitch</td>
            <td class="tbl_td">
                <?php
                    if(count($totalStitch) > 0){
                        echo round(array_sum($totalStitch) / count($totalStitch));
                    }else{
                        echo "0";
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td class="tbl_td">Total Bonus</td>
            <td class="tbl_td">{{array_sum($totalBonus)}}</td>
        </tr>
        <tr>
            <td class="tbl_td">Total Salary</td>
            <td class="tbl_td">{{array_sum($totalSalary)}}</td>
        </tr>
        <tr>
            <td class="tbl_td">Total Bonus + Salary</td>
            <td class="tbl_td">{{array_sum($totalTotal)}}</td>
        </tr>
    </tbody>
</table>
 

