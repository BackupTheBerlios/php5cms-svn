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


define( 'FILEUPLOADER_OK',                  100 );
define( 'FILEUPLOADER_NOT_UPLOADED',        101 );
define( 'FILEUPLOADER_FAILED_IO_OPERATION', 102 );
define( 'FILEUPLOADER_TYPE_NOT_ALLOWED',    103 );
define( 'FILEUPLOADER_IMAGE_TOO_WIDE',      104 );
define( 'FILEUPLOADER_IMAGE_TOO_HIGH',      105 );


/**
 * Handles file uploads on forms and places the files in the proper location
 * on the server.
 *
 * @package io
 */

class FileUploader extends PEAR
{
	/**
	 * Name of form element
	 * @access public
	 */
	var $file;
	
	/**
	 * Name of file
	 * @access public
	 */
	var $fileName;
	
	/**
	 * Type of file
	 * @access public
	 */
	var $fileType;

	/**
	 * Maximum file size in bytes
	 * @access public
	 */
	var $maxFileSize;

	/**
	 * @access public
	 */
	var $allowedTypes = array();

	/**
	 * Maximum width for an image
	 * @access public
	 */
	var $maxWidth  = "640";
	
	/**
	 * Maximum height for an image
	 * @access public
	 */
	var $maxHeight = "480";

	/**
	 * Message to display on success
	 * @access public
	 */
	var $successMessage;

	/**
	 * @access public
	 */
	var $registeredTypes = array(
		// images types
		"image/bmp"						=> ".bmp, .ico",
		"image/x-pcx"					=> ".pcx",
		"image/gif"						=> ".gif",
		"image/tiff"					=> ".tiff .tif .img",
		"image/tif"						=> ".tiff .tif .img",
		"image/pjpeg"					=> ".jpg, .jpeg",
		"image/jpeg"					=> ".jpg, .jpeg",
		"image/x-jpeg"					=> ".jpg, .jpeg",
		
		// archive file types
		"application/x-gzip-compressed"	=> ".tar.gz, .tgz",
		"application/x-zip-compressed"	=> ".zip",
		"application/x-tar"				=> ".tar",
		"application/x-gtar"			=> ".gtar",
		"application/zip"				=> ".zip",
		
		// text types
		"text/plain"					=> ".html, .php, .txt, .inc (etc)",
		"text/richtext"					=> " (etc)",

		// multimedia types
		"application/x-shockwave-flash" => ".swf",
		"application/futuresplash" 		=> ".spl",
		"audio/midi"                  	=> ".mid, .midi",
		"audio/wav"						=> ".wav",
		"audio/basic"					=> ".au, .snd",
		"audio/x-pn-realaudio"			=> ".ra, .ram, .rm",
		"audio/x-aiff"					=> ".aiff, .aifc",
		"video/mpeg"					=> ".mpeg, .mpg",
		"video/quicktime"				=> ".qt, .mov",
		"video/x-msvideo"				=> ".avi",

		// executable types
		"application/octet-stream"		=> ".exe, .fla (etc)",
		
		// document types
		"application/pdf"				=> ".pdf",
		"application/x-pdf"				=> ".pdf",
		"application/postscript"		=> ".ps, .eps (etc)",
		"application/ms-powerpoint"		=> ".ppt, .pot, pps, (etc)",
		"application/msword"			=> ".doc",
		"application/vnd.ms-excel"		=> ".xls"
	);

	
	/**
	 * Constructs a new FileUploader object given a file handle, file name,
	 * file type, and optional maximum file size.
	 *
	 * @param  $file int. A file handle
	 * @param  $fileName string. The name of the file
	 * @param  $fileType string. The type of the file
	 * @param  $maximumFileSize int. The maximum allowable file size
	 * @access public
	 */
	function FileUploader( $file, $fileName, $fileType,	$maximumFileSize = "76800" )
	{
		$this->file		   = $file;
		$this->fileName    = $fileName;
		$this->fileType    = $fileType;
		$this->maxFileSize = $maximumFileSize;
	}

	
	/**
	 * Sets the maximum width for an uploaded image.
	 *
	 * @param  $width int. Maximum image width
	 * @access public
	 * @return void
	 */
	function setMaximumWidth( $width ) 
	{
		$this->maxWidth = $width;
	}

