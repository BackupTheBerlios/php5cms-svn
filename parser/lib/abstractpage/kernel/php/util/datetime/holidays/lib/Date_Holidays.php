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


/*
define( "DATE_HOLIDAYS_RELIGION_BAHAI",               0 ); // Bahá'i
define( "DATE_HOLIDAYS_RELIGION_BUDDHISM_MAHAYANA",   1 );
define( "DATE_HOLIDAYS_RELIGION_BUDDHISM_THERAVADA",  2 );
define( "DATE_HOLIDAYS_RELIGION_BUDDHISM_VAJRAYANA",  3 );
define( "DATE_HOLIDAYS_RELIGION_CATHOLIC",            4 );
define( "DATE_HOLIDAYS_RELIGION_CATHOLIC_COPTIC",     5 );
define( "DATE_HOLIDAYS_RELIGION_CATHOLIC_MARONITE",   6 );
define( "DATE_HOLIDAYS_RELIGION_FARSI",               7 );
define( "DATE_HOLIDAYS_RELIGION_HINDUISM",            8 );
define( "DATE_HOLIDAYS_RELIGION_JAINISM",             9 );
define( "DATE_HOLIDAYS_RELIGION_JEWISH",             10 );
define( "DATE_HOLIDAYS_RELIGION_MORMON",             11 );
define( "DATE_HOLIDAYS_RELIGION_MUSLIM_MOURIDISM",   12 );
define( "DATE_HOLIDAYS_RELIGION_MUSLIM_SUFI",        13 );
define( "DATE_HOLIDAYS_RELIGION_ORTHODOX",           14 );
define( "DATE_HOLIDAYS_RELIGION_PROTESTANT",         15 );
define( "DATE_HOLIDAYS_RELIGION_RASTAFARIAN",        16 );
define( "DATE_HOLIDAYS_RELIGION_SHINTO",             17 );
define( "DATE_HOLIDAYS_RELIGION_ZOROASTRIAN",        18 );

define( "DATE_HOLIDAYS_ALL",           0 );
define( "DATE_HOLIDAYS_NATIONAL",      1 );
define( "DATE_HOLIDAYS_RELIGIOUS",     2 );
*/

define( "DATE_HOLIDAYS_SEC_HOUR",   3600 );  
define( "DATE_HOLIDAYS_SEC_DAY",   86400 );  
define( "DATE_HOLIDAYS_SEC_WEEK", 604800 );


define( 'DATE_HOLIDAYS_EASTER_DIFF_GOODFRIDAY',        -  2 * DATE_HOLIDAYS_SEC_DAY );
define( 'DATE_HOLIDAYS_EASTER_DIFF_EASTERMONDAY',             DATE_HOLIDAYS_SEC_DAY );
define( 'DATE_HOLIDAYS_EASTER_DIFF_CARNAVALMON',       - 48 * DATE_HOLIDAYS_SEC_DAY );
define( 'DATE_HOLIDAYS_EASTER_DIFF_CARNAVALTUE',       - 47 * DATE_HOLIDAYS_SEC_DAY );
define( 'DATE_HOLIDAYS_EASTER_DIFF_CARNAVALWED',       - 46 * DATE_HOLIDAYS_SEC_DAY );
define( 'DATE_HOLIDAYS_EASTER_DIFF_WHITSUNDAY',          49 * DATE_HOLIDAYS_SEC_DAY );
define( 'DATE_HOLIDAYS_EASTER_DIFF_WHITMONDAY',          50 * DATE_HOLIDAYS_SEC_DAY );
define( 'DATE_HOLIDAYS_EASTER_DIFF_ASCENSIONDAY',        39 * DATE_HOLIDAYS_SEC_DAY );
define( 'DATE_HOLIDAYS_EASTER_DIFF_FRONLEICHNAM',        60 * DATE_HOLIDAYS_SEC_DAY );

