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


using( 'sys.lib.SysInfo' );
using( 'util.Util' );


/**
 * @package sys_lib
 */
 
class SysInfo_NetBSD extends SysInfo
{
	/**
	 * @access public
	 */
    var $cpu_regexp;
	
	/**
	 * @access public
	 */
    var $scsi_regexp;


	/**
	 * Constructor
	 *
	 * @access public
	 */
    function SysInfo_NetBSD()
    {
		$this->os = "NetBSD";
		
        $this->cpu_regexp  = "^cpu.* @ (.*) MHz";
        $this->scsi_regexp = "^(.*) at scsibus.*: <(.*)> .*";
    }


	/**
	 * @access public
	 */	
    function get_sys_ticks()
    {
        $a = $this->grab_key( 'kern.boottime' );
        $sys_ticks = time() - $a;
        
		return $sys_ticks;
    }

	/**
	 * Get the pci device information out of dmesg.
	 *
	 * @access public
	 */
    function pci()
    {
        $results = array();

        for ( $i = 0; $i < count( $this->read_dmesg() ); $i++ ) 
		{
         	$buf = $this->dmesg[$i];
            
			if ( preg_match( '/(.*) at pci[0-9] (.*) "(.*)" (.*)$/', $buf, $ar_buf ) ) 
                $results[$i] = $ar_buf[1] . ": " . $ar_buf[3];
            else if ( preg_match( '/"(.*)" (.*).* at [.0-9]+ irq/', $buf, $ar_buf ) ) 
                $results[$i] = $ar_buf[1] . ": " . $ar_buf[2];
            
            sort( $results );
        }
		
        return $results;
    }

	/**
	 * @access public
	 */
    function network()
    {
        $netstat_b = Util::executeProgram( 'netstat', '-nbdi | cut -c1-25,44- | grep Link | grep -v \'* \'' );
        $netstat_n = Util::executeProgram( 'netstat', '-ndi  | cut -c1-25,44- | grep Link | grep -v \'* \'' );
        $lines_b   = split( "\n", $netstat_b );
        $lines_n   = split( "\n", $netstat_n );
        $results   = array();
        
		for ( $i = 0; $i < sizeof( $lines_b ); $i++ ) 
		{
            $ar_buf_b = preg_split( "/\s+/", $lines_b[$i] );
            $ar_buf_n = preg_split( "/\s+/", $lines_n[$i] );
			
            if ( !empty( $ar_buf_b[0] ) && !empty( $ar_buf_n[3] ) ) 
			{
                $results[$ar_buf_b[0]] = array();

                $results[$ar_buf_b[0]]['rx_bytes']   = $ar_buf_b[3];
                $results[$ar_buf_b[0]]['rx_packets'] = $ar_buf_n[3];
                $results[$ar_buf_b[0]]['rx_errs']    = $ar_buf_n[4];
                $results[$ar_buf_b[0]]['rx_drop']    = $ar_buf_n[8];

                $results[$ar_buf_b[0]]['tx_bytes']   = $ar_buf_b[4];
                $results[$ar_buf_b[0]]['tx_packets'] = $ar_buf_n[5];
                $results[$ar_buf_b[0]]['tx_errs']    = $ar_buf_n[6];
                $results[$ar_buf_b[0]]['tx_drop']    = $ar_buf_n[8];

                $results[$ar_buf_b[0]]['errs'] = $ar_buf_n[4] + $ar_buf_n[6];
                $results[$ar_buf_b[0]]['drop'] = $ar_buf_n[8];
            }
        }
		
        return $results;
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
            
			if ( preg_match( '/^(.*) at pciide[0-9] (.*): <(.*)>/', $buf, $ar_buf ) ) 
			{
                $s = $ar_buf[1];
                $results[$s]['model'] = $ar_buf[3];
                $results[$s]['media'] = 'Hard Disk';
                
				// now loop again and find the capacity
                for ( $j = 0; $j < count( $this->read_dmesg() ); $j++ ) 
				{
                    $buf_n = $this->dmesg[$j];
                    
					if ( preg_match( "/^($s): (.*), (.*), (.*)MB, .*$/", $buf_n, $ar_buf_n ) )
                        $results[$s]['capacity'] = $ar_buf_n[4] * 2048 * 1.049;
                }
            }
        }
		
        return $results;
    }
} // END OF SysInfo_NetBSD

?>
