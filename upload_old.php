<?php
//upload.php

/**
 * This page allow the user to select the data files from a directory and upload them into the database
 *
 * TODO:
 * - Implement directory/file selection
 * - implement database selection
 *
 **/


include "functions.php";
define ('DEBUG', 'DEBUG');

if(isset($_REQUEST['action'])){$Action = (trim($_REQUEST['action']));}else{$Action = "";}

?>


    <!DOCTYPE html>
    <html>
    <head>

        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <!-- jQuery -->
        <script src="js/jquery-2.1.4.js"></script>

        <title>Upload files</title>

    </head>
    <body class="upload_page">

    <?php
    switch ($Action)
    {//check 'action' for type of process
        case "upload": //upload selected file(s)
            if (isset($_REQUEST['files']) && count($_REQUEST['files'])>0) {
                //display files if any has been selected
                uploadFiles();
                echo '<div class="back"><a href="' . THIS_PAGE . '">Back</a></div>';
            } else {
                //else prompt the user to select one
                $fileList = getFileList("_data");
                displayFileList($fileList);
                echo '<div class="error" id="error">Please select at least one file</a></div>';
                echo '<div class="reload"><a href="' . THIS_PAGE . '">Reload page</a></div>';

            }
            break;
        default:
            //Show existing projects
            $fileList = getFileList("_data");
            displayFileList($fileList);
            echo '<div class="reload"><a href="' . THIS_PAGE . '">Reload page</a></div>';
    }//end switch

    ?>

    <script type="text/javascript" src="js/script.js"></script>

    </body>
    </html>



<?php

/***************************************************************************************
 * Uploads the Measure(s)/file(s) into the DB table and echos the result
TODO: perform check validity of $directory parameter
 ***************************************************************************************/
function uploadFiles() {

    for ($i=0; $i<count($_REQUEST['files']); $i++) {
        //$valuesArr[$i] will contain data from the file[$i]
        //$r indicates the row of the table
        $r = $_REQUEST['files'][$i];
        if (
            isset($_REQUEST['fileList'.$r]) &&
            isset($_REQUEST['netColor'.$r]) &&
            isset($_REQUEST['measurePosition'.$r]) &&
            isset($_REQUEST['measureNumber'.$r]) &&
            isset($_REQUEST['measureType'.$r])
        ) {
            $scattered = (isset($_REQUEST['scattered'.$r]) && $_REQUEST['scattered'.$r] == 'scattered') ? '_SCAT' : '';
            $reference = (isset($_REQUEST['reference'.$r]) && $_REQUEST['reference'.$r] == 'reference') ? '_REF' : '';
            $number = (isset($_REQUEST['measureNumber'.$r]) && $_REQUEST['measureNumber'.$r] != '') ? "_".$_REQUEST['measureNumber'.$r] : '';

            $valuesArr = getLinesFromFile('_data/'.$_REQUEST['fileList'.$r]);
            $netColor = $_REQUEST['netColor'.$r];


            $position = $_REQUEST['measurePosition'.$r].$number.$scattered.$reference;


            $measurementType = $_REQUEST['measureType' . $r];
            $sessionDate = $_REQUEST['measureDate'];

            $Measure[$i] = new Measure($valuesArr, $netColor, $position, $measurementType, $sessionDate);
            $insertOK = insertExecute(
                $Measure[$i]->valuesArr,
                $Measure[$i]->netColor,
                $Measure[$i]->position,
                $Measure[$i]->measurementType,
                $Measure[$i]->sessionDate
            );
            if ($insertOK) {
                echo '<h3>' . $_REQUEST['fileList'.$r] . ' uploaded successfully!</h3>';
            } else {
                echo '<h3>' . $_REQUEST['fileList'.$r] . ' NOT uploaded :(</h3>';
            }
        } else {
            echo '<h3>' . $_REQUEST['fileList'.$r] . ' NOT uploaded :(</h3>';
        }
    }
}//end uploadFiles()


/***************************************************************************************
 * Dysplays the list of file in the folder and allow the user to select the file(s) to upload.
 * Returns POST [ "files" (array of strings), "netColor#" (string), "measurePosition#" (string), "measureType" (string), measureDate#" (string) and "action#" (=upload) ]
TODO: vaildate text input from form
 ***************************************************************************************/
