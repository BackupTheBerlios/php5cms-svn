<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'util.datetime.holidays.lib.Date_Holidays' );


/**
 * @package util_datetime_holidays_lib
 */
 
class Date_Holidays_es_ES extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_es_ES( $params = array() )
    {
        $this->Date_Holidays( $params );
		
		$this->_populate();
    }
    

	// private methods
	
    /** 
     * @access private
     */
    function _populate()
    {
		$easter = $this->_getEaster();
		$firstadvent = $this->_getFirstAdvent();
		
        $this->_holidays = array(
            "Año Nuevo" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"Reyes" => array( 
				"date" => "06-01",
				"mark" => true,
				"type" => ""
			),
			"Viernes Santo" => array( 
				"date" => "29-03",
				"mark" => true,
				"type" => ""
			),
			"Lunes de Pascua" => array( 
				"date" => "01-04",
				"mark" => true,
				"type" => ""
			),
			"Fiesta del Trabajo" => array( 
				"date" => "01-05",
				"mark" => true,
				"type" => ""
			),
			"San Juan" => array( 
				"date" => "24-06",
				"mark" => true,
				"type" => ""
			),
			"Asuncion" => array( 
				"date" => "15-08",
				"mark" => true,
				"type" => ""
			),
			"Dia de la Hispanidad" => array( 
				"date" => "12-10",
				"mark" => true,
				"type" => ""
			),
			"Todos los Santos" => array( 
				"date" => "01-11",
				"mark" => true,
				"type" => ""
			),
			"Constitución" => array( 
				"date" => "06-12",
				"mark" => true,
				"type" => ""
			),
			"Inmaculada Concepción" => array( 
				"date" => "08-12",
				"mark" => true,
				"type" => ""
			),
			"Navidad" => array( 
				"date" => "25-12",
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_es_ES

?>