	/**
	 * Sets the maximum height for an uploaded image.
	 *
	 * @param  $height int. Maximum image height
	 * @access public
	 * @return void
	 */
	function setMaximumHeight( $height ) 
	{
		$this->maxHeight = $height;
	}

	/**
	 * Uploads the file (if it passes validation) to the destination directory
	 * 'name' from the document root and then renames the file and moves it to 
	 * the actual destination directory using the new file name.  This filename
	 * SHOULD NOT contain the extension.  If no new file name is specified, it 
	 * uses the existing file name.  The function returns the complete file
	 * and path as a string if it was uploaded or returns false otherwise.
	 *
	 * @param  $location string. Where the file is to be placed
	 * @param  $newFileName string. The new name of the file
	 * @access public
	 * @return string
	 */
	function upload( $location, $newFileName ) 
	{
		// ensure the file is good
		$errorCode = $this->validate();

		// if an error occured during validation, output the error code
		if ( $errorCode != FILEUPLOADER_OK ) 
		{
			return $errorCode;
		} 
		else 
		{
			$originalFile = $this->getOriginalFile( $location );
			$isCopied     = $this->copyToOriginalFile( $originalFile );
			$newFile      = $this->getNewFile( $location, $newFileName );
			$isRenamed    = $this->renameOriginalToNew( $originalFile, $newFile );

			// if either copy or rename has went wrong, report error
			// otherwise return new file name
			if ( !$isCopied || !$isRenamed )
				return FILEUPLOADER_FAILED_IO_OPERATION;
			else
				return $this->removeDocumentRoot( $newFile );
		}
	}

	/**
	 * Returns the full path of the original file name that was posted.
	 *
	 * @param  $location string. Where the file is located
	 * @access public
	 * @return string
	 */
	function getOriginalFile( $location ) 
	{
		$originalFile = $location . '/' . $this->fileName;

		// in case the original file already exists, kill it
		if ( file_exists( $originalFile ) )
			unlink( $originalFile );

		return $originalFile;
	}

	/**
	 * Given the full path to the original file that was posted, copies the
	 * temporary file on the server to that location.  Deletes the temporary
	 * file afterwards.  Returns true if the copy was successful and false
	 * otherwise.
	 *
	 * @param  $originalFile string. Where the file is located
	 * @access public
	 * @return boolean
	 */
	function copyToOriginalFile( $originalFile ) 
	{
		// copy temp file to original file name in proper location
		$isCopied = copy( $this->file, $originalFile );

		// delete temp file made by server during post (if possible)
		if ( file_exists( $this->file ) )
			unlink( $this->file );

		return $isCopied;
	}

	/**
	 * Returns the full path of the new, destination file name.
	 *
	 * @param  $location string. Where the file is located
	 * @param  $newFileName string. The new name of the file
	 * @access public
	 * @return string
	 */
	function getNewFile( $location, $newFileName ) 
	{
		$fileExtension = strrchr( basename( $this->fileName ), '.' );
		$newFile = $location . '/' . $newFileName . $fileExtension;

		return $newFile;
	}

	/**
	 * Renames the original file to the new file name.
	 *
	 * @param  $originalFile string. The original file name
	 * @param  $newFile string. The new file name
	 * @access public
	 * @return boolean
	 */
	function renameOriginalToNew( $originalFile, $newFile ) 
	{
		// don't rename a file with the same name
		if ( $originalFile != $newFile ) 
		{
			// delete the new with the same name to ensure we can rename properly
			if ( file_exists( $newFile ) )
				unlink( $newFile );

			$isRenamed = rename( $originalFile, $newFile );
		} 
		else 
		{
			$isRenamed = true;
		}

		return $isRenamed;
	}

