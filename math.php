<?php
//view_chart.php

include "functions.php";
define ('DEBUG', 'DEBUG');

if(isset($_REQUEST['action'])){$Action = (trim($_REQUEST['action']));}else{$Action = "";}

switch ($Action) {
    //check 'act' for type of process
	case "CHART!":

        /*dumpDie($_REQUEST);
            ["selected"]=>
            array(2) {
                    [0]=>
                string(1) "1"
                    [1]=>
                string(1) "2"
          }*/

        $measurementType = $_REQUEST['measurementType'];
        $chartTitle = $measurementType;

        $selectedMeasures = array();
        for ($i=0; $i<$_REQUEST['measuresToChart']; $i++) {

            $scattered = (isset($_REQUEST['scattered'.$i]) && $_REQUEST['scattered'.$i] == 'scattered') ? '_SCAT' : '';
            $position = $_REQUEST['position'.$i]."_".$_REQUEST['number'.$i].$scattered;

            //generate the array of functions (measure) to chart
            $measuresFromTable[] = getMeasureFromDB($_REQUEST['netColor'.$i], $position,
                                                   $measurementType, $_REQUEST['sessionDate'.$i]);
        }

        //if no data available alert message and return to previous page
        $empty = false;
        foreach ($measuresFromTable as $measure) {
            $empty = empty($measure);
        }
        if ($empty) {
            header('Location: '.THIS_PAGE.'?action=Go&error=error&measuresToChart='.$_REQUEST['measuresToChart']);
        }

        $measuresToAverage = array();
        //filter selected measures
        for ($i = 0; $i < count($_REQUEST["selected"]); $i++) {
            $measuresToAverage[] = $measuresFromTable[$_REQUEST["selected"][$i]];
        }
        //dumpDie(count($measuresToAverage));

        $averagedMeasure = calculateAverage($measuresToAverage);

        //create the Chart object
        $Chart = new Chart($chartTitle, $measurementType, $averagedMeasure);
        //generate the data table for the charting tool passing the array of parameters
        $dataTable = 'google.visualization.arrayToDataTable(['.generateDataTable($Chart).'])';
        //activate the charting function
        $drawCharts ='chart.draw(dataTable, options);';

        $chartTitle = $Chart->chartTitle;

        $page = '
            <div>
                <h3>Selected chart</h3>
            </div>
        ';
        //provide the div tag containing the chart
        $chart = '
            <div id="curve_chart"></div>
            <div>
                 <button id="newChart" class="newChart" type="button">New chart</button>
            </div>
        ';
        
        break;

    default:
        $page = '
            <form action="' . THIS_PAGE . '" method="post">

                <table>
                    <tr>
                        <th>Measurement type</th>
                    </tr>
                    <tr>
                        <td class="measureType" >
                            <input type="radio" name="measurementType" value="Irradiance" required>IRR
                            <input type="radio" name="measurementType" value="Transmittance">TRM
                            <input type="radio" name="measurementType" value="Reference">SSM(Ref.)
                        </td>
                    </tr>
                </table>

                <div>
                    <button id="addRow" class="addRow" type="button">Add a row</button>
                    <button id="delRow" class="delRow" type="button">Delete last row</button>
                </div>

                <table id="table2">
                    <tr id="header">
                        <th>Select</th>
                        <th>Net Color</th>
                        <th>Measurement</th>
                        <th>Date</th>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="selected[]" value="0">
                        </td>
                        <td>
                            <input type="radio" name="netColor0" value="Blue" required>Blue
                            <input type="radio" name="netColor0" value="Red">Red
                            <input type="radio" name="netColor0" value="White">White <br>
                            <input type="radio" name="netColor0" value="Light_Ref">Light_Ref
                            <input type="radio" name="netColor0" value="Ctrl">Ctrl
                        </td>
                        <td>
                            <input type="radio" name="position0" id="0_pos1" value="1" required>
                                <label for="0_pos1" id="0_Mark1">Mark 1</label>
                            <input type="radio" name="position0" id="0_pos2" value="2" >
                                <label for="0_pos2" id="0_Mark2">Mark 2</label> <br>
                            <input type="radio" name="number0" id="0_num1" value="1" required>
                                <label for="0_num1" id="0_1st">1st</label>
                            <input type="radio" name="number0" id="0_num2" value="2">
                                <label for="0_num2" id="0_2nd">2nd</label>
                            <input type="radio" name="number0" id="0_num3" value="3">
                                <label for="0_num3" id="0_3dr">3dr</label> <br>

                            <input type="checkbox" name="scattered0" id="0_scat" value="scattered">
                                <label for="0_scat" id="0_scat">SCAT</label> <br>
                        </td>
                        <td>
                            <input type="text" name="sessionDate0" placeholder="mmddyy" required />
                        </td>
                    </tr>
                </table>
                <input id="files" type="hidden" name="measuresToChart" value="1">

                <div>
                    <input type="submit" name="action" value="CHART!">
                </div>
            </form>
        ';

        if (isset($_REQUEST['error']) && $_REQUEST['error'] == 'error') {
            $page .= '
                <br>
                <h3 class="error">No value matches the selection</h3>
            ';

        }

        //no charts to display
        $chart = '';
        $chartTitle = '';
        $drawCharts = '';
        $dataTable = '""';
        break;

}//end switch


//data.php?oRequest=<?php echo json_encode($_REQUEST)? >
?>
<!DOCTYPE html>
<html>
  <head>

      <meta name="viewport" content="width=device-width" />
      <link rel="stylesheet" type="text/css" href="style.css">
      <title>Charts</title>

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
      
    <?=$page?>    
      
    <?=$chart?>

    <div class="upload"><a href="upload.php">Upload data</a></div>


    <script type="text/javascript" src="script_math.js"></script>

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
            Wavelength>299.5 AND Wavelength<1100.5 AND
            NetColor = '".$netColor."' AND
            Position = '".$position."' AND
            MeasurementType = '".$measurementType."' AND
            SessionDate='".$sessionDate."'";

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
TODO:
 ***************************************************************************************/
function generateDataTable($Chart) {
    //define meanings for dataArr values based on position in the array
    $Wavelength = 0;
    $Amplitude = 1;

    //constructing the part with the columns related to the amplitudes
    $columnNames = '';
    for ($i=0; $i < count($Chart->functions); $i++) {

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


function calculateAverage($measuresToAverage) {

    $averagedMeasure = array();


    return $averagedMeasure;
}