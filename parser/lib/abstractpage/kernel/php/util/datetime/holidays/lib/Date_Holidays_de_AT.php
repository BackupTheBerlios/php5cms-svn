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
 
class Date_Holidays_de_AT extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_de_AT( $params = array() )
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
            "Neujahr" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"Heilige Drei Könige" => array( 
				"date" => "06-01",
				"mark" => true,
				"type" => ""
			),
			"Tag der Arbeit" => array( 
				"date" => "01-05",
				"mark" => true,
				"type" => ""
			),
			"Maria Himmelfahrt" => array( 
				"date" => "15-08",
				"mark" => true,
				"type" => ""
			),
			"Nationalfeiertag" => array( 
				"date" => "26-10",
				"mark" => true,
				"type" => ""
			),
			"Allerheiligen" => array( 
				"date" => "01-11",
				"mark" => true,
				"type" => ""
			),
			"Maria Empfängnis" => array( 
				"date" => "08-12",
				"mark" => true,
				"type" => ""
			),
			"Erster Advent" => array(
				"date" => date( "d-m", $firstadvent ),
				"mark" => true,
				"type" => ""
			),
			"Zweiter Advent" => array(
				"date" => date( "d-m", $firstadvent + DATE_HOLIDAYS_1STADVENT_DIFF_ZWEITERADVENT ),
				"mark" => true,
				"type" => ""
			),			
			"Dritter Advent" => array(
				"date" => date( "d-m", $firstadvent + DATE_HOLIDAYS_1STADVENT_DIFF_DRITTERADVENT ),
				"mark" => true,
				"type" => ""
			),			
			"Vierter Advent" => array(
				"date" => date( "d-m", $firstadvent + DATE_HOLIDAYS_1STADVENT_DIFF_VIERTERADVENT ),
				"mark" => true,
				"type" => ""
			),
			"Weihnachten" => array( 
				"date" => "25-12",
				"mark" => true,
				"type" => ""
			),
			"Stephanstag" => array( 
				"date" => "26-12",
				"mark" => true,
				"type" => ""
			),
			"Ostersonntag" => array( 
				"date" => date( "d-m", $easter ),
				"mark" => true,
				"type" => ""
			),
			"Ostermontag" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_EASTERMONDAY ),
				"mark" => true,
				"type" => ""
			),
			"Christi Himmelfahrt" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_ASCENSIONDAY ),
				"mark" => true,
				"type" => ""
			),
			"Pfingstsonntag" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_WHITSUNDAY ),
				"mark" => true,
				"type" => ""
			),
			"Pfingstmontag" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_WHITMONDAY ),
				"mark" => true,
				"type" => ""
			),
			"Fronleichnam" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_FRONLEICHNAM ),
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_de_AT

?>
