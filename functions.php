<?php
//functions.php

include "credentials.php";
define ('SORT_ASCENDING', 0);//define constant for scandir() function
define('THIS_PAGE', basename($_SERVER['PHP_SELF'])); //Current page name, stripped of folder info - (saves resources)


/***************************************************************************************
 * troubleshooting wrapper function for var_dump
 *
 * saves annoyance of needing to type pre-tags
 *
 * Optional parameter $adminOnly if set to TRUE will require 
 * currently logged in admin to view crash - will not interfere with 
 * public's view of the page
 *
 * WARNING: Use for troubleshooting only: will crash page at point of call!
 *
 * <code>
 * dumpDie($myObject);
 * </code>
 *
 * @param object $myObj any object or data we wish to view internally 
 * @param boolean $adminOnly if TRUE will only show crash to logged in admins (optional) 
 * @return none
 ***************************************************************************************/
function dumpDie($myObj,$adminOnly = FALSE)
{
	if(!$adminOnly || startSession() && isset($_SESSION['AdminID'])) 
	{#if optional TRUE passed to $adminOnly check for logged in admin
		echo '<pre>';
		var_dump($myObj);
		echo '</pre>';
		die;
	}
}
function dump($name='', $myObj)
{
    echo $name.':<br><pre>';
        var_dump($myObj);
        echo '</pre>';
}
function echoit($name='', $myVar)
{
    echo $name.': '.$myVar.'<br>';
}

/***************************************************************************************
* Gets the file and extracts the pairs of data
* It returns an array of arrays, each array holding the pair of measured values
TODO: 
***************************************************************************************/
function getLinesFromFile($filePath)
{
    
    $linesArr = file($filePath, FILE_SKIP_EMPTY_LINES);
    for ($i=0; $i<count($linesArr); $i++) 
    {
        $linesArr[$i] = explode(" ", trim(str_replace('  ',' ',$linesArr[$i]), " \t\n\r\0\x0B"));
        if(count($linesArr[$i]) == 2)
        {//check if the line contains a "value pair"
            if (is_numeric($linesArr[$i][0]) && is_numeric($linesArr[$i][1])) 
            {//if the two values are not numeric discard the line
                $dataArr[] = $linesArr[$i]; 
            }
        }
    }
    return $dataArr;
}//end getLinesFromFile()

/***************************************************************************************
* Inserts the data from an array of value pairs in the table
* Returns TRUE if insertion successful, FALSE otherwise
TODO: 
***************************************************************************************/
function insertExecute($dataArr, $netColor, $position, $measurementType, $sessionDate)
{
    //define meanings for dataArr values based on position in the array
    $Wavelength = 0;
    $Amplitude = 1; 
	//ID, Wavelength, Amplitude, NetColor, MeasurementType, SessionDate	
    $sql = "INSERT INTO `t_IRR_Data` VALUES "; //ID
    for ($i=0; $i<count($dataArr)-1; $i++) 
    {//add all the couples of data
        $sql .= "(
            ".$dataArr[$i][$Wavelength].", 
            ".$dataArr[$i][$Amplitude].", 
            '$netColor', 
            '$position', 
            '$measurementType', 
            '$sessionDate' ), ";
    }
    //the last row will not end with comma
    $sql .= "( 
        ".$dataArr[count($dataArr)-1][0].", 
        ".$dataArr[count($dataArr)-1][1].", 
        '$netColor', 
        '$position', 
        '$measurementType', 
        '$sessionDate' );";

    $iConn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die(myerror(__FILE__,__LINE__,mysqli_connect_error()));
    $result = mysqli_query($iConn,$sql) or die(myerror(__FILE__,__LINE__,mysqli_error($iConn)));

    # connection comes first in mysqli (improved) function
    //$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
   
    //@mysqli_free_result($result);
    mysqli_close($iConn);

    return $success = ($result = 1) ? TRUE : FALSE;
    //header("Location:".THIS_PAGE);
}

/**
 * Provides active connection to MySQL DB.
 *
 * A set of default credentials should be placed in the conn() function, and optional
 * levels of access can be chosen on a case by case basis on specific pages.
 *
 * One of 5 strings indicating a MySQL user can be passed to the function
 *
 * 1 admin
 * 2 delete
 * 3 insert
 * 4 update
 * 5 select
 *
 * MySQL accounts must be setup for each level, with 'select' account only able
 * to access db via 'select' command, and update able to 'select' and 'update' etc.
 * Each credential set must exist in MySQL before it can be used.
 *
 * If no data is entered into conn() function when it is called, a mysqli connection with the
 * default access is returned:
 *
 *<code>
 * $myConn = conn();
 *</code>
 *
 * If you create multiple MySQL users and have a 'select only' user, you can create a 'select only' connection:
 *
 * <code>
 * $myConn = conn("select");
 * </code>
 *
 * You can also create a mysql classic (mysql) connection by declaring FALSE as a second optional argument:
 *
 * <code>
 * $iConn = conn("select",FALSE);
 * </code>
 *
 * There are times you may want to use a mysql classic connnection over mysqli for security or compatibility
 *
 * @param string $access represents level of access
 * @param boolean $improved If TRUE, uses mysqli improved connection
 * @return object Returns active connection to MySQL db.
 * @todo error logging, or emailing admin not implemented
 */

