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
 * Emulate unix tail -f functionality.
 *
 * @package io
 */

class TailFile extends PEAR
{
	/**
	 * Frequency of file checking
	 * @access public
	 */
	var $interval_sec = 1;
	
	/**
	 * Buffer for line size
	 * @access public
	 */
	var $linesize = 1024;

	/**
	 * Is current size of tailed file
	 * @access public
	 */
	var $filesize = 0;

	/**
	 * Last file size if tailed file
	 * @access public
	 */
	var $lastfilesize = 0;
	
	/**
	 * Updated data, if tailed file changed
	 * @access public
	 */
	var $data = '';
	
	/**
	 * Name of the tailed file
	 * @access public
	 */
	var $filename = '';

	/**
	 * Flag for class
	 * @access public
	 */
	var $not_done = 0;
	
	/**
	 * Flag to Destroys $data
	 * @access public
	 */
	var $flushdata = 0;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function TailFile( $filename )
	{
		if ( file_exists( $filename ) )
			$this->not_done = 1;
		
		$this->filename = $filename;
	}


	/**
	 * @access public
	 */
	function checkUpdates()
	{
		if ( $this->not_done )
		{
			$this->lastfilesize	= $this->filesize;
			$this->filesize		= filesize($this->filename);
     
			if ( $this->filesize <> $this->lastfilesize )
			{
				if ( $fd = fopen( $this->filename, "r" ) )
				{
					fseek( $fd, intval( $this->lastfilesize ) - intval( $this->filesize ) - 1, SEEK_END );
					$i = 0;
					
					while ( !( feof( $fd ) ) && ( intval( $this->lastfilesize ) > 0 ) )
						$this->data .= fgets( $fd, $this->linesize );

					fclose( $fd );
				}
				else
				{
					$this->end();
				}
			}
			
			clearstatcache();
		}
	}

	/**
	 * @access public
	 */
	function wait( $interval = 0 )
	{
		if ( $interval )
			$this->interval_sec = $interval;
		
		sleep( $this->interval_sec );
	}

	/**
	 * Checks to see if the tail file is open.
	 *
	 * @access public
	 */
	function isOpen()
	{
		return $this->not_done;
	}

	/**
	 * Clears the data variable.
	 *
	 * @access public
	 */
	function flushData()
	{
		$this->data = '';
	}

	/**
	 * Returns the current results of data.
	 * Flushes the data variable.
	 *
	 * @access public
	 */
	function getResults()
	{
		$results = $this->data;
		$this->flushData();

		if ( strlen( $results ) > 0 )
			return $results;
		else
			return false;
	}

	/**
	 * Initiates the ending sequence for shutdown.
	 *
	 * @access public
	 */
	function end()
	{
		$this->data     = '';
		$this->not_done = 0;
	}
} // END OF TailFile

?>
