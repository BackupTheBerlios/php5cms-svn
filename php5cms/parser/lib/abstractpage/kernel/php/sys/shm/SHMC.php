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


define( "SHMC_SHARED_LOCK",    1 );
define( "SHMC_EXCLUSIVE_LOCK", 2 );
define( "SHMC_RELEASE_LOCK",   3 );


/**
 * Shared Memory Connectivity Class
 *
 * This class can be used to easily handle shared memory including semaphore locking.
 *
 * @package sys_shm
 */

class SHMC extends PEAR
{
	/**
	 * Constructor
	 *
	 * Create a new shared memory connectivity.
 	 *
	 * @param	integer	$shm_key	unique key for the shared memory
	 * @param	integer	$shm_size	size of the shared memory in bytes
	 * @param	integer $sem_key	unique key for the semaphore that is used for locking
	 * @param	integer $perm		permissions
	 */
	function SHMC( $shm_key, $shm_size, $sem_key, $perm = 438 )
	{
		$this->shm_key	= $shm_key;
		$this->shm_size	= $shm_size;
		$this->sem_key	= $sem_key;

		// get ID to access Semaphore for locking shared memory
		if ( !$this->sem_id	= sem_get( $sem_key, 1, $perm ) )
		{
			$this = new PEAR_Error( "Could not get semaphore handle." );
			return;
		}
		
		// get ID to access shared memory, if called the first time attach the memory
		if ( !$this->shm_id	= shm_attach( $shm_key, $shm_size, $perm ) )
		{
			$this = new PEAR_Error( "Could not attach shared memory." );
			return;
		}
	}


	/**
	 * Lock or unlock the shared memory.
	 *
	 * @access	public
	 * @param	integer	$operation	2 = exclusive lock, 3 = free lock
	 */
	function lock( $operation )
	{
		switch ( $operation )
		{
			case SHMC_EXCLUSIVE_LOCK:
				sem_acquire( $this->sem_id );
				break;
				
			case SHMC_RELEASE_LOCK:
				sem_release( $this->sem_id );
				break;
		}
	}

	/**
	 * Write a variable to the shared memory
	 *
	 * @access	public
	 * @param	integer	$key	unique key for the variable
	 * @param	mixed	$value	value of the variable
	 */
	function put_var( $key, $value )
	{
		shm_put_var( $this->shm_id, $key, $value );
	}

	/**
	 * Read a variable from the shared memory.
	 *
	 * @access	public
	 * @param	integer	$key	unique key for the variable
	 * @return	mixed	$value	value of the variable
	 */
	function get_var( $key )
	{
		return @shm_get_var( $this->shm_id, $key );
	}

	/**
	 * Remove a variable from the shared memory.
	 *
	 * @access	public
	 * @param	integer	$key	unique key for the variable
	 */
	function remove_var( $key )
	{
		return @shm_remove_var( $this->shm_id, $key );
	}

	/**
	 * Disconnect from the shared memory.
	 *
	 * @access	public
	 */
	function detach()
	{
		shm_detach( $this->shm_id );	
	}

	/**
	 * Remove (clear) shared memory.
	 *
	 * @access	public
	 */
	function remove()
	{
		shm_remove( $this->shm_key );	
	}
} // END OF SHMC

?>
