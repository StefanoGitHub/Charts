<?php
//upload.php

/**
 * This page allow the user to select the data files from a directory and upload them into the database
 *
 * TODO: implement directory/file selection
 * TODO: implement database selection
 *
 **/

include "functions.php";
define ('DEBUG', 'DEBUG');
if(isset($_REQUEST['action'])){$Action = (trim($_REQUEST['action']));}else{$Action = "";}


//HEADER
include "includes/header_inc.php";

//###########  BODY ################//
//check 'action' for type of process
    switch ($Action) {
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

    }//end switch

//###########  END BODY ################//

//FOOTER
include "includes/footer_inc.php";






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
            $sessionDate = str_replace("-", "", $_REQUEST['measureDate']); ; // 11-03-15 -> '110315'

            $Measure[$i] = new Measure($valuesArr, $netColor, $position, $measurementType, $sessionDate);
            $result = insertExecute(
                $Measure[$i]->valuesArr,
                $Measure[$i]->netColor,
                $Measure[$i]->position,
                $Measure[$i]->measurementType,
                $Measure[$i]->sessionDate
            );

            if ($result === true) {
                echo '<h3>' .$_REQUEST['fileList'.$r]. ' [' .$_REQUEST['measureDate']. '] uploaded successfully! 
                         <i class="fa fa-smile-o"></i>
                      </h3>';
            } else {
                //check if the data was already in the DB, i.e. if the error contains 'Duplicate entry'
                $errorMsg = (strpos($result , 'Duplicate entry') !== FALSE) ? 'Data already in the database!' : '';
                echo '<h3 class="error">' . $_REQUEST['fileList'.$r] . ' NOT uploaded 
                         <i class="fa fa-frown-o"></i> <br>
                         '. $errorMsg .'
                      </h3>';
            }
        }
        //probably this is never used...
        else {
            echo '<h3>' . $_REQUEST['fileList'.$r] . ' NOT uploaded 
                     <i class="fa fa-frown-o"></i>
                  </h3>';
        }
    }
}//end uploadFiles()


/***************************************************************************************
 * Displays the list of file in the '_data' folder and allow the user to select the file(s) to upload.
 * Returns POST [
 *      "files" (array of strings),
 *      "netColor#" (string),
 *      "measurePosition#" (string),
 *      "measureType" (string),
 *      measureDate#" (string),
 *      "action#" (=upload)
 * ]
 * TODO: vaildate text input from form
 ***************************************************************************************/
