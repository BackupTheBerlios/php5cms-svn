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
 * The TarArchive class helps in creating and managing GNU TAR format
 * files compressed by GNU ZIP or not.
 *
 * The class offers basic functions like creating an archive, adding
 * files in the archive, extracting files from the archive and listing
 * the archive content.
 *
 * It also provide advanced functions that allow the adding and
 * extraction of files with path manipulation. 
 *
 * Usage:
 *
 * // ----- Creating the object (uncompressed archive)
 * $tar_object = new TarArchive( "tarname.tar" );
 *
 * // ----- Creating the archive
 * $v_list[0] = "file.txt";
 * $v_list[1] = "data/";
 * $v_list[2] = "file.log";
 * $tar_object->create( $v_list );
 *
 * // ----- Adding files
 * $v_list[0] = "dev/file.txt";
 * $v_list[1] = "dev/data/";
 * $v_list[2] = "log/file.log";
 * $tar_object->add( $v_list );
 *
 * // ----- Adding more files
 * $tar_object->add( "release/newfile.log release/readme.txt" );
 *
 * // ----- Listing the content
 * if ( ( $v_list  =  $tar_object->listContent() ) != 0 )
 * {
 *   	for ( $i = 0; $i < sizeof( $v_list ); $i++ )
 *   	{
 *     		echo "Filename :'"   . $v_list[$i][filename] . "'<br>";
 *     		echo " .size :'"     . $v_list[$i][size]     . "'<br>";
 *     		echo " .mtime :'"    . $v_list[$i][mtime]    . "' (" . date( "l dS of F Y h:i:s A", $v_list[$i][mtime] ) . ")<br>";
 *     		echo " .mode :'"     . $v_list[$i][mode]     . "'<br>";
 *     		echo " .uid :'"      . $v_list[$i][uid]      . "'<br>";
 *     		echo " .gid :'"      . $v_list[$i][gid]      . "'<br>";
 *     		echo " .typeflag :'" . $v_list[$i][typeflag] . "'<br>";
 *   	}
 * }
 *
 * // ----- Extracting the archive in directory "install"
 * $tar_object->extract( "install" );
 *
 * @package io_archive
 */
  
class TarArchive extends PEAR
{
	/**
     * @var string Name of the Tar
	 * @access private
     */
    var $_tarname;

    /**
     * @var boolean if true, the Tar file will be gzipped
	 * @access private
     */
    var $_compress;

    /**
     * @var file descriptor
	 * @access private
     */
    var $_file;


    /**
     * Constructor
	 *
	 * This flavour of the constructor only declare a new TarArchive
	 * object, identifying it by the name of the tar file.
     * If the compress argument is set the tar will be read or created as a
     * gzip compressed TAR file.
     *
     * @param  string  $p_tarname  The name of the tar archive to create
     * @param  boolean $p_compress if true, the archive will be gezip(ped)
     * @access public
     */
    function TarArchive( $p_tarname, $p_compress = false )
    {
        $this->_tarname = $p_tarname;
		
		// assert zlib extension support
        if ( $p_compress )
		{
            $extname = 'zlib';
			
            if ( !Util::loadExtension( $extname ) )
			{
				$this = new PEAR_Error( "Extension not found: " . $extname );
				return;
			}
        }
		
        $this->_compress = $p_compress;
    }

    function _Archive_Tar()
    {
        $this->_close();
    }


    /**
     * This method creates the archive file and add the files / directories
     * that are listed in $p_filelist.
     * If the file already exists and is writable, it is replaced by the
     * new tar. It is a create and not an add. If the file exists and is
     * read-only or is a directory it is not replaced. The method returns
     * false and an error text.
     * The $p_filelist parameter can be an array of string, each string
     * representing a filename or a directory name with their path if
     * needed. It can also be a single string with names separated by a
     * single blank.
     * See also createModify() method for more details.
     *
     * @param array  $p_filelist An array of filenames and directory names, or a single
     *                           string with names separated by a single blank space.
     * @return                   true on success, false on error.
     * @access public
     */
    function create( $p_filelist )
    {
        return $this->createModify( $p_filelist, "", "" );
    }

	/**
	 * @access public
	 */
    function add( $p_filelist )
    {
        return $this->addModify( $p_filelist, "", "" );
    }

	/**
	 * @access public
	 */
    function extract( $p_path = "" )
    {
        return $this->extractModify( $p_path, "" );
    }

