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
 
class Date_Holidays_en_US extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_en_US( $params = array() )
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
            "New Year" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"Valentine's Day" => array( 
				"date" => "14-02",
				"mark" => true,
				"type" => ""
			),
			"St. Patrick's Day" => array( 
				"date" => "17-03",
				"mark" => true,
				"type" => ""
			),
			"Cinco De Mayo" => array( 
				"date" => "05-05",
				"mark" => true,
				"type" => ""
			),
			"Independence Day" => array( 
				"date" => "04-07",
				"mark" => true,
				"type" => ""
			),
			"Nevada Admissions Day" => array( 
				"date" => "31-10",
				"mark" => true,
				"type" => ""
			),
			"Veterans Day" => array( 
				"date" => "11-11",
				"mark" => true,
				"type" => ""
			),
			"Halloween" => array( 
				"date" => "31-10",
				"mark" => true,
				"type" => ""
			),
			"Christmas" => array( 
				"date" => "25-12",
				"mark" => true,
				"type" => ""
			),
			"Easter Sunday" => array( 
				"date" => date( "d-m", $easter ),
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_en_US

?>
