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
 * Usage
 *
 * $os = SysInfo::factory();
 * print "<pre>";
 * print $os->kernel() . "<br><br>\n\n";
 * print_r( $os->memory() )      . "<br><br>\n\n";
 * print_r( $os->filesystems() ) . "<br><br>\n\n";
 * print_r( $os->scsi() )        . "<br><br>\n\n";
 * print_r( $os->ide() )         . "<br><br>\n\n";
 * print "</pre>";
 *
 * @package sys_lib
 */
 
class SysInfo extends PEAR
{
	/**
	 * @access public
	 */
	var $os;
	
	/**
	 * @access public
	 */
	var $dmesg;
	
	
	/**
	 * @access public
	 */
	function &factory()
	{
		$os = PHP_OS;
		
		switch ( strtolower( PHP_OS ) )
		{
			case 'Darwin':
				$sys = 'Darwin';
				break;
					
			case 'FreeBSD':
				$sys = 'FreeBSD';
				break;
				
			case 'Linux':
				$sys = 'Linux';
				break;
				
			case 'NetBSD':
				$sys = 'NetBSD';
				break;
				
			case 'OpenBSD':
				$sys = 'OpenBSD';
				break;
								
			default:
				return PEAR::raiseError( "OS not supported." );
		}
		
		$sys_class = 'SysInfo_' . $sys;
		using( 'sys.lib.' . $sys_class );
				
		if ( class_registered( $sys_class ) )
			return new $sys_class;
		else
			return PEAR::raiseError( "Driver not supported." );
	}
	
	/**
	 * @access public
	 */	
	function get_os()
	{
		return $this->os;
	}
	
	/**
	 * Read /var/run/dmesg.boot, but only if we haven't already.
	 *
	 * @access public
	 */
    function read_dmesg()
    {
        if ( !$this->dmesg )
            $this->dmesg = file( '/var/run/dmesg.boot' );
        
        return $this->dmesg;
    }

	/**
	 * Grabs a key from sysctl(8).
	 *
	 * @access public
	 */
    function grab_key( $key )
    {
        return Util::executeProgram( 'sysctl', "-n $key" );
    }

	/**
	 * Get our apache SERVER_NAME or vhost.
	 *
	 * @access public
	 */
    function hostname()
    {
        if ( !( $result = getenv( 'SERVER_NAME' ) ) ) 
            $result = "N.A.";
        
        return $result;
    }

	/**
	 * Get our canonical hostname.
	 *
	 * @access public
	 */
    function chostname()
    {
        return Util::executeProgram( 'hostname' );
    }

	/**
	 * Get the IP address of our canonical hostname.
	 *
	 * @access public
	 */
    function ip_addr()
    {
        if ( !( $result = getenv( 'SERVER_ADDR' ) ) ) 
            $result = gethostbyname( $this->chostname() );
        
        return $result;
    }

	/**
	 * @access public
	 */
    function kernel()
    {
        $s = $this->grab_key( 'kern.version' );
        $a = explode( ':', $s );

        return $a[0];
    }

	/**
	 * @access public
	 */
    function uptime()
    {
        $sys_ticks = $this->get_sys_ticks();

        $min   = $sys_ticks / 60;
        $hours = $min / 60;
        $days  = floor( $hours / 24 );
        $hours = floor( $hours - ( $days * 24 ) );
        $min   = floor( $min   - ( $days * 60 * 24 ) - ( $hours * 60 ) );

        if ( $days != 0 )
            $result = "$days days, ";

        if ( $hours != 0 )
            $result .= "$hours hours, ";
        
        $result .= "$min minutes";
        return $result;
    }

	/**
	 * @access public
	 */
    function users()
    {
        return Util::executeProgram( 'who', '| wc -l' );
    }

	/**
	 * @access public
	 */
    function loadavg()
    {
        $s = $this->grab_key( 'vm.loadavg' );
        $s = ereg_replace( '{ ', '', $s );
        $s = ereg_replace( ' }', '', $s );
        $results = explode( ' ', $s );

        return $results;
    }

	/**
	 * @access public
	 */
    function cpu_info()
    {
        $results = array();
        $ar_buf  = array();

        $results['model'] = $this->grab_key( 'hw.model' );
        $results['cpus']  = $this->grab_key( 'hw.ncpu'  );

        for ( $i = 0; $i < count( $this->read_dmesg() ); $i++ ) 
		{
            $buf = $this->dmesg[$i];
            
			if ( preg_match( "/$this->cpu_regexp/", $buf, $ar_buf ) ) 
			{
                $results['mhz'] = round( $ar_buf[2] );
                break;
            }
        }
		
        return $results;
    }

	/**
	 * Get the scsi device information out of dmesg.
	 *
	 * @access public
	 */
    function scsi()
    {
        $results = array();
        $s = 0;
		
        for ( $i = 0; $i < count( $this->read_dmesg() ); $i++ ) 
		{
            $buf = $this->dmesg[$i];
            
			if ( preg_match( "/$this->scsi_regexp/", $buf, $ar_buf ) ) 
            	$results[$s++] = $ar_buf[1].": ".$ar_buf[2];
        }
		
        return array_values(array_unique( $results ) );
    }

	/**
	 * Get the pci device information out of dmesg.
	 *
	 * @access public
	 */
    function pci()
    {
        $results = array();
        $s = 0;
		
        for ( $i = 0; $i < count( $this->read_dmesg() ); $i++ ) 
		{
            $buf = $this->dmesg[$i];
            
			if ( preg_match( '/(.*): <(.*)>(.*) pci[0-9]$/', $buf, $ar_buf ) ) 
                $results[$s++] = $ar_buf[1] . ": " . $ar_buf[2];
            else if ( preg_match( '/(.*): <(.*)>.* at [.0-9]+ irq/', $buf, $ar_buf ) ) 
                $results[$s++] = $ar_buf[1] . ": " . $ar_buf[2];
        }
		
        sort( $results );
        return array_values( array_unique( $results ) );
    }
	
