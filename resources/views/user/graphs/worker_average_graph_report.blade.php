<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Stitches Graph</h5>
            </div>
            <div class="card-block">
                <div id="stitches_graph" style="width: 100%; height: 700px;"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>RPM Graph</h5>
            </div>
            <div class="card-block">
                <div id="rpm_graph" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
</div>  
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>THRED BREAK Graph</h5>
            </div>
            <div class="card-block">
                <div id="tb_graph" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
</div>  
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>STOP TIME Graph</h5>
            </div>
            <div class="card-block">
                <div id="stop_time_graph" style="width: 100%; height: 600px;"></div>
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
        
        var stitchesGraphdata = google.visualization.arrayToDataTable({!! $stitches_graph_data !!});
        var rpmGraphdata = google.visualization.arrayToDataTable({!! $rpm_graph_data !!});
        var tbGraphdata = google.visualization.arrayToDataTable({!! $tb_graph_data !!});

        var stopTimeGraphdata = new google.visualization.DataTable();
        stopTimeGraphdata.addColumn('string', 'WORKER');
        stopTimeGraphdata.addColumn('number', '{!! $total_stoptime !!}');
        stopTimeGraphdata.addColumn({type: 'string', role: 'tooltip'});
        stopTimeGraphdata.addColumn({type: 'string', role: 'annotation'});
        stopTimeGraphdata.addRows({!! $stoptime_graph_data !!});

        var stitchesGraphOptions = {
            title: '',
            vAxis: { title: 'STITCHES' },
            hAxis: { title: 'WORKER' },
            seriesType: 'bars',
            series: { 5: { type: 'line' } },
            colors: ['#93BE52']
        };

        var rpmGraphOptions = {
            title: '',
            vAxis: { title: 'MAX RPM' },
            hAxis: { title: 'WORKER' },
            seriesType: 'bars',
            series: { 5: { type: 'line' } },
            colors: ['#4099ff']
        };

        var tbGraphOptions = {
            title: '',
            vAxis: { title: 'THRED BREAK' },
            hAxis: { title: 'WORKER' },
            seriesType: 'bars',
            series: { 5: { type: 'line' } },
            colors: ['#ff0000']
        };

        var stopTimeGraphOptions = {
            title: '',
            vAxis: { title: 'STOP TIME IN MINUTE'},
            hAxis: { title: 'WORKER' },
            seriesType: 'bars',
            series: { 5: { type: 'line' } },
            colors: ['#FF5370']
        };

        var stitchesChart = new google.visualization.ColumnChart(document.getElementById('stitches_graph'));
        stitchesChart.draw(stitchesGraphdata, stitchesGraphOptions);

        var rpmChart = new google.visualization.ColumnChart(document.getElementById('rpm_graph'));
        rpmChart.draw(rpmGraphdata, rpmGraphOptions);

        var tbChart = new google.visualization.ColumnChart(document.getElementById('tb_graph'));
        tbChart.draw(tbGraphdata, tbGraphOptions);

        var stopTimeChart = new google.visualization.ColumnChart(document.getElementById('stop_time_graph'));
        stopTimeChart.draw(stopTimeGraphdata, stopTimeGraphOptions);

    }    
});
</script>