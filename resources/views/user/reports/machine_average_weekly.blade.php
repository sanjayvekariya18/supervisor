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
            <div id="center">MACHINE AVERAGE WEEKLY REPORT</div>
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
<style>
table tr th{
    text-align:center;
}
</style>

<table class="table table-striped" width="100%" style="padding: 3px;border-collapse: collapse;">
    <thead>        
        <tr>
            <th class="tbl_th">#</th>
            <th class="tbl_th" colspan="2" >Day 1</th>
            <th class="tbl_th" colspan="2" >Day 2</th>
            <th class="tbl_th" colspan="2" >Day 3</th>
            <th class="tbl_th" colspan="2" >Day 4</th>
            <th class="tbl_th" colspan="2" >Day 5</th>
            <th class="tbl_th" colspan="2" >Day 6</th>
            <th class="tbl_th" colspan="2">Day 7</th>
            <th class="tbl_th" colspan="2">Avg</th>
        </tr>
        <tr>
            <th class="tbl_th">Machine Number</th>
            <th class="tbl_th">Day</th>
            <th class="tbl_th">Night</th>
            <th class="tbl_th">Day</th>
            <th class="tbl_th">Night</th>
            <th class="tbl_th">Day</th>
            <th class="tbl_th">Night</th>
            <th class="tbl_th">Day</th>
            <th class="tbl_th">Night</th>
            <th class="tbl_th">Day</th>
            <th class="tbl_th">Night</th>
            <th class="tbl_th">Day</th>
            <th class="tbl_th">Night</th>
            <th class="tbl_th">Day</th>
            <th class="tbl_th">Night</th>
            <th class="tbl_th">Day Avg</th>
            <th class="tbl_th">Night Avg</th>
        </tr>
    </thead>
    <tbody>

    <?php
               
        foreach ($report_data as $machineKey => $machineData) {        
    ?>
        <tr>
            <td class="tbl_td"><?php echo $machineKey ?></td>            
            <?php 
                $average = $machineData['avg'];
                unset($machineData['avg']);
                foreach ($machineData as $dateKey => $dateValues) {
                    foreach ($dateValues as $shift => $stiches) {                        
            ?>
                <td><?= $stiches ?></td>
            <?php }           
                }
            ?>
            <td><?= intval($average[1]/7) ?></td>
            <td><?= intval($average[2]/7) ?></td>
        </tr>        
    <?php
        }
    ?>
    <!-- Summary -->
        
    </tbody>
</table>
