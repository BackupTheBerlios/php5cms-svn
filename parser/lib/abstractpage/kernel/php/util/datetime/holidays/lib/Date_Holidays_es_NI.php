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
 
class Date_Holidays_es_NI extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_es_NI( $params = array() )
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
            "A�o Nuevo" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"D�a de Sn Valent�n" => array( 
				"date" => "14-02",
				"mark" => true,
				"type" => ""
			),
			"D�a del Trabajo" => array( 
				"date" => "01-05",
				"mark" => true,
				"type" => ""
			),
			"D�a de la Madre" => array( 
				"date" => "30-05",
				"mark" => true,
				"type" => ""
			),
			"D�a del Padre" => array( 
				"date" => "13-06",
				"mark" => true,
				"type" => ""
			),
			"Aniv. de la Revoluci�n" => array( 
				"date" => "19-07",
				"mark" => true,
				"type" => ""
			),
			"Bajada de Sto Domingo" => array( 
				"date" => "01-08",
				"mark" => true,
				"type" => ""
			),
			"Subida de Sto Domingo" => array( 
				"date" => "10-08",
				"mark" => true,
				"type" => ""
			),
			"Aniv. Batalla de Sn Jacinto" => array( 
				"date" => "14-09",
				"mark" => true,
				"type" => ""
			),
			"D�a de la Indenpencia" => array( 
				"date" => "15-09",
				"mark" => true,
				"type" => ""
			),
			"D�a del Descubrimiento de America" => array( 
				"date" => "12-10",
				"mark" => true,
				"type" => ""
			),
			"D�a de la Virgen Mar�a" => array( 
				"date" => "08-12",
				"mark" => true,
				"type" => ""
			),
			"Navidad" => array( 
				"date" => "25-12",
				"mark" => true,
				"type" => ""
			),
			"Fin de A�o" => array( 
				"date" => "31-12",
				"mark" => true,
				"type" => ""
			),
			"Viernes Santo" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_GOODFRIDAY ),
				"mark" => true,
				"type" => ""
			),
			"Jueves Santo" => array( 
				"date" => "GOODTHURSDAY",
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_es_NI

?>
