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
 ***************************************************************************************/
class Measure {
    public $netColor = '';
    public $position = '';
    public $measurementType = '';
    public $sessionDate = '';
    public $valuesArr = array(); //array of value pairs (Wavelength, Amplitude)
    /**              [$Wavelength][$Amplitude]
     * valuesArr[0][]     225        7834
     * valuesArr[1][]     300        2645
     * valuesArr[2][]     305        4975
     *  ..
     * valuesArr[$i][]     ..         ..
     */

    const AMPLITUDE = 1;
    const WAVELENGTH = 0;

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
            }
    //end constructors


    
    //getters
    public function getAmplitude($i) {
        return $this->valuesArr[$i][self::AMPLITUDE];
    }
    public function getWavelength($i) {
        return $this->valuesArr[$i][self::WAVELENGTH];
    }

    //setters
    public function setAmplitude($i, $value) {
        return $this->valuesArr[$i][self::AMPLITUDE] = $value;
    }
    public function setWavelength($i, $value) {
        $this->valuesArr[$i][self::WAVELENGTH] = $value;
    }

    //for DEBUGGING
    //get measure identifier
    public function getMeasureID() {
        echo '<pre>' .
                $this->netColor .'<br>' .
                $this->position .'<br>' .
                $this->measurementType .'<br>' .
                $this->sessionDate .
            '</pre>';
    }

}// end Measure Class


