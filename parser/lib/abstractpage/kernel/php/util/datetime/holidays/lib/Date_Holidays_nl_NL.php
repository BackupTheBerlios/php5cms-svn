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
 
class Date_Holidays_nl_NL extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_nl_NL( $params = array() )
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
            "Nieuwjaardsdag" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"Driekoningen" => array( 
				"date" => "06-01",
				"mark" => false,
				"type" => ""
			),
			"Valentijnsdag" => array( 
				"date" => "14-02",
				"mark" => false,
				"type" => ""
			),
			"Carnaval Ma" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_CARNAVALMON ),
				"mark" => false,
				"type" => ""
			),
			"Carnaval Di" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_CARNAVALTUE ),
				"mark" => false,
				"type" => ""
			),
			"Aswoensdag" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_CARNAVALWED ),
				"mark" => false,
				"type" => ""
			),
			"Goede Vrijdag" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_GOODFRIDAY ),
				"mark" => false,
				"type" => ""
			),
			"Eerste paasdag" => array( 
				"date" => date( "d-m", $easter ),
				"mark" => true,
				"type" => ""
			),
			"Tweede paasdag" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_EASTERMONDAY ),
				"mark" => true,
				"type" => ""
			),
			"1 april" => array( 
				"date" => "01-04",
				"mark" => false,
				"type" => ""
			),
			"Koninginnedag" => array( 
				"date" => "30-04",
				"mark" => true,
				"type" => ""
			),
			"Dag van de Arbeid" => array( 
				"date" => "01-05",
				"mark" => true,
				"type" => ""
			),
			"Dodenherdenking" => array( 
				"date" => "04-05",
				"mark" => false,
				"type" => ""
			),
			"Nationale Bevrijdingsdag" => array( 
				"date" => "05-05",
				"mark" => true,
				"type" => ""
			),
			"Moederdag" => array( 
				"date" => "MOTHERDAY",
				"mark" => false,
				"type" => ""
			),
			"Vaderdag" => array( 
				"date" => "FATHERDAY",
				"mark" => false,
				"type" => ""
			),
			"Hemelvaartsdag" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_ASCENSIONDAY ),
				"mark" => true,
				"type" => ""
			),
			"Eerste Pinksterdag" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_WHITSUNDAY ),
				"mark" => true,
				"type" => ""
			),
			"Tweede Pinksterdag" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_WHITMONDAY ),
				"mark" => true,
				"type" => ""
			),
			"Prinsjesdag" => array( 
				"date" => "PRINSDAY",
				"mark" => false,
				"type" => ""
			),
			"Sinterklaas" => array( 
				"date" => "05-12",
				"mark" => false,
				"type" => ""
			),
			"Eerste Kerstdag" => array( 
				"date" => "25-12",
				"mark" => true,
				"type" => ""
			),
			"Tweede Kerstdag" => array( 
				"date" => "26-12",
				"mark" => true,
				"type" => ""
			),
			"Oudejaar" => array( 
				"date" => "31-12",
				"mark" => false,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_nl_NL

?>
