<?php
//select_root.php

/**
 * Created by PhpStorm.
 * User: Stefano
 * Date: 9/2/15
 * Time: 2:51 PM
 */

include "functions.php";

$selectedDir = (isset($_REQUEST["selected"])) ? $_REQUEST["selected"] : "_data";


?>

<!DOCTYPE html>
<html>
<head>

    div>div>

    <meta name="viewport" content="width=device-width" />
    <title>Upload files</title>
    <style>
        body { background-color: darkseagreen }
        td, tr, th { text-align:center; border:grey solid 1px; }
        table { margin:0 auto; border:grey solid 1px; }
        h2, h3 { text-align:center; }
        div { text-align:center; margin: 2em; }
        table.transparent, table.transparent tr, table.transparent td { border:none; }
        .error { color:red; }
    </style>

</head>
<body>

<h2>Select file(s) to upload</h2>


<table class="transparent">
    <tr>
        <td>
            <p>Folder: </p>
        </td>
        <td>
            <select id="select" required autofocus>
                <!-- <option value="_data">_data</option> -->
                <?=getFolderListFormDir($selectedDir);?>
                hello
            </select>
        </td>
    </tr>
</table>


<?php

    /**
     * TODO: Change directoy ("_data") based on menu selection
     */


/*    if (getFileList($dir) != 0) {
        echo displayFileList(getFileList($dir));
    }*/

?>

<script type="text/javascript" src="script.js"></script>

</body>
</html>


<?php
/***************************************************************************************
 * Scans the folder and return the list of directories inside it and echo the options for
 * the select tag.
 * It discards all the paths with a dot
TODO:
 ***************************************************************************************/
function getFolderListFormDir($directory) {
//get list of file inside the directory

    $contentList = scandir($directory, SORT_ASCENDING);
    //if not empty delete all hidden files (for MAC)
    $dirList = array();
    for ($i=0; $i<count($contentList); $i++) {
    //if the name contains a dot discard it
        if (!strstr($contentList[$i], ".")) {
            $dirList[] = $contentList[$i];
        }
    }

    $selected = ($directory == "_data") ? 'selected' : "";
    echo '<option '.$selected.' value="_data">_data/</option>';

    //dumpDie($dirList);
    if (count($dirList)>0 ) {

        //$selection = (isset($_REQUEST["selected"])) ? $_REQUEST["selected"] : "_data";

        for ($i=0; $i<count($dirList); $i++) {
            $selected = ($directory == $dirList[$i]) ? 'selected' : "";
            echo '<option '.$selected.' value="'.$directory.'/'.$dirList[$i].'">'.$directory.' / '.$dirList[$i].'/</option>';
        }

    }
}//end getFolderListFormDir()


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
* Dysplays the list of file in the folder and allow the user to select the file(s) to upload.
* Returns POST [ "files" (array of strings), "netColor#" (string), "measurePosition#" (string), "measureType" (string), measureDate#" (string) and "action#" (=upload) ]
TODO: vaildate text input from form
***************************************************************************************/
function displayFileList($fileList) {
//Show the list of files and a way to select one or more to upload


if (!empty($fileList)) {
//if at least one file in the list, show results
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
        for ($i=0; $i<count($fileList); $i++) {
        //create a line for each file in the folder

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
        <input type="submit" name="action" value="">
    </div>
</form>
';
} else {
//no files in the list
echo '<div>
    <h3>Currently no files in the folder</h3>
    <!-- <a href="' . THIS_PAGE . '">Check folder</a> -->
</div>
';
}
}//end displayFileList()

