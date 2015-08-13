<?php
//charts.php

include "functions.php";
define ('DEBUG', 'DEBUG');
//startSession();
//session_start();

if(isset($_REQUEST['action'])){$Action = (trim($_REQUEST['action']));}else{$Action = "";}

switch ($Action) 
{
    //check 'act' for type of process
	case "chart":
        //startSession();
        //dumpDie($_SESSION);

        $measurementType = $_REQUEST['measurementType'];
        $chartTitle = $measurementType;

        $functions = array();
        for ($i=0; $i<$_REQUEST['measuresToChart']; $i++) {
            //generate the array of functions (measure) to chart
            $functions[] = getMeasureFromDB($_REQUEST['netColor'.$i], $_REQUEST['position'.$i], $measurementType, $_REQUEST['sessionDate'.$i]);
        }
        //create the Grafico object
        $Grafico = new Grafico($chartTitle, $measurementType, $functions);

        //$_SESSION['Grafico'] = $Grafico;

        //$Grafico = $_SESSION['Grafico'];
        //generate the data table for the charting tool passing the array of parameters
        $dataTable = generateDataTableOOP($Grafico);
        //activate the charting function
        $drawCharts ='chart.draw(dataTable, options);';

        $chartTitle = $Grafico->chartTitle;

        $page = '
            <div>
                <h3>Selected chart</h3>
            </div>
        ';
        //provide the div tag containing the chart
        $chart = '
            <div id="curve_chart"></div>
            <div>
                <a href="' . THIS_PAGE . '">New chart</a>
            </div>
        ';
        
        break;
    
    case "Go":
        //provide page content
        //startSession();

        $page = '
            <form action="'.THIS_PAGE.'" method="post">

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
            <br>
            <table>
                <tr>
                    <th>Net Color</th>
                    <th>Measurement position</th>
                    <th>Measurement date</th>
                </tr>';

        for ($i=0; $i<$_REQUEST['measuresToChart']; $i++)
        {//create the number of lines requested by the user 
        $page .= '<tr>
                <td>
                    <input type="radio" name="netColor'.$i.'" value="Blue" required>Blue 
                    <input type="radio" name="netColor'.$i.'" value="Red">Red
                    <input type="radio" name="netColor'.$i.'" value="White">White 
                </td>
                <td>
                    <input type="radio" name="position'.$i.'" value="1_Centro" required>1_Centro 
                    <input type="radio" name="position'.$i.'" value="1_Est">1_Est
                    <input type="radio" name="position'.$i.'" value="1_West">1_West <br>
                    <input type="radio" name="position'.$i.'" value="2_Centro">2_Centro 
                    <input type="radio" name="position'.$i.'" value="2_Est">2_Est
                    <input type="radio" name="position'.$i.'" value="2_West">2_West <br> 
                    <input type="radio" name="position'.$i.'" value="SCAT_Centro">SCAT_Centro 
                    <input type="radio" name="position'.$i.'" value="SCAT_Est">SCAT_Est
                    <input type="radio" name="position'.$i.'" value="SCAT_West">SCAT_West <br>
                    <input type="radio" name="position'.$i.'" value="1_REF">1_REF 
                    <input type="radio" name="position'.$i.'" value="2_REF">2_REF

                </td>
                <td>
                    <input type="text" name="sessionDate'.$i.'" placeholder="mmddyy" required />
                </td>
            </tr>
            ';
        }//end for loop
        $page .= '
                </table>
                <input id="files" type="hidden" name="measuresToChart" value="'.(int)$_REQUEST['measuresToChart'].'">

                <div>
                    <input type="submit" name="action" value="chart">
                </div>
                <div>
                    <a href="' . THIS_PAGE . '">Back</a>
                </div>
            </form>
            ';
        //no charts to display
        $chart = '';
        $chartTitle = '';
        $drawCharts = '';
        break;
        
    default: //Show existing projects
        //provide page content
        unset($_REQUEST);
        //clearSession();
        //startSession();
        //@session_destroy();
        //session_start();

        $page = '
            <form action="'.THIS_PAGE.'" method="post">
                <table style="margin:0 auto; border:grey solid 1px;">
                    <tr>
                        <th>Insert measurement(s) to display</th>
                    </tr>
                    <tr>
                        <td style="text-align:center;"><input type="text" name="measuresToChart" required /></td>
                    </tr>
                </table>
                <div>
                    <input type="submit" name="action" value="Go">
                </div>
            </form>               
        ';
        //no charts to display
        $chart = '';
        $chartTitle = '';
        $drawCharts = '';
        
}//end switch


