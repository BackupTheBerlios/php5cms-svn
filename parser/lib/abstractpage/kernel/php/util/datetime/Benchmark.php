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


using( 'util.Util' );


/**
 * Benchmark Class
 *
 * @package util_datetime
 */

class Benchmark extends PEAR
{
    /**
     * Contains the markers
     *
     * @var    array
     * @access public
     */
    var $markers = array();
	
	
    /**
     * Set "Start" marker.
     *
     * @see    setMarker(), stop()
     * @access public
     */
    function start()
	{
        $this->setMarker( 'Start' );
    }

    /**
     * Set "Stop" marker.
     *
     * @see    setMarker(), start()
     * @access public
     */
    function stop()
	{
        $this->setMarker( 'Stop' );
    }

    /**
     * Set marker.
     *
     * @param  string  name of the marker to be set
     * @see    start(), stop()
     * @access public
     */
    function setMarker( $name )
	{
        $microtime = explode( ' ', microtime() );
        $this->markers[$name] = $microtime[1] . substr( $microtime[0], 1 );
    }

    /**
     * Returns the time elapsed betweens two markers.
     *
     * @param  string  $start        start marker, defaults to "Start"
     * @param  string  $end          end marker, defaults to "Stop"
     * @return double  $time_elapsed time elapsed between $start and $end
     * @access public
     */
    function timeElapsed( $start = 'Start', $end = 'Stop' )
	{
        if ( Util::extensionExists( 'bcmath' ) )
            return bcsub( $this->markers[$end], $this->markers[$start], 6 );
        else
            return $this->markers[$end] - $this->markers[$start];
    }

    /**
     * Returns profiling information.
     *
     * $profiling[x]['name']  = name of marker x
     * $profiling[x]['time']  = time index of marker x
     * $profiling[x]['diff']  = execution time from marker x-1 to this marker x
     * $profiling[x]['total'] = total execution time up to marker x
     *
     * @return array $profiling
     * @access public
     */
    function getProfiling()
	{
        $i = 0;
        $total  = 0;
        $result = array();
        
        foreach ( $this->markers as $marker => $time )
		{
            if ( $marker == 'Start' )
			{
                $diff = '-';
            }
			else
			{
                if ( Util::extensionExists( 'bcmath' ) )
				{
                    $diff  = bcsub( $time,  $temp, 6 );
                    $total = bcadd( $total, $diff, 6 );
                }
				else
				{
                    $diff  = $time - $temp;
                    $total = $total + $diff;
                }
            }
            
            $result[$i]['name']  = $marker;
            $result[$i]['time']  = $time;
            $result[$i]['diff']  = $diff;
            $result[$i]['total'] = $total;
            
            $temp = $time;
            $i++;
        }

        return $result;
    }
} // END OF Benchmark

?>
