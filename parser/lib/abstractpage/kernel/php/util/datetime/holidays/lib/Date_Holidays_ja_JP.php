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
            "元旦" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"成人の日" => array( 
				"date" => "14-01",
				"mark" => true,
				"type" => ""
			),
			"建国記念の日" => array( 
				"date" => "11-02",
				"mark" => true,
				"type" => ""
			),
			"春分の日" => array( 
				"date" => "21-03",
				"mark" => true,
				"type" => ""
			),
			"みどりの日" => array( 
				"date" => "29-04",
				"mark" => true,
				"type" => ""
			),
			"憲法記念日" => array( 
				"date" => "03-05",
				"mark" => true,
				"type" => ""
			),
			"国民の休日" => array( 
				"date" => "04-05",
				"mark" => true,
				"type" => ""
			),
			"こどもの日" => array( 
				"date" => "05-05",
				"mark" => true,
				"type" => ""
			),
			"振替休日" => array( 
				"date" => "06-05",
				"mark" => true,
				"type" => ""
			),
			"海の日" => array( 
				"date" => "20-07",
				"mark" => true,
				"type" => ""
			),
			"敬老の日" => array( 
				"date" => "15-09",
				"mark" => true,
				"type" => ""
			),
			"振替休日" => array( 
				"date" => "16-09",
				"mark" => true,
				"type" => ""
			),
			"秋分の日" => array( 
				"date" => "23-09",
				"mark" => true,
				"type" => ""
			),
			"体育の日" => array( 
				"date" => "14-10",
				"mark" => true,
				"type" => ""
			),
			"文化の日" => array( 
				"date" => "03-11",
				"mark" => true,
				"type" => ""
			),
			"振替休日" => array( 
				"date" => "04-11",
				"mark" => true,
				"type" => ""
			),
			"勤労感謝の日" => array( 
				"date" => "23-11",
				"mark" => true,
				"type" => ""
			),
			"天皇誕生日" => array( 
				"date" => "23-12",
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_ja_JP

?>