	/**
	 * @access public
	 */
    function listContent()
    {
        $v_list_detail = array();
		$res = $this->_openRead();
		
        if ( $res && !PEAR::isError( $res ) )
		{
			$res = $this->_extractList( "", $v_list_detail, "list", "", "" );
			
            if ( !$res || PEAR::isError( $res ) )
			{
                unset( $v_list_detail );
                return false;
            }
			
            $this->_close();
        }

        return $v_list_detail;
    }

    /**
    * This method creates the archive file and add the files / directories
    * that are listed in $p_filelist.
    * If the file already exists and is writable, it is replaced by the
    * new tar. It is a create and not an add. If the file exists and is
    * read-only or is a directory it is not replaced. The method returns
    * false and an error text.
    * The $p_filelist parameter can be an array of string, each string
    * representing a filename or a directory name with their path if
    * needed. It can also be a single string with names separated by a
    * single blank.
    * The path indicated in $p_remove_dir will be removed from the
    * memorized path of each file / directory listed when this path
    * exists. By default nothing is removed (empty path "")
    * The path indicated in $p_add_dir will be added at the beginning of
    * the memorized path of each file / directory listed. However it can
    * be set to empty "". The adding of a path is done after the removing
    * of path.
    * The path add/remove ability enables the user to prepare an archive
    * for extraction in a different path than the origin files are.
    * See also addModify() method for file adding properties.
    *
    * @param array  $p_filelist     An array of filenames and directory names, or a single
    *                               string with names separated by a single blank space.
    * @param string $p_add_dir      A string which contains a path to be added to the
    *                               memorized path of each element in the list.
    * @param string $p_remove_dir   A string which contains a path to be removed from
    *                               the memorized path of each element in the list, when
    *                               relevant.
    * @return boolean               true on success, false on error.
    * @access public
    */
    function createModify( $p_filelist, $p_add_dir, $p_remove_dir = "" )
    {
        $v_result = true;
		$res = $this->_openWrite();
		
        if ( !$res || PEAR::isError( $res ) )
            return false;

        if ( $p_filelist != "" )
		{
            if ( is_array( $p_filelist ) )
			{
                $v_list = $p_filelist;
            }
			else if (is_string( $p_filelist ) )
            {
			    $v_list = explode( " ", $p_filelist );
			}
            else
			{
                $this->_cleanFile();
                return PEAR::raiseError( "Invalid file list." );
            }

            $v_result = $this->_addList( $v_list, "", "" );
        }

        if ( $v_result && !PEAR::isError( $v_result ) )
		{
            $this->_writeFooter();
            $this->_close();
        }
		else
		{
            $this->_cleanFile();
		}
		
        return $v_result;
    }
	
    /**
    * This method add the files / directories listed in $p_filelist at the
    * end of the existing archive. If the archive does not yet exists it
    * is created.
    * The $p_filelist parameter can be an array of string, each string
    * representing a filename or a directory name with their path if
    * needed. It can also be a single string with names separated by a
    * single blank.
    * The path indicated in $p_remove_dir will be removed from the
    * memorized path of each file / directory listed when this path
    * exists. By default nothing is removed (empty path "")
    * The path indicated in $p_add_dir will be added at the beginning of
    * the memorized path of each file / directory listed. However it can
    * be set to empty "". The adding of a path is done after the removing
    * of path.
    * The path add/remove ability enables the user to prepare an archive
    * for extraction in a different path than the origin files are.
    * If a file/dir is already in the archive it will only be added at the
    * end of the archive. There is no update of the existing archived
    * file/dir. However while extracting the archive, the last file will
    * replace the first one. This results in a none optimization of the
    * archive size.
    * If a file/dir does not exist the file/dir is ignored. However an
    * error text is send.
    * If a file/dir is not readable the file/dir is ignored. However an
    * error text is send.
    * If the resulting filename/dirname (after the add/remove option or
    * not) string is greater than 99 char, the file/dir is
    * ignored. However an error text is send.
    *
    * @param array      $p_filelist     An array of filenames and directory names, or a single
    *                                   string with names separated by a single blank space.
    * @param string     $p_add_dir      A string which contains a path to be added to the
    *                                   memorized path of each element in the list.
    * @param string     $p_remove_dir   A string which contains a path to be removed from
    *                                   the memorized path of each element in the list, when
    *                                   relevant.
    * @return                           true on success, false on error.
    * @access public
    */
    function addModify( $p_filelist, $p_add_dir, $p_remove_dir = "" )
    {
        $v_result = true;

        if ( !@is_file( $this->_tarname ) )
		{
            $v_result = $this->createModify( $p_filelist, $p_add_dir, $p_remove_dir );
		}
        else
		{
            if ( is_array( $p_filelist ) )
				$v_list = $p_filelist;
			else if ( is_string( $p_filelist ) )
				$v_list = explode( " ", $p_filelist );
			else
				return PEAR::raiseError( "Invalid file list." );

            $v_result = $this->_append( $v_list, $p_add_dir, $p_remove_dir );
        }

        return $v_result;
    }

