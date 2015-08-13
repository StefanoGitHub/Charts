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
class Measure
{
    public $valuesArr = array(); //array of value pairs (Wavelength, Amplitude)
    public $netColor = '';
    public $position = '';
    public $measurementType = '';
    public $sessionDate = '';

    public function __construct()
    {
        $this->valuesArr = array();
        $this->netColor = '';
        $this->position = '';
        $this->measurementType = '';
        $this->sessionDate = '';
    }



/*    public function __construct($valuesArr, $netColor, $position, $measurementType, $sessionDate)
    {
        $this->valuesArr = $valuesArr;
        $this->netColor = $netColor;
        $this->position = $position;
        $this->measurementType = $measurementType;
        $this->sessionDate = $sessionDate;
    }//end constructor*/


    public function getValue($i)
    {
        return $this->valuesArr[$i];
    }

}// end Question Class


