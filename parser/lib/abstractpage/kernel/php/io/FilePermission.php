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
 * Purpose of this class is to handle file permissions.
 *
 * It enable to get human readding strings for example -rw-rw-rw- or 755
 * to set permissions by ugo string or and array and save them.
 *
 * different representations of a file permission
 *
 *  octal
 *
 *       0755
 *
 *  alpha or 'human readable'
 *
 *       -rw-rw-rw-
 *
 *  array. used inside class only. bits are represented
 *
 *       first dimension
 *               second dimension
 *
 *       ["owner"]
 *               ["read"]
 *               ["write"]
 *               ["execute"]
 *       ["group"]
 *               ["read"]
 *               ["write"]
 *               ["execute"]
 *       ["others"]
 *               ["read"]
 *               ["write"]
 *               ["execute"]
 *       ["bits"]
 *               ["user"]
 *               ["group"]
 *               ["sticky"]
 *
 * @package io
 */

class FilePermission extends PEAR
{
    /**
	 * Form variable name for permission array
	 * @access public
	 */
    var $form_array_name = 'fileperm';
	
	/**
	 * Current file on which operations are done
	 * @access public
	 */
    var $_current_file = '';
    
    /**
	 * Permissions extracted or to be written
	 * @access public
	 */
    var $_current_permission = '';
    

    /**
     * Set the working file.
	 *
	 * @access public
     */
    function setFile( $path )
    {
		$this->_current_file = $path;
        $this->_current_permission = '';
    }

    /**
     * Get permission for current file.
	 *
	 * @access public
     */
    function readPermissions()
    {
        if ( '' == $this->_current_file ) 
			return false;

        $this->_current_permission = fileperms( $this->_current_file );

        if ( '' == $this->_current_permission ) 
			return false; 
		else 
			return true;
	}

    /**
     * Returns a string used to represent permission in file explorers like -rw-rw-rw-
	 *
	 * @access public
     */
    function getPermissionAlpha() 
	{ 
		return $this->getHumanReadableString(); 
	}

    /**
     * Returns a string used to represent permission in file explorers like -rw-rw-rw-
	 *
	 * @access public
     */
    function getHumanReadableString()
    {
		$mode = $this->_current_permission;

		if ( $mode & 0x1000 ) 
			$type = 'p'; // FIFO pipe
        else if ( $mode & 0x2000 ) 
			$type = 'c'; // Character special
        else if ( $mode & 0x4000 ) 
			$type = 'd'; // Directory
        else if ( $mode & 0x6000 ) 
			$type = 'b'; // Block special
        else if ( $mode & 0x8000 ) 
			$type = '-'; // Regular
        else if ( $mode & 0xA000 ) 
			$type = 'l'; // Symbolic Link
        else if ( $mode & 0xC000 ) 
			$type = 's'; // Socket
        else 
			$type = 'u'; // UNKNOWN

        $owner["read"]     = ( $mode & 00400 )? 'r' : '-';
        $owner["write"]    = ( $mode & 00200 )? 'w' : '-';
        $owner["execute"]  = ( $mode & 00100 )? 'x' : '-';
        
		$group["read"]     = ( $mode & 00040 )? 'r' : '-';
        $group["write"]    = ( $mode & 00020 )? 'w' : '-';
        $group["execute"]  = ( $mode & 00010 )? 'x' : '-';
        
		$others["read"]    = ( $mode & 00004 )? 'r' : '-';
        $others["write"]   = ( $mode & 00002 )? 'w' : '-';
        $others["execute"] = ( $mode & 00001 )? 'x' : '-';

        if ( $mode & 0x800 ) 
			$owner["execute"]  = ( $owner[execute]  == 'x' )? 's' : 'S';
        
		if ( $mode & 0x400 ) 
			$group["execute"]  = ( $group[execute]  == 'x' )? 's' : 'S';
        
		if ( $mode & 0x200 ) 
			$others["execute"] = ( $others[execute] == 'x' )? 't' : 'T';

        return "$type$owner[read]$owner[write]$owner[execute]$group[read]$group[write]$group[execute]$others[read]$others[write]$others[execute]";
    }

    /**
     * Returns the octal representation of permissions like 0755.
	 *
	 * @access public
     */
    function getPermissionOctal()
	{ 
		return $this->getOctalString(); 
	}

