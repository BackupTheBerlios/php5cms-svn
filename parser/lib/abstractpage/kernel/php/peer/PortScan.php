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
 * PortScan class
 *
 * This class provides methods to scan ports on machines,
 * that are connected to the internet. See README for more
 * information on how to use it.
 *
 * @package peer
 */
 
class PortScan extends PEAR
{
    /**
     * Check if there is a service available at a certain port.
     *
     * This function tries to open a connection to the port
     * $port on the machine $host. If the connection can be
     * established, there is a service listening on the port.
     * If the connection fails, there is no service.
     *
     * @access public
     * @param  string  Hostname
     * @param  integer Portnumber
     * @param  integer Timeout for socket connection in seconds (default is 30).
     * @return string
     */
    function checkPort( $host, $port, $timeout = 30 )
    {
        $socket = @fsockopen( $host, $port, $errorNumber, $errorString, $timeout );

        if ( !$socket )
            return false;

        return true;
    }

    /**
     * Check a range of ports at a machine
     *
     * This function can scan a range of ports (from $minPort
     * to $maxPort) on the machine $host for running services.
     *
     * @access public
     * @param  string Hostname
     * @param  integer Lowest port
     * @param  integer Highest port
     * @param  integer Timeout for socket connection in seconds (default is 30).
     * @return array  Associative array containing the result
     */
    function checkPortRange( $host, $minPort, $maxPort, $timeout = 30 )
    {
        for ( $i = $minPort; $i <= $maxPort; $i++ )            
            $retVal[$i] = PortScan::checkPort( $host, $i, $timeout );

        return $retVal;
    }

    /**
     * Get name of the service that is listening on a certain port.
     *
     * @access public
     * @param  integer Portnumber
     * @param  string Protocal (default is tcp)
     * @return string Name of the service
     */
    function getService( $port, $protocol = "tcp" )
    {
        return @getservbyport( $port, $protocol );
    }

    /**
     * Get port that a certain service uses.
     *
     * @access public
     * @param  string Name of the service
     * @param  string Protocal (default is tcp)
     * @return integer Portnumber
     */
    function getPort( $service, $protocol = "tcp" )
    {
        return @getservbyname( $service, $protocol );
    }
} // END OF PortScan

?>
