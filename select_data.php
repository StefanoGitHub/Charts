<?php
//select_data.php

$errorMessage = (isset($_REQUEST['error']) && $_REQUEST['error'] == 'error') ?
    '<h3 class="error">No value matches the selection</h3>' :
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
                <input type="radio" name="netColor{{ index }}" id="{{ index }}_Blue" value="Blue" required>
                <label for="{{ index }}_Blue" id="{{ index }}_Blue_label">Blue</label>
                <input type="radio" name="netColor{{ index }}" id="{{ index }}_Red" value="Red">
                <label for="{{ index }}_Red" id="{{ index }}_Red_label">Red</label>
                <input type="radio" name="netColor{{ index }}" id="{{ index }}_White" value="White">
                <label for="{{ index }}_White" id="{{ index }}_White_label">White</label>
                <br>
                <input type="radio" name="netColor{{ index }}" id="{{ index }}_Light_Ref" value="Light_Ref">
                <label for="{{ index }}_Light_Ref" id="{{ index }}_Light_Ref_label">Light_Ref</label>
                <input type="radio" name="netColor{{ index }}" id="{{ index }}_Ctrl" value="Ctrl">
                <label for="{{ index }}_Ctrl" id="{{ index }}_Ctrl_label">Ctrl</label>
            </td>
            <td>
                <input type="radio" name="position{{ index }}" id="{{ index }}_pos1" value="1" required>
                <label for="{{ index }}_pos1" id="{{ index }}_Mark1">Mark 1</label>
                <input type="radio" name="position{{ index }}" id="{{ index }}_pos2" value="2" >
                <label for="{{ index }}_pos2" id="{{ index }}_Mark2">Mark 2</label>
                <br>
                <input type="radio" name="number{{ index }}" id="{{ index }}_num1" value="1" required>
                <label for="{{ index }}_num1" id="{{ index }}_1st">1st</label>
                <input type="radio" name="number{{ index }}" id="{{ index }}_num2" value="2">
                <label for="{{ index }}_num2" id="{{ index }}_2nd">2nd</label>
                <input type="radio" name="number{{ index }}" id="{{ index }}_num3" value="3">
                <label for="{{ index }}_num3" id="{{ index }}_3dr">3dr</label>
                <br>

                <input type="checkbox" name="scattered{{ index }}" id="{{ index }}_scat" value="scattered">
                <label for="{{ index }}_scat" id="{{ index }}_scat">SCAT</label>
            </td>
            <td>
                <input type="text" name="sessionDate{{ index }}" placeholder="mmddyy" required />
            </td>
        </tr>
    </script>

    <title>Select data</title>
</head>

<body class="chart_page">

<h1>Select data</h1>

<form action="view_chart.php" method="get">

    <table>
        <tr>
            <th>Measurement type</th>
        </tr>
        <tr>
            <td class="measureType" >
                <input type="radio" name="measurementType" id="IRR" value="Irradiance" required>
                    <label for="IRR" id="IRR_label">IRR</label>
                <input type="radio" name="measurementType" id="TRM" value="Transmittance">
                    <label for="TRM" id="TRM_label">TRM</label>
                <input type="radio" name="measurementType" id="SSM" value="Reference">
                    <label for="SSM" id="SSM_label">SSM (Ref.)</label>
            </td>
        </tr>
    </table>
    <div>
        <button id="addRow" class="addRow" type="button">Add a row</button>
        <button id="delRow" class="delRow" type="button">Delete last row</button>
    </div>
    <table id="table2">
        <tr id="header">
            <th>Net Color</th>
            <th>Measurement</th>
            <th>Date</th>
        </tr>
        <tr>
            <td>
                <input type="radio" name="netColor0" id="0_Blue" value="Blue" required>
                    <label for="0_Blue" id="0_Blue_label">Blue</label>
                <input type="radio" name="netColor0" id="0_Red" value="Red">
                    <label for="0_Red" id="0_Red_label">Red</label>
                <input type="radio" name="netColor0" id="0_White" value="White">
                    <label for="0_White" id="0_White_label">White</label>
                <br>
                <input type="radio" name="netColor0" id="0_Light_Ref" value="Light_Ref">
                    <label for="0_Light_Ref" id="0_Light_Ref_label">Light_Ref</label>
                <input type="radio" name="netColor0" id="0_Ctrl" value="Ctrl">
                    <label for="0_Ctrl" id="0_Ctrl_label">Ctrl</label>
            </td>
            <td>
                <input type="radio" name="position0" id="0_pos1" value="1" required>
                    <label for="0_pos1" id="0_Mark1">Mark 1</label>
                <input type="radio" name="position0" id="0_pos2" value="2" >
                    <label for="0_pos2" id="0_Mark2">Mark 2</label>
                <br>
                <input type="radio" name="number0" id="0_num1" value="1" required>
                    <label for="0_num1" id="0_1st">1st</label>
                <input type="radio" name="number0" id="0_num2" value="2">
                    <label for="0_num2" id="0_2nd">2nd</label>
                <input type="radio" name="number0" id="0_num3" value="3">
                    <label for="0_num3" id="0_3dr">3dr</label>
                <br>

                <input type="checkbox" name="scattered0" id="0_scat" value="scattered">
                    <label for="0_scat" id="0_scat">SCAT</label>
            </td>
            <td>
                <input type="text" name="sessionDate0" placeholder="mmddyy" required />
            </td>
        </tr>
    </table>
    <input id="files" type="hidden" name="measuresToChart" value="1">

    <div>
        <input type="submit" name="action" value="Chart!">
    </div>
</form>

<?=$errorMessage?>

<div>
    <button id="newUpload" class="newUpload" type="button">Upload new data</button>
</div>

<script type="text/javascript" src="js/script.js"></script>

</body>
</html>