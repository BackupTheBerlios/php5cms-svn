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
 * @package sys_shm
 */
 
class SharedMemory extends PEAR
{
	/**
	 * maximum supported sessions
	 * @access public
	 */
	var $max_sessions;
	
	/**
	 * key of shared memory segment (unique)
	 * @access public
	 */
	var $shm_key;
	
	/**
	 * size in bytes
	 * @access public
	 */
	var $shm_size;
	
	/**
	 * our shared memory handle
	 * @access public
	 */
	var $shmid;
	
	/**
	 * our semaphore handle
	 * @access public
	 */
	var $semid;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SharedMemory()
	{
		$this->max_sessions = 500;
		$this->shm_key      = 900000;
		$this->shm_size     = 131072;
	}
	
	
	/**
	 * @access public
	 */
	function extract( $id )
	{
		return substr( $id, 0, strpos( $id, "_" ) );
	}

	/**
	 * @access public
	 */	
	function ac_start()
	{
		$this->shmid = shm_attach( $this->shm_key, $this->shm_size, 0600 );
	}

	/**
	 * @access public
	 */
	function ac_get_lock()
	{
		$this->semid = sem_get( $this->shm_key + 1 );
		sem_acquire( $this->semid );
	}

	/**
	 * @access public
	 */
	function ac_release_lock()
	{
		shm_detach( $this->shmid );
		sem_release( $this->semid );
	}

	/**
	 * @access public
	 */
	function ac_newid( $str, $name )
	{
		for ( $i = 1; $i <= $this->max_sessions && ( @shm_get_var( $this->shmid, $i ) != false ); $i++ );
			$id = $i."_".$str;
		
		$this->ac_store( $id, $name, "" );
		return $id;
	}

	/**
	 * @access public
	 */
	function ac_store( $id, $name, $str )
	{
		$val = "$id;" . urlencode( $name ) . ";" . urlencode( $str ) . ";" . time();
		shm_put_var( $this->shmid, $this->extract( $id ), $val );
		 
		return true;
	}

	/**
	 * @access public
	 */
	function ac_delete( $id, $name )
	{
		shm_remove_var( $this->shmid, $this->extract( $id ) );
	}

	/**
	 * @access public
	 */
	function ac_gc( $gc_time, $name )
	{
		$cmp = time() - $gc_time * 60;
		
		for ( $i = 1; $i <= $this->max_sessions; $i++ )
		{
			if ( ( $val = @shm_get_var( $this->shmid, $i ) ) != false )
			{
				$dat = explode( ";", $val );
				
				if ( $name == $dat[1] && strcmp( $dat[3], $cmp ) < 0 )
					shm_remove_var( $this->shmid, $i );
			}
		}
	}

	/**
	 * @access public
	 */
	function ac_get_value( $id, $name )
	{
		$i   = $this->extract( $id );
		$var = shm_get_var( $this->shmid, $i );
		
		if ( $var == "" )
			return( "" );
			
		$dat = explode( ";", $var );
		
		// if classname or md5 id does not match...
		if ( $name != urldecode( $dat[1] ) || $dat[0] != $id )
			return PEAR::raiseError( "Security stop: Classname or md5 id does not match..." );
			
		return urldecode( $dat[2] );
	}
} // END OF SharedMemory

?>
