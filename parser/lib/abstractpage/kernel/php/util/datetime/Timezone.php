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
 * Timezone class
 *
 * $tz = &Timezone::getByTimeZone( 'CST' );
 * printf( "Offset is %s\n", $tz->getOffset() );  // -0600
 *
 * @link     http://greenwichmeanTime.com/info/Timezone.htm
 * @link     http://www.worldtimezone.com/wtz-names/timezonenames.html
 * @link     http://scienceworld.wolfram.com/astronomy/TimeZone.html
 * @package  util_datetime
 */

class Timezone extends PEAR
{
	/**
	 * @access private
	 */
    var $_offset = '',
	
	/**
	 * @access private
	 */
    var $_tz = '';


	/**
	 * Constructor
	 *
	 * @param  array  $params
	 * @access public
	 */
	function Timezone( $params = array() )
	{
		if ( isset( $params['tz'] ) )
			$this->_tz = $params['tz'];
			
		if ( isset( $params['offset'] ) )
			$this->_offset = $params['offset'];	
	}
	
	
    /**
     * Gets the name of the timezone.
     *
     * @access  public
     * @return  string name
     */
    function getName()
	{
      	return $this->_tz;
    }

    /**
     * Retrieves the offset of the timezone.
     *
     * @access  public
     * @return  string offset
     */    
    function getOffset()
	{
      	return $this->_offset;
    }

    /**
     * Get the offset string by timezone name.
     *
     * @access  public
     * @param   string
     * @return  string
     */
    function getOffsetByTimeZoneString( $string ) 
	{
      	static $tz = array(
	        /* East of Greenwich */
			'IDLE'=> '+1200', /* International Date Line East */
	        'NZST'=> '+1200', /* New Zealand Standard         */
	        'GST' => '+1000', /* Guam Standard                */
	        'JST' => '+0900', /* Japan Standard Time          */
	        'CCT' => '+0800', /* China coast Time             */
	        'BT'  => '+0300', /* Baghdad                      */
	        'EET' => '+0200', /* Eastern European Time        */
	        'CET' => '+0100', /* Central European Time        */
	        
	        /* Greenwich */
	        'GMT' => '+0000', /* Greenwich mean Time          */
	        'UT'  => '+0000', /* Universal                    */
	        'UTC' => '+0000', /* Universal Co-ordinated       */
	        'WET' => '+0000', /* Western Europe               */
	
	        /* West of Greenwich */
	        'WAT' => '-0100', /* West Africa                  */
	        'AT'  => '-0200', /* Azores                       */
	        'AST' => '-0400', /* Atlantic Standard            */
	        'EST' => '-0500', /* Eastern Standard Time        */
	        'CST' => '-0600', /* Central Standard Time        */
	        'MST' => '-0700', /* Mountain Standard Time       */
	        'PST' => '-0800', /* Pacific Standard Time        */
	        'YST' => '-0900', /* Yukon Standard               */
	        'AHST'=> '-1000', /* Alaska-Hawaii Standard       */
 	        'NT'  => '-1100', /* Nome                         */
			'IDLE'=> '-1200', /* International Date Line West */
        
 	        /* Summer time */
 	        'BST' => '+0100', /* British Summer Time          */
 	        'CEST'=> '+0200', /* Central European Summer Time */
 	        'MEST'=> '+0200', /* Middle European Summer Time  */
 	        'MESZ'=> '+0200', /* Middle European Summer Time  */
 	        'SST' => '+0200', /* Swedish Summer               */
 	        'FST' => '+0200', /* French Summer                */
 	        'BST' => '+0100', /* British Summer Time          */
 	        'ADT' => '-0300', /* Atlantic Daylight            */
  	        'EDT' => '-0400', /* Eastern Daylight             */
 	        'CDT' => '-0500', /* Central Daylight             */
 	        'MDT' => '-0600', /* Mountain Daylight            */
 	        'PDT' => '-0700', /* Pacific Daylight             */
 	        'YDT' => '-0800', /* Yukon Daylight               */
 	        'HDT' => '-0900', /* Hawaii Daylight              */
		);

		if ( !isset( $tz[$string] ) )
        	return false;
      
      	return $tz[$string];
  	}

    /**
     * Returns a Timezone object by a time zone name.
     *
     * @static
     * @access  public
     * @param   string abbrev
     * @return  &Timezone
     * @throws  Error
     */    
    function &getByName( $abbrev )
	{
      	if ( ( $offset = Timezone::getOffsetByTimeZoneString( $abbrev ) ) === false ) 
			return PEAR::raiseError( "Unknown time zone abbreviation: " . $abbrev );
      
      	$tz = &new Timezone( array(
			'tz'     => $abbrev,
			'offset' => $offset
		) );
      
		return $tz;
	}
    
    /**
     * Get a timezone object for the machines local timezone.
     *
     * @static
     * @access  public
     * @return  &Timezone
     */
    function &getLocal()
	{
      	return Timezone::getByName( date( 'T' ) );
    }

    /**
     * Retrieves the timezone offset to UTC/GMT.
     *
     * @access  public
     * @return  int offset
     */    
    function getOffsetInSeconds()
	{
      	list( $s, $h, $m ) = sscanf( $this->_offset, '%c%2d%2d' );
      	return ( ( '+' == $s? 1 : -1 ) * ( ( 3600 * $h ) + ( 60 * $m ) ) );
    }
    
    /**
     * Converts a date from one timezone to a date of this
     * timezone.
     *
     * @access  public
     * @param   &Date
     * @param   &Timezone
     * @return  &Date
     */
    function convertDate( &$date, &$tz ) 
	{
      	return new Date( $date->getTime() + ( $this->getOffsetInSeconds() - $tz->getOffsetInSeconds() ) );
    }

    /**
     * Converts a date in the machines local timezone to a date in this
     * timezone.
     *
     * @access  public
     * @param   &Date
     * @return  &Date
     */    
    function &convertLocalDate( &$date )
	{
      	return $this->convertDate( $date, Timezone::getLocal() );
    }
} // END OF Timezone

?>
