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


/**
 * @package format_vformat
 */
 
class RFC2425 extends PEAR
{
	/**
	 * @access public
	 */
    function import( $text )
    {
        $lines = explode( "\n", $text );
        $data  = array();

        // unfolding
        foreach ( $lines as $line ) 
		{
            if ( preg_match( '/^[ \t]/', $line ) && count( $data ) > 1 )
                $data[count( $data ) - 1] .= substr( $line, 1 );
            else if ( trim( $line ) != '' )
                $data[] = $line;
            
            $data[count( $data ) - 1] = trim( $data[count( $data ) - 1] );
        }
		
        $lines = $data;
        $data  = array();
        
		foreach ( $lines as $line ) 
		{
            $line = preg_replace( '/"([^":]*):([^":]*)"/', "\"\\1\x00\\2\"", $line );
            list( $name, $value ) = explode( ':', $line, 2 );
            $name   = preg_replace( '/\0/', ':', $name  );
            $value  = preg_replace( '/\0/', ':', $value );
            $name   = explode( ';', $name );
            $params = array();
 
            if ( isset( $name[1] ) ) 
			{
                for ( $i = 1; $i < count( $name ); $i++ ) 
				{
                    $name_value = explode( '=', $name[$i] );
                    $paramname  = $name_value[0];
                    $paramvalue = isset( $name_value[1] )? $name_value[1] : null;

                    if ( isset( $paramvalue ) ) 
					{
                        preg_match_all( '/("((\\\\"|[^"])*)"|[^,]*)(,|$)/', $paramvalue, $split );
						
                        for ( $j = 0; $j < count( $split[1] ) - 1; $j++ )
                            $params[$paramname][] = stripslashes( $split[1][$j] );
                    } 
					else 
					{
                        $params[$paramname] = true;
                    }
                }
            }
			
            $value  = preg_replace( '/\\\\,/', "\x00", $value );
            $values = explode( ',', $value );
            
			for ( $i = 0; $i < count( $values ); $i++ ) 
			{
                $values[$i] = preg_replace( '/\0/',       ',',  $values[$i] );
                $values[$i] = preg_replace( '/\\\\n/',    "\n", $values[$i] );
                $values[$i] = preg_replace( '/\\\\,/',    ',',  $values[$i] );
                $values[$i] = preg_replace( '/\\\\\\\\/', '\\', $values[$i] );
            }
			
            $data[] = array(
				'name'   => strtoupper( $name[0] ), 
				'params' => $params, 
				'values' => $values
			);
        }
		
        $start = 0;
        $this->cards = $this->_build( $data, $start );
        return $this->cards;
    }

	/**
	 * @access public
	 */
    function read( $attribute, $index = 0 )
    {
        $value = $attribute['values'][$index];

        if ( isset( $attribute['params']['ENCODING']) ) 
		{
            switch ( $attribute['params']['ENCODING'][0] ) 
			{
            	case 'QUOTED-PRINTABLE':
                	$value = quoted_printable_decode( $value );
                	break;
            }
        }

        return $value;
    }

	/**
	 * @access public
	 */
    function getValues( $attribute, $card = 0 )
    {
        $values    = array();
        $attribute = strtoupper( $attribute );

        for ( $i = 0; $i < count( $this->cards[$card]['params'] ); $i++ ) 
		{
            $param = $this->cards[$card]['params'][$i];
            
			if ( $param['name'] == $attribute ) 
			{
                for ( $j = 0; $j < count( $param['values'] ); $j++ ) 
				{
                    $values[] = array(
						'value'  => $this->read( $param, $j ), 
						'params' => $param['params']
					);
                }
            }
        }

        return $values;
    }

	/**
	 * @access public
	 */
    function mapDate( $datestring )
    {
        @list( $date, $time ) = explode( 'T', $datestring );

        if ( strlen( $date ) == 10 ) 
		{
            $dates = explode( '-', $date );
        } 
		else 
		{
            $dates = array();
            $dates[] = substr( $date, 0, 4 );
            $dates[] = substr( $date, 4, 2 );
            $dates[] = substr( $date, 6, 2 );
        }

        $date_arr = array(
			'mday'  => $dates[2],
			'month' => $dates[1],
			'year'  => $dates[0]
		);

        if ( isset( $time ) ) 
		{
            @list( $time, $zone ) = explode( 'Z', $time );
			
            if ( strstr( $time, ':' ) !== false ) 
			{
                $times = explode( ':', $time );
            } 
			else 
			{
                $times = array();
                $times[] = substr( $time, 0, 2 );
                $times[] = substr( $time, 2, 2 );
                $times[] = substr( $time, 4 );
            }

            $date_arr['hour'] = $times[0];
            $date_arr['min']  = $times[1];
            $date_arr['sec']  = $times[2];
        }

        return $date_arr;
    }
	
	
	// private methods
	
	/**
	 * @access private
	 */
    function _build( $data, &$i )
    {
        $objects = array();

        while ( isset( $data[$i] ) ) 
		{
            if ( strtoupper( $data[$i]['name'] ) != 'BEGIN' ) 
				return PEAR::raiseError( sprintf( "Expected \"BEGIN\" in the line %d.", $i ) );

            $type   = $data[$i]['values'][0];
            $object = array( 'type' => $type );
            $object['objects'] = array();
            $object['params']  = array();
            $i++;

            while ( isset( $data[$i] ) && strtoupper( $data[$i]['name'] ) != 'END' ) 
			{
                if ( $data[$i]['name'] == 'BEGIN' )
                    $object['objects'][] = $this->_build( $data, $i );
                else
                    $object['params'][] = $data[$i];
                
                $i++;
            }
			
            if ( !isset( $data[$i] ) )
				return PEAR::raiseError( "Unexpected end of file." );
            
            if ( $data[$i]['values'][0] != $type ) 
				return PEAR::raiseError( sprintf( "Expected \"END:%s\" in line %d.", $type, $i ) );

            $objects[] = $object;
            $i++;
        }

        return $objects;
    }
} // END OF RFC2425

?>
