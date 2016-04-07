<?php
//show_chart.php
/**
 * This page shows the selected data in a Google line chart
 **/

include "functions.php";
define ('DEBUG', 'DEBUG');

$measurementType = $_REQUEST['measurementType'];

$linesToChart = array();
for ($i=0; $i < $_REQUEST['linesToChart']; $i++) {

    $positions = $_REQUEST['position'.$i];
    $numbers = $_REQUEST['number'.$i];
    $scattered = (isset($_REQUEST['scattered'.$i]) && $_REQUEST['scattered'.$i] == 'scattered') ? '_SCAT' : '';
    $reference = (isset($_REQUEST['reference'.$i]) && $_REQUEST['reference'.$i] == 'reference') ? '_REF' : '';

    $selectedMeasures = array();
    $measureID = '';
    
    $sessionDate = str_replace("-", "", $_REQUEST['sessionDate' . $i]);

    if (is_array($positions)) {
        foreach ( $positions as $position ) {
            foreach ( $numbers as $number ) {
                //define the identification of the measure (like '1_3_SCAT', or 'N_2', or simply '_1')
                $measureID = $position . '_' . $number . $scattered . $reference;
                //generate the array of selected measures
                $contentFromDB = getMeasureFromDB($_REQUEST['netColor' . $i], $measureID, $measurementType, $sessionDate);
                //if no data available return to previous page with error message
                if ($contentFromDB == false) {
                    $errorMessage = '['.$_REQUEST['netColor' . $i].' '.$measureID.' '.$measurementType.' '. $_REQUEST['sessionDate' . $i].']';
                    header('Location: select_data.php?action=Go&error='.$errorMessage);
                    exit();
                }
                $selectedMeasures[] = $contentFromDB;
            }
        }
    } else {
        foreach ( $numbers as $number ) {
            //define the identification of the measure (like '1_3_SCAT', or 'N_2', or simply '_1')
            $position = $_REQUEST['position'.$i];
            $measureID = $position . '_' . $number . $scattered . $reference;
            //generate the array of selected measures
            $contentFromDB = getMeasureFromDB($_REQUEST['netColor' . $i], $measureID, $measurementType, $sessionDate);
            //if no data available return to previous page with error message
            if ($contentFromDB == false) {
                $errorMessage = '[' . $_REQUEST['netColor' . $i] . ' ' . $measureID . ' ' . $measurementType . ' ' . $sessionDate . ']';
                header('Location: select_data.php?action=Go&error=' . $errorMessage);
                exit();
            }
            $selectedMeasures[] = $contentFromDB;
        }
    }



    if (count($selectedMeasures) > 1) {
        $linesToChart[] = calculateAverage($selectedMeasures);
    } else {
        $linesToChart[] = $selectedMeasures[0];
    }
}


$chartTitle = $measurementType;
//if all the lines in the chart come from the same session date, add this to the title
$allSameDate = true;
for ($i=0; $i < $_REQUEST['linesToChart']; $i++) {
    if (str_replace("-", "", $_REQUEST['sessionDate0']) != $sessionDate) {
        $allSameDate = false;
    }
}
if ($allSameDate) {
    $chartTitle .= '  -  ' . implode(".", str_split(str_replace("-", "", $_REQUEST['sessionDate0']), 2));
}
//if all measures come from the same location, add this to the title
$measuresInQuincy = 0;
for ($i=0; $i < $_REQUEST['linesToChart']; $i++) {
    if (substr($_REQUEST['netColor'.$i], -1) == 'Q') {
        $measuresInQuincy++;
    }
}
$location = '';
if ($measuresInQuincy == $_REQUEST['linesToChart']) {
    $location = ' @ Quincy';
}
if ($measuresInQuincy == 0) {
    $location = ' @ TFREC';
}
$chartTitle .= $location;

//create the Chart object
$Chart = new Chart($chartTitle, $measurementType, $linesToChart); //$selectedMeasures
//from the Chart obj generate the data table for the charting tool
$dataTable = generateDataTable($Chart);
//dumpDie($dataTable);

//create vertical axis label for the chart
if($measurementType == 'Transmittance') {
    $vAxisLabel = $measurementType.' (%)';
}
if($measurementType == 'Irradiance') {
    $vAxisLabel = 'Light (uMol / m2)';
}
if($measurementType == 'Reference') {
    $vAxisLabel = 'Light (uMol / m2)';
}