    /**
    * This method extract all the content of the archive in the directory
    * indicated by $p_path. When relevant the memorized path of the
    * files/dir can be modified by removing the $p_remove_path path at the
    * beginning of the file/dir path.
    * While extracting a file, if the directory path does not exists it is
    * created.
    * While extracting a file, if the file already exists it is replaced
    * without looking for last modification date.
    * While extracting a file, if the file already exists and is write
    * protected, the extraction is aborted.
    * While extracting a file, if a directory with the same name already
    * exists, the extraction is aborted.
    * While extracting a directory, if a file with the same name already
    * exists, the extraction is aborted.
    * While extracting a file/directory if the destination directory exist
    * and is write protected, or does not exist but can not be created,
    * the extraction is aborted.
    * If after extraction an extracted file does not show the correct
    * stored file size, the extraction is aborted.
    * When the extraction is aborted, an error text is set and false
    * is returned. However the result can be a partial extraction that may
    * need to be manually cleaned.
    *
    * @param string $p_path         The path of the directory where the files/dir need to by
    *                               extracted.
    * @param string $p_remove_path  Part of the memorized path that can be removed if
    *                               present at the beginning of the file/dir path.
    * @return boolean               true on success, false on error.
    * @access public
    * @see extractList()
    */
    function extractModify( $p_path, $p_remove_path )
    {
        $v_result = $this->_openRead();
        $v_list_detail = array();

        if ( $v_result && !PEAR::isError( $v_result ) )
		{
            $v_result = $this->_extractList( $p_path, $v_list_detail, "complete", 0, $p_remove_path );
            $this->_close();
        }

        return $v_result;
    }

    /**
    * This method extract from the archive only the files indicated in the
    * $p_filelist. These files are extracted in the current directory or
    * in the directory indicated by the optional $p_path parameter.
    * If indicated the $p_remove_path can be used in the same way as it is
    * used in extractModify() method.
    * @param array  $p_filelist     An array of filenames and directory names, or a single
    *                               string with names separated by a single blank space.
    * @param string $p_path         The path of the directory where the files/dir need to by
    *                               extracted.
    * @param string $p_remove_path  Part of the memorized path that can be removed if
    *                               present at the beginning of the file/dir path.
    * @return                       true on success, false on error.
    * @access public
    * @see extractModify()
    */
    function extractList( $p_filelist, $p_path = "", $p_remove_path = "" )
    {
        $v_result = true;
        $v_list_detail = array();

        if ( is_array( $p_filelist ) )
			$v_list = $p_filelist;
        else if ( is_string( $p_filelist ) )
			$v_list = explode( " ", $p_filelist );
        else
			return PEAR::raiseError( "Invalid string list." );

		$v_result = $this->_openRead();
		
        if ( $v_result && !PEAR::isError( $v_result ) )
		{
            $v_result = $this->_extractList( $p_path, $v_list_detail, "complete", $v_list, $p_remove_path );
            $this->_close();
        }

        return $v_result;
    }

	
	// private methods
	
	/**
	 * @access private
	 */
    function _openWrite()
    {
        if ( $this->_compress )
            $this->_file = @gzopen( $this->_tarname, "w" );
        else
            $this->_file = @fopen( $this->_tarname, "w" );

        if ( $this->_file == 0 )
			return PEAR::raiseError( "Unable to open in write mode: " . $this->_tarname );

        return true;
    }

	/**
	 * @access private
	 */
    function _openRead()
    {
        if ( $this->_compress )
            $this->_file = @gzopen( $this->_tarname, "rb" );
        else
            $this->_file = @fopen( $this->_tarname, "rb" );

        if ( $this->_file == 0 )
			return PEAR::raiseError( "Unable to open in read mode: " . $this->_tarname );

        return true;
    }

	/**
	 * @access private
	 */
    function _openReadWrite()
    {
        if ( $this->_compress )
            $this->_file = @gzopen( $this->_tarname, "r+b" );
        else
            $this->_file = @fopen( $this->_tarname, "r+b" );

        if ( $this->_file == 0 )
			return PEAR::raiseError( "Unable to open in read/write mode: " . $this->_tarname );

        return true;
    }