define( 'DATE_HOLIDAYS_1STADVENT_DIFF_ZWEITERADVENT',    DATE_HOLIDAYS_SEC_DAY *  7 );
define( 'DATE_HOLIDAYS_1STADVENT_DIFF_DRITTERADVENT',    DATE_HOLIDAYS_SEC_DAY * 14 );
define( 'DATE_HOLIDAYS_1STADVENT_DIFF_VIERTERADVENT',    DATE_HOLIDAYS_SEC_DAY * 21 );
define( 'DATE_HOLIDAYS_1STADVENT_DIFF_TOTENSONNTAG',   - DATE_HOLIDAYS_SEC_DAY * 28 );
define( 'DATE_HOLIDAYS_1STADVENT_DIFF_BUSSUNDBETTAG',  - DATE_HOLIDAYS_SEC_DAY * 32 );
define( 'DATE_HOLIDAYS_1STADVENT_DIFF_VOLKSTRAUERTAG', - DATE_HOLIDAYS_SEC_DAY * 35 );

 
/**
 * Library to retrieve all holidays which are constant or can be calculated.
 *
 * What is the "standard" definition of a holiday?
 * See http://dictionary.reference.com/search?q=holiday
 *
 * 1. A consecrated day; religious anniversary; a day set apart in honor of
 *    some person, or in commemoration of some event.
 * 2. A day of exemption from labor; a day of amusement and gayety; a festival
 *    day.
 * 3. (Law) A day fixed by law for suspension of business; a legal holiday.
 *
 * All examples mentioned here are holidays, according to the dictionary from.
 *
 * Base class for all Holidays_* classes.
 *
 * @link http://www.jours-feries.com/index2.php3?id_langue=2
 * @link http://www.tyzo.com/tools/holidays.html
 * @package util_datetime_holidays_lib
 */

class Date_Holidays extends PEAR
{
    /**
     * @access private
     */
    var $_year;
    
    /**
     * @access private
     */
    var $_params = array();

	/**
	 * Array with holidays for specific region
     *
	 * @access private
	 */
    var $_holidays = array();
	
	
    /**
     * Constructor
	 *
	 * @access public
     */
    function Date_Holidays( $params = array() )
    {
        if ( !empty( $params ) && !is_array( $params ) )
        {
            $this = new PEAR_Error( "Params need to be of type array." );
            return;
        }
        
        $this->_params = $params;
		
		// defaults to current year if not set
		$this->setYear( isset( $params['year'] )? isset( $params['year'] ) : null );
    }
    
    
    /**
     * Attempts to return a concrete Date_Holidays instance based on $driver.
     *
     * @param mixed $driver  The type of concrete Date_Holidays subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Date_Holidays The newly created concrete Date_Holidays instance, or
     *                      false an error.
     * @access public
     */
	function &factory( $driver, $params = array() )
	{
		if ( strlen( $driver ) == 2 )
			$driver = strtolower( $driver ) . "_" . strtoupper( $driver );
		
		$driver      = str_replace( "-", "_", $driver );
		$lang_parts  = explode( '_', $driver );
		
		$first_part  = strtolower( $lang_parts[0] );
		$second_part = strtoupper( $lang_parts[1] );
		
		$driver_class = 'Date_Holidays_' . $first_part . "_" . $second_part;
		
		using( 'util.datetime.holidays.lib.' . $driver_class );
		
		if ( class_registered( $driver_class ) )
		{
            $obj = new $driver_class( $params );
			return $obj;
        }
		
		// degrade gracefully and try again
		if ( $params['degrade'] == true )
		{
	        $driver_class = 'Date_Holidays_' . $first_part;
		
			using( 'util.datetime.holidays.lib.' . $driver_class );
		
			if ( class_registered( $driver_class ) )
			{
	            $obj = new $driver_class( $params );
				return $obj;
	        }
			else
			{
				return PEAR::raiseError( "Driver not supported." );
			}
		}
		else
		{
			return PEAR::raiseError( "Driver not supported." );
		}
	}

