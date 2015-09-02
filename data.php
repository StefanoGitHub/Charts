<?php

include "functions.php";
//startSession();
session_start();

dumpDie($_SESSION);
define ('DEBUG', 'DEBUG');

/***************************************************************************************
 * Get the specific key value of the POST array and create a single array for each
 * line (to be charted) selected by the user
 *
 * Returns an array of strings
TODO:
 ***************************************************************************************/
/*function postToArray($postKey)
{
    $originalRequest = json_decode($_REQUEST['oRequest'], TRUE);

    $Arr = array();
    for ($i=0; $i < $originalRequest['measuresToChart']; $i++)
    {//load all the values in the array
        $Arr[] = $originalRequest[$postKey.$i];
    }
    return $Arr;
}*/
/*    //load all the parameters the user selected in arrays
    $netColorArr = postToArray('netColor');
    $positionArr = postToArray('position');
    $measurementTypeArr = postToArray('measurementType');
    $measureDateArr = postToArray('measureDate');*/

    $Chart = $_SESSION['Chart'];
    //generate the data table for the charting tool passing the array of parameters
    $dataTable = generateDataTableOOP($Chart);
    //activate the charting function
    $drawCharts ='chart.draw(dataTable, options);';
    $chartTitle = $Chart->chartTitle;

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



<?php

/***************************************************************************************
* Gets the selected data from the Chart object
* and creates the DATA TABLE for the google chart
* Returns the DATA TABLE as a string
TODO:
***************************************************************************************/
function generateDataTableOOP($Chart)
{
    /* examples
    $netColorArr = array("Blue", "Red", "White");
    $positionArr = array("1_Centro", "1_Est", "2_West");
    $measurementTypeArr = array("Transmittance", "Irradiance", "Transmittance");
    $sessionDateArr = array("080415", "080315", "080415");*/

    //define meanings for dataArr values based on position in the array
    $Wavelength = 0;
    $Amplitude = 1;

    //constructing the part with the columns related to the amplitudes
    $columnNames = '';
    for ($i=0; $i<count($Chart->functions); $i++) {

        $columnNames .= ", '".$Chart->functions[$i]->Measure->netColor. '_' . $Chart->functions[$i]->Measure->position."'" ;

    }
    //first part of the string has all the columns
    $dataTableString = "['Wavelength'$columnNames], ";
    //constructing the values
    $values = '';
    for ($i=0; $i<count($Chart->functions[0]->Measure->valuesArr[0])-1; $i++) {
        $values .= "['".$Chart->functions[0]->Measure->valuesArr[$i][$Wavelength]."' ";
        for ($m=0; $m<count($Chart->functions); $m++) {
            $values .= $Chart->functions[$m]->Measure->valuesArr[$i][$Amplitude].', ';
        }
        $values .= "'], ";
    }
    //the last value will not end with comma
    $values .= "['".$Chart->functions[0]->Measure->valuesArr[$i][$Wavelength]."' ";
    for ($m=0; $m<count($Chart->functions); $m++) {
        $values .= $Chart->functions[$m]->Measure->valuesArr[$i][$Amplitude].', ';
    }
    $values .= "']";

    $dataTableString .= $values;

        //number_format( $Chart->functions[0]->Measure[0][0], 1)."', ".$values."]";

    dumpDie($dataTableString);

        /* espected string result:
        ['Wavelenght', 'Amp1', 'Amp2' ...],
        ['225',         1000,   400   ...],
        ['230',         1170,   460   ...],
        ['235',         660,    1120  ...],
        ['240',         1030,   540   ...]
        */

    return $dataTableString;
}//end getMeasureDataFromTableOOP()