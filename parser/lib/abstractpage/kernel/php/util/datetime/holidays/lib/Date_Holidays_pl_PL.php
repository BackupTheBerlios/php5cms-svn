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
 
class Date_Holidays_pl_PL extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_pl_PL( $params = array() )
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
        $this->_holidays = array(
            "Nowy Rok" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"安i皻o Pracy" => array( 
				"date" => "01-05",
				"mark" => true,
				"type" => ""
			),
			"Konstytucja 3 Maja" => array( 
				"date" => "03-05",
				"mark" => true,
				"type" => ""
			),
			"Wniebowzi璚ie NMP" => array( 
				"date" => "15-08",
				"mark" => true,
				"type" => ""
			),
			"Wszystkich 安i皻ych" => array( 
				"date" => "01-11",
				"mark" => true,
				"type" => ""
			),
			"Narodowe 安i皻o Niepodleg這軼i" => array( 
				"date" => "11-11",
				"mark" => true,
				"type" => ""
			),
			"Bo瞠 Narodzenie" => array( 
				"date" => "25-12",
				"mark" => true,
				"type" => ""
			),
			"Bo瞠 Narodzenie" => array( 
				"date" => "26-12",
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_pl_PL

?>