function displayFileList($fileList) {
//Show the list of files and a way to select one or more to upload

    echo '<h2>Select file(s) from <em>_data</em> to upload</h2>';

    if (!empty($fileList)) {
        //if at least one file in the list, show results
        echo '<form id="upload" action="' . THIS_PAGE . '" method="post">
            <table>
                <tr>
                    <th>Measurement date</th>
                </tr>
                <tr>
                    <td><input type="text" name="measureDate" placeholder="mmddyy" required /></td>
                </tr>
            </table>
            <br>
            <table class="upload">
            <tr>
				<th>
				<div class="tooltip">
                    <label for="selectAll" class="selectAll" id="selectAll_label">Select</label> <br>
                    <input type="checkbox" id="selectAll" >
                        <span>Select all</span>
                </div>
				</th>
                <th>File Name</th>
                <th>Net Color</th>
                <th>Measurement position</th>
                <th>Measurement type <br>
                    <input type="checkbox" id="sameType" >
				    <label for="sameType" class="sameType">All same type</label>
                </th>
			</tr>';
        for ($i=0; $i<count($fileList); $i++) {
            //create a line for each file in the folder
            echo '<tr>
                <td><input type="checkbox" id="'.($i+2).'" class="select_checkbox" name="files[]" value="'.$i.'"></td>
                <td class="file">'.$fileList[$i].'
                    <input id="fileList" type="hidden" name="fileList'.$i.'" value="'.$fileList[$i].'">
                </td>
                <td class="select">
                    <select form="upload" name="netColor'.$i.'" class="datalist" >
                        <option value="" selected></option>
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
                    </select>
                </td>

                <td class="measurePosition">
                    <input type="radio" name="measurePosition'.$i.'" value="1" >Mark 1
                    <input type="radio" name="measurePosition'.$i.'" value="2" >Mark 2
                    <br>

                    <input type="radio" name="measurePosition'.$i.'" value="N" >North
                    <input type="radio" name="measurePosition'.$i.'" value="S" >South
                    <input type="radio" name="measurePosition'.$i.'" value="" >N/A

                    <br>

                    <input type="radio" name="measureNumber'.$i.'" value="1">1st
                    <input type="radio" name="measureNumber'.$i.'" value="2">2nd
                    <input type="radio" name="measureNumber'.$i.'" value="3">3dr
                    <input type="radio" name="measureNumber'.$i.'" value="" >N/A
                    <br>
                    <input type="checkbox" name="scattered'.$i.'" value="scattered">SCAT
<!--                    <input type="checkbox" name="reference'.$i.'" value="reference">REF -->
                </td>
                <td class="measureType">
                    <input type="radio" name="measureType'.$i.'" class="irradiance" value="Irradiance" >.IRR
                    <input type="radio" name="measureType'.$i.'" class="transmittance" value="Transmittance">.TRM
                    <input type="radio" name="measureType'.$i.'" class="reference" value="Reference">.SSM (Light Ref)
                </td>

                <!-- <td><input type="text" name="measureDate'.$i.'" placeholder="mmddyy" /></td> -->
            </tr>
            ';
        }
        echo '</table>
            <div>
                <input type="submit" name="action" value="upload">
            </div>
        </form>
        <div class="chart"><a href="select_data.php" target="_blank">Chart Measures</a></div>

    ';
    } else {
        //no files in the list
        echo '<div>
                <h3>Currently no files in the <em>_data</em> folder</h3>
                <!-- <a href="' . THIS_PAGE . '">Check folder</a> -->
            </div>
        ';
    }
}//end displayFileList()


/***************************************************************************************
 * Scans the folder and return the list of file inside it as array; if empty return 0.
 * It discards also all the files named with a starting dot
TODO: check validity of $directory parameter
 ***************************************************************************************/
function getFileList($directory) {
//get list of file inside the directory
    $fileList = scandir($directory, SORT_ASCENDING);
    if (count($fileList)>0) {
        //if not empty delete all hidden files (for MAC)
        $fileListCleaned = array();
        for ($i=0; $i<count($fileList); $i++) {
            //if the file name starts with a dot discard it
            if (substr($fileList[$i], 0, 1) != '.' && is_file($directory.'/'.$fileList[$i])) {
                $fileListCleaned[] = $fileList[$i];
            }
        }
        return $fileListCleaned;
    }
    else {
        //if directory is empty return 0
        return 0;
    }
}//end getFileList()


