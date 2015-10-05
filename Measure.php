<?php

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
class Measure {
    public $valuesArr = array(); //array of value pairs (Wavelength, Amplitude)
    public $netColor = '';
    public $position = '';
    public $measurementType = '';
    public $sessionDate = '';

    //constructors
    function __construct() {
        $argv = func_get_args();
        switch(func_num_args()) {
            case 0:
                self::__construct_0();
                break;
            case 5:
                self::__construct_5($argv[0], $argv[1], $argv[2], $argv[3], $argv[4]);
        }
    }

    public function __construct_0() {
        $this->valuesArr = array();
        $this->netColor = '';
        $this->position = '';
        $this->measurementType = '';
        $this->sessionDate = '';
    }

    public function __construct_5($valuesArr, $netColor, $position, $measurementType, $sessionDate) {
        $this->valuesArr = $valuesArr;
        $this->netColor = $netColor;
        $this->position = $position;
        $this->measurementType = $measurementType;
        $this->sessionDate = $sessionDate;
    }//end constructor


    //getter
    public function getValue($i) {
        return $this->valuesArr[$i];
    }

    //get measure identifier
    public function getMeasureID() {
        echo '<pre>' .
                $this->netColor .'<br>' .
                $this->position .'<br>' .
                $this->measurementType .'<br>' .
                $this->sessionDate .
            '</pre>';
    }

}// end Question Class


