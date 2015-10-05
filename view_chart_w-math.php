<?php
//view_chart_w-math.php

include "functions.php";
define ('DEBUG', 'DEBUG');

//dumpDie($_REQUEST);

$measurementType = $_REQUEST['measurementType'];
//dumpDie($measurementType);

$linesToChart = array();
for ($i=0; $i<$_REQUEST['linesToChart']; $i++) {

    $position = (isset($_REQUEST['position'.$i]) && $_REQUEST['position'.$i] != '') ? $_REQUEST['position'.$i] : '';
    $numbers = (isset($_REQUEST['number'.$i]) && count($_REQUEST['number'.$i]) > 0) ? $_REQUEST['number'.$i] : '';
    $scattered = (isset($_REQUEST['scattered'.$i]) && $_REQUEST['scattered'.$i] == 'scattered') ? '_SCAT' : '';
    $reference = (isset($_REQUEST['reference'.$i]) && $_REQUEST['reference'.$i] == 'reference') ? '_REF' : '';

    $selectedMeasures = array();
    $measureID = array();

    if (is_array($numbers)) {

        if (count($numbers) > 1) {
            foreach ( $numbers as $number ) {
                //define the identification of the measure (like '1_3_SCAT', or 'N_2', or simply '_1')
                $measureID = $position . '_' . $number . $scattered . $reference;
                /*            dump($_REQUEST['netColor'.$i]);
                            dump($measureID);
                            dump($measurementType);
                            dump($_REQUEST['sessionDate'.$i]);*/

                //generate the array of selected measures
                //                    getMeasureFromDB($netColor               , $position , $measurementType, $sessionDate               )
                $selectedMeasures[] = getMeasureFromDB($_REQUEST['netColor' . $i], $measureID, $measurementType, $_REQUEST['sessionDate' . $i]);

                //dumpDie(getMeasureFromDB($_REQUEST['netColor'.$i], $measureID, $measurementType, $_REQUEST['sessionDate'.$i]));
            }
            $linesToChart[] = calculateAverage($selectedMeasures);
        } else {
            $measureID = $position . '_' . $numbers[0] . $scattered . $reference;
            $linesToChart[] = getMeasureFromDB($_REQUEST['netColor' . $i], $measureID, $measurementType, $_REQUEST['sessionDate' . $i]);
        }
        //dumpDie($linesToChart);
    } else {
        //define the identification of the measure (like '1_3_SCAT', or 'N_2', or simply '_1')
        $measureID = $position . $scattered . $reference;
/*        dump($_REQUEST['netColor'.$i]);
        dump($measureID);
        dump($measurementType);
        dump($_REQUEST['sessionDate'.$i]);*/

        //echo 'number not an array: "' . toString($numbers) . '"';

        $linesToChart[] = getMeasureFromDB($_REQUEST['netColor'.$i], $measureID, $measurementType, $_REQUEST['sessionDate'.$i]);
    }
    //dumpDie(getMeasureFromDB('dummy50', '1_1', "Transmittance", "010101"));




    //dumpDie($linesToChart);

}

//if no data available alert message and return to previous page
$empty = false;
foreach ($selectedMeasures as $measure) {
    $empty = empty($measure);
}
if ($empty) {
    header('Location: '.THIS_PAGE.'?action=Go&error=error&linesToChart='.$_REQUEST['linesToChart']);
}



$chartTitle = $measurementType;

//create the Chart object
$Chart = new Chart($chartTitle, $measurementType, $linesToChart); //$selectedMeasures
//generate the data table for the charting tool passing the array of parameters
$dataTable = 'google.visualization.arrayToDataTable(['.generateDataTable($Chart).'])';
//activate the charting function
$drawCharts ='chart.draw(dataTable, options);';

$chartTitle = $Chart->chartTitle;

//data.php?oRequest=<?php echo json_encode($_REQUEST)? >
?>
<!DOCTYPE html>
<html>
    <head>

        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Charts</title>

        <!-- jQuery -->
        <script src="js/jquery-2.1.4.js"></script>

        <!-- Load the AJAX API and the Visualization API and the corechart package.
        Do this only once per web page! -->
        <script type="text/javascript"
                src="https://www.google.com/jsapi?autoload={
              'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
              }]
            }"></script>

        <script type="text/javascript">
            // Set a callback to run when the Google Visualization API is loaded.
            google.setOnLoadCallback(drawChart);

            // Callback that creates and populates a data table,
            // instantiates the line chart, passes in the data and
            // draws it.
            function drawChart() {
                // Create the DATA TABLE.
                var dataTable = <?=$dataTable?>;

                //Set chart options.
                var options = {
                    title: '<?=$chartTitle?>',
                    curveType: 'function',
                    legend: { position: 'bottom' }
                };

                //Instantiate and draw the chart, passing in some options.
                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
                //chart.draw(dataTable, options);
                <?=$drawCharts?>
            }

        </script>

    </head>
    <body class="chart_page">

        <h1>Selected chart</h1>
        <!-- here the chart will be displayed -->
        <div id="curve_chart"></div>
        <div>
            <button id="newChart" class="newChart" type="button">New chart</button>
            <button id="newUpload" class="newUpload" type="button">Upload new data</button>
        </div>

        <script type="text/javascript" src="js/script.js"></script>
    </body>
</html>




<?php

