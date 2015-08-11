<?php
//charts.php

include "functions.php";
define ('DEBUG', 'DEBUG');

if(isset($_REQUEST['action'])){$Action = (trim($_REQUEST['action']));}else{$Action = "";}

switch ($Action) 
{
    //check 'act' for type of process
	case "chart": //upload selected file(s)
        
        $page = '<div style="text-align:center;">
                <h3>Selected chart</h3>
            </div>
        ';
        //provide the div tag containing the chart
        $chart = '
            <div id="curve_chart" style="width:1400px; height:700px; margin:0 auto;"></div>
            <div style="color:blue; text-align:center;">
                <a href="' . THIS_PAGE . '">New chart</a>
            </div>
        ';
        
        break;
    
    case "Go":
        //provide page content
        $page = '
            <form action="'.THIS_PAGE.'" method="post">
                <table style="margin:0 auto; border:grey solid 1px; border-collapse:collapse">
                <tr>
                    <th>Measurement type</th>
                    <th>Net Color</th>
                    <th>Measurement position</th>
                    <th>Measurement date</th>
                </tr>';
        
        for ($i=0; $i<$_REQUEST['measuresToChart']; $i++)
        {//create the number of lines requested by the user 
        $page .= '<tr>
                <td style="text-align:center">
                    <input type="radio" name="measurementType'.$i.'" value="Irradiance" required>IRR 
                    <input type="radio" name="measurementType'.$i.'" value="Transmittance">TRM
                    <input type="radio" name="measurementType'.$i.'" value="Reference">SSM(Ref.) 
                </td>
                <td style="text-align:center">
                    <input type="radio" name="netColor'.$i.'" value="Blue" required>Blue 
                    <input type="radio" name="netColor'.$i.'" value="Red">Red
                    <input type="radio" name="netColor'.$i.'" value="White">White 
                </td>
                <td style="text-align:center">
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
                <td style="text-align:center"><input type="text" name="measureDate'.$i.'" placeholder="mmddyy" required /></td>
            </tr>
            ';
        }//end for loop
        $page .= '
                </table>
                <input id="files" type="hidden" name="measuresToChart" value="'.(int)$_REQUEST['measuresToChart'].'">
                <div style="text-align:center;" colspan="4">
                    <input type="submit" name="action" value="chart">
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
                <div style="text-align:center;" colspan="3">
                    <input type="submit" name="action" value="Go">
                </div>
            </form>               
        ';
        //no charts to display
        $chart = '';
        $chartTitle = '';
        $drawCharts = '';
        
}//end switch

?>


<!DOCTYPE html>
<html>
  <head>
      <style>
        td { margin:0 auto; border:grey solid 1px; }

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

      <script type="text/javascript" src='data.php?oRequest=<?php echo json_encode($_REQUEST) ?>'></script>

  </head>
  <body>
      
    <?=$page?>    
      
    <?=$chart?> 

  </body>
</html>