	/**
	 * Returns the *relative path* of a file string, taking out the document
	 * root.
	 *
	 * @param  $newFile string. The file name
	 * @access public
	 * @return void
	 */
	function removeDocumentRoot( $newFile ) 
	{
		global $DOCUMENT_ROOT;	
		return ereg_replace( $DOCUMENT_ROOT, '', $newFile );
	}

	/**
	 * Ensures the file that was uploaded exists, that it is one of the allowed
	 * types, and if the file is an image, it ensures the file has proper
	 * height and width.  The function will return false if no errors occured
	 * and will return and error code if any did occur.
	 *
	 * @return int
	 * @access public
	 */
	function validate() 
	{
		// if no file was posted, send an response otherwise valid the file type
		// against the allowed types
		if ( !$this->isFilePresent() ) 
		{
			return FILEUPLOADER_NOT_UPLOADED;
		} 
		else 
		{ 
			if ( !$this->isFileTypeValid() )
				return FILEUPLOADER_TYPE_NOT_ALLOWED;
			
			// if the file is an image, ensure it has proper height and width
			// and give an error if it doesn't
			if ( $this->isFileAnImage() ) 
			{
				$imageSize = GetImageSize( $this->file );

				// get the width and height of the image
				// list( $foo, $width, $bar, $height ) = explode( "\"", $imageSize[3] );
	
				// ensure width of the image is less than the maximum
				// if ( $width > $this->maxWidth )
				//	return FILEUPLOADER_IMAGE_TOO_WIDE;
				//

				// ensure height of the image is less than the maximum
				// if ( $height > $this->maxHeight )
				//	return FILEUPLOADER_IMAGE_TOO_HIGH;
			}
		}

		return FILEUPLOADER_OK;
	}

	/**
	 * Adds an allowed type for this file that is to be uploaded.  If the type
	 * is not in the registered types, than the type specified will not be
	 * added.  Returns true if type was added and false otherwise.
	 *
	 * @param  $type string. A file type
	 * @return boolean
	 * @access public
	 */
	function addAllowedType( $type ) 
	{
		// if the type exist inside the registered types, add it
		if ( isset( $this->registeredTypes[ $type ] ) ) 
		{
			$this->allowedTypes[] = $type;
			return true;
		}

		return false;
	}

	/**
	 * Adds all images types to allowed file types.
	 *
	 * @return void
	 * @access public
	 */
	function addImageTypes() 
	{
		foreach ( $this->registeredTypes as $type => $value ) 
		{
			if ( ereg( "image/", $type) )
				$this->addAllowedType( $type );
		}	
	}

	/**
	 * Adds all archive types to allowed file types.
	 *
	 * @return void
	 * @access public
	 */
	function addArchiveTypes() 
	{
		foreach ( $this->registeredTypes as $type => $value ) 
		{
			if ( ( $type == "application/zip"   ) ||
			     ( $type == "application/x-tar" ) ||
			     ( $type == "application/x-gzip-compressed" ) ||
			     ( $type == "application/x-tar-compressed"  ) ||
			     ( $type == "application/g-tar" ) )   
			{
				$this->addAllowedType( $type );
			}
		}	
	}

	/**
	 * Adds all types for this file that is to be uploaded.
	 *
	 * @return void
	 * @access public
	 */
	function addAllTypes() 
	{
		foreach ( $this->registeredTypes as $type => $value )
			$this->addAllowedType( $type );
	}

	/**
	 * Returns true if a file has been uploaded and false if it hasn't.
	 *
	 * @return boolean
	 * @access public
	 */
	function isFilePresent() 
	{
		return $this->file != "none";
	}

	/**
	 * Returns true if the file uploaded is from one of the valid file types
	 * specified to this object or false otherwise.
	 *
	 * @return boolean
	 * @access public
	 */
	function isFileTypeValid() 
	{
		return in_array( $this->fileType, $this->allowedTypes );
	}

	/**
	 * Returns true if the file uploaded is an image (from the mime type) or
	 * false otherwise.
	 *
	 * @return boolean
	 * @access public
	 */
	function isFileAnImage() 
	{
		// searches for the word "image" in the mime type
		return ereg( "image", $this->fileType );
	}
} // END OF FileUploader

?>
