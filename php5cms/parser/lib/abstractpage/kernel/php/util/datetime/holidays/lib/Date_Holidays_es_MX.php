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
 
class Date_Holidays_es_MX extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_es_MX( $params = array() )
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
			"Aniv. de la Constitución" => array( 
				"date" => "05-02",
				"mark" => true,
				"type" => ""
			),
			"Día de la Bandera" => array( 
				"date" => "24-02",
				"mark" => true,
				"type" => ""
			),
			"Nat. de Benito Juarez" => array( 
				"date" => "21-03",
				"mark" => true,
				"type" => ""
			),
			"Día del Trabajo" => array( 
				"date" => "01-05",
				"mark" => true,
				"type" => ""
			),
			"Batalla de Puebla" => array( 
				"date" => "05-05",
				"mark" => true,
				"type" => ""
			),
			"Día de la Independencia" => array( 
				"date" => "16-09",
				"mark" => true,
				"type" => ""
			),
			"Día de la Raza" => array( 
				"date" => "12-10",
				"mark" => true,
				"type" => ""
			),
			"Aniv. de la Revolución" => array( 
				"date" => "20-11",
				"mark" => true,
				"type" => ""
			),
			"Día de la Virgen de Guadalupe" => array( 
				"date" => "12-12",
				"mark" => true,
				"type" => ""
			),
			"Fin de Año" => array( 
				"date" => "31-12",
				"mark" => true,
				"type" => ""
			),
			"Viernes Santo" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_GOODFRIDAY ),
				"mark" => true,
				"type" => ""
			),
			"Domingo Santo" => array( 
				"date" => date( "d-m", $easter ),
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_es_MX

?>
