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
 * Cron Class
 *
 * Determine if an action is due based on two timestamps
 * and a specifier in the classic crontab format.
 *
 * @package sys_cron
 */

class Cron extends PEAR
{
	/**
	 * @access public
	 */
    function due( $tLast, $tNow, $sSpec )
    {
        // this array describes the classic crontab format
        // for internal use the elements are listed in reverse order
		$arSeg = array(
			"wday", 
			"mon",
			"mday",
			"hours",
			"minutes"
		);
		
        // alternate crontab format includes year
        // this format is internally not (yet) supported!!!
        /*
		$arSeg = array(
			"year", 
			"wday",
			"mon",
			"mday",
			"hours",
			"minutes"
		);
        */

        // this array contains the offset in case for the carry over status
        // see below for the determination of the carry over status
        $arPeriod = array(
			"wday"    => 7,
			"mon"     => 12,
			"mday"    => array( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ),
			"hours"   => 24,
			"minutes" => 60
		);

        $arTime = array(
			"wday"    => 604800,	// 7 * 24 * 60 * 60
			"mon"     => 31536000,	// 365 * 24 * 60 * 60
			"mday"    => array(
				31 * 86400,			// 31 * 24 * 60 * 60
				28 * 86400,
				31 * 86400,
				30 * 86400,
				31 * 86400,
				30 * 86400,
				31 * 86400,
				31 * 86400,
				30 * 86400,
				31 * 86400,
				30 * 86400,
				31 * 86400
			),
			"hours"   => 86400,		// 24 * 60 * 60
			"minutes" => 3600		// 60 * 60
		);

        $iSeg = 0;		// segment index
        $iCmpVal = 0;	// compare value

        // these lines added in 0.2.5
        $bStatus  = false;	// procedure status
        $iPFaktor = 0;		// period factor
        $iTFaktor = 0;		// time factor

        if ( $tNow == null )
			$tNow = time();
        
		// this line added in version 0.2.2
        if ( $tLast == null )
			return false;

        // convert strings to time
        if ( is_string( $tLast ) )
			$tLast = strtotime( $tLast );
			
        if ( is_string( $tNow ) )
			$tNow = strtotime( $tNow );

        if ( $tNow < $tLast )
			return false;

        // convert time variables to arrays
        $arLast = getdate( $tLast );
        $arNow  = getdate( $tNow  );
        $arSpec = array_reverse( explode( " ", $sSpec ) );

        // walk through segments of crontab specifier
        for ( $iSeg = 0; $iSeg < count( $arSeg ); $iSeg++ )
		{
            // obtain segment key
            $sSeg = $arSeg[$iSeg];
            
			// does specifier segment contain '*'?
            if ( strstr( $arSpec[$iSeg], "*" ) != false )
			{
                // week days need special treatment
                if ( $sSeg == "wday" )
					$iCmpVal = $arLast[$sSeg];
				// use same segment of time reference
                else
					$iCmpVal = $arNow[$sSeg];
            // specifier segment contains specific criteria
            }
			else
			{
                // get reference value
                $iCmpVal = Cron::_nextLowerVal( $arSpec[$iSeg], $arNow[$sSeg] );
            }

            // this section completely changed in 0.2.5
            // obtain period factor
            $iPFactor = $arPeriod[$sSeg];
            
			// numbers of days per month are always different ...
            if ( $sSeg == "mday" )
                $iPFactor = $iPFactor[$arLast["mon"]];

            // obtain period time factor
            $iTFactor = $arTime[$sSeg];

            // numbers of days per month are always different ...
            if ( $sSeg == "mday" )
                $iTFactor = $iTFactor[$arLast["mon"]];

            // this is the decisive part of the function:
            if ( $arLast[$sSeg] < $iCmpVal && $iCmpVal <= $arNow[$sSeg] )
 				$bStatus = true;

            if ( strstr( $arSpec[$iSeg], "*" ) == false )
			{
                // next two lines changed in 0.2.7
                if ( ( ( $bStatus == true && $arNow[$sSeg] == $arLast[$sSeg] ) || $arNow[$sSeg] < $arLast[$sSeg] ) &&
						 $arLast[$sSeg] < $iCmpVal + $iPFactor &&
						 $iCmpVal + $iPFactor <= $arNow[$sSeg] + $iPFactor &&
						 $iCmpVal >= 0 )
				{
					$bStatus = true;
				}
                else if ( $tNow > $tLast + $iTFactor )
				{
					$bStatus = true;
				}
                // note that this condition causes a premature return:
                else if ( $arLast[$sSeg] > $iCmpVal )
				{
					return false;
				}
                else if ( $iCmpVal < $arNow[$sSeg] && $iCmpVal == $arLast[$sSeg] )
				{
					return false;
				}
			}
		}

		return $bStatus;
	}

	
	// private methods
	
	/**
	 * @access private
	 */
    function _nextLowerVal( $sSpec, $iRef )
    {
        $arSpec1 = explode( ",", $sSpec );	// divide segment into details
        $arInt   = array();					// array of potential integers
        $arSpec2 = array();					// array of details if
											// specified as range
        $i   = 0;
        $sEl = "";

        // walk through list of details
        foreach ( $arSpec1 as $sEl )
		{
            // specified as range?
            if ( strchr( $sEl, "-" ) != false )
			{
                // split again
                $arSpec2 = explode( "-", $sEl );
				
                // add all numbers within range to list of integers
                for ( $i = $arSpec2[0]; $i <= $arSpec2[1]; $i++ )
                    array_push( $arInt, $i );
            }
			// not a range, add directly to list of integers
			else
			{
                array_push( $arInt, $sEl );
            }
        }

        // sort reverse, highest number is now 1st element
        rsort( $arInt );
		
        // walk backwards through list
        foreach( $arInt as $iEl )
		{
            // if element is smaller than reference, return element
            if ( $iEl <= $iRef )
				return $iEl;
        }

        // no element found
        return -1;
    }
} // END OF Cron

?>
