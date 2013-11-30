<!--
CarbonTally Charts.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>CarbonTally Charts</title>
        <script src="../js/CarbontallyCharts.js"></script>
        <script src="../js/jquery.min.js"></script>
        <script type="text/javascript">
        $(function () {
            var chart;
            //var chart; // globally available
            $(document).ready(function() {
                var options = {
                    chart: {
                        renderTo: 'container',
                        type: 'column'
                        //events: {
                        //    load: CTC.getFuelTypeChart
                        //}
                    },
                    title: {
                        text: 'Fuel Type'
                    },
                    xAxis: {
                        categories: []
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Number of cars'
                        }
                    },
                    series: [{
                        name: 'Fuel Types',
                        data: []
                    }]
                };      
                
                CTC.getFuelTypeChart(options);
            });
        });
        </script>
    </head>
    <body>
        <script src="../js/highcharts.js" type="text/javascript"></script>
        <div id="container" style="width: 100%; height: 400px"></div>
    </body>
</html>