    /**
     * Attempts to return a reference to a concrete Date_Holidays instance
     * based on $driver. It will only create a new instance if no
     * Date_Holidays instance with the same parameters currently exists.
     *
     * This method must be invoked as: $var = &Date_Holidays::singleton()
     *
     * @param mixed $driver  The type of concrete Date_Holidays subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $params  (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Date_Holidays  The concrete Date_Holidays reference, or false on an
     *                       error.
     * @access public
     */
    function &singleton( $driver, $params = array() )
    {
        static $instances;
        
        if ( !isset( $instances ) )
            $instances = array();

        if ( is_array( $driver ) )
            $drivertag = implode( ':', $driver );
        else
            $drivertag = $driver;
        
        $signature = md5( strtolower( $drivertag ) . '][' . implode( '][', $params ) );

        if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &Date_Holidays::factory( $driver, $params );

        return $instances[$signature];
    }
    
    /**
     * @access public
     */
    function setYear( $year = null )
    {
        // use the current year if nothing is specified
        if ( !$year || ( $year < 1583 ) || ( $year > 4099 ) ) // Gregorian Calendar
            $this->_year = date( "Y", time() );
        else
            $this->_year = $year;
			
		// refresh dates
		$this->_populate();
    }
    
    /**
     * @access public
     */
    function getYear()
    {
        return $this->_year;
    }

	/**
	 * Returns whether a day is a Holiday or not.
	 *
	 * @access public
	 * @todo   add support for type, e.g. religious or national holiday
	 * @return boolean true if date is a Holiday, otherwise false
     */
	function isHoliday( $m = null, $d = null, $type = DATE_HOLIDAYS_ALL )
	{
		// TODO: add support for type, e.g. religious or national holiday, sort result
		
		if ( empty( $m ) || empty( $d ) )
			return false;

		$result = false;
		foreach ( $this->_holidays as $desc => $values )
		{
			$dt = $values["date"];			
			$date_arr = $this->_splitDate( $dt );

			if ( ( $date_arr["month"] == $m ) && ( $date_arr["day"] == $d ) )
			{
				$result = true;
				break;
			}
		}
		
		return $result;
	}
	
	/**
     * @access public
	 * @todo   add support for type, e.g. religious or national holiday
	 * @return string Holiday or empty string
     */
	function getHoliday( $m = null, $d = null, $type = DATE_HOLIDAYS_ALL )
	{
		// TODO: add support for type, e.g. religious or national holiday, sort result
		
		if ( !$this->isHoliday( $m, $d, $type ) )
			return "";

		$result = "";
		foreach ( $this->_holidays as $desc => $values )
		{
			$dt = $values["date"];
			$date_arr = $this->_splitDate( $dt );

			if ( ( $date_arr["month"] == $m ) && ( $date_arr["day"] == $d ) )
			{
				$result = (string)$desc;
				break;
			}
		}

		return $result;
	}
	
	/**
     * @access public
     */	
	function getHolidays( $month = null, $marked_only = false, $type = DATE_HOLIDAYS_ALL )
	{
		// TODO: add support for type, e.g. religious or national holiday, sort result
		
		$result = array();
		
		if ( $month )
		{
			// TODO: fetch dates from array $this->_holidays, resolve specials
			
			$result = array();
		}
		else
		{
			foreach ( $this->_holidays as $desc => $values )
			{
				$dt = $values["date"];
				$result[$desc] = $dt;
			}
		}
		
		// TODO: sort result
		
		return $result;
	}

    
    // private methods
    
