<?php
//upload.php

/**
 * This page allow the user to select the data files from a directory and upload them into the database
 *
 * TODO:
 * - Alert before cleaning the table
 * - Implement drop down menu for selecting the directory and hide any subfolder in the path
 *
 **/


include "functions.php";
define ('DEBUG', 'DEBUG');

if(isset($_REQUEST['action'])){$Action = (trim($_REQUEST['action']));}else{$Action = "";}

//dumpDie($_REQUEST);

?>


<!DOCTYPE html>
<html>
    <head>

        <meta name="viewport" content="width=device-width" />
        <title>Upload</title>
        <style>
            td, tr, th { text-align:center; border:grey solid 1px; }
            table { margin:0 auto; border:grey solid 1px; }
            h2, h3 { text-align:center; }
            div { text-align:center; margin: 2em; }
            .error { color:red; }
        </style>

    </head>
    <body>

<?php
switch ($Action) 
{//check 'act' for type of process
	case "upload": //upload selected file(s)
        //dumpDie($_REQUEST);
        if (isset($_REQUEST['files']) && count($_REQUEST['files'])>0)
        {//display files if any has been selected
            uploadFiles();
            echo '<div class="back"><a href="' . THIS_PAGE . '">Back</a></div>';
            echo '<div class="chart"><a href="charts_OLD.php">Chart Measures</a></div>';

            echo '<div><a href="' . THIS_PAGE . '?action=delete">Clean table</a></div>';
        }
        else { //else prompt the user to select one
            $fileList = getFileList("file_test");
            displayFileList($fileList);
            //dumpDie($_REQUEST);
            echo '<div class="error">Please select at least one file</a></div>';
            echo '<div class="reload"><a href="' . THIS_PAGE . '">Reload page</a></div>';
            echo '<div class="chart"><a href="charts_OLD.php?">Chart Measures</a></div>';

            echo '<div><a href="' . THIS_PAGE . '?action=delete">Clean table</a></div>';
            }
        break;

    case "delete":
        echo '<div>
                <h3>Do you really want to clean the table??</h3>
                <a href="' . THIS_PAGE . '?action=deleteOK">Clean table</a>
                <br>
                <a href="' . THIS_PAGE . '">Back</a>
              </div>';
        break;

    case "deleteOK":
        $success = deleteTable();
        if ($success) 
            { echo '<h3>Table cleaned!</h3>'; }
        else {echo '<h3 class="error">ERROR, table NOT cleaned :(';}

        echo '<div><a href="' . THIS_PAGE . '">Reload page</a></div>';
        break;
        
    default: //Show existing projects
        $fileList = getFileList("file_test");
        displayFileList($fileList);
        echo '<div class="reload"><a href="' . THIS_PAGE . '">Reload page</a></div>';
        echo '<div class="chart"><a href="charts_OLD.php">Chart Measures</a></div>';

        echo '<div><a href="' . THIS_PAGE . '?action=delete">Clean table</a></div>';


}//end switch

?>


    </body>
</html>



<?php

