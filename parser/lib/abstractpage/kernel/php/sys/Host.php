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
 * @package sys
 */
 
class Host extends PEAR
{
	/**
	 * @access public
	 */
	var $path_mounts;
	
	/**
	 * @access public
	 */
	var $path_cpuinfo;
	
	/**
	 * @access public
	 */
	var $path_uptime;
	
	/**
	 * @access public
	 */
	var $path_loadavg;
	
	/**
	 * @access public
	 */
	var $path_version;
	
	/**
	 * @access public
	 */
	var $path_dev;
	
	/**
	 * @access public
	 */
	var $path_meminfo;
	
	/**
	 * @access public
	 */
	var $path_scsi;
	
	/**
	 * @access public
	 */
	var $path_ide;
	
	/**
	 * @access public
	 */
	var $path_pci;
	
	/**
	 * @access public
	 */
	var $path_hostname;
	
	/**
	 * @access public
	 */
	var $path_uptime;
	
	/**
	 * @access public
	 */
	var $path_status;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Host()
	{
		$this->path_mounts   = ap_ini_get( "file_mounts",   "file" );
		$this->path_cpuinfo  = ap_ini_get( "file_cpuinfo",  "file" );
		$this->path_uptime	 = ap_ini_get( "file_uptime",   "file" );
		$this->path_loadavg	 = ap_ini_get( "file_loadavg",  "file" );
		$this->path_version	 = ap_ini_get( "file_version",  "file" );
		$this->path_dev		 = ap_ini_get( "file_dev",      "file" );
		$this->path_meminfo	 = ap_ini_get( "file_meminfo",  "file" );
		$this->path_scsi	 = ap_ini_get( "file_scsi",     "file" );
		$this->path_ide		 = ap_ini_get( "file_ide",      "file" );
		$this->path_pci		 = ap_ini_get( "file_pci",      "file" );
		$this->path_hostname = ap_ini_get( "file_hostname", "file" );
		$this->path_uptime	 = ap_ini_get( "file_uptime",   "file" );
		$this->path_status   = ap_ini_get( "file_status",   "file" );
	}
	

	/**
	 * Returns or prints out the memory usage for the current script 
	 * (only work in unixey systems).
	 *
	 * @access public
	 */
	function mem_check() 
	{
		global $MEM_CHECK_LAST_HIT;

		$f = fopen( $this->path_status, "r" );
		while( $line = fgets( $f, 1024 ) ) 
		{
			list( $k, $v ) = explode( ":", $line );
			
			if ( $k == "VmSize" ) 
			{
				$mem = trim( $v );
				break;
			}
		}
	
		fclose( $f );
		$hit = (int)$mem;
	
		if ( $MEM_CHECK_LAST_HIT ) 
		{
			if ( $hit > $MEM_CHECK_LAST_HIT )
				$mem .= " (+ " . ( $hit - $MEM_CHECK_LAST_HIT ) . ")";
		
			if ( $hit < $MEM_CHECK_LAST_HIT )
				$mem .= " (- " . ( $MEM_CHECK_LAST_HIT - $hit ) . ")";
		}
	
		$MEM_CHECK_LAST_HIT = $hit;
		return $mem;
	}

	/**
	 * Usage
	 *
	 * echo $obj->checkdaemon( "mysqld", "mysql daemon" ); 
	 * echo $obj->checkdaemon( "httpd",  "httpd daemon" );
	 *
	 * @access public
	 */
	function checkdaemon( $daemon, $name )
	{ 
		$ps     = "ps ax | grep $daemon | wc -l"; 
		$origps = exec( $ps ); 
		$minone = $origps - 2;
	 
  		if ( $minone < 1 )
			$dataps = "only $minone daemon for $daemon, $name TOTALY DOWN";
 
  		if ( $minone == 1 )
			$dataps = "up only with $minone $daemon daemon"; 

  		if ( $minone > 1 )
			$dataps = "up with $minone $daemon daemons";
	
		return $dataps; 
	}
	