	/**
	 * @access private
	 */
	function _getEaster()
	{
		$y = $this->getYear();

		/* calculate easter */
		
		// easter algorithm #1 - failed

		/* This algorithm is from Practical Astronomy With Your Calculator, 2nd Edition by Peter
     	 * Duffett-Smith. It was originally from Butcher's Ecclesiastical Calendar, published in
     	 * 1876. This algorithm has also been published in the 1922 book General Astronomy by
     	 * Spencer Jones; in The Journal of the British Astronomical Association (Vol.88, page
     	 * 91, December 1977); and in Astronomical Algorithms (1991) by Jean Meeus. */
        $a  = $y % 19;
        $b  = intval( $y / 100 );
        $c  = $y % 100;
        $d  = intval( $b / 4 );
        $e  = $b % 4;
        $f  = intval( ( $b + 8 ) / 25 );
        $g  = intval( ( $b -$f + 1 ) / 3 );
        $h  = ( 19 * $a + $b - $d - $g + 15 ) % 30;
        $i  = intval( $c / 4 );
        $k  = $c % 4;
        $l  = ( 32 + 2 * $e + 2 * $i - $h - $k ) % 7;
        $m  = intval( ( $a + 11 * $h + 22 * $l ) / 451 );
        $p  = ( $h + $l - 7 * $m + 114 ) % 31;
        $om = intval( ( $h + $l - 7 * $m + 114 ) / 31 ); // [3 = March, 4 = April]
        $od = $p + 1; // day in Easter Month
		
		
		/*
		// easter algorithm #2 - failed
		
   		$a  = $y % 19;
   		$b  = $y % 4;
   		$c  = $y % 7;
   		$d  = ( 19 * $a + 24 ) % 30;
   		$e  = (  2 * $b + 4 * $c + 6 * $d + 5 ) % 7;
   		$od = 22 + $d + $e;
   		$om = 3;
   
   		if ( $od > 31 ) 
		{
     		$od = $d + $e - 9;
     		$om = 4;
   		}
   
   		if ( ( $od == 26 ) && ( $om == 4 ) )
     		$od = 19;
   
   		if ( ( $od == 25 ) && ( $om == 4 ) && ( $d == 28 ) && ( $e == 6 ) && ( $a > 10 ) )
     		$od = 18;
		*/
		
		/*
		// easter algorith #3 - failed
		
		// the Golden number      
		$golden = ( $year % 19 ) + 1;            
		
		// the "Domincal number"      
		$dom = ( $year + (int)( $year / 4 ) - (int)( $year / 100 ) + (int)( $year / 400 ) ) % 7;      
		
		if ( $dom < 0) 
			$dom += 7;            
			
		// the solar and lunar corrections
		$solar = ( $year - 1600 ) / 100 - ( $year - 1600 ) / 400;      
		$lunar = ( ( ( $year - 1400 ) / 100 ) * 8 ) / 25;
		
		// uncorrected date of the Paschal full moon
		$pfm = ( 3 - ( 11 * $golden ) + $solar - $lunar ) % 30;
		
		if ( $pfm < 0 ) 
			$pfm += 30;
			
		// corrected date of the Paschal full moon
		// days after 21st March
		if ( ( $pfm == 29 ) || ( $pfm == 28 && $golden > 11 ) )
			$pfm--;
		
		$tmp = ( 4 - $pfm - $dom ) % 7;
		
		if ( $tmp < 0 ) 
			$tmp += 7;
			
		// Easter as the number of days after 21st March
		$easter = $pfm + $tmp + 1;
		
		if ( $easter < 11 ) 
		{
			$om = 3;
			$od = $easter + 21;
		} 
		else 
		{
			$om = 4;
			$od = $easter - 10;
		}
		*/
		
		
		$ts_easter = mktime( 0, 0, 0, $om, $od, $y );
		return $ts_easter;
	}
	
	/**
	 * @access private
	 */
	function _getFirstAdvent()
	{
		$y = $this->getYear();
		$erster_advent = mktime( 0, 0, 0, 11, 26, $y );
		
		while ( 0 != date( 'w', $erster_advent ) ) 
			$erster_advent += DATE_HOLIDAYS_SEC_DAY;
			
		return $erster_advent;
	}
	
    /**
     * Pad single digit months/days with a leading zero for consistency (aesthetics)
     * and format the date as desired: YYYY-MM-DD by default.
     *
     * @access private
     */
    function _formatDate( $month, $day )
    {
        if ( strlen( $month ) == 1 )
            $month = "0" . $month;
    
        if ( strlen( $day ) == 1 )
            $day = "0" . $day;
    
        $date = $day . "-" . $month;
        return $date;
    }
	
	/**
	 * @access private
	 */
	function _splitDate( $date_str = "" )
	{
		// TODO: some input checking, strip leading zeros
		
		$arr = explode( "-", $date_str );
		
		return array(
			"day"   => (int)$arr[0],
			"month" => (int)$arr[1]
		);
	}
	
	/**
	 * Abstract method. Needs to be overwritten by subclass.
	 *
	 * @access private
	 * @abstract
	 */
	function _populate()
	{
		return PEAR::raiseError( "Abstract method." );
	}
} // END OF Date_Holidays

?>
