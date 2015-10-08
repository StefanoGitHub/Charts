<?php
//select_data_w-math.php

$errorMessaqe = (isset($_REQUEST['error'])) ?
    '<h3 class="error">No value matches the selection: '.$_REQUEST['error'].'</h3>' :
    '';

//data.php?oRequest=<?php echo json_encode($_REQUEST)? >
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />

    <link rel="stylesheet" type="text/css" href="css/style.css">

    <!-- jQuery -->
    <script src="js/jquery-2.1.4.js"></script>
    <!-- ICanHaz templating -->
    <script type="text/javascript" src="js/ICanHaz.js"></script>
    <script id="newRow" type="text/html">
        <tr>
            <td>
                <input list="netColor{{ index }}" name="netColor{{ index }}" class="datalist">
                <datalist id="netColor{{ index }}">
                    <option value="Blue">Blue TFREC</option>
                    <option value="Blue1Q">Blue1 Quincy</option>
                    <option value="Blue2Q">Blue2 Quincy</option>
                    <option value="Red">Red TFREC</option>
                    <option value="Red1Q">Red1 Quincy</option>
                    <option value="Red2Q">Red2 Quincy</option>
                    <option value="White">White TFREC</option>
                    <option value="White1Q">White1 Quincy</option>
                    <option value="White2Q">White2 Quincy</option>
                    <option value="OpenField">Open Field TFREC</option>
                    <option value="OpenFieldQ">Open Field Quincy</option>
                </datalist>
            </td>
            <td>
                <input type="radio" name="position{{ index }}" id="{{ index }}_pos1" value="1" required>
                <label for="{{ index }}_pos1" id="{{ index }}_Mark1">Mark 1</label>
                <input type="radio" name="position{{ index }}" id="{{ index }}_pos2" value="2" >
                <label for="{{ index }}_pos2" id="{{ index }}_Mark2">Mark 2</label>
                <br>
                <input type="radio" name="position{{ index }}" id="{{ index }}_pos3" value="N">
                <label for="{{ index }}_pos3" id="{{ index }}_N">North</label>
                <input type="radio" name="position{{ index }}" id="{{ index }}_pos4" value="S" >
                <label for="{{ index }}_pos4" id="{{ index }}_S">South</label>
                <input type="radio" name="position{{ index }}" id="{{ index }}_pos5" value="" >
                <label for="{{ index }}_pos5" id="{{ index }}_posNA">N/A</label>
                <br class="moreSpace">

                <b>AVG samples:</b> <input type="checkbox" name="number{{ index }}[]" id="{{ index }}_num1" value="1" >
                <label for="{{ index }}_num1" id="{{ index }}_1st">1st</label>
                <input type="checkbox" name="number{{ index }}[]" id="{{ index }}_num2" value="2">
                <label for="{{ index }}_num2" id="{{ index }}_2nd">2nd</label>
                <input type="checkbox" name="number{{ index }}[]" id="{{ index }}_num3" value="3">
                <label for="{{ index }}_num3" id="{{ index }}_3dr">3dr</label>
                <input type="checkbox" class="none" name="number{{ index }}none" id="{{ index }}_none" value="" >
                <label for="{{ index }}_none" id="{{ index }}_numNA">none</label>
                <br class="moreSpace">

                <b>Scattered light?</b>
                <input type="checkbox" name="scattered{{ index }}" id="{{ index }}_scat" value="scattered">
                <label for="{{ index }}_scat" id="{{ index }}_scat">Yes</label>
            </td>
            <td>
                <input type="text" class="date" name="sessionDate{{ index }}" placeholder="mmddyy" required />
            </td>
        </tr>
    </script>

    <title>Select data</title>
</head>

<body class="chart_page">

<h1>Select data</h1>

<form action="view_chart_w-math.php" id="select" method="get">

    <table>
        <tr>
            <th>Measurement type</th>
        </tr>
        <tr>
            <td class="measureType" >
                <input type="radio" name="measurementType" id="IRR" value="Irradiance" required>
                <label for="IRR" id="IRR_label">.IRR</label>
                <input type="radio" name="measurementType" id="TRM" value="Transmittance">
                <label for="TRM" id="TRM_label">.TRM</label>
                <input type="radio" name="measurementType" id="SSM" value="Reference">
                <label for="SSM" id="SSM_label">.SSM (Light Ref)</label>
            </td>
        </tr>
    </table>
    <div>
        <button id="addRow" class="addRow" type="button">Add measure</button>
        <button id="delRow" class="delRow" type="button">Delete last measure</button>
    </div>
    <table id="table2">
        <tr id="header">
            <th>Net Color</th>
            <th>Measurement</th>
            <th>Date
                <button type="button" id="sameDate" >All same date</button>
            </th>
        </tr>
        <tr>
            <td>
                <input list="netColor0" name="netColor0" class="datalist">
                <datalist id="netColor0">
                    <option value="Blue">Blue TFREC</option>
                    <option value="Blue1Q">Blue1 Quincy</option>
                    <option value="Blue2Q">Blue2 Quincy</option>
                    <option value="Red">Red TFREC</option>
                    <option value="Red1Q">Red1 Quincy</option>
                    <option value="Red2Q">Red2 Quincy</option>
                    <option value="White">White TFREC</option>
                    <option value="White1Q">White1 Quincy</option>
                    <option value="White2Q">White2 Quincy</option>
                    <option value="OpenField">Open Field TFREC</option>
                    <option value="OpenFieldQ">Open Field Quincy</option>
                </datalist>
            </td>
            <td>
                <input type="radio" name="position0" id="0_pos1" value="1" required>
                <label for="0_pos1" id="0_Mark1">Mark 1</label>
                <input type="radio" name="position0" id="0_pos2" value="2" >
                <label for="0_pos2" id="0_Mark2">Mark 2</label>
                <br>
                <input type="radio" name="position0" id="0_pos3" value="N">
                <label for="0_pos3" id="0_N">North</label>
                <input type="radio" name="position0" id="0_pos4" value="S" >
                <label for="0_pos4" id="0_S">South</label>
                <input type="radio" name="position0" id="0_pos5" value="" >
                <label for="0_pos5" id="0_posNA">N/A</label>
                <br class="moreSpace">

                <b>AVG samples:</b> <input type="checkbox" name="number0[]" id="0_num1" value="1" >
                <label for="0_num1" id="0_1st">1st</label>
                <input type="checkbox" name="number0[]" id="0_num2" value="2">
                <label for="0_num2" id="0_2nd">2nd</label>
                <input type="checkbox" name="number0[]" id="0_num3" value="3">
                <label for="0_num3" id="0_3dr">3dr</label>
                <input type="checkbox" class="none" name="number0none" id="0_none" value="" >
                <label for="0_none" id="0_numNA">none</label>
                <br class="moreSpace">

                <b>Scattered light?</b>
                <input type="checkbox" name="scattered0" id="0_scat" value="scattered">
                <label for="0_scat" id="0_scat">Yes</label>
                <!--                <input type="checkbox" name="reference0" id="0_ref" value="reference">
                                    <label for="0_ref" id="0_ref">REF</label>
                -->            </td>
            <td>
                <input type="text" class="date" id="thisDate" name="sessionDate0" placeholder="mmddyy" required />
            </td>
        </tr>
    </table>
    <input id="linesToChart" type="hidden" name="linesToChart" value="1">

    <div>
        <input class="chart" type="submit" name="action" value="Chart!">
    </div>
</form>

<?=$errorMessaqe?>

<div>
    <button id="newUpload" class="newUpload" type="button">Upload new data</button>
</div>

<script type="text/javascript" src="js/script.js"></script>

</body>
</html>