	/**
	 * Example
	 *
	 * echo $obj->uptime( "Server up for %days days, %hours hours, %mins minutes and %secs seconds" );
	 * You can also check if days is set, just use this:
	 *
	 * if ( $obj->uptime( "%days" ) == "" )
	 *		echo( $obj->uptime( "Up for only %hours" );
	 * else if ( $obj->uptime( "%days" ) > 30 )
	 * 		echo( $obj->uptime( "Up for %days days!! wohoo!" );
	 * else
	 *		echo( $obj->uptime( "Up for %days." );
	 *
	 * @access public
	 */
	function uptime( $string )
	{
		global $_uptime;

		if ( !empty( $GLOBALS["uptime"] ) )
			$string = uptime;
 
 		if ( !is_array( $_uptime ) )
		{
  			if ( !$this->uptime_init() )
				return false;
 		}
 
 		return( str_replace( "%days", $_uptime["days"], str_replace( "%hours", $_uptime["hours"], str_replace( "%mins", $_uptime["mins"], str_replace( "%secs", $_uptime["secs"], $string ) ) ) ) );
	}

	/**
	 * @access public
	 */
	function uptime_init()
	{
		global $_uptime;
	
		$fp = fopen( $this->path_uptime, "r" );
 
 		if ( !$fp )
			return false;
 
 		$text = fgets( $fp, 100 );
 		fclose( $fp );
 		$uptime = substr( $text, 0, strpos( $text, " " ) );
		
 		$_uptime["days"]  = floor(   $uptime / 86400 );
 		$_uptime["hours"] = floor( ( $uptime - ( $_uptime["days"] * 86400 ) ) / 3600 );
 		$_uptime["mins"]  = floor( ( $uptime - ( $_uptime["days"] * 86400 ) - ( $_uptime["hours"] *  3600 ) ) / 60 );
 		$_uptime["secs"]  = floor(   $uptime - ( $_uptime["mins"] *    60 ) - ( $_uptime["days"]  * 86400 ) - ( $_uptime["hours"] * 3600 ) );

		return true;
	}
	
	/**
	 * Check which kind of WebServer is running (e.g. NS Fasttrack, Apache etc.).
	 *
	 * @access public
	 */
	function getServer( $ServerURL )  
	{ 
		$filepointer = fsockopen( $ServerURL, 80, &$errno, &$errstr ); 

		if ( !$filepointer )
		{
  			$WebServer = "Error: $errstr ($errno)\n"; 
  		}
		else  
  		{	 
    		fputs( $filepointer, "GET / HTTP/1.0\n\n" ); 
    
			while( !feof( $filepointer ) )  
    		{ 
      			$WebServer = fgets( $filepointer, 4096 ); 
      
	  			if ( ereg( "^Server:", $WebServer ) )  
      			{ 
        			$WebServer = trim( ereg_replace( "^Server:", "", $WebServer ) ); 
        			break; 
      			} 
    		} 
    
			fclose( $filepointer ); 
  		} 

		return( $WebServer ); 
	}

	/**
	 * Note: "du" is Linux stuff.
	 *
	 * @param  string  $user_home  e.g. /home/docuverse/
	 * @access public
	 */
	function getAvailableDiscspace( $user_home = '', $available_space = 0 )
	{
		// split "/" at the end
		$user_home = substr( $user_home, 0, strlen( $user_home ) - 1 );
		
		// this will output something like "3383    /home/user"
		exec( "du -s $user_home", $du );

		$used      = split( " ", $du[0] );
		$used      = $used[0] / 1024;
		$remaining = $available_space - $used;

		/*
		$p = $available_space / 100;
		$p_used = round( $used / $p );
		$p_remaining = round( $remaining / $p );
		*/
	
		return $remaining;
	}
	
	
	// System Information

	/**
	 * Returns the virtual hostname accessed.
	 *
	 * @access public
	 */
	function sys_vhostname()
	{
		if ( !( $result = $_SERVER["SERVER_NAME"] ) )
			$result = "N.A.";

		return $result;
	}

	/**
	 * Returns the Cannonical machine hostname.
	 *
	 * @access public
	 */
	function sys_chostname()
	{
		if ( $fp = fopen( $this->path_hostname, 'r' ) )
		{
			$result = trim( fgets( $fp, 4096 ) );
			fclose( $fp );
			$result = gethostbyaddr( gethostbyname( $result ) );
		}
		else
		{
			$result = "N.A.";
		}

		return $result;
	}

	/**
	 * Returns the IP address that the request was made on.
	 *
	 * @access public
	 */
	function sys_ip_addr()
	{
		if ( !( $result = getenv( 'SERVER_ADDR' ) ) )
			$result = gethostbyname( $this->sys_chostname() );

		return $result;
	} 

