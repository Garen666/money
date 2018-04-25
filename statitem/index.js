google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBasic);

function drawBasic() {

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'X');
    data.addColumn('number', 'CountBuy');
    data.addColumn('number', 'CountSale');

    data.addRows(globalData);

    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            title: 'Nalichie'
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

    chart.draw(data, options);
}


google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBasic2);

function drawBasic2() {

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'X');
    data.addColumn('number', 'PriceBuy');
    data.addColumn('number', 'PriceSale');
    data.addColumn('number', 'Margin');

    data.addRows(globalData2);

    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            title: 'Price'
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div2'));

    chart.draw(data, options);
}


google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBasic3);

function drawBasic3() {

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'X');
    data.addColumn('number', 'Coef 3 hour');
    //data.addColumn('number', 'Coef 10 day');

    data.addRows(globalData3);

    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            title: 'Coef'
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div3'));

    chart.draw(data, options);
}


google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBasic4);

function drawBasic4() {

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'X');
    data.addColumn('number', 'Buy Count for day');
    data.addColumn('number', 'Sale Count for day');

    data.addRows(globalData4);

    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            title: 'Count'
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div4'));

    chart.draw(data, options);
}