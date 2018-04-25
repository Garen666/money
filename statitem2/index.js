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
google.charts.setOnLoadCallback(drawBasic3);

function drawBasic3() {

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'X');
    data.addColumn('number', 'Coef 8 day');

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
    data.addColumn('number', 'Count buy');
    data.addColumn('number', 'Count sale');

    data.addRows(globalData4);

    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            title: 'Coef'
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div4'));

    chart.draw(data, options);
}

google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBasic5);

function drawBasic5() {

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'X');
    data.addColumn('number', 'Price buy');
    data.addColumn('number', 'Price sale');

    data.addRows(globalData5);

    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            title: 'Coef'
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div5'));

    chart.draw(data, options);
}

