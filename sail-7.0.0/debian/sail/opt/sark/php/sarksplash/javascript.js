
  $(document).ready(function() {

    google.charts.load('current', {'packages':['corechart','gauge']});
    google.charts.setOnLoadCallback(drawSysChart);
//google.charts.setOnLoadCallback(drawCallChart);

 function drawSysChart() {


    var datalavg = google.visualization.arrayToDataTable([
        ['Label', 'Value'],
        ['LdAvg1', 0],
        ['LdAvg5', 0],
        ['LdAvg15', 0],
    ]);

    var dataMemWait = google.visualization.arrayToDataTable([
        ['Label', 'Value'],
        ['Mem%', 0],
        ['Disk%', 0],
        ['Wait%', 0],
    ]);


    var options = {
        width: 330,
        height: 120,
        max: 1,
        redFrom: .8,
        redTo: 1,
        yellowFrom: .65,
        yellowTo: .8,
        minorTicks: 0
    };

    var optionsMemWait = {
        width: 330,
        height: 120,
        max: 100,
        redFrom: .9,
        redTo: 1,
        yellowFrom: .8,
        yellowTo: .9,
        minorTicks: 5
    };

    var chart = new google.visualization.Gauge(document.getElementById('ldavg_div'));
    var memChart = new google.visualization.Gauge(document.getElementById('sys_div'));

    setInterval(function() {
        updateChans();
    }, 5000);


    function updateChans() {
      $.get('ajaxchannels.php',
        function (response) {
//        var obj = JSON.parse(response);
          $('#chantable').html(response);
//          console.log('Done');  
      });
    };

    doSystem();
    doData();
    doEndpoints();

    // dynamic update, randomly assign new values and redraw
    // 
    setInterval(function() {
        doSystem();
    }, 5000);
    setInterval(function() {
        doData();
    }, 10000);
    setInterval(function() {
        doEndpoints();
    }, 60000);    

    function doSystem() {
    	$.ajax({
            url: 'system.php',
            success: function (response) {
            	var obj = JSON.parse(response);
//            	console.log(obj.lga);
              datalavg.setValue(0, 1, obj.lga);  
              datalavg.setValue(1, 1, obj.lgb);
              datalavg.setValue(2, 1, obj.lgc);
              
              dataMemWait.setValue(0, 1, obj.mem);  
              dataMemWait.setValue(1, 1, obj.disk);
              dataMemWait.setValue(2, 1, obj.iowait);
              var upcalls = document.getElementById('upcalls');
              upcalls.innerHTML = obj.upcalls;                      
            }
        });

        chart.draw(datalavg, options);
        memChart.draw(dataMemWait, optionsMemWait);        
    }

    function doData() {
      $.ajax({
            url: 'cdrcount.php',
            success: function (response) {
              var dataobj = JSON.parse(response);
//              console.log(obj.lga);
              var inbound = document.getElementById('inbound');
              inbound.innerHTML = dataobj.inbound;
              var outbound = document.getElementById('outbound');
              outbound.innerHTML = dataobj.outbound;
              var internal = document.getElementById('internal');
              internal.innerHTML = dataobj.internal;                      
            }

        });   
    }

    function doEndpoints() {
      $.ajax({
            url: 'endpoints.php',
            success: function (response) {
              var dataobj = JSON.parse(response);
//              console.log(obj.lga);
              var extensions = document.getElementById('extensions');
              extensions.innerHTML = dataobj.phoneUpCount + '/' + dataobj.phoneCount;
              var trunks = document.getElementById('trunks');
              trunks.innerHTML = dataobj.trunkUpCount + '/' + dataobj.trunkCount;                      
            }

        });   
    }
}



/*
function drawCallChart() {


    var data = google.visualization.arrayToDataTable([
          ['Calltype', 'Minutes'],
          ['Outbound',     11],
          ['Inbound',      2],
          ['Internal',    7]
        ]);

        var options = {
          title: 'Calls Today',
          pieHole: 0.3,
          pieSliceText: 'value',

          legend: {
          		position: 'bottom',
          }
          
        };

        var chart = new google.visualization.PieChart(document.getElementById('callchart_div'));
        chart.draw(data, options);

    var data = google.visualization.arrayToDataTable([
          ['Calltype', 'Minutes'],
          ['Outbound',     50],
          ['Inbound',      0],
          ['Internal',    30]
        ]);

        var options = {
          title: 'Calls Yesterday',
          pieHole: 0.3,
          pieSliceText: 'value',

          legend: {
          		position: 'bottom',
          }

        };
        
        var chart = new google.visualization.PieChart(document.getElementById('callchartyday_div'));
        chart.draw(data, options);

}
*/
         
});
 

      
