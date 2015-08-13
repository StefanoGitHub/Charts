<?php

/***************************************************************************************
 * A Grafico Object will host all the data related to the graph the user wants to chart:
 * $chartTitle: string
 * $measurementType: string (Irradiance, Transmittance, Reference)
 * $functions: array of Measures
 *
   TODO
 ***************************************************************************************/
class Grafico
{
    public $chartTitle = '';
    public $measurementType = '';
    public $functions = array(); //array of Measures

    public function __construct($chartTitle, $measurementType, $functions)
    {
        $this->chartTitle = $chartTitle;
        $this->measurementType = $measurementType;
        $this->functions = $functions;
    }//end constructor


/*    public function getValue($i)
    {
        return $this->valuesArr[$i];
    }*/

/*    public function getValue($i)
    {
        return $this->valuesArr[$i];
    }*/







}// end Question Class


