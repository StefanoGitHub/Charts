<?php

include "functions.php";
define ('DEBUG', 'DEBUG');

/***************************************************************************************
 * Get the specific key value of the POST array and create a single array for each
 * line (to be charted) selected by the user
 *
 * Returns an array of strings
TODO:
 ***************************************************************************************/
function postToArray($postKey)
{
    $originalRequest = json_decode($_REQUEST['oRequest'], TRUE);

    $Arr = array();
    for ($i=0; $i < $originalRequest['measuresToChart']; $i++)
    {//load all the values in the array
        $Arr[] = $originalRequest[$postKey.$i];
    }
    return $Arr;
}


//load all the parameters the user selected in arrays
$netColorArr = postToArray('netColor');
$positionArr = postToArray('position');
$measurementTypeArr = postToArray('measurementType');
$measureDateArr = postToArray('measureDate');
//generate the data table for the charting tool passing the array of parameters
$dataTable = generateDataTable($netColorArr, $positionArr , $measurementTypeArr, $measureDateArr);
//activate the charting function
$drawCharts ='chart.draw(dataTable, options);';
$chartTitle = "$measurementTypeArr[0]";

header('Content-Type: application/javascript');
?>

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawChart);

// Callback that creates and populates a data table,
// instantiates the line chart, passes in the data and
// draws it.
function drawChart() {
    // Create the DATA TABLE.
    var dataTable = google.visualization.arrayToDataTable([
        <?=$dataTable?>
    ]);//data table from DB

    //Set chart options.
    var options = {
        title: '<?=$chartTitle?>',
        curveType: 'function',
        legend: { position: 'bottom' }
    };

    //Instantiate and draw the chart, passing in some options.
    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

    <?=$drawCharts?>//chart.draw(dataTable, options);
}


