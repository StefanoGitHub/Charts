<?php
//graphs.php

include "functions.php";
define ('DEBUG', 'DEBUG');

if(isset($_REQUEST['action'])){$Action = (trim($_REQUEST['action']));}else{$Action = "";}

switch ($Action) 
{//check 'act' for type of process
	case "upload": //upload selected file(s)
        if (isset($_REQUEST['files']) && count($_REQUEST['files'])>1)
        {//display files if any has been selected
            uploadFiles();
            //echo '<div style="color:blue; text-align:center;"><a href="' . THIS_PAGE . '?action=delete">Clean table</a></div>';
            echo '<div class="back" text-align=center;"><a href="' . THIS_PAGE . '">Back</a></div>';
        }
        else { //else prompt the user to select one
            $fileList = getFileList("file_test");
            displayFileList($fileList);
            echo '<div style="color:red; text-align:center;">Please select at least one file</a></div>';
            echo '<div class="reload" text-align=center;"><a href="' . THIS_PAGE . '">Reload</a></div>';
            echo '<div class="chart" text-align=center;"><a href="charts.php?">Chart Measures</a></div>';
            //echo '<div style="color:blue; text-align:center;"><a href="' . THIS_PAGE . '?action=delete">Clean table</a></div>';
            }
        break;
    
    case "delete":
        //$success = deleteTable();
        if ($success) 
            { echo '<h3 style="text-align:center;">Table cleaned!</h3>'; }
        else {echo '<h3 style="text-align:center;">ERROR, table NOT cleaned :(';}
        //echo '<div text-align=center;"><a href="' . THIS_PAGE . '">Reload</a></div>';
        break;
        
    default: //Show existing projects
        $fileList = getFileList("file_test");
        displayFileList($fileList);	 
        //echo '<div style="color:blue; text-align:center;"><a href="' . THIS_PAGE . '?action=delete">Clean table</a></div>';
        echo '<div class="reload" text-align=center;"><a href="' . THIS_PAGE . '">Reload</a></div>';
        echo '<div class="chart" text-align=center;"><a href="charts.php">Chart Measures</a></div>';

}//end switch


/***************************************************************************************
* Uploads the Measure(s)/file(s) into the DB table and echos the result
TODO: perform check validity of $directory parameter
***************************************************************************************/
function uploadFiles() 
{//
    $valuesArr = array(); //array of arrays of values from each file (i.e. measure)
    for ($i=0; $i<count($_REQUEST['files']); $i++) 
    {//$dataArr[$i] will contain data from the file[$i]
        $valuesArr[$i] = getLinesFromFile('file_test/'.$_REQUEST['files'][$i]);
        //dumpDie($valuesArr);
        $netColor = $_REQUEST['netColor'.$i];
        $position = $_REQUEST['measurePosition'.$i];
        $measurementType = $_REQUEST['measureType'.$i];
        $sessionDate = $_REQUEST['measureDate'.$i];

        $Measure[$i] = new Measure($valuesArr[$i], $netColor, $position, $measurementType, $sessionDate);
        $insertOK = insertExecute(
                $Measure[$i]->valuesArr,
                $Measure[$i]->netColor,
                $Measure[$i]->position,
                $Measure[$i]->measurementType,
                $Measure[$i]->sessionDate
                );
        if ($insertOK) 
            { echo '<h3 style="text-align:center;">'.$_REQUEST['files'][$i].' uploaded successfully!</h3>'; }
        else {echo '<h3 style="text-align:center;">'.$_REQUEST['files'][$i].' NOT uploaded :(</h3>';}
    }    
}//end uploadFiles()


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
            if (substr($fileList[$i], 0, 1) != '.') { $fileListCleaned[] = $fileList[$i]; }
        }
        return $fileListCleaned;
    }
    else { return 0; }//if directory is empty return 0
}//end getDataFromFile()


/***************************************************************************************
* Dysplays the list of file in the folder and allow the user to select the file(s) to upload.
* Returns POST [ "files" (array of strings), "netColor#" (string), "measurePosition#" (string), "measureType" (string), measureDate#" (string) and "action#" (=upload) ]
TODO: vaildate text input from form
***************************************************************************************/
function displayFileList($fileList) 
{//Show the list of files and a way to select one or more to upload

	echo '<h2 style="text-align=center;">Select file(s) to upload</h2>';
    
    if (!empty($fileList))
	{//if at least one file in the list, show results
		echo '<form action="' . THIS_PAGE . '" method="post">
            <table style="margin:0 auto; border:grey solid 1px;">
            <tr>
				<th>Select</th>
                <th>File Name</th>
                <th>Net Color</th>
                <th>Measurement position</th>
                <th>Measurement type</th>
                <th>Measurement date</th>
			</tr>';
        for ($i=0; $i<count($fileList); $i++)
        {//create a line for each file in the folder
            
            echo '<tr>
                <td><input type="checkbox" name="files[]" value="'.$fileList[$i].'"></td>
                <td style="text-align:center">'.$fileList[$i].'</td>
                <td style="text-align:center">
                    <input type="radio" name="netColor'.$i.'" value="Blue" >Blue 
                    <input type="radio" name="netColor'.$i.'" value="Red">Red
                    <input type="radio" name="netColor'.$i.'" value="White">White 
                </td>
                <td style="text-align:center"><input type="text" name="measurePosition'.$i.'" placeholder="line#_position" /></td>
                <td style="text-align:center">
                    <input type="radio" name="measureType'.$i.'" value="Irradiance" >IRR 
                    <input type="radio" name="measureType'.$i.'" value="Transmittance">TRM
                    <input type="radio" name="measureType'.$i.'" value="Reference">SSM(Ref.) 
                </td>
                
                <td style="text-align:center"><input type="text" name="measureDate'.$i.'" placeholder="mmddyy" /></td>
            </tr>
            ';
        }		
        echo '</table>
            <div style="text-align:center;" colspan="3">
                <input type="submit" name="action" value="upload">
            </div>
        </form>        
    ';
	}
    else
    {//no files in the list
        echo '<div style="text-align:center;">
            <h3>Currently no files in the folder</h3>
            <a href="' . THIS_PAGE . '">Check folder</a></div>
        ';
	}
}//end displayFileList()



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