	/**
	 * @access private
	 */
    function _close()
    {
        if ( $this->_file )
		{
            if ( $this->_compress )
                @gzclose( $this->_file );
            else
                @fclose( $this->_file );

            $this->_file = 0;
        }

        return true;
    }

	/**
	 * @access private
	 */
    function _cleanFile()
    {
        $this->_close();
        @unlink($this->tarname);

        return true;
    }

	/**
	 * @access private
	 */
    function _writeFooter()
    {
		if ( $this->_file )
		{
			// write the last 0 filled block for end of archive
			$v_binary_data = pack( "a512", "" );
		  
			if ( $this->_compress )
				@gzputs($this->_file, $v_binary_data );
			else
				@fputs( $this->_file, $v_binary_data );
		}
		
		return true;
    }

	/**
	 * @access private
	 */
    function _addList( $p_list, $p_add_dir, $p_remove_dir )
    {
		$v_result = true;
		$v_header = array();

		if ( !$this->_file )
			return PEAR::raiseError( "Invalid file descriptor." );

		if ( sizeof( $p_list ) == 0 )
			return true;

		for ( $j = 0; ( $j < count( $p_list ) ) && ( $v_result ); $j++ )
		{
			$v_filename = $p_list[$j];

			// Skip the current tar name
			if ( $v_filename == $this->_tarname )
				continue;

			if ( $v_filename == "" )
				continue;

			if ( !file_exists( $v_filename ) )
				continue;

			// Add the file or directory header
			$add = $this->_addFile( $v_filename, $v_header, $p_add_dir, $p_remove_dir );
			
			if ( !$add || PEAR::isError( $add ) )
				return false;

			if ( @is_dir( $v_filename ) )
			{
				if ( !( $p_hdir = opendir( $v_filename ) ) )
					continue;
				
				$p_hitem = readdir( $p_hdir ); // '.'  directory
				$p_hitem = readdir( $p_hdir ); // '..' directory
				
				while ( $p_hitem = readdir( $p_hdir ) )
				{
					if ( $v_filename != "." )
						$p_temp_list[0] = $v_filename . DIRECTORY_SEPARATOR . $p_hitem;
					else
						$p_temp_list[0] = $p_hitem;

					$v_result = $this->_addList( $p_temp_list, $p_add_dir, $p_remove_dir );
				}

				unset( $p_temp_list );
				unset( $p_hdir  );
				unset( $p_hitem );
			}
		}

		return $v_result;
	}

	/**
	 * @access private
	 */
    function _addFile($p_filename, &$p_header, $p_add_dir, $p_remove_dir)
    {
		if ( !$this->_file )
			return PEAR::raiseError( "Invalid file descriptor." );

		if ( $p_filename == "" )
			return PEAR::raiseError( "Invalid file name." );

		// calculate the stored filename
		$v_stored_filename = $p_filename;
		
		if ( $p_remove_dir != "" )
		{
			if ( substr( $p_remove_dir, -1 ) != DIRECTORY_SEPARATOR )
				$p_remove_dir .= DIRECTORY_SEPARATOR;

			if ( substr( $p_filename, 0, strlen( $p_remove_dir ) ) == $p_remove_dir )
				$v_stored_filename = substr( $p_filename, strlen( $p_remove_dir ) );
		}
		
		if ( $p_add_dir != "" )
		{
			if ( substr( $p_add_dir, -1 ) == DIRECTORY_SEPARATOR )
				$v_stored_filename = $p_add_dir . $v_stored_filename;
			else
				$v_stored_filename = $p_add_dir . DIRECTORY_SEPARATOR . $v_stored_filename;
		}

		if ( strlen( $v_stored_filename ) > 99 )
		{
			fclose( $v_file );
			return true;
		}

		if ( is_file( $p_filename ) )
		{
			if ( ( $v_file = @fopen( $p_filename, "rb" ) ) == 0 )
				return true;

			if ( !$this->_writeHeader( $p_filename, $v_stored_filename ) )
				return false;

			while ( ( $v_buffer = fread( $v_file, 512 ) ) != "" )
			{
				$v_binary_data = pack( "a512", "$v_buffer" );
				
				if ( $this->_compress )
					@gzputs( $this->_file, $v_binary_data );
				else
					@fputs( $this->_file, $v_binary_data );
			}

			fclose( $v_file );
		}
		else
		{
			// only header for dir
			if ( !$this->_writeHeader( $p_filename, $v_stored_filename ) )
				return false;
		}

		return true;
    }

