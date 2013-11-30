/*
 * Carbontally Chart Module.
 */

CTC = {};

/**
 * Request data from the server, add it to the graph and set a timeout to request again
 */
CTC.getFuelTypeChart = function(options){
    $.ajax({
        url: "/carbontally/public_html/controllers/chart.php",
        data: {chart: "fuelType"},
        success: function(data) {
            var fuelTypes = [];
            
            for(var i=0;i<data.length;i++){
                fuelTypes.push([data[i]]);
                //chart.categories[0].addPoint['']
                //options.series[0].push(data[i]);   
            }
            options.series[0].data = fuelTypes;
            chart = new Highcharts.Chart(options);
        },
        error: function(jqXHR, textStatus, errorThrown){
            //cars were not uploaded please try again later.
            alert("Problem.")
        },
        cache: false
    });
}