//data.php?oRequest=<?php echo json_encode($_REQUEST)? >
?>


<!DOCTYPE html>
<html>
  <head>

      <meta name="viewport" content="width=device-width" />
      <title>Charts</title>
      <style>
          td, tr, th { text-align:center; border:grey solid 1px; }
          table { margin:0 auto; border:grey solid 1px; }
          div { text-align:center; margin: 2em; }
          #curve_chart { width:1200px; height:700px; margin:0 auto; }
      </style>

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
              var dataTable = google.visualization.arrayToDataTable([
                  <?=$dataTable?>
              ]);

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
  <body>
      
    <?=$page?>    
      
    <?=$chart?> 

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
function getMeasureFromDB($netColor, $position, $measurementType, $sessionDate)
{
    //define meanings for dataArr values based on position in the array
    $Wavelength = 0;
    $Amplitude = 1;

    $sql = "SELECT Wavelength, Amplitude
            FROM t_IRR_Data
            WHERE
            Wavelength>299.5 AND Wavelength<1000.5 AND
            NetColor = '".$netColor."' AND
            Position = '".$position."' AND
            MeasurementType = '".$measurementType."' AND
            SessionDate='".$sessionDate."'";

    //dumpDie($sql);

    //connection comes first in mysqli (improved) function
    $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

    if(mysqli_num_rows($result) > 0)
    {
        $Measure = new Measure();
        $Measure->netColor = $netColor;
        $Measure->position = $position;
        $Measure->measurementType = $measurementType;
        $Measure->sessionDate = $sessionDate;

        $i=0;
        while ($row = mysqli_fetch_assoc($result))
        {
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
    else
    {
        return FALSE;
    }

}



/***************************************************************************************
 * Gets the selected data from the Grafico object
 * and creates the DATA TABLE for the google chart
 * Returns the DATA TABLE as a string
TODO:
 ***************************************************************************************/
function generateDataTableOOP($Grafico)
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
    for ($i=0; $i<count($Grafico->functions); $i++) {

        $columnNames .= ", '".$Grafico->functions[$i]->netColor. '_' . $Grafico->functions[$i]->position."'" ;

    }
    //first part of the string has all the columns
    $dataTableString = "['Wavelength'$columnNames], ";

    //constructing the values by row
    $values = '';
    for ($i=0; $i<count($Grafico->functions[0]->valuesArr); $i++) {
        //last row will not end with comma
        $comma = ($i == count($Grafico->functions[0]->valuesArr)-1) ? '' : ', ';
        //row $i, first column
        $values .= "['".number_format($Grafico->functions[0]->valuesArr[$i][$Wavelength], 1)."', ";
        //row $i, all columns minus last
        for ($m=0; $m<count($Grafico->functions)-1; $m++) {
            $values .= ($Grafico->functions[$m]->valuesArr[$i][$Amplitude]).', ';
        }
        //(row $i) the last column of the row without comma
        $values .= ($Grafico->functions[$m]->valuesArr[$i][$Amplitude]);
        $values .= "]$comma";
    }
/*    //last value will not end with comma
        //last row, first column
        $values .= "['".$Grafico->functions[0]->valuesArr[$i][$Wavelength]."' ";

        for ($m=0; $m<count($Grafico->functions)-1; $m++) {
            $values .= number_format($Grafico->functions[$m]->valuesArr[$i][$Amplitude], 1).', ';
        }
        $values .= ($Grafico->functions[$m]->valuesArr[$i][$Amplitude]);
        $values .= "]";*/

    $dataTableString .= $values;

    //number_format( $Grafico->functions[0]->Measure[0][0], 1)."', ".$values."]";

    //dumpDie($dataTableString);

    /* espected string result:
    ['Wavelength', 'Amp1', 'Amp2' ...],
    ['225',         1000,   400   ...],
    ['230',         1170,   460   ...],
    ['235',         660,    1120  ...],
    ['240',         1030,   540   ...]
    */

    return $dataTableString;
}//end getMeasureDataFromTableOOP()