	/**
	 * @access private
	 */
    function _writeHeader($p_filename, $p_stored_filename)
    {
		if ( $p_stored_filename == "" )
			$p_stored_filename = $p_filename;
		
		$v_reduce_filename = $this->_pathReduction( $p_stored_filename );

		$v_info  = stat( $p_filename );
        $v_uid   = sprintf( "%6s ",  decoct( $v_info[4] ) );
        $v_gid   = sprintf( "%6s ",  decoct( $v_info[5] ) );
        $v_perms = sprintf( "%6s ",  decoct( fileperms( $p_filename ) ) );

        clearstatcache();
        $v_size  = sprintf( "%11s ", decoct( filesize(  $p_filename ) ) );
        $v_mtime = sprintf( "%11s",  decoct( filemtime( $p_filename ) ) );

		if ( @is_dir( $p_filename ) )
			$v_typeflag = "5";
		else
			$v_typeflag = "";

		$v_linkname = "";
        $v_magic    = "";
        $v_version  = "";
        $v_uname    = "";
        $v_gname    = "";
        $v_devmajor = "";
        $v_devminor = "";
        $v_prefix   = "";

        $v_binary_data_first = pack( "a100a8a8a8a12A12", $v_reduce_filename, $v_perms, $v_uid, $v_gid, $v_size, $v_mtime );
        $v_binary_data_last  = pack( "a1a100a6a2a32a32a8a8a155a12", $v_typeflag, $v_linkname, $v_magic, $v_version, $v_uname, $v_gname, $v_devmajor, $v_devminor, $v_prefix, "" );

        // calculate the checksum
		$v_checksum = 0;
		
		// first part of the header
        for ( $i = 0; $i < 148; $i++ )
			$v_checksum += ord( substr( $v_binary_data_first, $i, 1 ) );
			
        // ignore the checksum value and replace it by ' ' (space)
        for ( $i = 148; $i < 156; $i++ )
            $v_checksum += ord(' ');

		// last part of the header
        for ( $i = 156, $j = 0; $i < 512; $i++, $j++ )
            $v_checksum += ord( substr( $v_binary_data_last, $j, 1 ) );

        // write the first 148 bytes of the header in the archive
        if ( $this->_compress )
            @gzputs( $this->_file, $v_binary_data_first, 148 );
        else
            @fputs( $this->_file, $v_binary_data_first, 148 );

        // write the calculated checksum
        $v_checksum = sprintf( "%6s ", decoct( $v_checksum ) );
        $v_binary_data = pack( "a8", $v_checksum );
		
		if ( $this->_compress )
			@gzputs( $this->_file, $v_binary_data, 8 );
        else
			@fputs( $this->_file, $v_binary_data, 8 );

        // write the last 356 bytes of the header in the archive
        if ( $this->_compress )
            @gzputs( $this->_file, $v_binary_data_last, 356 );
        else
            @fputs( $this->_file, $v_binary_data_last, 356 );

        return true;
    }

	/**
	 * @access private
	 */
	function _readHeader( $v_binary_data, &$v_header )
    {
        if ( strlen( $v_binary_data ) == 0 )
		{
            $v_header[filename] = "";
            return true;
        }

        if ( strlen( $v_binary_data ) != 512 )
		{
            $v_header[filename] = "";
			return PEAR::raiseError( "Invalid block size: " . strlen( $v_binary_data ) );
        }

        // calculate the checksum
        $v_checksum = 0;
		
        // first part of the header
        for ( $i = 0; $i < 148; $i++ )
            $v_checksum += ord( substr( $v_binary_data, $i, 1 ) );
			
        // ignore the checksum value and replace it by ' ' (space)
        for ( $i = 148; $i < 156; $i++ )
            $v_checksum += ord(' ');
			
        // last part of the header
        for ( $i = 156; $i < 512; $i++ )
           $v_checksum += ord( substr( $v_binary_data, $i, 1 ) );

        $v_data = unpack( "a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor", $v_binary_data );

        // extract the checksum
        $v_header[checksum] = octdec( trim( $v_data[checksum] ) );
		
        if ( $v_header[checksum] != $v_checksum )
		{
			$v_header[filename] = "";

            // look for last block (empty block)
            if ( ( $v_checksum == 256) && ( $v_header[checksum] == 0 ) )
                return true;

			return PEAR::raiseError( "Invalid checksum (calculated - expected): " . $v_checksum . " - " . $v_header[checksum] );
        }

        // extract the properties
        $v_header[filename] = trim( $v_data[filename] );
        $v_header[mode]     = octdec( trim( $v_data[mode]  ) );
        $v_header[uid]      = octdec( trim( $v_data[uid]   ) );
        $v_header[gid]      = octdec( trim( $v_data[gid]   ) );
        $v_header[size]     = octdec( trim( $v_data[size]  ) );
        $v_header[mtime]    = octdec( trim( $v_data[mtime] ) );
        $v_header[typeflag] = $v_data[typeflag];
		
        // all these fields are removed form the header because they do not carry interesting info
        /*
		$v_header[link]     = trim( $v_data[link]     );
        $v_header[magic]    = trim( $v_data[magic]    );
        $v_header[version]  = trim( $v_data[version]  );
        $v_header[uname]    = trim( $v_data[uname]    );
        $v_header[gname]    = trim( $v_data[gname]    );
        $v_header[devmajor] = trim( $v_data[devmajor] );
        $v_header[devminor] = trim( $v_data[devminor] );
        */

        return true;
    }