//HEADER
include "includes/header_inc.php";

//###########  BODY ################//
?>
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
                /** at the moment the data from the db is loaded here from PHP */
                var dataTable = google.visualization.arrayToDataTable([<?=$dataTable?>]);

/*
                //Alternative way
                var data = new google.visualization.DataTable();
                data.addColumn('number', 'Day');
                data.addColumn('number', 'Guardians of the Galaxy');
                data.addColumn('number', 'The Avengers');
                data.addColumn('number', 'Transformers: Age of Extinction');

                data.addRows([
                    [1,  37.8, 80.8, 41.8],
                    [2,  30.9, 69.5, 32.4],
                    [3,  25.4,   57, 25.7],
                    [4,  11.7, 18.8, 10.5],
                    [5,  11.9, 17.6, 10.4],
                    [6,   8.8, 13.6,  7.7],
                    [7,   7.6, 12.3,  9.6],
                    [8,  12.3, 29.2, 10.6],
                    [9,  16.9, 42.9, 14.8],
                    [10, 12.8, 30.9, 11.6],
                    [11,  5.3,  7.9,  4.7],
                    [12,  6.6,  8.4,  5.2],
                    [13,  4.8,  6.3,  3.6],
                    [14,  4.2,  6.2,  3.4]
                ]);
*/



                //define (dark) color generators
                var darkBlue = '#000E73',   lightBlue = '#DCF9FF',
                    darkRed = '#970000',    lightRed = '#FBA281',
                    darkGrey = '#444444',   lightGrey = '#FEFEFE',
                    darkYellow = '#D48D00', lightYellow = '#88BF00', //E16C00 97D400
                    darkGreen = '#016F25', lightGreen = '#C0DC9D', //if necessary in the future
                    steps = 18; //number of shades per color wheel

                //create color palette objs
                var blueHue = new KolorWheel(darkBlue).abs(lightBlue, steps);
                var redHue = new KolorWheel(darkRed).abs(lightRed, steps);
                var greyHue = new KolorWheel(darkGrey).abs(lightGrey, steps);
                var yellowHue = new KolorWheel(darkYellow).abs(lightYellow, steps);
                var greenHue = new KolorWheel(darkGreen).abs(lightGreen, steps);

                var createColorPalette = function(wheel, steps) {
                    var colorPalette = [];
                    //get only intense distinct colors in each palette
                    for (var n = 0; n <= steps - 3; n += 3) {
                        colorPalette.push(wheel.get(n).getHex());
                    }
                    return colorPalette;
                };

                var bluePalette = createColorPalette(blueHue, steps);
                var redPalette = createColorPalette(redHue, steps);
                var greyPalette = createColorPalette(greyHue, steps);
                var yellowPalette = createColorPalette(yellowHue, steps);
                var greenPalette = createColorPalette(greenHue, steps);

                //get colors from line names
                var colorLabels = [];
                var numberOfLinesInChart = dataTable.getNumberOfColumns();
                for (var i = 1; i < numberOfLinesInChart; i++) {
                    var colorLabel = dataTable.getColumnLabel(i).split('_')[0];
                    console.log(colorLabel);
                    if (colorLabel.slice(-1) == 'Q') {
                        if (colorLabel.toLowerCase().indexOf('open') >= 0) {
                            colorLabel = colorLabel.slice(0, -1);
                        } else {
                            colorLabel = colorLabel.slice(0, -2);
                        }
                    }
                    colorLabels.push(colorLabel);
                }
                console.log(colorLabels);

                var colorSeriesArr = [];
                var blueLines = 0, redLines = 0, whiteLines = 0, yellowLines = 0, greenLines = 0;
                for (var j = 0; j < colorLabels.length; j++) {
                    switch (colorLabels[j].toLowerCase()) {
                        case 'blue':
                            colorSeriesArr.push(bluePalette[blueLines]);
                            blueLines++;
                            if (blueLines == bluePalette.length) {
                                blueLines = 0;
                            }
                           break;
                        case 'red':
                            colorSeriesArr.push(redPalette[redLines]);
                           redLines++;
                            if (redLines == redPalette.length) {
                              redLines = 0;
                            }
                            break;
                        case 'white':
                           colorSeriesArr.push(greyPalette[whiteLines]);
                            whiteLines++;
                            if (whiteLines == greyPalette.length-1) {
                                whiteLines = 0;
                            }
                            break;
//                       case 'ctrl':
//                            colorSeriesArr.push(yellowPalette[yellowLines]);
//                            yellowLines++;
//                            if (yellowLines == yellowPalette.length) {
//                                yellowLines = 0;
//                            }
//                            break;
                        case 'openfield':
                            colorSeriesArr.push(greenPalette[greenLines]);
                            greenLines++;
                            if (greenLines == greenPalette.length) {
                                greenLines = 0;
                            }
                            break;
                    }
                }

                //create color series obj
                var colorSeries = {};
                for (var k = 0; k < numberOfLinesInChart - 1; k++) { //first column is x-Axis
                    colorSeries[k] = { color: colorSeriesArr[k]}
                }

                //Set chart options.
                var options = {
                    animation: { startup: true, duration: 800, easing: 'inAndOut' },
                    forceIFrame : true,
                    title: '<?=$chartTitle?>',
                    titleTextStyle: { fontSize: 22,
                                      bold: true,
                                      italic: true
                                    },
                    curveType: 'function',
                    hAxis: { title: 'Wavelength (nm)',
                             ticks: [300, 400, 500, 600, 700, 800, 900, 1000],
                             textStyle: { fontSize: 13 }
                            },
                    vAxis: { title: '<?=$vAxisLabel?>',
                             //ticks: [40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150],
                            viewWindowMode: 'pretty',
                            textStyle: { fontSize: 13 }
                           } ,
                    chartArea: {left:'10%',
                                width:'70%' , height:'75%'
                               },
                    legend: { textStyle: { fontSize: 12 } }
                };

                options.series = colorSeries;

                //Instantiate and draw the chart, passing in some options.
                var chartDiv = document.getElementById('curve_chart')
                var chart = new google.visualization.LineChart(chartDiv);

                //generate the .png version
                //Wait for the chart to finish drawing before calling the getImageURI() method.
                google.visualization.events.addListener(chart, 'ready', function () {
                    curve_chart.innerHTML = '<img src="' + chart.getImageURI() + '">';
                    console.log(chartDiv.innerHTML);
                });

                //draw the chart
                chart.draw(dataTable, options);

                var pngDiv = document.getElementById('png');
                pngDiv.outerHTML = '<a id="png" href="' + chart.getImageURI() + '" ' +
                    'target="_blank" ' +
                    'download = "chart.png">Download <i class="fa fa-download fa-fw"></i></a>';

            }


        </script>


    <h1>Selected chart</h1>
        <div class="container">
            <!-- the chart will be displayed here -->
            <div id="curve_chart"></div>
            <!-- link to download the png version -->
            <div id="png"></div>
        </div>

    <div class="submit_button">
        <button id="newChart" class="newChart" type="button">
            <i class="fa fa-plus FA-"></i> &nbsp; New chart <i class="fa fa-line-chart fa-fw"></i>
        </button>

        <button id="newUpload" class="newUpload" type="button">
            <i class="fa fa-plus FA-"></i> &nbsp; Upload new data <i class="fa fa-table fa-fw"></i>
        </button>
    </div>


    <!-- generate TEST color palette   from http://linkbroker.hu/stuff/kolorwheel.js/  - ->
        <br><br>
        <div id="results"></div>
        <script>
            //test colors
            var r = document.getElementById('results');
            var steps = 17;

            for (var n = 0; n < steps; n++ ) {
                r.innerHTML += '<div class="box">['+n+']</div>';
                //console.log(n);
            };

            var make = function(start, end, steps){
                var base = new KolorWheel(start);
                var target = base.abs(end, steps);
                var drawBox = function(color){
                    return '<div class="box" style="color:'+color+';"><b>'+color+' </b></div>';
                };

                for (var n = 0; n < steps; n++ ) {
                    r.innerHTML += drawBox(target.get(n).getHex());
                    //console.log(n +': ' + target.get(n).getHex());
                };

                r.innerHTML += '<br clear="both" />';
            };

            make('#000E73', '#DCF9FF',  steps);//blue
            make('#970000', '#FBA281',  steps);//rosso
            make('#444444', '#FEFEFE',  steps);//grigio
            make('#D48D00', '#88BF00',  steps);//giallo 97D400
            make('#016F25', '#C0DC9D',  steps);//verde
        </script>
        <!-- end test color -->