function displayFileList($fileList) {
//Show the list of files and a way to select one or more to upload

    echo '
        <h1>Select file(s) to upload</h1>
        
        <div class="select_file_heder">
            <p class="select_file_heder">File inside the "<b>_data</b>" folder, located in the root of the program, will be automatically
            displayed in the panel; if the proper naming convention is respected, the form will be automatically pre-filled.
            <b>Please verify the auto-filled data before upload</b>.
            </p>
            
            <img id="nomenclature" src="images/file_nomenclature.png">
        </div>
    ';

    //if at least one file in the list, show results
    if (!empty($fileList)) {
        echo '<form id="upload" name="upload" action="' . THIS_PAGE . '" method="post">
            <table>
                <tr>
                    <th>Measurement date</th>
                </tr>
                <tr>
                    <td><input type="text" name="measureDate" class="date" placeholder="mm-dd-yy" required /></td>
                </tr>
            </table>
            <br>
            <table class="upload">
            <tr>
                <th>Select<br>
                    <div class="tooltip">
                        <input type="checkbox" id="selectAll">
                        <!-- <label for="selectAll" id="selectAll_label">Select</label> --> <br>
                        <span><strong>Select/Deselect all</strong><br></span>
                    </div>
                </th>
                <th>File Name</th>
                <th>Net Color</th>
                <th>Measurement position</th>
                <th>Measurement type <br>
                    <div class="tooltip">
                        <input type="checkbox" id="sameType">
                        <label for="sameType">All same type</label><br>
                            <span>
                                <strong>Same as the first file</strong><br/>
                                 Select the type first
                            </span>
                    </div>
                </th>
			</tr>';

        //create a line for each file in the folder
        for ($i=0; $i<count($fileList); $i++) {
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
                        <option value="OpFld">Open Field TFREC</option>
                        <option value="OpFldQ">Open Field Quincy</option>
                        <!-- <option value="Ctrl">Ctrl TFREC</option>
                        <option value="CtrlQ">CtrlQ</option> -->
                    </select>
                </td>

                <td class="measurePosition">
<!--                    <input type="radio" name="measurePosition'.$i.'" value="1" >Mark 1
                    <input type="radio" name="measurePosition'.$i.'" value="2" >Mark 2
                    <br> -->
                    <input type="radio" name="measurePosition'.$i.'" value="N" >North
                    <input type="radio" name="measurePosition'.$i.'" value="S" >South
                    <input type="radio" name="measurePosition'.$i.'" value="_" >N/A

                    <br class="moreSpace">

                    <input type="radio" name="measureNumber'.$i.'" value="1">1st
                    <input type="radio" name="measureNumber'.$i.'" value="2">2nd
                    <input type="radio" name="measureNumber'.$i.'" value="3">3dr
                    <input type="radio" name="measureNumber'.$i.'" value="" >N/A

                    <br class="moreSpace">

                    <input type="checkbox" name="scattered'.$i.'" value="scattered">SCAT
<!--                    <input type="checkbox" name="reference'.$i.'" value="reference">REF -->
                </td>
                <td class="measureType">
                    <input type="radio" name="measureType'.$i.'" class="irradiance" value="Irradiance" >.IRR
                    <input type="radio" name="measureType'.$i.'" class="transmittance" value="Transmittance">.TRM
                    <!-- <input type="radio" name="measureType'.$i.'" class="reference" value="Reference">
                    .SSM (Light Ref) -->
                </td>
                
                <!-- <td><input type="text" name="measureDate'.$i.'" placeholder="mmddyy" /></td> -->
            </tr>
            ';
        }

        echo '</table>
            <div class="submit_button">
                <input type="hidden" name="action" value="upload">
                <button id="upload_button" type="submit" form="upload" >
                    Upload <i class="fa fa-arrow-right"></i> <i class="fa fa-database"></i>
                </button>
            </div>
            <h3 class="error" id="error"></h3>

        </form>

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

/***************************************************************************************
 * Inserts the data from an array of value pairs in the db table `IRR_Data_year`
 * Returns TRUE if insertion successful, the error string otherwise
 ***************************************************************************************/
function insertExecute($dataArr, $netColor, $position, $measurementType, $sessionDate) {
    $year = date('Y');
    //define meanings for dataArr values based on position in the array
    $Wavelength = 0;
    $Amplitude = 1;
    //ID, Wavelength, Amplitude, NetColor, MeasurementType, SessionDate
    $sql = "INSERT INTO `IRR_Data_". $year."` VALUES ";

    for ( $i = 0; $i < count($dataArr) - 1; $i++ ) {
        //add all the couples of data
        $sql .= "(
            " . $dataArr[$i][$Wavelength] . ", 
            " . $dataArr[$i][$Amplitude] . ", 
            '$netColor', 
            '$position', 
            '$measurementType', 
            '$sessionDate' ), ";
    }
    //the last row will not end with comma
    if (count($dataArr) - 1 > 0) {
        $sql .= "(
        " . $dataArr[count($dataArr) - 1][0] . ",
        " . $dataArr[count($dataArr) - 1][1] . ",
        '$netColor',
        '$position',
        '$measurementType',
        '$sessionDate' );";
    } else {
        $sql .= "(
        " . $dataArr[0][0] . ",
        " . $dataArr[0][1] . ",
        '$netColor',
        '$position',
        '$measurementType',
        '$sessionDate' );";
    }

    $iConn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die(myerror(__FILE__, __LINE__, mysqli_connect_error()));
//    $result = mysqli_query($iConn, $sql) or die(myerror(__FILE__, __LINE__, mysqli_error($iConn)));
    $result = (mysqli_query($iConn, $sql) == TRUE) ? TRUE : mysqli_error($iConn);
    mysqli_close($iConn);

    return $result;
}