function conn($access="",$improved = TRUE)
{
    $myUserName = "";
    $myPassword = "";

    if($access != "")
    {#only check access if overwritten in function
        switch(strtolower($access))
        {# Optionally overwrite access level via function
            case "admin":
                $myUserName = ""; #your MySQL username
                $myPassword = ""; #your MySQL password
                break;
            case "delete":
                $myUserName = "";
                $myPassword = "";
                break;
            case "insert":
                $myUserName = "";
                $myPassword = "";
                break;
            case "update":
                $myUserName = "";
                $myPassword = "";
                break;
            case "select":
                $myUserName = "";
                $myPassword = "";
                break;
        }
    }

    if($myUserName == ""){$myUserName = DB_USER;}#fallback to constants
    if($myPassword == ""){$myPassword = DB_PASSWORD;}#fallback to constants
    if($improved)
    {//create mysqli improved connection
        $myConn = @mysqli_connect(DB_HOST, $myUserName, $myPassword, DB_NAME) or die(trigger_error(mysqli_connect_error(), E_USER_ERROR));
    }else{//create standard connection
        $myConn = @mysql_connect(DB_HOST,$myUserName,$myPassword) or die(trigger_error(mysql_error(), E_USER_ERROR));
        @mysql_select_db(DB_NAME, $myConn) or die(trigger_error(mysql_error(), E_USER_ERROR));
    }
    return $myConn;
}

/*************************************************************************************** 
 * Placing the DB connection inside a class allows us to create a shared 
 * connection to improve use of resources.
 *
 * Returns a mysqli connection:
 *
 * <code>
 * $iConn = IDB::conn();
 * </code>
 *
 * All calls to this class will use the same shared connection.
 ***************************************************************************************/ 
class IDB 
{ 
	private static $instance = null; #stores a reference to this class

	private function __construct() 
	{#establishes a mysqli connection - private constructor prevents direct instance creation 
		#hostname, username, password, database
		$this->dbHandle = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME) or die(trigger_error(mysqli_connect_error(), E_USER_ERROR)); 
	} 

	/** 
	* Creates a single instance of the database connection 
	* 
	* @return object singleton instance of the database connection
	* @access public 
	*/ 
	public static function conn() 
    { 
      if(self::$instance == null){self::$instance = new self;}#only create instance if does not exist
      return self::$instance->dbHandle;
    }
}//end IDB class

/***************************************************************************************
* PDO & SQL Injection: 
* PDO tutorial: http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers
* return PDO object
***************************************************************************************/
function pdo()
{
	try {
	   $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',DB_USER,DB_PASSWORD);
	} catch(PDOException $ex) {
	   trigger_error($ex->getMessage(), E_USER_ERROR);
	}
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//make errors catchable
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);//disable emulated prepared statements

	return $db;

}//end pdo()


/**
 * wrapper function for PHP session_start(), to prevent 'session already started' error messages.
 *
 * To view any session data, sessions must be explicitly started in PHP.
 * In order to use sessions in a variety of INC files, we'll check to see if a session
 * exists first, then start the session only when necessary.
 *
 *
 * @return void
 * @todo none
 */
function startSession()
{
    //if(!isset($_SESSION)){@session_start();}
    if(isset($_SESSION))
    {
        return true;
    }else{
        @session_start();
    }
    if(isset($_SESSION)){return true;}else{return false;}
} #End startSession()



function myerror($myFile, $myLine, $errorMsg)
{
    if(defined('DEBUG') && DEBUG)
    {
       echo "Error in file: <b>" . $myFile . "</b> on line: <b>" . $myLine . "</b><br />";
       echo "Error Message: <b>" . $errorMsg . "</b><br />";
       die();
    }else{
		echo "I'm sorry, we have encountered an error";
		die();
    }
}





/***************************************************************************************
 * Wrapper function for processing data pulled from db
 *
 * Forward slashes are added to MySQL data upon entry to prevent SQL errors.  
 * Using our dbOut() function allows us to encapsulate the most common functions for removing  
 * slashes with the PHP stripslashes() function, plus the trim() function to remove spaces.
 *
 * Later, we can add to this function sitewide, as new requirements or vulnerabilities develop.
 *
 * @param string $str data as pulled from MySQL
 * @return $str data cleaned of slashes, spaces around string, etc.
 * @see dbIn()
 * @todo none
 ***************************************************************************************/
