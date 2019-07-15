<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Day Stitches Graph</h5>
            </div>
            <div class="card-block">
                <div id="stitches_graph" style="width: 100%;height: 600px;"></div>
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
        var view = new google.visualization.DataView(stitchesGraphdata);
        view.setColumns([0, 1, {
            calc: "stringify",
            sourceColumn: 1,
            type: "string",
            role: "annotation"
        }, 2, {
            calc: "stringify",
            sourceColumn: 2,
            type: "string",
            role: "annotation"
        }]);
        var stitchesGraphOptions = {
            title: '',
            vAxis: { title: 'STITCHES' },
            hAxis: { title: 'DATE' },
            seriesType: 'bars',            
            series: { 5: { type: 'line' } },
            colors: ['#FF5370','#000000'],
            bar: { groupWidth: '90%' },
        };        

        var stitchesChart = new google.visualization.ComboChart(document.getElementById('stitches_graph'));
        stitchesChart.draw(view, stitchesGraphOptions);

    }    
});
</script>