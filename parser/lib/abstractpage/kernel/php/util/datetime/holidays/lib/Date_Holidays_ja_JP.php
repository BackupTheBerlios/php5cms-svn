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
            "��ö" => array( 
				"date" => "01-01",
				"mark" => true,
				"type" => ""
			),
			"���ͤ���" => array( 
				"date" => "14-01",
				"mark" => true,
				"type" => ""
			),
			"����ǰ����" => array( 
				"date" => "11-02",
				"mark" => true,
				"type" => ""
			),
			"��ʬ����" => array( 
				"date" => "21-03",
				"mark" => true,
				"type" => ""
			),
			"�ߤɤ����" => array( 
				"date" => "29-04",
				"mark" => true,
				"type" => ""
			),
			"��ˡ��ǰ��" => array( 
				"date" => "03-05",
				"mark" => true,
				"type" => ""
			),
			"��̱�ε���" => array( 
				"date" => "04-05",
				"mark" => true,
				"type" => ""
			),
			"���ɤ����" => array( 
				"date" => "05-05",
				"mark" => true,
				"type" => ""
			),
			"���ص���" => array( 
				"date" => "06-05",
				"mark" => true,
				"type" => ""
			),
			"������" => array( 
				"date" => "20-07",
				"mark" => true,
				"type" => ""
			),
			"��Ϸ����" => array( 
				"date" => "15-09",
				"mark" => true,
				"type" => ""
			),
			"���ص���" => array( 
				"date" => "16-09",
				"mark" => true,
				"type" => ""
			),
			"��ʬ����" => array( 
				"date" => "23-09",
				"mark" => true,
				"type" => ""
			),
			"�ΰ����" => array( 
				"date" => "14-10",
				"mark" => true,
				"type" => ""
			),
			"ʸ������" => array( 
				"date" => "03-11",
				"mark" => true,
				"type" => ""
			),
			"���ص���" => array( 
				"date" => "04-11",
				"mark" => true,
				"type" => ""
			),
			"��ϫ���դ���" => array( 
				"date" => "23-11",
				"mark" => true,
				"type" => ""
			),
			"ŷ��������" => array( 
				"date" => "23-12",
				"mark" => true,
				"type" => ""
			)
        );
    }
} // END OF Date_Holidays_ja_JP

?>