function dbOut($str)
{
	if($str!=""){$str = stripslashes(trim($str));}//strip out slashes entered for SQL safety
	return $str;
} #End dbOut()

/***************************************************************************************
 * mysqli version of dbIn()
 * 
 * Filters data per MySQL standards before entering database. 
 *
 * Adds slashes and helps prevent SQL injection per MySQL standards.    
 * Function enclosed in 'wrapper' function to add further functionality when 
 * as vulnerabilities emerge.
 *
 * @param string $var data as entered by user
 * @param object $myConn active mysqli DB connection, passed by reference.
 * @return string returns data filtered by MySQL, adding slashes, etc.
 * @see dbIn() 
 * @todo none
 ***************************************************************************************/
function idbIn($var,&$iConn)
{
	if(isset($var) && $var != "")
	{
		return mysqli_real_escape_string($iConn,$var);
	}else{
		return "";
	}
	
}//end idbIn()

/***************************************************************************************
* A Measure Object will host all the data related a single measure:
* $valuesArr: array of vale pairs (Wavelength-Amplitude)
* $netColor: string (i.e. BLUE)
* $position: string (i.e. 1_Centro)
* $measurementType: string (i.e. Irradiance)
* $sessionDate: string (i.e. 08032015)
*
* Returns the result from the DB
TODO: 
***************************************************************************************/
class Measure
{
    public $valuesArr = array();
    public $netColor = '';
    public $position = '';
    public $measurementType = '';
    public $sessionDate = '';
    
    public function __construct($valuesArr, $netColor, $position, $measurementType, $sessionDate) 
    {
        $this->valuesArr = $valuesArr;
        $this->netColor = $netColor;
        $this->position = $position;
        $this->measurementType = $measurementType;
        $this->sessionDate = $sessionDate;
    }//end constructor
    
}// end Question Class


/***************************************************************************************
* Gets the elements from the Array paramater and generates an OR separated string 
* used in the SQL statement to select multiple columns
* Returns a string
TODO: 
***************************************************************************************/
function arrToSQLString($Arr) 
{
    $string = '';
    for ($i=0; $i<count($Arr)-1; $i++) 
    {//load all the values of the array separated by OR
        $string .= "'$Arr[$i]' OR ";
    }//the last value will not ternimate with OR
    $string .= "'".$Arr[count($Arr)-1]."'";   
    return $string;
}//end arrToString() 


