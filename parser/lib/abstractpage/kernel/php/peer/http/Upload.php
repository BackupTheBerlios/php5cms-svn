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
 * @todo Check that source_dir is writeable
 * @todo Allow for auto-rename option on overwrite
 * @package peer_http
 */
	
class Upload extends PEAR
{
	/**
	 * @access public
	 */
	var $uploadErrors;
	
	/**
	 * @access public
	 */
	var $registeredMimeTypes;
	
	/**
	 * @access public
	 */
	var $allowedMimeTypes;
	
	/**
	 * @access public
	 */
	var $maxImageWidth;
	
	/**
	 * @access public
	 */
	var $maxImageHeight;
	
	/**
	 * @access public
	 */
	var $maxFileSize;
	
	/**
	 * @access public
	 */
	var $uploadPath;
	
	/**
	 * @access public
	 */
	var $uploadFieldName;
	
	/**
	 * @access public
	 */
	var $fieldName;
	
	/**
	 * @access public
	 */
	var $errorType;
	
	/**
	 * @access public
	 */
	var $imageSizeOk;
	
	/**
	 * @access public
	 */
	var $uploadValidated;
	
	/**
	 * @access public
	 */
	var $uploadFail;
	
	/**
	 * @access public
	 */
	var $dubious_img_allowed;
	
	/**
	 * Used to track the number of fields created and name them accordingly 
	 * @access public
	 */
	var $fieldCounter;
	

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Upload( $check_env = false, $allow_dubious_images = true )
	{
		$this->debug = new Debug();
		$this->debug->Off();
		
		$this->uploadErrors 		= array();
		$this->registeredMimeTypes 	= array();
		$this->allowedMimeTypes 	= array();
		
		$this->maxImageWidth		= 0;
		$this->maxImageHeight		= 0;
		$this->maxFileSize			= 0;
		$this->fieldCounter			= 0;
		
		$this->uploadFieldName 		= "";
		$this->uploadPath			= "";
		
		$this->dubious_img_allowed  = $allow_dubious_images;
		$this->imageSizeOk			= false;
		$this->uploadValidated		= false;
		$this->uploadFail			= false;
		
		
		// set defaults
		if ( !$this->registeredMimeTypes )
			$this->setRegisteredMimeTypes();
		
		if ( !$this->maxImageWidth || !$this->maxImageHeight )
			$this->setMaxImageSize();
		
		if ( !$this->maxFileSize )
			$this->setMaxFileSize();
		
		if ( $check_env == true )
			$this->checkLocalEnv();
	}
	

	/**
	 * @access public
	 */
	function setMaxImageSize( $maxImageWidth = 300, $maxImageHeight = 300 )
	{
		$this->maxImageWidth  = $maxImageWidth;
		$this->maxImageHeight = $maxImageHeight;
	}

	/**
	 * @access public
	 */
	function setUploadPath( $uploadPath )
	{
		$this->uploadPath = $uploadPath;
	}

	/**
	 * @access public
	 */
	function setDestinationFileName( $destinationFileName = "uploadedFile.file" )
	{
		$this->uploadFieldName = $destinationFileName;
	}

	/**
	 * @access public
	 */	
	function setRegisteredMimeTypes( $registeredMimeTypes = array() )
	{
		if ( sizeof( $registeredMimeTypes ) == 0 )
		{
			$this->registeredMimeTypes = array(
				"application/x-gzip-compressed" 	=> ".tar.gz, .tgz",
				"application/x-zip-compressed" 		=> ".zip",
				"application/x-tar"					=> ".tar",
				"text/plain"						=> ".php, .txt, .inc (etc)",
				"text/html"							=> ".html, .htm (etc)",
				"image/bmp" 						=> ".bmp, .ico",
				"image/gif" 						=> ".gif",
				"image/pjpeg"						=> ".jpg, .jpeg",
				"image/jpeg"						=> ".jpg, .jpeg",
				"image/x-png"						=> ".png",
				"audio/mpeg"						=> ".mp3 etc",
				"audio/wav"							=> ".wav",
				"application/pdf"					=> ".pdf",
				"application/x-shockwave-flash" 	=> ".swf",
				"application/msword"				=> ".doc",
				"application/vnd.ms-excel"			=> ".xls",
				"application/octet-stream"			=> ".exe, .fla, .psd (etc)"
			);
		}
		else
		{
			$this->registeredMimeTypes = $registeredMimeTypes;
		}
	}

