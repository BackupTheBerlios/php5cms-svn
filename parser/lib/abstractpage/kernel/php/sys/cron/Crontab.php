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
 * Create cron jobs with ease.
 *
 * Usage:
 *
 * $crontab = new Crontab( "/www/test/cron/", "filename" );
 * $crontab->setDateParams( 5, 10, 5, 5, "*" );
 * $crontab->setCommand( "curl http://www.mysite.com/?action=do_me" );
 * $crontab->saveCronFile();
 * $crontab->addToCrontab();
 * $crontab->destroyFilePoint(); // OPTIONAL
 *
 * @package sys_cron
 */
 
class Crontab extends PEAR
{
	/**
	 * @access public
	 */
	var $minute = null;
	
	/**
	 * @access public
	 */
	var $hour = null;
	
	/**
	 * @access public
	 */
	var $day = null;
	
	/**
	 * @access public
	 */
	var $month = null;
	
	/**
	 * @access public
	 */
	var $dayofweek = null;
	
	/**
	 * @access public
	 */
	var $command = null;
	
	/**
	 * @access public
	 */
	var $directory = null;
	
	/**
	 * @access public
	 */
	var $crontabPath = null;
	
	/**
	 * @access public
	 */
	var $handle = null;
	
	/**
	 * @access public
	 */
	var $filename = "crons";
	
		
	/**
	 * Constructor. Attempts to create directory for
	 * holding cron jobs
	 *
	 * @param	string	$dir		 Directory to hold cron job files
	 * @param	string	$filename	 Filename to write to
	 * @param	string	$crontabPath Path to cron program
	 * @access	public
	 */
	function Crontab( $dir = null, $filename = null, $crontabPath = null )
	{
		$result = ( !$dir )? $this->setDirectory( "~/my_crontabs" ) : $this->setDirectory( $dir );
		
		if ( !$result )
		{
			$this = new PEAR_Error( "Directory error." );
			return;
		}
		
		$result = ( !$filename )? $this->createCronFile( "crons" ) : $this->createCronFile( $filename );
		
		if ( !$result )
		{
			$this = new PEAR_Error( "File error." );
			return;	
		}
		$this->pathToCrontab = ( $crontabPath )? null : $crontabPath;
	}
		
		
	/**
	 * Set date parameters.
	 *
	 * If any parameters are left null then they default to *
	 *
	 * A hyphen (-) between integers specifies a range of integers. For
	 * example, 1-4 means the integers 1, 2, 3, and 4.
	 *
	 * A list of values separated by commas (,) specifies a list. For
	 * example, 3, 4, 6, 8 indicates those four specific integers.
	 *
	 * The forward slash (/) can be used to specify step values. The value
	 * of an integer can be skipped within a range by following the range
	 * with /<integer>. For example, 0-59/2 can be used to define every other
	 * minute in the minute field. Step values can also be used with an asterisk.
	 * For instance, the value * /3 (no space) can be used in the month field to run the
	 * task every third month...
	 *
	 * @param	mixed	$min		Minute(s)... 0 to 59
	 * @param	mixed	$hour		Hour(s)... 0 to 23
	 * @param	mixed	$day		Day(s)... 1 to 31
	 * @param	mixed	$month		Month(s)... 1 to 12 or short name
	 * @param	mixed	$dayofweek	Day(s) of week... 0 to 7 or short name. 0 and 7 = sunday
	 * $access	public
	 */
	function setDateParams( $min = null, $hour = null, $day = null, $month = null, $dayofweek = null )
	{		
		if ( $min == "0" )
			$this->minute = 0;
		else if ( $min )
			$this->minute = $min;
		else
			$this->minute = "*";
			
		if ( $hour == "0" )
			$this->hour = 0;
		elseif($hour)
			$this->hour = $min;
		else
			$this->hour = "*";
		
		$this->month     = ( $month     )? $month     : "*";
		$this->day       = ( $day       )? $day       : "*";
		$this->dayofweek = ( $dayofweek )? $dayofweek : "*";	
	}
		
	/**
	 * Set the directory path. Will check it if it exists then
	 * try to open it. Also if it doesn't exist then it will try to
	 * create it, makes it with mode 0700.
	 *
	 * @param	string	$directory	Directory, relative or full path
	 * @access	public
	 * @return	boolean
	 */
	function setDirectory( $directory )
	{
		if ( !$directory ) 
			return false;
			
		if ( is_dir( $directory ) )
		{
			if ( $dh = opendir( $directory ) )
			{
				$this->directory = $directory;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if ( mkdir( $directory, 0700 ) )
			{
				$this->directory = $directory;
				return true;
			}
		}
		
		return false;
	}
		
	/**
	 * Create cron file
	 *
	 * This will create a cron job file for you and set the filename
	 * of this class to use it. Make sure you have already set the directory
	 * path variable with the consructor. If the file exists and we can write
	 * it then return true esle false. Also sets $handle with the resource handle
	 * to the file
	 *
	 * @param	string	$filename	Name of file you want to create
	 * @access	public
	 * @return	boolean
	 */
	function createCronFile( $filename = null )
	{
		if ( !$filename )
			return false;
			
		if ( file_exists( $this->directory . $filename ) )
		{
			if ( $handle = fopen( $this->directory . $filename, 'a' ) )
			{
				$this->handle   = &$handle;
				$this->filename =  $filename;
				
				return true;
			}
			else
			{
				return false;
			}
		}
			
		if ( !$handle = fopen( $this->directory . $filename, 'a' ) )
		{
			return false;
		}
		else
		{
			$this->handle   = &$handle;
			$this->filename =  $filename;
			
			return true;
		}
	}
		
	/**
	 * Set command to execute.
	 *
	 * @param	string	$command	Command to set
	 * @access	public
	 * @return	string	$command
	 */
	function setCommand( $command )
	{
		if ( $command )
		{
			$this->command=$command;
			return false;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Write cron command to file. Make sure you used createCronFile
	 * before using this function of it will return false
	 *
	 * @access	public
	 * @return	void
	 */
	function saveCronFile()
	{
		$command = $this->minute . " " . $this->hour . " " . $this->day . " " . $this->month . " " . $this->dayofweek . " " . $this->command . "\n";
		
		if ( !fwrite( $this->handle, $command ) )
			return true;
		else
			return false;
	}
			
	/**
	 *	Save cron in system.
	 *
	 *	@access	public
	 *	@return boolean				true if successful else false
	 */
	function addToCrontab()
	{		
		if ( !$this->filename )
			return PEAR::raiseError( "No name specified for cron file." );
						
		if ( exec( $crontabPath . "crontab " . $this->directory . $this->filename ) )
			return true;
		else
			return false;
	}
		
	/**
	 *	Destroy file pointer.
	 *
	 *	@access	public
	 *	@return void
	 */
	function destroyFilePoint()
	{
		fclose( $this->handle );
		return true;
	}
} // END OF Crontab
		
?>