	/**
	 * Get the ide device information out of dmesg.
	 *
	 * @access public
	 */
    function ide()
    {
        $results = array();
        $s = 0;
		
        for ( $i = 0; $i < count( $this->read_dmesg() ); $i++ ) 
		{
            $buf = $this->dmesg[$i];
            
			if ( preg_match( '/^(ad[0-9]): (.*)MB <(.*)> (.*) (.*)/', $buf, $ar_buf ) ) 
			{
                $s = $ar_buf[1];
                $results[$s]['model']    = $ar_buf[3];
                $results[$s]['media']    = 'Hard Disk';
                $results[$s]['capacity'] = $ar_buf[2] * 2048 * 1.049;
            }
			
            if ( preg_match( '/^(acd[0-9]): (.*) <(.*)> (.*)/', $buf, $ar_buf ) ) 
			{
                $s = $ar_buf[1];
                $results[$s]['model'] = $ar_buf[3];
                $results[$s]['media'] = 'CD-ROM';
            }
        }
		
        return array_values( array_unique( $results ) );
    }

	/**
	 * @access public
	 */
    function memory()
    {
        $s = $this->grab_key( 'hw.physmem' );

        if ( PHP_OS == 'FreeBSD' ) 
		{
            // vmstat on fbsd 4.4 or greater outputs kbytes not hw.pagesize
            // I should probably add some version checking here, but for now
            // we only support fbsd 4.4
            $pagesize = 1024;
        } 
		else 
		{
            $pagesize = $this->grab_key( 'hw.pagesize' );
        }

        $results['ram'] = array();
        $pstat = Util::executeProgram( 'vmstat' );
        $lines = split( "\n", $pstat );
        
		for ( $i = 0; $i < sizeof( $lines ); $i++ ) 
		{
            $ar_buf = preg_split( "/\s+/", $lines[$i], 19 );

            if ( $i == 2 ) 
                $results['ram']['free'] = $ar_buf[5] * $pagesize / 1024;
        }

        $results['ram']['total']   = $s / 1024;
        $results['ram']['shared']  = 0;
        $results['ram']['buffers'] = 0;
        $results['ram']['used']    = $results['ram']['total'] - $results['ram']['free'];
        $results['ram']['cached']  = 0;
        $results['ram']['t_used']  = $results['ram']['used'];
        $results['ram']['t_free']  = $results['ram']['free'];
        $results['ram']['percent'] = round( ( $results['ram']['used'] *100 ) / $results['ram']['total'] );

        $pstat = Util::executeProgram( 'swapinfo', '-k' );
        $lines = split( "\n", $pstat );

        for ( $i = 0; $i < sizeof( $lines ); $i++ ) 
		{
            $ar_buf = preg_split( "/\s+/", $lines[$i], 6 );

            if ( $i == 0 ) 
			{
                $results['swap']['total'] = 0;
                $results['swap']['used']  = 0;
                $results['swap']['free']  = 0;
            } 
			else 
			{
                $results['swap']['total'] = $results['swap']['total'] + $ar_buf[1];
                $results['swap']['used']  = $results['swap']['used']  + $ar_buf[2];
                $results['swap']['free']  = $results['swap']['free']  + $ar_buf[3];
            }
        }
		
        $results['swap']['percent'] = round( ( $results['swap']['used'] * 100 ) / $results['swap']['total'] );
        return $results;
    }

	/**
	 * @access public
	 */
    function filesystems()
    {
        $df     = Util::executeProgram( 'df', '-k' );
        $mounts = split( "\n", $df );
        $fstype = array();
        $s      = Util::executeProgram( 'mount' );
        $lines  = explode( "\n", $s );

        $i = 0;
        while ( list(,$line) = each( $lines ) ) 
		{
            ereg( '(.*) \((.*)\)', $line, $a );
            $m = explode( ' ', $a[0] );
            $fsdev[$m[0]] = $a[2];
        }

        for ( $i = 1, $j = 0; $i < sizeof( $mounts ); $i++ ) 
		{
            $ar_buf = preg_split( "/\s+/", $mounts[$i], 6 );

            // skip the proc filesystem
            if ( $ar_buf[0] == 'procfs' ) 
                continue;

            $results[$j] = array();
            $results[$j]['disk']    = $ar_buf[0];
            $results[$j]['size']    = $ar_buf[1];
            $results[$j]['used']    = $ar_buf[2];
            $results[$j]['free']    = $ar_buf[3];
            $results[$j]['percent'] = $ar_buf[4];
            $results[$j]['mount']   = $ar_buf[5];
            
			( $fstype[$ar_buf[5]] )? $results[$j]['fstype'] = $fstype[$ar_buf[5]] : $results[$j]['fstype'] = $fsdev[$ar_buf[0]];
            $j++;
        }
		
        return $results;
    }

	/**
	 * @abstract
	 */	
	function get_sys_ticks()
	{
		return PEAR::raiseError( "Method not implemented." );
	}
	
	/**
	 * @abstract
	 */	
	function vhostname()
	{
		return PEAR::raiseError( "Method not implemented." );
	}
	
	/**
	 * @abstract
	 */	
	function network()
	{
		return PEAR::raiseError( "Method not implemented." );
	}
	
	/**
	 * @abstract
	 */	
	function sys_connections()
	{
		return PEAR::raiseError( "Method not implemented." );
	}
} // END OF SysInfo

?>
