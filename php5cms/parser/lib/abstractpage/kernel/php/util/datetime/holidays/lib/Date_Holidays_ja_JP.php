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
 
class Date_Holidays_ja_JP extends Date_Holidays
{
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays_ja_JP( $params = array() )
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
            "¸µÃ¶" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"À®¿Í¤ÎÆü" => array( 
				"date" => "14-01",
				"mark" => true,
				"type" => ""
			),
			"·ú¹ñµ­Ç°¤ÎÆü" => array( 
				"date" => "11-02",
				"mark" => true,
				"type" => ""
			),
			"½ÕÊ¬¤ÎÆü" => array( 
				"date" => "21-03",
				"mark" => true,
				"type" => ""
			),
			"¤ß¤É¤ê¤ÎÆü" => array( 
				"date" => "29-04",
				"mark" => true,
				"type" => ""
			),
			"·ûË¡µ­Ç°Æü" => array( 
				"date" => "03-05",
				"mark" => true,
				"type" => ""
			),
			"¹ñÌ±¤ÎµÙÆü" => array( 
				"date" => "04-05",
				"mark" => true,
				"type" => ""
			),
			"¤³¤É¤â¤ÎÆü" => array( 
				"date" => "05-05",
				"mark" => true,
				"type" => ""
			),
			"¿¶ÂØµÙÆü" => array( 
				"date" => "06-05",
				"mark" => true,
				"type" => ""
			),
			"³¤¤ÎÆü" => array( 
				"date" => "20-07",
				"mark" => true,
				"type" => ""
			),
			"·ÉÏ·¤ÎÆü" => array( 
				"date" => "15-09",
				"mark" => true,
				"type" => ""
			),
			"¿¶ÂØµÙÆü" => array( 
				"date" => "16-09",
				"mark" => true,
				"type" => ""
			),
			"½©Ê¬¤ÎÆü" => array( 
				"date" => "23-09",
				"mark" => true,
				"type" => ""
			),
			"ÂÎ°é¤ÎÆü" => array( 
				"date" => "14-10",
				"mark" => true,
				"type" => ""
			),
			"Ê¸²½¤ÎÆü" => array( 
				"date" => "03-11",
				"mark" => true,
				"type" => ""
			),
			"¿¶ÂØµÙÆü" => array( 
				"date" => "04-11",
				"mark" => true,
				"type" => ""
			),
			"¶ÐÏ«´¶¼Õ¤ÎÆü" => array( 
				"date" => "23-11",
				"mark" => true,
				"type" => ""
			),
			"Å·¹ÄÃÂÀ¸Æü" => array( 
				"date" => "23-12",
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_ja_JP

?>