    /**
     * Returns the octal representation of permissions like 0755.
	 *
	 * @access public
     */
    function getOctalString()
    {
        if ( '' == $this->_current_file ) 
			return false;
        
		$decperms   = fileperms( $this->_current_file );
        $octalperms = sprintf( "%o", $decperms );
        
		// -4 value should include ugo and strange bits for files and folders
        return substr( $octalperms, -4 );
    }

    /**
     * Returns an array of permissions.
	 *
	 * @access public
     */
    function getPermissionArray()
    {
        $mode = $this->_current_permission;

		if ( $mode & 0x1000 ) 
			$type = 'p'; // FIFO pipe
        else if ( $mode & 0x2000 ) 
			$type = 'c'; // Character special
        else if ( $mode & 0x4000 ) 
			$type = 'd'; // Directory
        else if ( $mode & 0x6000 ) 
			$type = 'b'; // Block special
        else if ( $mode & 0x8000 ) 
			$type = '-'; // Regular
        else if ( $mode & 0xA000 ) 
			$type = 'l'; // Symbolic Link
        else if ( $mode & 0xC000 ) 
			$type = 's'; // Socket
        else 
			$type = 'u'; // UNKNOWN

        $return_array['type'] = $type;

        $return_array['owner']["read"]     = ( $mode & 00400 )? true : false;
        $return_array['owner']["write"]    = ( $mode & 00200 )? true : false;
        $return_array['owner']["execute"]  = ( $mode & 00100 )? true : false;
		
        $return_array['group']["read"]     = ( $mode & 00040 )? true : false;
        $return_array['group']["write"]    = ( $mode & 00020 )? true : false;
        $return_array['group']["execute"]  = ( $mode & 00010 )? true : false;
		
        $return_array['others']["read"]    = ( $mode & 00004 )? true : false;
        $return_array['others']["write"]   = ( $mode & 00002 )? true : false;
        $return_array['others']["execute"] = ( $mode & 00001 )? true : false;

        if ( $mode & 0x800 ) 
			$return_array['bits']['user']   = ( $return_array['owner']["execute"]  == 'x' )? 's' : 'S';
        
		if ( $mode & 0x400 ) 
			$return_array['bits']['group']  = ( $return_array['group']["execute"]  == 'x' )? 's' : 'S';
        
		if ( $mode & 0x200 ) 
			$return_array['bits']['sticky'] = ( $return_array['others']["execute"] == 'x' )? 't' : 'T';

        return $return_array;
    }

	/**
	 * @access public
	 */
    function setPermissionOctal( $mode )
    {
        $this->_current_permission = $mode;
    }

    /**
     * Wrapper function to chmod.
     * Saves for current file the current rights.
	 *
	 * @access public
     */
    function savePermissions()
    {
        return chmod( $this->_current_file, $this->_current_permission );
    }
    
    /**
     * Set current permission with an input array, which one could be 
     * drawn with method chmodBoxHTML().
	 *
	 * @access public
     */
    function setPermissionArray( $input_array )
    {
        if ( is_array( $input_array ) )
        {
			$this->_current_permission = $this->permissionArrayToOctal( $input_array );
            return true;
        }
        else 
		{
			return false;
		}
    }

    /**
     * Transform an array of permissions in it's octal equivalent.
	 *
	 * @access public
     */
    function permissionArrayToOctal( $inarray )
    {
        // READ : 4
        // WRITE: 2
        // EXEC : 1

        // strange bits
        // set user ID (4)
        // set group ID
        // save text image (1)
        $strange  = '0';
        $strange += 4 * $inarray['SUID'];
        $strange += 2 * $inarray['SGID'];    
        $strange += 1 * $inarray['sticky'];    

        // owner
        $cent  = 0;
        $cent += 4 * $inarray['owner_read'];
        $cent += 2 * $inarray['owner_write'];    
        $cent += 1 * $inarray['owner_execute'];

        // group
        $diz  = '0';
        $diz += 4 * $inarray['group_read'];
        $diz += 2 * $inarray['group_write'];    
        $diz += 1 * $inarray['group_execute'];

        // the rest of the world
        $unit  = '0';
        $unit += 4 * $inarray['others_read'];
        $unit += 2 * $inarray['others_write'];    
        $unit += 1 * $inarray['others_execute'];

        return $strange . $cent . $diz . $unit;
    }

	/**
	 * @access public
	 */
    function printHTMLFormForPermissions()
    {
		return $this->chmodBoxHTML( $this->getPermissionArray(), $this->form_array_name );
    }
    
