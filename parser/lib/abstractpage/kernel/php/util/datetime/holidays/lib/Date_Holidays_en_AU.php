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
 
class Date_Holidays_en_AU extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_en_AU( $params = array() )
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
            "New Year's Day" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"Australia Day" => array( 
				"date" => "26-01",
				"mark" => true,
				"type" => ""
			),
			"Australia Day Holiday" => array( // AU ex NSW,Vic
				"date" => "28-01",
				"mark" => true,
				"type" => ""
			),
			"Labour Day" => array( // AU WA
				"date" => "04-03",
				"mark" => false,
				"type" => ""
			),
			"Labour Day" => array( // AU Vic
				"date" => "11-03",
				"mark" => false,
				"type" => ""
			),
			"8 Hours Day" => array( // AU Tas
				"date" => "11-03",
				"mark" => false,
				"type" => ""
			),
			"Canberra Day" => array( // AU ACT
				"date" => "18-03",
				"mark" => false,
				"type" => ""
			),
			"Anzac Day" => array( 
				"date" => "25-04",
				"mark" => true,
				"type" => ""
			),
			"Labour Day" => array( // AU Qld
				"date" => "06-05",
				"mark" => false,
				"type" => ""
			),
			"May Day" => array( // AU NT
				"date" => "06-05",
				"mark" => false,
				"type" => ""
			),
			"Adelaide Cup" => array( // AU SA
				"date" => "20-05",
				"mark" => false,
				"type" => ""
			),
			"Foundation Day" => array( // AU WA
				"date" => "03-06",
				"mark" => false,
				"type" => ""
			),
			"Queen's Birthday" => array( // AU ex-WA
				"date" => "10-06",
				"mark" => true,
				"type" => ""
			),
			"Picnic Day" => array( // AU NT
				"date" => "05-08",
				"mark" => false,
				"type" => ""
			),
			"Queen's Birthday" => array( // AU WA
				"date" => "30-09",
				"mark" => false,
				"type" => ""
			),
			"Labour Day" => array( // AU ACT,NSW,SA
				"date" => "07-10",
				"mark" => false,
				"type" => ""
			),
			"Melbourne Cup" => array( // AU Vic
				"date" => "",
				"mark" => false,
				"type" => ""
			),
			"Christmas Day" => array( 
				"date" => "25-12",
				"mark" => true,
				"type" => ""
			),
			"Boxing Day" => array( // AU ex-SA
				"date" => "26-12",
				"mark" => true,
				"type" => ""
			),
			"Proclamation Day" => array( // AU SA
				"date" => "26-12",
				"mark" => true,
				"type" => ""
			),
			"Good Friday" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_GOODFRIDAY ),
				"mark" => true,
				"type" => ""
			),
			"Easter Saturday" => array( 
				"date" => date( "d-m", $easter ),
				"mark" => true,
				"type" => ""
			),
			"Easter Monday" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_EASTERMONDAY ),
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_en_AU

?>