	/**
	 * @access private
	 */
    function _extractList( $p_path, &$p_list_detail, $p_mode, $p_file_list, $p_remove_path )
    {
		$v_result      = true;
		$v_nb          = 0;
		$v_extract_all = true;
		$v_listing     = false;        

		if ( $p_path == "" || ( substr( $p_path, 0, 1 ) != DIRECTORY_SEPARATOR && substr( $p_path, 0, 3 ) != ( ".." . DIRECTORY_SEPARATOR ) && substr( $p_path, 1, 3 ) != ":\\" ) )
			$p_path = "." . DIRECTORY_SEPARATOR . $p_path;

		// look for path to remove format (should end by /)
		if ( ( $p_remove_path != "" ) && ( substr( $p_remove_path, -1 ) != DIRECTORY_SEPARATOR ) )
			$p_remove_path .= DIRECTORY_SEPARATOR;
		
		$p_remove_path_size = strlen( $p_remove_path );

		switch ( $p_mode )
		{
			case "complete" :
				$v_extract_all = true;
				$v_listing = false;
				break;
				
			case "partial" :
				$v_extract_all = false;
				$v_listing = false;
				break;
				
			case "list" :
				$v_extract_all = false;
				$v_listing = true;
				break;
				
			default :
				return PEAR::raiseError( "Invalid extract mode: " . $p_mode );
		}

		clearstatcache();

		while ( !( $v_end_of_file = ( $this->_compress? @gzeof( $this->_file ) : @feof( $this->_file ) ) ) )
		{
			$v_extract_file = false;
			$v_extraction_stopped = 0;

			if ( $this->_compress )
				$v_binary_data = @gzread( $this->_file, 512 );
			else
				$v_binary_data = @fread( $this->_file, 512 );

			$read = $this->_readHeader( $v_binary_data, $v_header );
			
			if ( !$read || PEAR::isError( $read ) )
				return false;

			if ( $v_header[filename] == "" )
				continue;

			if ( ( !$v_extract_all ) && ( is_array( $p_file_list ) ) )
			{
				// by default no unzip if the file is not found
				$v_extract_file = false;

				for ( $i = 0; $i < sizeof( $p_file_list ); $i++ )
				{
					// look if it is a directory
					if ( substr( $p_file_list[$i], -1 ) == DIRECTORY_SEPARATOR )
					{
						// look if the directory is in the filename path
						if ( ( strlen( $v_header[filename] ) > strlen( $p_file_list[$i] ) ) && ( substr( $v_header[filename], 0, strlen( $p_file_list[$i] ) ) == $p_file_list[$i] ) )
						{
							$v_extract_file = true;
							break;
						}
					}
					// it is a file, so compare the file names
					else if ( $p_file_list[$i] == $v_header[filename] )
					{
						$v_extract_file = true;
						break;
					}
				}
			}
			else
			{
				$v_extract_file = true;
			}

			// look if this file need to be extracted
			if ( ( $v_extract_file ) && ( !$v_listing ) )
			{              
				if ( ( $p_remove_path != "" ) && ( substr( $v_header[filename], 0, $p_remove_path_size ) == $p_remove_path ) )
					$v_header[filename] = substr( $v_header[filename], $p_remove_path_size );
				
				if ( ( $p_path != ( "." . DIRECTORY_SEPARATOR ) ) && ( $p_path != DIRECTORY_SEPARATOR ) )
				{
					while ( substr( $p_path, -1 ) == DIRECTORY_SEPARATOR )
						$p_path = substr( $p_path, 0, strlen( $p_path ) - 1 );

					if ( substr( $v_header[filename], 0, 1 ) == DIRECTORY_SEPARATOR )
						$v_header[filename] = $p_path . $v_header[filename];
					else
						$v_header[filename] = $p_path . DIRECTORY_SEPARATOR . $v_header[filename];
				}
				
				if ( file_exists( $v_header[filename] ) )
				{
					if ( ( @is_dir( $v_header[filename] ) ) && ( $v_header[typeflag] == "" ) )
						return PEAR::raiseError( "File already exists in directory: " . $v_header[filename] );
					
					if ( ( is_file( $v_header[filename] ) ) && ( $v_header[typeflag] == "5" ) )
						return PEAR::raiseError( "Directory already exists as a file: " . $v_header[filename] );
					
					if ( !is_writeable( $v_header[filename] ) )
						return PEAR::raiseError( "File already exists and is write protected: " . $v_header[filename] );
					
					if ( filemtime( $v_header[filename] ) > $v_header[mtime] )
					{
						// To be completed: An error or silent no replace?
					}
				}
				// Check the directory availability and create it if necessary
				else if ( ( $v_result = $this->_dirCheck( ( ( $v_header[typeflag] == "5" )? $v_header[filename] : dirname( $v_header[filename] ) ) ) ) != 1 )
				{
					return PEAR::raiseError( "Unable to create path for " . $v_header[filename] );
				}

				if ( $v_extract_file )
				{
					if ( $v_header[typeflag] == "5" )
					{
						if ( !@file_exists( $v_header[filename] ) )
						{
							if ( !@mkdir( $v_header[filename], 0777 ) )
								return PEAR::raiseError( "Unable to create directory: " . $v_header[filename] );
						}
					}
					else
					{
						if ( ( $v_dest_file = @fopen( $v_header[filename], "wb" ) ) == 0 )
						{
							return PEAR::raiseError( "Error while opening file in write binary mode: " . $v_header[filename] );
						}
						else
						{
							$n = floor( $v_header[size] / 512 );
							
							for ( $i = 0; $i < $n; $i++ )
							{
								if ($this->_compress)
									$v_content = @gzread( $this->_file, 512 );
								else
									$v_content = @fread( $this->_file, 512 );
								
								fwrite( $v_dest_file, $v_content, 512 );
							}
							
							if ( ( $v_header[size] % 512 ) != 0 )
							{
								if ($this->_compress)
									$v_content = @gzread( $this->_file, 512 );
								else
									$v_content = @fread( $this->_file, 512 );
								
								fwrite( $v_dest_file, $v_content, ( $v_header[size] % 512 ) );
							}

							@fclose( $v_dest_file );

							// change the file mode, mtime
							@touch( $v_header[filename], $v_header[mtime] );
							
							// To be completed
							// chmod( $v_header[filename], decoct( $v_header[mode] ) );
						}

						// check the file size
						if ( filesize( $v_header[filename] ) != $v_header[size] )
							return PEAR::raiseError( "Archive may be corrupted. Extracted file does not have the correct file size (extracted file - file size - expected file size): " . $v_header[filename] . " - " . filesize( $v_filename ) . " - " . $v_header[size] );
					}
				}
				else
				{
					// jump to next file
					if ($this->_compress)
						@gzseek( $this->_file, @gztell( $this->_file ) + ( ceil( ( $v_header[size] / 512 ) ) * 512 ) );
					else
						@fseek( $this->_file, @ftell( $this->_file ) + ( ceil( ( $v_header[size] / 512 ) ) * 512 ) );
				}
			}
			else
			{
				// jump to next file
				if ($this->_compress)
					@gzseek( $this->_file, @gztell( $this->_file ) + ( ceil( ( $v_header[size] / 512 ) ) * 512 ) );
				else
					@fseek( $this->_file, @ftell( $this->_file ) + ( ceil( ( $v_header[size] / 512 ) ) * 512 ) );
			}

			if ( $this->_compress )
				$v_end_of_file = @gzeof( $this->_file );
			else
				$v_end_of_file = @feof( $this->_file );

			if ( $v_listing || $v_extract_file || $v_extraction_stopped )
			{
				// log extracted files
				if ( ( $v_file_dir = dirname( $v_header[filename] ) ) == $v_header[filename] )
					$v_file_dir = "";
				
				if ( ( substr( $v_header[filename], 0, 1 ) == DIRECTORY_SEPARATOR ) && ( $v_file_dir == "" ) )
					$v_file_dir = DIRECTORY_SEPARATOR;

				$p_list_detail[$v_nb++] = $v_header;
			}
		}

		return true;
	}