	/**
	 * Returns an array of all meaningful devices on the PCI bus.
	 *
	 * @access public
	 */
	function sys_pcibus()
	{
		$results = array();

		if ( $fd = fopen( $this->path_pci, "r" ) )
		{
			while ( $buf = fgets( $fd, 4096 ) )
			{
				if ( preg_match( "/Bus/", $buf ) )
				{
					$device = 1;
					continue;
				} 

				if ( $device )
				{ 
					list( $key, $value ) = split( ": ", $buf, 2 );
	
					if ( !preg_match( "/bridge/i", $key ) && !preg_match( "/USB/i", $key ) )
						$results[] = preg_replace("/\([^\)]+\)\.$/", "", trim( $value ) );
				
					$device = 0;
				}
			}
		} 

		return $results;
	}

	/**
	 * Returns an array of all ide devices attached
	 * to the system, as determined by the aliased
	 * shortcuts in /proc/ide
	 *
	 * @access public
	 */
	function sys_idebus()
	{
		$results = array();
		$handle  = opendir( $this->path_ide );

		while ( $file = readdir( $handle ) )
		{
			if ( preg_match( "/^hd/", $file ) )
			{ 
				$results["$file"] = array();

				if ( $fd = fopen( "/proc/ide/$file/model", "r" ) )
				{
					$results["$file"]["model"] = trim( fgets( $fd, 4096 ) );
					fclose( $fd );
				}
			
				if ( $fd = fopen( "/proc/ide/$file/capacity", "r" ) )
				{
					$results["$file"]["capacity"] = trim( fgets( $fd, 4096 ) );
					fclose( $fd );
				}
			}
		}

		closedir( $handle ); 
		return $results;
	}

	/**
	 * Returns an array of all meaningful devices 
	 * on the SCSI bus.
	 *
	 * @access public
	 */
	function sys_scsibus()
	{
		$results	= array();
		$dev_vendor = "";
		$dev_model 	= "";
		$dev_rev 	= "";
		$dev_type 	= "";

		if ( $fd = fopen( $this->path_scsi, "r" ) )
		{
			while ( $buf = fgets( $fd, 4096 ) )
			{
				if ( preg_match( "/Vendor/", $buf ) )
				{
					preg_match( "/Vendor: (.*) Model: (.*) Rev: (.*)/i", $buf, $dev );
					list( $key, $value ) = split( ": ", $buf, 2 );
					
					$dev_str  = $value;
					$get_type = 1;
	
					continue;
				} 

				if ( $get_type )
				{ 
					preg_match( "/Type:\s+(\S+)/i", $buf, $dev_type );
					
					$results[] = "$dev[1] $dev[2] ( $dev_type[1] )";
					$get_type  = 0;
				}
			}
		} 

		return $results;
	}

	/**
	 * Returns an associative array of two associative
	 * arrays, containg the memory statistics for RAM and swap.
	 *
	 * @access public
	 */
	function sys_meminfo()
	{
		if ( $fd = fopen( $this->path_meminfo, "r" ) )
		{
			while ( $buf = fgets( $fd, 4096 ) )
			{
				if ( preg_match( "/Mem:\s+(.*)$/", $buf, $ar_buf ) )
				{
					$ar_buf = preg_split( "/\s+/", $ar_buf[1], 6 );

					$results['ram'] = array();
					$results['ram']['total']	= $ar_buf[0] / 1024;
					$results['ram']['used']  	= $ar_buf[1] / 1024;
					$results['ram']['free'] 	= $ar_buf[2] / 1024;
					$results['ram']['shared'] 	= $ar_buf[3] / 1024;
					$results['ram']['buffers'] 	= $ar_buf[4] / 1024;
					$results['ram']['cached'] 	= $ar_buf[5] / 1024;
	
					$results['ram']['t_used'] 	= $results['ram']['used']  - $results['ram']['cached'] - $results['ram']['buffers'];
					$results['ram']['t_free'] 	= $results['ram']['total'] - $results['ram']['t_used'];
					$results['ram']['percent'] 	= round( ($results['ram']['t_used'] * 100) / $results['ram']['total']);
				}

				if ( preg_match("/Swap:\s+(.*)$/", $buf, $ar_buf ) )
				{
					$ar_buf = preg_split("/\s+/", $ar_buf[1], 3);

					$results['swap'] = array();

					$results['swap']['total']	= $ar_buf[0] / 1024;
					$results['swap']['used'] 	= $ar_buf[1] / 1024;
					$results['swap']['free'] 	= $ar_buf[2] / 1024;
					$results['swap']['percent'] = round( ($ar_buf[1] * 100) / $ar_buf[0] );
	
					break;
				}
			}
		
			fclose( $fd );
		}
		else
		{
			$results['ram']  = array();
			$results['swap'] = array();
		}

		return $results;
	}

