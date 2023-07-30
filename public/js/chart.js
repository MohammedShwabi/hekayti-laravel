
// Load Google Charts library
google.charts.load('current', {
    'packages': ['corechart']
});

// Callback function when Google Charts library is loaded
google.charts.setOnLoadCallback(drawChart);


function drawChart() {
    
    // Create a new DataTable
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'الشهر'); // Add column for month
    data.addColumn('number', 'عدد المستخدمين'); // Add column for user count

    // Populate the data rows using the userGrowthData
    Object.entries(userGrowthData).forEach(function ([month, count]) {
        data.addRow([month, count]);
    });


    // Set chart options to change needed style of chart
    var options = {
        colors: ['#E86565'],
        backgroundColor: '#FEEEDC',
        curveType: 'function',
        legend: {
            position: 'top',
            textStyle: {
                color: '#4D1F00',
                fontSize: 16,
                fontName: 'Alhurra'
            }
        },
        annotations: {
            textStyle: {
                fontName: 'Arial',
                fontSize: 18,
                bold: true,
                italic: true,
                color: '#871b47',
                auraColor: '#d799ae',
                opacity: 0.8
            }
        },
        hAxis: {
            title: 'الشهر',
            titleTextStyle: {
                color: '#4D1F00',
                italic: false,
                fontSize: 18
            }
        },
        vAxis: {
            title: 'المستخدمين',
            titleTextStyle: {
                color: '#4D1F00',
                italic: false,
                fontSize: 18
            }
        }
    };

    // Create a new LineChart instance and pass the chart container element
    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

    // Draw the chart with data and options
    chart.draw(data, options); 
}