	/**
	 * @access public
	 */	
	function setAllowedMimeTypes( $allowedMimeTypes = array() )
	{
		$this->allowedMimeTypes = $allowedMimeTypes;
	}

	/**
	 * @access public
	 */
	function setMaxFileSize( $maxFileSize = 1048576 ) // means 1 MB
	{
		$this->maxFileSize = $maxFileSize;
	}

	/**
	 * @access public
	 */	
	function printFormStart( $formAction = "./", $formMethod = "POST", $formName = "uploadForm", $formTarget = "_self", $formInlineJavaScript="" )
	{
		echo( "<FORM ACTION='" . $formAction . 
			"' METHOD='" . $formMethod . 
			"' TARGET='" . $formTarget . 
			"' NAME='"   . $formName   . 
			"' ENCTYPE='multipart/form-data'" . $formInlineJavaScript . ">\n" );
	}

	/**
	 * @access public
	 */	
	function printFormField($fieldName = "") //+++
	{
		if ( !$fieldName )
			$fieldName = "uploadFile" . "_" . $this->fieldCounter;
		
		echo( "<INPUT TYPE='FILE' NAME='" . $fieldName . "'>\n" );
		echo( "<INPUT TYPE='HIDDEN' NAME='uploadFileName[" . $this->fieldCounter . "]' VALUE='" . $fieldName . "'>\n" );
		
		$this->fieldCounter++;
	}

	/**
	 * @access public
	 */	
	function printFormSubmit( $name = "submit", $value = "Upload", $formInlineJavaScript = "" )
	{
		echo( "<INPUT TYPE='HIDDEN' NAME='fieldCounter' VALUE='" . $this->fieldCounter . "'>\n" );
		echo( "<INPUT TYPE='SUBMIT' NAME='" . $name . "' VALUE='" . $value . "'" . $formInlineJavaScript . ">\n" );
	}

	/**
	 * @access public
	 */	
	function printFormEnd()
	{
		echo( "</FORM>\n" );
	}

	/**
	 * @access public
	 */
	function checkLocalEnv()
	{
		// this is a developer helper method and a pre-emptive strike
		// towards any support emails ;)
		echo( "<br />" . "::PHP Environment - php.ini settings::" . "<br />" );
		echo( "<br />" . "(php.ini variable: file_uploads)" . "<br />" );
		echo( "HTTP File Uploads are " );

		if ( ini_get( "file_uploads" ) )
		{
			echo( "[ On ]" );
		}
		else
		{
			echo( "[ Off ] - This is a *major* issue. This script WILL NOT WORK!" );
			echo( "<br />" . "Please check php.ini if you have access to it, if not you cannot use this Script, sorry." );
		}
			
		echo( "<br /><br />" . "(php.ini variable: upload_tmp_dir)" );
		echo( "<br />" . "Temp Upload Directory is set to [ " . ini_get("upload_tmp_dir") . " ]" );
		echo( "<br />" . "Note, this is a fully qualified path on the *server*" );
		echo( "<br />" . "<br />" . "(php.ini variable: upload_max_filesize)" );
		echo( "<br />" . "Maximum allowed file size is set to [ " . ini_get("upload_max_filesize") . " ]" );
		echo( "<br /><br />" . "(php.ini variable: safe_mode)" . "<br />" );
		echo( "Safe mode is " );
			
		if ( !ini_get( "safe_mode" ) )
		{
			echo( "[ Off ]" );
		}
		else
		{
			echo( "[ On ] - This script will almost certainly not work!" );
			echo( "<br />" . "Please check php.ini if you have access to it, if not you cannot use this Script, sorry." );
		}
	}

	/**
	 * @access public
	 */		
	function getAllowedMimeTypes()
	{
		return $this->allowedMimeTypes;
	}

	/**
	 * @access public
	 */		
	function getUploadImageSize()
	{
		$dimensions = getimagesize( $this->uploadFile );
		
		// I've been having some issues when uploading images with regards 
		// to the array passed back (i.e. No values)
		$this->debug->Message( "WIDTH: " . $dimensions[0] . "<br />" . "HEIGHT: " . $dimensions[1] . "<br />" );
		
		if ( !$this->dubious_img_allowed )
			return PEAR::raiseError( "Cannot get image size." );

		return array( $dimensions[0], $dimensions[1] );
	}

	/**
	 * @access public
	 */		
	function checkMimeType()
	{
		if ( !in_array( $this->http_post_files[$this->uploadFieldName]['type'], $this->getAllowedMimeTypes()) )
			return PEAR::raiseError( "Mime type is not in list." );
		else
			return true;
	}