/**
 *
 *
 * @param $netColor
 * @param $position
 * @param $measurementType
 * @param $sessionDate
 * @return bool|Measure
 *
 * TODO
 */
function getMeasureFromDB($netColor, $position, $measurementType, $sessionDate) {
    //define meanings for dataArr values based on position in the array
    $Wavelength = 0;
    $Amplitude = 1;

    $sql = "SELECT Wavelength, Amplitude
            FROM t_IRR_Data
            WHERE
             Wavelength > 299.5 AND Wavelength < 1000.5 AND

            -- Wavelength > 225 AND Wavelength < 241 AND

            NetColor = '".$netColor."' AND
            Position = '".$position."' AND
            MeasurementType = '".$measurementType."' AND
            SessionDate = '".$sessionDate."'";

    //dumpDie($sql);
    //connection comes first in mysqli (improved) function
    $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

    if(mysqli_num_rows($result) > 0) {
        $Measure = new Measure();
        $Measure->netColor = $netColor;
        $Measure->position = $position;
        $Measure->measurementType = $measurementType;
        $Measure->sessionDate = $sessionDate;

        $i=0;
        while ($row = mysqli_fetch_assoc($result)) {
            //              [$Wavelength][$Amplitude]
            //valuesArr[0][]    225        7834
            //valuesArr[1][]    300        2645
            //valuesArr[2][]    305        4975
            //valuesArr[..][]    ..         ..
            $Measure->valuesArr[$i][$Wavelength]= dbOut($row["Wavelength"]);
            $Measure->valuesArr[$i][$Amplitude]= dbOut($row["Amplitude"]);

            $i++;
        }

        @mysqli_free_result($result);

        return $Measure;
    }
    else {
        return FALSE;
    }
}//end getMeasureFromDB()



/***************************************************************************************
 * Gets the selected data from the Chart object
 * and creates the DATA TABLE for the google chart
 * Returns the DATA TABLE as a string
 * @param $Chart
 * @return string
TODO:
 ***************************************************************************************/
function generateDataTable($Chart) {
    //define meanings for dataArr values based on position in the array
    $Wavelength = 0;
    $Amplitude = 1;

    //constructing the part with the columns related to the amplitudes
    $columnNames = '';
    for ($i=0; $i < count($Chart->functions); $i++) {
        //generate the name of the lines to chart
        $columnNames .= ", '".$Chart->functions[$i]->netColor. '_' . $Chart->functions[$i]->position."'" ;
    }
    //first part of the string has all the columns
    $dataTableString = "['Wavelength'$columnNames], ";

    //constructing the values by row
    $values = '';
    for ($i=0; $i<count($Chart->functions[0]->valuesArr); $i++) {
        //last row will not end with comma
        $comma = ($i == count($Chart->functions[0]->valuesArr)-1) ? '' : ', ';
        //row $i, first column
        $values .= "['".number_format($Chart->functions[0]->valuesArr[$i][$Wavelength], 1)."', ";
        //row $i, all columns minus last
        for ($m=0; $m<count($Chart->functions)-1; $m++) {
            $values .= ($Chart->functions[$m]->valuesArr[$i][$Amplitude]).', ';
        }
        //(row $i) the last column of the row without comma
        $values .= ($Chart->functions[$m]->valuesArr[$i][$Amplitude]);
        $values .= "]$comma";
    }

    $dataTableString .= $values;

    /* espected string result:
    ['Wavelength', 'Amp1', 'Amp2' ...],
    ['225',         1000,   400   ...],
    ['230',         1170,   460   ...],
    ['235',         660,    1120  ...],
    ['240',         1030,   540   ...]
    */

    return $dataTableString;
}//end generateDataTable()


/**
 * gets an array of up to three Measures and returns a single Measure obj with the average Amplitudes
 * @param $measuresToAverage
 * @return Measure
 */
function calculateAverage($measuresToAverage) {
    //define meanings for dataArr values based on position in the array
    $Wavelength = 0;
    $Amplitude = 1;
    $countMeasuresToAverage = count($measuresToAverage);

/*    foreach ($measuresToAverage as $measure) {
        $measure->getMeasureID();
    }
    die;*/

    //create Measure object to return (all attributes are common to all measures, except Amplitudes in valuesArr[] )
    $lineToChart = $measuresToAverage[0];

    //now overwrite the Amplitude values of $lineToChart with the average:
    //for each point of the measure
    for ($i = 0; $i < count($lineToChart->valuesArr); $i++) {
        $sum = 0;
        //sum the Amplitude values of every measure to average
        for ( $m = 0; $m < $countMeasuresToAverage; $m++ ) {
             $sum += $measuresToAverage[$m]->valuesArr[$i][$Amplitude];
        }
        //average the values and overwrite the value in $lineToChart
        $lineToChart->valuesArr[$i][$Amplitude] = $sum / $countMeasuresToAverage;
    }

/*    echo $lineToChart->position;
    die;*/

    //change the data used to generate the name of the line in the chart
    $newName = explode('_', $lineToChart->position);
    $last = array_pop($newName);
    if ($last == 'SCAT') {
        array_pop($newName);
    }
    $newName = implode('_', $newName).'_AVG('.$countMeasuresToAverage.')';
    if ($last == 'SCAT') {
        $newName .= '_SCAT';
    }
    $lineToChart->position = $newName;

    return $lineToChart;
}//end calculateAverage()