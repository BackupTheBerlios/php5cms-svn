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
 
class Date_Holidays_fr_FR extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_fr_FR( $params = array() )
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
            "Nouvel An" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"Pâques" => array( 
				"date" => date( "d-m", $easter ),
				"mark" => true,
				"type" => ""
			),
			"Lundi de Pâques" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_EASTERMONDAY ),
				"mark" => true,
				"type" => ""
			),
			"Fête du Travail" => array( 
				"date" => "01-05",
				"mark" => true,
				"type" => ""
			),
			"Victoire 1945" => array( 
				"date" => "08-05",
				"mark" => true,
				"type" => ""
			),
			"Ascension" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_ASCENSIONDAY ),
				"mark" => true,
				"type" => ""
			),
			"Pentecôte" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_WHITSUNDAY ),
				"mark" => true,
				"type" => ""
			),
			"Lundi de Pentecôte" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_WHITMONDAY ),
				"mark" => true,
				"type" => ""
			),
			"Fête Nationale" => array( 
				"date" => "14-07",
				"mark" => true,
				"type" => ""
			),
			"Assomption" => array( 
				"date" => "15-08",
				"mark" => true,
				"type" => ""
			),
			"Toussaint" => array( 
				"date" => "01-11",
				"mark" => true,
				"type" => ""
			),
			"Armistice 1918" => array( 
				"date" => "11-11",
				"mark" => true,
				"type" => ""
			),
			"Noël" => array( 
				"date" => "25-12",
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_fr_FR

?>
