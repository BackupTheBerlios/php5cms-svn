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
 
class Date_Holidays_hu_HU extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_hu_HU( $params = array() )
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
            "Újév" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"Nemzeti ünnep 1848" => array( 
				"date" => "15-03",
				"mark" => true,
				"type" => ""
			),
			"Munka ünnepe" => array( 
				"date" => "01-05",
				"mark" => true,
				"type" => ""
			),
			"Szt. István" => array( 
				"date" => "20-08",
				"mark" => true,
				"type" => ""
			),
			"Nemzeti ünnep 1956" => array( 
				"date" => "23-10",
				"mark" => true,
				"type" => ""
			),
			"Mindenszentek" => array( 
				"date" => "01-11",
				"mark" => true,
				"type" => ""
			),
			"Karácsony" => array( 
				"date" => "25-12",
				"mark" => true,
				"type" => ""
			),
			"Karácsony" => array( 
				"date" => "26-12",
				"mark" => true,
				"type" => ""
			),
			"Húsvét" => array( 
				"date" => date( "d-m", $easter ),
				"mark" => true,
				"type" => ""
			),
			"Húsvét hétfö" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_EASTERMONDAY ),
				"mark" => true,
				"type" => ""
			),
			"Pünkösd" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_WHITSUNDAY ),
				"mark" => true,
				"type" => ""
			),
			"Pünkösd hétfö" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_WHITMONDAY ),
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_hu_HU

?>
