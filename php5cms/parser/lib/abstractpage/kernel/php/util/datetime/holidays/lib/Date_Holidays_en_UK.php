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
 
class Date_Holidays_en_UK extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_en_UK( $params = array() )
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
            "New Years Day" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"Epiphany" => array( 
				"date" => "06-01",
				"mark" => true,
				"type" => ""
			),
			"Conversion of St Paul" => array( 
				"date" => "25-01",
				"mark" => true,
				"type" => ""
			),
			"Shrove Tuesday" => array( 
				"date" => "12-02",
				"mark" => true,
				"type" => ""
			),
			"Ash Wednesday" => array( 
				"date" => "13-02",
				"mark" => false,
				"type" => ""
			),
			"St Valentines Day" => array( 
				"date" => "14-02",
				"mark" => false,
				"type" => ""
			),
			"St Davids Day" => array( 
				"date" => "01-03",
				"mark" => true,
				"type" => ""
			),
			"Mothering Sunday" => array( 
				"date" => "10-03",
				"mark" => true,
				"type" => ""
			),
			"Commonwealth Day" => array( 
				"date" => "11-03",
				"mark" => true,
				"type" => ""
			),
			"St Patricks Day" => array( 
				"date" => "18-03",
				"mark" => true,
				"type" => ""
			),
			"April Fools Day" => array( 
				"date" => "01-04",
				"mark" => true,
				"type" => ""
			),
			"Queens Birthday" => array( 
				"date" => "21-04",
				"mark" => true,
				"type" => ""
			),
			"St Georges Day" => array( 
				"date" => "23-04",
				"mark" => true,
				"type" => ""
			),
			"May Bank Holiday" => array( 
				"date" => "06-0",
				"mark" => true,
				"type" => ""
			),
			"Golden Jubilee Bank Holiday" => array( 
				"date" => "03-06",
				"mark" => true,
				"type" => ""
			),
			"Spring Bank Holiday" => array( 
				"date" => "04-06",
				"mark" => true,
				"type" => ""
			),
			"Fathers Day" => array( 
				"date" => "16-06",
				"mark" => true,
				"type" => ""
			),
			"Summer Bank Holiday" => array( 
				"date" => "26-08",
				"mark" => true,
				"type" => ""
			),
			"Good Friday" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_GOODFRIDAY ),
				"mark" => true,
				"type" => ""
			),
			"Easter Sunday" => array( 
				"date" => date( "d-m", $easter ),
				"mark" => true,
				"type" => ""
			),
			"Easter Monday" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_EASTERMONDAY ),
				"mark" => true,
				"type" => ""
			),
			"Whit Sunday" => array( 
				"date" => date( "d-m", $easter + DATE_HOLIDAYS_EASTER_DIFF_WHITSUNDAY ),
				"mark" => true,
				"type" => ""
			),
			"Halloween" => array( 
				"date" => "31-10",
				"mark" => true,
				"type" => ""
			),
			"All Saints Day" => array( 
				"date" => "01-11",
				"mark" => true,
				"type" => ""
			),
			"Guy Fawkes Night" => array( 
				"date" => "05-11",
				"mark" => true,
				"type" => ""
			),
			"Remembrance Sunday" => array( 
				"date" => "10-11",
				"mark" => true,
				"type" => ""
			),
			"St Andrews Day" => array( 
				"date" => "30-11",
				"mark" => true,
				"type" => ""
			),
			"Christmas Day" => array( 
				"date" => "25-12",
				"mark" => true,
				"type" => ""
			),
			"Boxing Day" => array( 
				"date" => "26-12",
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_en_UK

?>
