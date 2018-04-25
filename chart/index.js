google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawTrendlines);

function drawTrendlines() {
    var data = new google.visualization.DataTable();
    data.addColumn('timeofday', 'Time of Day');
    data.addColumn('number', 'Motivation Level');

    data.addRows([
        [1, .25],
        [ 2, .5],
        [3, 1],
        [4, 2.25],
    ]);

    var options = {
        title: '',
        trendlines: {
            0: {type: 'linear', lineWidth: 5, opacity: .3},
            1: {type: 'exponential', lineWidth: 10, opacity: .3}
        },


    };

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}