	/**
	 * Returns an array of all network devices 
	 * and their tx/rx stats.
	 *
	 * @access public
	 */
	function sys_netdevs()
	{
		$results = array();

		if ( $fd = fopen( $this->path_dev, "r" ) )
		{
			while ( $buf = fgets( $fd, 4096 ) )
			{
				if ( preg_match( "/:/", $buf ) )
				{
					list( $dev_name, $stats_list ) = preg_split( "/:/", $buf, 2 );
					$stats = preg_split( "/\s+/", trim( $stats_list ) );

					$results[$dev_name] = array();
					$results[$dev_name]['rx_bytes']		= $stats[0];
					$results[$dev_name]['rx_packets'] 	= $stats[1];
					$results[$dev_name]['rx_errs']		= $stats[2];
					$results[$dev_name]['rx_drop'] 		= $stats[3];
					$results[$dev_name]['tx_bytes'] 	= $stats[8];
					$results[$dev_name]['tx_packets'] 	= $stats[9];
					$results[$dev_name]['tx_errs'] 		= $stats[10];
					$results[$dev_name]['tx_drop'] 		= $stats[11];
					$results[$dev_name]['errs'] 		= $stats[2] + $stats[10];
					$results[$dev_name]['drop'] 		= $stats[3] + $stats[11];
				}
			}
		} 

		return $results;
	}

	/**
	 * Returns a string equivilant to `uname --release`.
	 *
	 * @access public
	 */
	function sys_kernel()
	{
		if ( $fd = fopen( $this->path_version, "r" ) )
		{
			$buf = fgets( $fd, 4096 );
			fclose( $fd );

			if ( preg_match( "/version (.*?) /", $buf, $ar_buf ) )
			{
				$result = $ar_buf[1];

				if ( preg_match( "/SMP/", $buf ) )
					$result .= " (SMP)";	
			}
			else
			{
				$result = "N.A.";
			}
		}
		else
		{
			$result = "N.A.";
		}

		return $result;
	}

	/**
	 * Returns a 1x3 array of load avg's in
	 * standard order and format.
	 *
	 * @access public
	 */
	function sys_loadavg()
	{
		if ( $fd = fopen( $this->path_loadavg, "r" ) )
		{
			$results = split( " ", fgets( $fd, 4096 ) );
			fclose( $fd );
		}
		else
		{
			$results = array( "N.A.", "N.A.", "N.A." );
		}

		return $results;
	}