	/**
	 * @access private
	 */
	function _append( $p_filelist, $p_add_dir = "", $p_remove_dir = "" )
    {
		if ( $this->_compress )
		{
			$this->_close();

			if ( !@rename( $this->_tarname, $this->_tarname . ".tmp" ) )
				return PEAR::raiseError( "Error while renaming file to tempfile: " . $this->_tarname . " - " . $this->_tarname . ".tmp" );

			if ( ( $v_temp_tar = @gzopen( $this->_tarname . ".tmp", "rb" ) ) == 0 )
			{
				@rename( $this->_tarname . ".tmp", $this->_tarname );
				return PEAR::raiseError( "Unable to open tempfile in binary read mode: " . $this->_tarname . ".tmp" );
            }
			
			$res = $this->_openWrite();
			
			if ( !$res || PEAR::isError( $res ) )
			{
				@rename( $this->_tarname . ".tmp", $this->_tarname );
				return false;
            }

            $v_buffer = @gzread( $v_temp_tar, 512 );

            // read the following blocks but not the last one
            if ( !@gzeof( $v_temp_tar ) )
			{
				do
				{
					$v_binary_data = pack( "a512", "$v_buffer" );
                    @gzputs( $this->_file, $v_binary_data );
                    $v_buffer = @gzread( $v_temp_tar, 512 );
				} while ( !@gzeof( $v_temp_tar ) );
            }

			$res = $this->_addList( $p_filelist, $p_add_dir, $p_remove_dir );
			
            if ( $res && !PEAR::isError( $res ) )
				$this->_writeFooter();

			$this->_close();
			@gzclose( $v_temp_tar );

			if ( !@unlink( $this->_tarname . ".tmp" ) )
				return PEAR::raiseError( "Error while deleting tempfile: " . $this->_tarname . ".tmp" );

            return true;
        }

        // for not compressed tar, just add files before the last 512 bytes block
		$res = $this->_openReadWrite();
		
		if ( !$res || PEAR::isError( $res ) )
			return false;

		$v_size = filesize( $this->_tarname );
		fseek( $this->_file, $v_size - 512 );

		$res = $this->_addList( $p_filelist, $p_add_dir, $p_remove_dir );
		
        if ( $res && !PEAR::isError( $res ) )
           $this->_writeFooter();

        $this->_close();
        return true;
    }