/***************************************************************************************
* Uploads the Measure(s)/file(s) into the DB table and echos the result
TODO: perform check validity of $directory parameter
***************************************************************************************/
function uploadFiles() 
{//
    //dumpDie($_REQUEST);
    //$valuesArr = array(); //array of arrays of values from each file (i.e. measure)
    for ($i=0; $i<count($_REQUEST['files']); $i++)
    {//$valuesArr[$i] will contain data from the file[$i]
        //$r indicates the row of the table
        $r = $_REQUEST['files'][$i];
        if (
        isset($_REQUEST['fileList'.$r])  &&
        isset($_REQUEST['netColor'.$r]) &&
        isset($_REQUEST['measurePosition'.$r]) &&
        isset($_REQUEST['measureNumber'.$r]) &&
        isset($_REQUEST['measureType'.$r])
        //isset($_REQUEST['measureDate'])
        ) {
            $scattered = (isset($_REQUEST['scattered'.$r]) && $_REQUEST['scattered'.$r] == 'scattered') ? '_SCAT' : '';

            $valuesArr = getLinesFromFile('file_test/'.$_REQUEST['fileList'.$r]);
            $netColor = $_REQUEST['netColor'.$r];
            $position = $_REQUEST['measurePosition'.$r]."_".$_REQUEST['measureNumber'.$r].$scattered;
            $measurementType = $_REQUEST['measureType' . $r];
            $sessionDate = $_REQUEST['measureDate'];
            //dumpDie($position);

            $Measure[$i] = new Measure($valuesArr, $netColor, $position, $measurementType, $sessionDate);
            //dumpDie($Measure[$i]);
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
function displayFileList($fileList) 
{//Show the list of files and a way to select one or more to upload

	echo '<h2>Select file(s) to upload</h2>';
    
    if (!empty($fileList))
	{//if at least one file in the list, show results
		echo '<form action="' . THIS_PAGE . '" method="post">
            <table>
                <tr>
                    <th>Measurement date</th>
                </tr>
                <tr>
                    <td><input type="text" name="measureDate" placeholder="mmddyy" required /></td>
                </tr>
            </table>
            <br>

            <table>
            <tr>
				<th>Select</th>
                <th>File Name</th>
                <th>Net Color</th>
                <th>Measurement position</th>
                <th>Measurement type</th>
			</tr>';
        for ($i=0; $i<count($fileList); $i++)
        {//create a line for each file in the folder
            
            echo '<tr>
                <td><input type="checkbox" name="files[]" value="'.$i.'"></td>
                <td>'.$fileList[$i].'
                    <input id="fileList" type="hidden" name="fileList'.$i.'" value="'.$fileList[$i].'">
                </td>
                <td>
                    <input type="radio" name="netColor'.$i.'" value="Blue" >Blue 
                    <input type="radio" name="netColor'.$i.'" value="Red">Red
                    <input type="radio" name="netColor'.$i.'" value="White">White
                    <input type="radio" name="netColor'.$i.'" value="Ctrl">Ctrl
                </td>

                <!-- <td><input type="text" name="measurePosition'.$i.'" placeholder="line#_position" /></td> -->

                <td>
                    <input type="radio" name="measurePosition'.$i.'" value="1" >Mark 1
                    <input type="radio" name="measurePosition'.$i.'" value="2" >Mark 2 <br>
                    <input type="radio" name="measureNumber'.$i.'" value="1">1st
                    <input type="radio" name="measureNumber'.$i.'" value="2">2nd
                    <input type="radio" name="measureNumber'.$i.'" value="3">3dr <br>
                    <input type="checkbox" name="scattered'.$i.'" value="scattered">SCAT</td>
                <td>
                    <input type="radio" name="measureType'.$i.'" value="Irradiance" >.IRR
                    <input type="radio" name="measureType'.$i.'" value="Transmittance">.TRM
                    <input type="radio" name="measureType'.$i.'" value="Light_Ref">Light Ref (.SSM)
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
    ';
	}
    else
    {//no files in the list
        echo '<div>
                <h3>Currently no files in the folder</h3>
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
function getFileList($directory)
{//get list of file inside the directory
    $fileList = scandir($directory, SORT_ASCENDING);
    if (count($fileList)>0)
    {//if not empty delete all hidden files (for MAC)
        $fileListCleaned = array();
        for ($i=0; $i<count($fileList); $i++)
        {//if the file name starts with a dot discard it
            if (substr($fileList[$i], 0, 1) != '.' && is_file($directory.'/'.$fileList[$i])) {
                $fileListCleaned[] = $fileList[$i];
            }
        }
        return $fileListCleaned;
    }
    else { return 0; }//if directory is empty return 0
}//end getDataFromFile()



/***************************************************************************************
* DEBUG ONLY!!!
* Deletes all rows in the table
* Returns TRUE if successful, FALSE otherwise
TODO: 
***************************************************************************************/
function deleteTable()
{
	//ID, Wavelength, Amplitude, NetColor, MeasurementName, SessionDate	
    $sql = "DELETE FROM `Graphs`.`t_IRR_Data` WHERE 1;";

    //connect to the DB and execute the SQL statement
    $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

/*    $iConn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die(myerror(__FILE__,__LINE__,mysqli_connect_error()));
    $result = mysqli_query($iConn,$sql) or die(myerror(__FILE__,__LINE__,mysqli_error($iConn)));
    mysqli_close($iConn);*/

    //clear result
    @mysqli_free_result($result);

    return $success = ($result = 1) ? TRUE : FALSE;
    //header("Location:".THIS_PAGE);
}