	/**
	 * Returns a formatted english string,
	 * enumerating the uptime verbosely.
	 *
	 * @access public
	 */
	function sys_uptime()
	{
		$fd     = fopen( $this->path_uptime, "r" );
		$ar_buf = split( " ", fgets( $fd, 4096 ) );
		
		fclose( $fd );

		$sys_ticks = trim( $ar_buf[0] );
		
		$min   = $sys_ticks / 60;
		$hours = $min / 60;
		$days  = floor( $hours / 24 );
		$hours = floor( $hours - ( $days * 24 ) );
		$min   = floor( $min - ( $days * 60 * 24 ) - ( $hours * 60 ) );
    
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
	function linuxUptime()
	{
		$ut    = strtok( exec( "cat $this->path_uptime" ), "." );
		$days  = sprintf( "%2d", (   $ut / ( 3600 * 24 ) ) );
		$hours = sprintf( "%2d", ( ( $ut % ( 3600 * 24 ) ) / 3600 )  );
		$min   = sprintf( "%2d", (   $ut % ( 3600 * 24 ) % 3600 ) / 60 );
		$sec   = sprintf( "%2d", (   $ut % ( 3600 * 24 ) % 3600 ) % 60 );
		
		return array( $days, $hours, $min, $sec );
	}

	/**
	 * Returns the number of users currently logged in.
	 *
	 * @access public
	 */
	function sys_users()
	{
		$result = trim( `who | wc -l` );
		return $result;
	}

	/**
	 * Returns an associative array containing all
	 * relevant info about the processors in the system.
	 *
	 * @access public
	 */
	function sys_cpu()
	{
		$results = array();
   	 	$ar_buf  = array();
	
		if ( $fd = fopen( $this->path_cpuinfo, "r" ) )
		{
			while ( $buf = fgets( $fd, 4096 ) )
			{
				list( $key, $value ) = preg_split( "/\s+:\s+/", trim( $buf ), 2 );
	
				// All of the tags here are highly architecture dependant.
				// The only way I could reconstruct them for machines I don't
				// have is to browse the kernel source.
				switch ( $key )
				{
					case "model name" :
						$results['model'] = $value;
						break;
					
					case "cpu MHz" :
						$results['mhz'] = sprintf( "%.2f", $value );
						break;
					
					case "clock" : // for PPC arch (damn borked POS)
						$results['mhz'] = sprintf( "%.2f", $value );
						break;
					
					case "cpu" : // for PPC arch (damn borked POS)
						$results['model'] = $value;
						break;
					
					case "revision": // for PPC arch (damn borked POS)
						$results['model'] .= " ( rev: " . $value . ")";
						break;
					
					case "cache size" :
						$results['cache'] = $value;
						break;
					
					case "bogomips" :
						$results['bogomips'] += $value;
						break;
					
					case "processor" :
						$results['cpus'] += 1;
						break;
				}	
			}
		
			fclose( $fd );
		}

    	$keys = $this->compat_array_keys( $results );
		
    	$keys2be = array(
			"model",
			"mhz",
			"cache",
			"bogomips",
			"cpus"
		);

	    while ( $ar_buf = each( $keys2be ) )
		{
        	if ( !$this->compat_in_array( $ar_buf[1], $keys ) )
				$results[$ar_buf[1]] = 'N.A.';
    	}

		return $results;
	}

	/**
	 * Returns an array of associative arrays
	 * containing information on every mounted partition.
	 *
	 * @access public
	 */
	function sys_fsinfo()
	{
		$df     = `/bin/df -kP`;
		$mounts = split( "\n", $df );
		$fstype = array();

		if ( $fd = fopen( $this->path_mounts, "r" ) )
		{
			while ( $buf = fgets( $fd, 4096 ) )
			{
				list( $dev, $mpoint, $type ) = preg_split( "/\s+/", trim( $buf ), 4 );
				
				$fstype[$mpoint] = $type;
				$fsdev[$dev]     = $type;
			}
		
			fclose( $fd );
		}

		for ( $i = 1; $i < sizeof( $mounts ) - 1; $i++ )
		{
			$ar_buf = preg_split( "/\s+/", $mounts[$i], 6 );

			$results[$i - 1] = array();
			$results[$i - 1]['disk']    = $ar_buf[0];
			$results[$i - 1]['size']    = $ar_buf[1];
			$results[$i - 1]['used']    = $ar_buf[2];
			$results[$i - 1]['free']    = $ar_buf[3];
			$results[$i - 1]['percent'] = $ar_buf[4];
			$results[$i - 1]['mount']   = $ar_buf[5];
			
			( $fstype[$ar_buf[5]] )? $results[$i - 1]['fstype'] = $fstype[$ar_buf[5]] : $results[$i - 1]['fstype'] = $fsdev[$ar_buf[0]];
		}

		return $results;
	}
	
	
	// helper methods
	
	/**
	 * @access public
	 */
	function compat_array_keys( $arr )
	{
		$result = array();

		while ( list( $key, $val ) = each( $arr ) )
			$result[] = $key;
	
		return $result;
	}

	/**
	 * @access public
	 */
	function compat_in_array( $value, $arr )
	{
		while ( list( $key, $val ) = each( $arr ) )
		{
			if ( $value == $val )
				return true;
		}
	
		return false;
	}

	/**
	 * A helper function, when passed a number representing KB,
	 * and optionally the number of decimal places required,
	 * it returns a formated number string, with unit identifier.
	 *
	 * @access public
	 */
	function format_bytesize( $kbytes, $dec_places = 2 )
	{   
		if ( $kbytes > 1048576 )
		{
			$result  = sprintf( "%." . $dec_places . "f", $kbytes / 1048576 );
			$result .= ' GB';
		}
		else if ( $kbytes > 1024 )
		{
			$result  = sprintf( "%." . $dec_places . "f", $kbytes / 1024 );
			$result .= ' MB';   
		} 
		else
		{
			$result  = sprintf( "%." . $dec_places . "f", $kbytes );
			$result .= ' KB';   
		}  
   
		return $result;
	}
} // END OF Host

?>
