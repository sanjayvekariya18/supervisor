<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Day Stitches Graph</h5>
            </div>
            <div class="card-block">
                <div id="day_stitches_graph" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
</div>  
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Night Stitches Graph</h5>
            </div>
            <div class="card-block">
                <div id="night_stitches_graph" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
</div>  
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Day RPM Graph</h5>
            </div>
            <div class="card-block">
                <div id="day_rpm_graph" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
</div>  
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Night RPM Graph</h5>
            </div>
            <div class="card-block">
                <div id="night_rpm_graph" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
</div>  
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Day STOP TIME Graph</h5>
            </div>
            <div class="card-block">
                <div id="day_stop_time_graph" style="width: 100%; height: 600px;"></div>
            </div>
        </div>
    </div>
</div>  
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Night STOP TIME Graph</h5>
            </div>
            <div class="card-block">
                <div id="night_stop_time_graph" style="width: 100%; height: 600px;"></div>
            </div>
        </div>
    </div>
</div>    
<script type="text/javascript" src="{{ URL::asset('js/chart/google/js/loader.js') }}"></script>
<script>
$(document).ready(function() {
    //Stitches Graph
    google.charts.load('current', { 'packages': ['corechart','bar'] });
    google.charts.setOnLoadCallback(drawVisualization);

    function drawVisualization() {
        
        var dayStitchesGraphdata = google.visualization.arrayToDataTable({!! $day_stitches_graph_data !!});
        var nightStitchesGraphdata = google.visualization.arrayToDataTable({!! $night_stitches_graph_data !!});
        var dayRpmGraphdata = google.visualization.arrayToDataTable({!! $day_rpm_graph_data !!});
        var nightRpmGraphdata = google.visualization.arrayToDataTable({!! $night_rpm_graph_data !!});

        var dayStopTimeGraphdata = new google.visualization.DataTable();
        dayStopTimeGraphdata.addColumn('string', 'MACHINE');
        dayStopTimeGraphdata.addColumn('number', '{!! $total_day_stoptime !!}');
        dayStopTimeGraphdata.addColumn({type: 'string', role: 'tooltip'});
        dayStopTimeGraphdata.addColumn({type: 'string', role: 'annotation'});
        dayStopTimeGraphdata.addRows({!! $day_stoptime_graph_data !!});

        var nightStopTimeGraphdata = new google.visualization.DataTable();
        nightStopTimeGraphdata.addColumn('string', 'MACHINE');
        nightStopTimeGraphdata.addColumn('number', '{!! $total_night_stoptime !!}');
        nightStopTimeGraphdata.addColumn({type: 'string', role: 'tooltip'});
        nightStopTimeGraphdata.addColumn({type: 'string', role: 'annotation'});
        nightStopTimeGraphdata.addRows({!! $night_stoptime_graph_data !!});

        var stitchesGraphOptions = {
            title: '',
            vAxis: { title: 'STITCHES' },
            hAxis: { title: 'MACHINE' },
            seriesType: 'bars',
            series: { 5: { type: 'line' } },
            colors: ['#93BE52']
        };

        var rpmGraphOptions = {
            title: '',
            vAxis: { title: 'MAX RPM' },
            hAxis: { title: 'MACHINE' },
            seriesType: 'bars',
            series: { 5: { type: 'line' } },
            colors: ['#4099ff']
        };

        var stopTimeGraphOptions = {
            title: '',
            vAxis: { title: 'STOP TIME IN MINUTE'},
            hAxis: { title: 'MACHINE NUMBER' },
            seriesType: 'bars',
            series: { 5: { type: 'line' } },
            colors: ['#FF5370']
        };

        var dayStitchesChart = new google.visualization.ColumnChart(document.getElementById('day_stitches_graph'));
        dayStitchesChart.draw(dayStitchesGraphdata, stitchesGraphOptions);

        var nightStitchesChart = new google.visualization.ColumnChart(document.getElementById('night_stitches_graph'));
        nightStitchesChart.draw(nightStitchesGraphdata, stitchesGraphOptions);

        var dayRpmChart = new google.visualization.ColumnChart(document.getElementById('day_rpm_graph'));
        dayRpmChart.draw(dayRpmGraphdata, rpmGraphOptions);

        var nightRpmChart = new google.visualization.ColumnChart(document.getElementById('night_rpm_graph'));
        nightRpmChart.draw(nightRpmGraphdata, rpmGraphOptions);

        var dayStopTimeChart = new google.visualization.ColumnChart(document.getElementById('day_stop_time_graph'));
        dayStopTimeChart.draw(dayStopTimeGraphdata, stopTimeGraphOptions);

        var nightStopTimeChart = new google.visualization.ColumnChart(document.getElementById('night_stop_time_graph'));
        nightStopTimeChart.draw(nightStopTimeGraphdata, stopTimeGraphOptions);
    }    
});
</script>