    /**
     * Return a html box for accesses to file
     * has inputs for changing it.
     *
     * @param  array  $default_values      which boxes to check by default
     * @param  string $form_array_name     name of array that will contain 
	 * @access public
     */
    function chmodBoxHTML( $default_values,$form_array_name )
    {
        $owner_read      = ( $default_values['owner']['read']     )? ' checked' : '';
        $owner_write     = ( $default_values['owner']['write']    )? ' checked' : '';
        $owner_execute   = ( $default_values['owner']['execute']  )? ' checked' : '';

        $group_read      = ( $default_values['group']['read']     )? ' checked' : '';
        $group_write     = ( $default_values['group']['write']    )? ' checked' : '';
        $group_execute   = ( $default_values['group']['execute']  )? ' checked' : '';

        $others_read     = ( $default_values['others']['read']    )? ' checked' : '';
        $others_write    = ( $default_values['others']['write']   )? ' checked' : '';
        $others_execute  = ( $default_values['others']['execute'] )? ' checked' : '';

        $suid            = ( $default_values['bits']['user']      )? ' checked' : '';
        $sgid            = ( $default_values['bits']['group']     )? ' checked' : '';
        $sticky          = ( $default_values['bits']['sticky']    )? ' checked' : '';    

        $ret  = "<table border=0 cellspacing=0 cellpadding=2>\n";
        $ret .= "<tr>\n";
        $ret .= "  <td>Type</td>\n";
        $ret .= "  <td colspan=3>\n";
		 
        switch ( $default_values['type'] )
        {
             case 'p': 
			 	$ret .= 'FIFO pipe'; 
				break;
             
			 case 'c': 
			 	$ret .= 'Character special'; 
				break;
             
			 case 'd': 
			 	$ret .= 'Directory'; 
				break;
             
			 case 'b': 
			 	$ret .= 'Block special'; 
				break;
             
			 case '-': 
			 	$ret .= 'Regular File'; 
				break;
             
			 case 'l': 
			 	$ret .= 'Symbolic Link'; 
				break;
             
			 case 's': 
			 	$ret .= 'Socket';
				break;
             
			 case 'u':
             
			 default: 
			 	$ret .= 'Unknown';
				break;
        }
		
        $ret .= "  </td>\n";
        $ret .= "</tr>\n";    
        $ret .= "  <th> </th>\n";
        $ret .= "  <th>User</th>\n";
        $ret .= "  <th>Group</th>\n";
        $ret .= "  <th>others</th>\n";
        $ret .= "</tr>\n";
        $ret .= "<tr>\n";
        $ret .= "  <th>Read</th>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[owner_read]\" value=\"1\"$owner_read></td>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[group_read]\" value=\"1\"$group_read></td>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[others_read]\" value=\"1\"$others_read></td>\n";
        $ret .= "</tr>\n";
        $ret .= "  <th>Write</th>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[owner_write]\" value=\"1\"$owner_write></td>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[group_write]\" value=\"1\"$group_write></td>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[others_write]\" value=\"1\"$others_write></td>\n";
        $ret .= "</tr>\n";
        $ret .= "  <th>Execute</th>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[owner_execute]\" value=\"1\"$owner_execute></td>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[group_execute]\" value=\"1\"$group_execute></td>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[others_execute]\" value=\"1\"$others_execute></td>\n";
        $ret .= "</tr>\n";
        $ret .= "</tr>\n";    
        $ret .= "  <th> </th>\n";
        $ret .= "  <th>SUID</th>\n";
        $ret .= "  <th>SGID</th>\n";
        $ret .= "  <th>sticky</th>\n";
        $ret .= "</tr>\n";        
        $ret .= "</tr>\n";
        $ret .= "  <th>Sp. bits</th>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[SUID]\" value=\"1\"$suid></td>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[SGID]\" value=\"1\"$sgid></td>\n";
        $ret .= "  <td align=\"center\"><input type=checkbox name=\"".$form_array_name."[sticky]\" value=\"1\"$sticky></td>\n";
        $ret .= "</tr>\n";    
        $ret .= "</table>\n";
        $ret .= "<!--\n";
        $ret .= "given mode :\n";
		
        foreach ( $default_values as $people => $p_array )
        {
            $ret .= "$people:";
            
			if ( is_array( $p_array ) )
			{
            	foreach ( $p_array as $right => $value ) 
					$ret .= "$right$value/";
			}
            else
			{
				$ret .= "$p_array";
			}
            
			$ret .= "\n";
        }
		
        $ret .= "-->\n";

        //SUID, SGID et sticky bit
        return $ret;
    }    
} // END OF FilePermission

?>