	/**
	 * @access public
	 */		
	function checkImageSize()
	{
		$this->imageSize = $this->getUploadImageSize( $this->uploadFile );
		$imageSizeOK = true;
		
		if ( $this->imageSize[0] > $this->maxImageWidth )
		{
			$imageSizeOK = false;
			return PEAR::raiseError( "Wrong image width." );
		}

		if ( $this->imageSize[1] > $this->maxImageHeight )
		{
			$imageSizeOK = false;
			return PEAR::raiseError( "Wrong image height." );
		}
		
		return $imageSizeOK;
	}

	/**
	 * @access public
	 */		
	function copyFile()
	{
		// TODO check for is_writeable()
		return move_uploaded_file( $this->uploadFile, $this->uploadPath . DIRECTORY_SEPARATOR . $this->http_post_files[$this->uploadFieldName]['name'] );
	}

	/**
	 * @access public
	 */		
	function checkMaxFileSize()
	{
		if ( $this->http_post_files[$this->uploadFieldName]['size'] > $this->maxFileSize )
			return false;
		else
			return true;
	}

	/**
	 * @access public
	 */		
	function setDefaults()
	{
		if ( !$this->registeredMimeTypes )
			$this->setRegisteredMimeTypes();
		
		if ( !$this->maxImageWidth || !$this->maxImageHeight )
			$this->setMaxImageSize();
		
		if ( !$this->maxFileSize )
			$this->setMaxFileSize();
	}

	/**
	 * @access public
	 */		
	function processUpload()
	{
		/*
		 * Some MIME types seem to be rather randomly set, I'm assuming that this
		 * is an OS issue. For example MS Word documents have been, in my experience,
		 * application/octet-stream, text/richtext or application/msword.
		 * This is arguably useful for a development environment.
		 * Disabled by default.
		 */
		$this->debug->Message( "<br />" . "::DEBUG::" . "<br />"  .
			"Field Name: " . $this->uploadFieldName . "<br />" .
			"Mime Type: "  . $this->http_post_files[$this->uploadFieldName]['type']     . "<br />" .
			"File Name: "  . $this->http_post_files[$this->uploadFieldName]['name']     . "<br />" .
			"File Size: "  . $this->http_post_files[$this->uploadFieldName]['size']     . "<br />" .
			"Temp Name: "  . $this->http_post_files[$this->uploadFieldName]['tmp_name'] . "<br />"
		);
		
		$this->uploadFile = $this->http_post_files[$this->uploadFieldName]['tmp_name'];
		$this->setDefaults();

		if ( !$this->uploadPath )
		{
			$this->uploadFail = true;
			return PEAR::raiseError( "No upload path specified." );
		}

		if ( !$this->allowedMimeTypes )
		{
			$this->uploadFail = true;
			return PEAR::raiseError( "Mime type not allowed." );
		}

		if ( $this->uploadFile == "none" )
		{
			$this->uploadFail = true;
			return PEAR::raiseError( "No file specified." );
		}
		
		if ( !$this->checkMaxFileSize() )
		{
			$this->uploadFail = true;
			return PEAR::raiseError( "File is too large." );
		}
		
		if ( !$this->uploadFail )
		{
			if ( ereg( "image", $this->http_post_files[$this->uploadFieldName]['type'] ) )
				$this->imageSizeOk = $this->checkImageSize();
			else
				$this->imageSizeOk = true;
		}
		
		if ( $this->checkMimeType() && $this->imageSizeOk && !$this->uploadFail )
		{
			if ( !$this->copyFile() )
				return PEAR::raiseError( "Cannot copy file." );
		}
		
		if ( sizeof( $this->uploadErrors ) == 0 )
			$this->uploadValidated = true;
		
		return $this->uploadValidated;
	}

	/**
	 * @access public
	 */		
	function doUpload()
	{
		$this->http_post_files = $_FILES; // array
		$this->fieldCounter    = $GLOBALS['fieldCounter']; // int

		for ( $i = 0; $i < $this->fieldCounter; $i++ )
		{
			$this->uploadFieldName = $GLOBALS['uploadFileName'][$i];
			$currentUpload = $this->processUpload();

			if ( !$currentUpload || PEAR::isError( $currentUpload ) )
				$errorsOccured = true;
		}
		
		if ( $errorsOccured )
			return false;
		else
			return true;
	}
} // END OF Upload

?>