	/**
	 * @access private
	 */
    function _dirCheck( $p_dir )
    {
        if ( ( @is_dir( $p_dir ) ) || ( $p_dir == "" ) )
            return true;

        $p_parent_dir = dirname( $p_dir );

        if ( ( $p_parent_dir != $p_dir ) && ( $p_parent_dir != "" ) && ( !$this->_dirCheck( $p_parent_dir ) ) )
			return false;

		if ( !@mkdir( $p_dir, 0777 ) )
			return false;

        return true;
    }

	/**
	 * @access private
	 */
	function _pathReduction( $p_dir )
    {
        $v_result = "";

        // Look for not empty path
        if ( $p_dir != "" )
		{
            // Explode path by directory names
            $v_list = explode( DIRECTORY_SEPARATOR, $p_dir );

            // Study directories from last to first
            for ( $i = sizeof( $v_list ) - 1; $i >= 0; $i-- )
			{
                // Look for current path
                if ( $v_list[$i] == "." )
				{
                    // Ignore this directory
                    // Should be the first $i=0, but no check is done
                }
                else if ( $v_list[$i] == ".." )
				{
                    // Ignore it and ignore the $i-1
                    $i--;
                }
                else if ( ( $v_list[$i] == "" ) && ( $i != ( sizeof( $v_list ) - 1 ) ) && ( $i != 0 ) )
				{
                    // Ignore only the double '//' in path,
                    // but not the first and last '/'
                }
				else
				{
                    $v_result = $v_list[$i] . ( $i != ( sizeof( $v_list ) - 1 )? DIRECTORY_SEPARATOR . $v_result : "" );
                }
            }
        }
		
        return $v_result;
    }
} // END OF TarArchive

?>