/***************************************************************************************
* Gets the selected data from the table 
* and creates the DATA TABLE for the google chart
* Returns the DATA TABLE as a string
TODO: 
***************************************************************************************/
function generateDataTable($netColorArr, $positionArr, $measurementTypeArr, $sessionDateArr)
{
    /* examples
    $netColorArr = array("Blue", "Red", "White");
    $positionArr = array("1_Centro", "1_Est", "2_West");
    $measurementTypeArr = array("Transmittance", "Irradiance", "Transmittance");
    $sessionDateArr = array("080415", "080315", "080415");*/

    //the length of any array parameter will determine the number of JOINTs in the SQL statement
    $chartLineNumber = count($measurementTypeArr);

    //convert Array to OR separated string
    $netColor = arrToSQLString($netColorArr);
    $position = arrToSQLString($positionArr);
    $measurementType = arrToSQLString($measurementTypeArr);
    $sessionDate = arrToSQLString($sessionDateArr);

    //dump('$netColorArr', $netColorArr);

    
    //t_IRR_Dat table columns: ID, Wavelength, Amplitude, NetColor, MeasurementType, SessionDate	
    $sqlSELECT = "SELECT t0.Wavelength, t0.Amplitude AS $netColorArr[0]_$positionArr[0]";
    
    if ($chartLineNumber > 1) 
    {
        for ($i=1; $i<$chartLineNumber; $i++) 
        {
            $sqlSELECT .= ", t$i.Amplitude AS $netColorArr[$i]_$positionArr[$i]";
        }
    }
    
    $sqlJOINT = "
            FROM 
            (SELECT Wavelength, Amplitude 
                 FROM t_IRR_Data 
                 WHERE
                     Wavelength>299.5 AND 
                     Wavelength<1000.5 AND
                     NetColor = '".$netColorArr[0]."' AND  
                     Position = '".$positionArr[0]."' AND  
                     MeasurementType = '".$measurementTypeArr[0]."' AND  
                     SessionDate='".$sessionDateArr[0]."'
            ) AS t0 
        ";
    
    if ($chartLineNumber > 1) 
    {
        for ($i=1; $i<$chartLineNumber; $i++) 
        {       
            $sqlJOINT .= 
                "INNER JOIN 
                    (SELECT Wavelength, Amplitude
                     FROM t_IRR_Data 
                     WHERE
                        Wavelength>299.5 AND 
                        Wavelength<1000.5 AND
                        NetColor = '".$netColorArr[$i]."' AND  
                        Position = '".$positionArr[$i]."' AND  
                        MeasurementType = '".$measurementTypeArr[$i]."' AND  
                        SessionDate='".$sessionDateArr[$i]."'
                    ) AS t$i
                ON t0.Wavelength = t$i.Wavelength 
        ";
        }
    }

    
    $sql = $sqlSELECT.$sqlJOINT;
    
    //dump('statement', $sql);
        
    //connect to the DB and execute the SQL statement
    $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
       
    if(mysqli_num_rows($result) > 0) 
    {//if any records exists, generate table data  
        //first "row" of the table data indicates the name of the columns
        $columnNames = "'".$netColorArr[0]."_".$positionArr[0]."'";
        if ($chartLineNumber > 1) 
        {//if there are more than one lines to chart append the names to the first row of table data
            for ($i=1; $i<$chartLineNumber; $i++) 
            { $columnNames .= ", '".$netColorArr[$i]."_".$positionArr[$i]."'"; }
        }
        //first "row" of the table data indicates the name of the columns
        $dataTableString = "['Wavelength', $columnNames], ";
        //set number of rows to be processed
        $rows = mysqli_num_rows($result);
        //set the number of rows processed
        $r = 0;
        //append each row of the sql table to the dataTableString
        while ($row = mysqli_fetch_assoc($result))
        {
            //increment number of rows processed
            $r++;
            //if this is the last row (rows processed = rows to be processed), no comma will be appended
            $comma = ($r == $rows) ? '' : ', ';
            //dbOut() function is a 'wrapper' designed to strip slashes, etc. of data leaving db
            $values = dbOut($row["$netColorArr[0]_$positionArr[0]"]);
            if ($chartLineNumber > 1) 
            {
                for ($i=1; $i<$chartLineNumber; $i++) 
                {
                    $values .= ', '.dbOut($row["$netColorArr[$i]_$positionArr[$i]"]);
                }
            }
            //assamble the two components of the dataTableString: x-axis value and y-axis(axes) value(s)     
            $dataTableString .= "['".number_format(dbOut($row['Wavelength']), 1)."', ".$values."]".$comma;
            /* espected string result:
                ['Wavelenght', 'Amp1', 'Amp2', ...],
                ['2004',        1000,   400,   ...],
                ['2005',        1170,   460    ...],
                ['2006',        660,    1120   ...],
                ['2007',        1030,   540    ...]      
            */
        }//end processing while loop
    }//end if record exists    
    else 
    {//no files in the list
        echo '<div style="text-align:center;">
                <h3>No match in the database</h3>
                <a href="' . THIS_PAGE . '">Back</a>
            </div>
        ';
    }
    
    @mysqli_free_result($result);//clear result
    //return the DATA TABLE, if created
    if(isset($dataTableString)) {return $dataTableString;}
}//end getMeasureDataFromTable2()



/**
 * Creates a smart (sic) title from words present in the php file name (page)
 *
 * If no string is input, will take current PHP file name, strip of extension
 * and replace "-" and "_" with spaces
 *
 * Will also title case first letter of significant words in title
 *
 * A comma separated string named $skip can be used to add/delete more
 * words that are NOT title cased
 *
 * First word is always title case by default
 *
 * <code>
 * $config->titleTag = smartTitle();
 * </code>
 *
 * added version 2.07
 *
 * @param string $myTitle file name or etc to amend (optional)
 * @return string converted title cased version of file name/string
 * @todo none
 */
function smartTitle($myTitle = '')
{
    if($myTitle == ''){$myTitle = THIS_PAGE;}
    $myTitle = strtolower(substr($myTitle, 0, strripos($myTitle, '.'))); #remove extension, lower case
    $separators = array("_", "-");  #array of possible separators to remove
    $myTitle = str_replace($separators, " ", $myTitle); #replace separators with spaces
    $myTitle = explode(" ",$myTitle); #create an array from the title
    $skip = "this|is|of|a|an|the|but|or|not|yet|at|on|in|over|above|under|below|behind|next to| beside|by|among|between|by|till|since|during|for|throughout|to|and|my";
    $skip = explode("|",$skip); # words to skip in title case

    for($x=0;$x<count($myTitle);$x++)
    {#title case words not skipped
        if($x == 0 || !in_array($myTitle[$x], $skip)) {$myTitle[$x] = ucwords($myTitle[$x]);}
        //echo $word . '<br />';
    }
    return implode(" ",$myTitle); #return imploded (spaces re-added) version
}# End smartTitle()