<?php
    //###########  END BODY ################//

    //FOOTER
    include "includes/footer_inc.php";





/**
 * gets the data from IRR_Data_year table, limiting the spectrum Wavelength between 299.5 and 1000.5
 *
 * @param $netColor
 * @param $position
 * @param $measurementType
 * @param $sessionDate
 * @return boolean|Measure - returns a Measure obj or false if unsuccessful
 *
 */
function getMeasureFromDB($netColor, $position, $measurementType, $sessionDate) {
    $year = date('Y');
    $sessionDate = str_replace("-", "", $sessionDate); // 11-03-15 -> '110315'

    $sql = "SELECT Wavelength, Amplitude
            FROM IRR_Data_". $year."
            WHERE
            Wavelength > 299.5 AND Wavelength < 1000.5 AND
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
            /**              [$Wavelength][$Amplitude]
             * valuesArr[0][]    225        7834
             * valuesArr[1][]    300        2645
             * valuesArr[2][]    305        4975
             * valuesArr[..][]    ..         ..
             */
            $Measure->setWavelength($i, dbOut($row["Wavelength"]));
            $Measure->setAmplitude($i, dbOut($row["Amplitude"]));

            $i++;
        }

        @mysqli_free_result($result);

        return $Measure;
    }
    else {
        return false;
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
    //constructing the string with the columns related to the amplitudes
    $columnNames = '';
    for ($i=0; $i < count($Chart->functions); $i++) {
        //generate the name of the lines to chart
        $columnNames .= ", {label: '".$Chart->functions[$i]->netColor. '_' . $Chart->functions[$i]->position."', type: 'number'}" ;
    }
    //first part of the string has all the columns
    $dataTableString = "[{label: 'Wavelength', type: 'number'}$columnNames], ";

    //constructing the values by row
    $values = '';
    for ($i=0; $i<count($Chart->functions[0]->valuesArr); $i++) {
        //last row will not end with comma
        $comma = ($i == count($Chart->functions[0]->valuesArr) - 1) ? '' : ', ';
        //row $i, first column
        $values .= "['".number_format($Chart->functions[0]->getWavelength($i), 1)."', ";
        //row $i, all columns minus last
        for ($m=0; $m<count($Chart->functions) - 1; $m++) {
            $values .= ($Chart->functions[$m]->getAmplitude($i)).', ';
        }
        //(row $i) the last column of the row without comma
        $values .= ($Chart->functions[$m]->getAmplitude($i));
        $values .= "]$comma";
    }
    $dataTableString .= $values;
    /* espected string result:
    ['Wavelength', 'Amp1', 'Amp2' ...],
    ['225',         1000,   400   ...],
    ['230',         1170,   460   ...],
    ['235',         660,    1120  ...],
     ..             ..      ..
    ['last',        #,      #     ...]
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
    $countMeasuresToAverage = count($measuresToAverage);

    //create Measure object to return (all attributes are common to all measures, except Amplitudes in valuesArr[] )
    $lineToChart = $measuresToAverage[0];

    //now overwrite the Amplitude values of $lineToChart with the average:
    //for each point of the measure
    for ($i = 0; $i < count($lineToChart->valuesArr); $i++) {
        $sum = 0;
        //sum the Amplitude values of every measure to average
        for ( $m = 0; $m < $countMeasuresToAverage; $m++ ) {
             $sum += $measuresToAverage[$m]->getAmplitude($i);
        }
        //average the values and overwrite the value in $lineToChart
        $lineToChart->setAmplitude($i, $sum / $countMeasuresToAverage);
    }

    //change the data used to generate the name of the line in the chart
    $newName = explode('_', $lineToChart->position);
    $last = array_pop($newName);
    if ($last == 'SCAT') {
        array_pop($newName);
    }
    $newName = ($countMeasuresToAverage < 4) ?
                        implode('_', $newName).'_AVG('.$countMeasuresToAverage.')' :
                        'AVG('.$countMeasuresToAverage.')';
    if ($last == 'SCAT') {
        $newName .= '_SCAT';
    }
    $lineToChart->position = $newName;

    return $lineToChart;
}//end calculateAverage()