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
|Authors: Chuck Hagenbuch <chuck@horde.org>                            |
|         Michael Slusarz <slusarz@bigworm.colorado.edu>               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'peer.mail.mime.lib.MIME' );
using( 'util.text.StringUtil' );
using( 'locale.NLS' );


/** 
 * The character(s) used internally for EOLs. 
 */
define( 'MIME_PART_EOL', "\n" );

/** 
 * The character string designated by RFCs 822/2045 to designate EOLs in MIME messages. 
 */
define( 'MIME_PART_RFC_EOL', "\r\n" );

/* Default MIME parameters. */
/** 
 * The default MIME character set. 
 */
define( 'MIME_DEFAULT_CHARSET', 'us-ascii' );

/** 
 * The default MIME description. 
 */
define( 'MIME_DEFAULT_DESCRIPTION', "unnamed" );

/** 
 * The default MIME disposition. 
 */
define( 'MIME_DEFAULT_DISPOSITION', 'inline' );

/** 
 * The default MIME encoding. 
 */
define( 'MIME_DEFAULT_ENCODING', '7bit' );


/**
 * The MIME_Part class provides a wrapper around MIME parts and methods
 * for dealing with them.
 *
 * @package peer_mail_mime_lib
 */
 
class MIME_Part extends PEAR
{
    /**
     * The type (ex.: text) of this part.
     * Per RFC 2045, the default is 'application'.
     *
     * @var string $_type
     */
    var $_type = 'application';

    /**
     * The subtype (ex.: plain) of this part.
     * Per RFC 2045, the default is 'octet-stream'.
     *
     * @var string $_subtype
     */
    var $_subtype = 'octet-stream';

    /**
     * The body of the part.
     *
     * @var string $_contents
     */
    var $_contents = '';

    /**
     * The transfer encoding of this part.
     *
     * @var string $_transferEncoding
     */
    var $_transferEncoding = null;

    /**
     * Should the message be encoded via 7-bit?
     *
     * @var boolean $_encode7bit
     */
    var $_encode7bit = true;

    /**
     * The description of this part.
     *
     * @var string $_description
     */
    var $_description = '';

    /**
     * The disposition of this part (inline or attachment).
     *
     * @var string $_disposition
     */
    var $_disposition = null;

    /**
     * The disposition parameters of this part.
     *
     * @var array $_dispositionParameters
     */
    var $_dispositionParameters = array();

    /**
     * The content type parameters of this part.
     *
     * @var array $_contentTypeParameters
     */
    var $_contentTypeParameters = array();

    /**
     * The subparts of this part.
     *
     * @var array $_parts
     */
    var $_parts = array();

    /**
     * Information/Statistics on the subpart.
     *
     * @var array $_information
     */
    var $_information = array();

    /**
     * The list of CIDs for this part.
     *
     * @var array $_cids
     */
    var $_cids = array();

    /**
     * The MIME ID of this part.
     *
     * @var string $_mimeid
     */
    var $_mimeid = null;

    /**
     * The sequence to use as EOL for this part.
     * The default is currently to output the EOL sequence internally as
     * just "\n" instead of the canonical "\r\n" required in RFC 822 & 2045.
     * To be RFC complaint, the full <CR><LF> EOL combination should be used
     * when sending a message.
     * It is not crucial here since the PHP mailing functions will handle
     * the EOL details.
     *
     * @var string $_eol
     */
    var $_eol = MIME_PART_EOL;

    /**
     * Internal class flags.
     *
     * @var array $_flags
     */
    var $_flags = array();

    /**
     * Part -> ID mapping cache.
     *
     * @var array $_idmap
     */
    var $_idmap = array();

    /**
     * Unique MIME_Part ID code.
     *
     * @var string $_uniqueid
     */
    var $_uniqueid;

    /**
     * Default value for this Part's size.
     *
     * @var integer $_bytes
     */
    var $_bytes = 0;


    /**
     * Constructor
     *
     * @access public
     *
     * @param optional string $mimetype     The mimetype (ex.: 'text/plain') of
     *                                      the part.
     * @param optional string $contents     The body of the part.
     * @param optional string $charset      The character set of the part.
     * @param optional string $disposition  The content disposition of the part.
     */
    function MIME_Part( $mimetype = null, $contents = null, $charset = null, $disposition = null )
    {
        if ( !is_null( $mimetype ) )
            $this->setType( $mimetype );
        
        if ( !is_null( $contents ) )
            $this->setContents( $contents );
        
        if ( !is_null( $charset ) )
            $this->setCharset( $charset );
        
        if ( !is_null( $disposition ) )
            $this->setDisposition( $disposition );

        /* Create the unique MIME_Part identifier. */
        $this->_uniqueid = $this->_createBoundaryString();
    }


    /**
     * Set the content-disposition of this part.
     *
     * @access public
     *
     * @param string $disposition  The content-disposition to set (inline or
     *                             attachment).
     */
    function setDisposition( $disposition )
    {
        $disposition = StringUtil::lower( $disposition );

        if ( ( $disposition == 'inline' ) || ( $disposition == 'attachment' ) )
            $this->_disposition = $disposition;
    }

    /**
     * Get the content-disposition of this part.
     *
     * @access public
     *
     * @return string  The part's content-disposition.
     */
    function getDisposition()
    {
        return ( is_null( $this->_disposition ) )? MIME_DEFAULT_DISPOSITION : $this->_disposition;
    }

    /**
     * Set the name of this part.
     *
     * @access public
     *
     * @param string $name The name to set.
     */
    function setName( $name )
    {
        $this->setContentTypeParameter( 'name', $name );
    }

    /**
     * Get the name of this part.
     *
     * @access public
     *
     * @param optional boolean $decode   MIME decode description?
     * @param optional boolean $default  If the name parameter doesn't exist,
     *                                   should we use the default name from
     *                                   the description parameter?
     *
     * @return string  The name of the part.
     */
    function getName( $decode = false, $default = false )
    {
        $name = $this->getContentTypeParameter( 'name' );

        if ( $default && empty( $name ) )
            $name = preg_replace('|\W|', '_', $this->getDescription( false, true ) );

        if ( $decode )
            return trim( MIME::decode( $name ) );
        else
            return $name;
    }

    /**
     * Set the body contents of this part.
     *
     * @access public
     *
     * @param string $contents  The part body.
     */
    function setContents( $contents )
    {
        $this->_contents = $contents;
        $this->_flags['contentsSet']    = true;
        $this->_flags['transferDecode'] = false;
    }

    /**
     * Add to the body contents of this part.
     *
     * @access public
     *
     * @param string $contents  The content to append to the part body.
     */
    function appendContents( $contents )
    {
        $this->setContents( $this->_contents . $contents );
    }

    /**
     * Clears the body contents of this part.
     *
     * @access public
     */
    function clearContents()
    {
        $this->_contents = '';
        $this->_flags['contentsSet']    = false;
        $this->_flags['transferDecode'] = false;
    }

    /**
     * Return the body of the part.
     *
     * @access public
     *
     * @return string  The raw body of the part.
     */
    function getContents()
    {
        return $this->_contents;
    }

    /**
     * Returns the contents in strict RFC 822 & 2045 output - namely, all
     * newlines end with the canonical <CR><LF> sequence.
     *
     * @access public
     *
     * @return string  The entire MIME part.
     */
    function getCanonicalContents()
    {
        $oldEOL = $this->getEOL();
        $this->setEOL( MIME_PART_RFC_EOL );
        $part = $this->replaceEOL( $this->getContents() );
        $this->setEOL( $oldEOL );

        return $part;
    }

    /**
     * Transfer decode the contents and set them as the new contents.
     *
     * @access public
     *
     * @param optional boolean $charset  The character set to use for decoding.
     */
    function transferDecodeContents( $charset = null )
    {
        $this->setContents( $this->transferDecode( $charset ) );

        /* If the original contents were either quoted-printable or base64,
           this has been changed to '8bit' by the decoding process. */
        $decoding = $this->getTransferEncoding();
        
		if ( ( $decoding == 'quoted-printable' ) || ( $decoding == 'base64' ) )
            $this->setTransferEncoding( '8bit' );

        $this->_flags['transferDecode'] = true;
    }

    /**
     * Set the mimetype of this part.
     *
     * @access public
     *
     * @param string $mimetype  The mimetype to set (ex.: text/plain).
     */
    function setType( $mimetype )
    {
        /* RFC 2045: Any entity with unrecognized encoding must be treated
           as if it has a Content-Type of "application/octet-stream"
           regardless of what the Content-Type field actually says. */

        if ( $this->_transferEncoding == 'x-unknown' )
            return;

        /* Set the 'setType' flag. */
        $this->_flags['setType'] = true;

        list( $this->_type, $this->_subtype ) = explode( '/', StringUtil::lower( $mimetype ) );
		
        if ( ( $type = MIME::type( $this->_type, MIME_STRING ) ) )
            $this->_type = $type;
        else
            $this->_type = 'x-unknown';

        /* Set the boundary string(s), if necessary. */
        $this->_setBoundary();
    }

    /**
     * Get the full mimetype of this part.
     *
     * @access public
     *
     * @return string  The mimetype of this part (ex.: text/plain).
     */
    function getType()
    {
        if ( !isset( $this->_type ) || !isset( $this->_subtype ) )
            return false;
        
        return $this->getPrimaryType() . '/' . $this->getSubType();
    }

    /**
     * If the subtype of a MIME part is unrecognized by an application, the
     * default type should be used instead (See RFC 2046). This method
     * returns the default subtype for a particular primary MIME Type.
     *
     * @access public
     *
     * @return string  The default mimetype of this part (ex.: text/plain).
     */
    function getDefaultType()
    {
        switch ( $this->getPrimaryType() ) 
		{
        	case 'text':
        	    /* RFC 2046 (4.1.4): text parts default to text/plain. */
        	    return 'text/plain';

        	case 'multipart':
        	    /* RFC 2046 (5.1.3): multipart parts default to multipart/mixed. */
            	return 'multipart/mixed';

        	default:
        	    /* RFC 2046 (4.2, 4.3, 4.4, 4.5.3, 5.2.4): all others default to
        	       application/octet-stream. */
        	    return 'application/octet-stream';
        }
    }

    /**
     * Get the primary type of this part.
     *
     * @access public
     *
     * @return string  The primary MIME type of this part.
     */
    function getPrimaryType()
    {
        return $this->_type;
    }

    /**
     * Get the subtype of this part.
     *
     * @access public
     *
     * @return string  The MIME subtype of this part.
     */
    function getSubType()
    {
        return $this->_subtype;
    }

    /**
     * Set the character set of this part.
     *
     * @access public
     *
     * @param string $charset  The character set of this part.
     */
    function setCharset( $charset )
    {
        $this->setContentTypeParameter( 'charset', $charset );
    }

    /**
     * Get the character set of this part. This function only will return
     * data if this is a text/* part.
     *
     * @access public
     *
     * @param optional boolean $default  If no charset set for this Part,
     *                                   get the default Horde-wide charset.
     *
     * @return string  The character set of this part.
     */
    function getCharset( $default = false )
    {
        /* If this is not a text/* part, character set isn't pertinent. */
        if ( $this->getPrimaryType() != 'text' )
            return;

        $charset = $this->getContentTypeParameter( 'charset' );

        if ( $default && empty( $charset ) )
            return NLS::getCharset( !MIME::is8bit( $this->getContents() ) );
        else
            return $charset;
    }

    /**
     * Set the description of this part.
     *
     * @access public
     *
     * @param string $charset  The description of this part.
     */
    function setDescription( $description, $charset = "" )
    {
        $this->_description = MIME::encode( $description, $charset );
    }

    /**
     * Get the description of this part.
     *
     * @access public
     *
     * @param optional boolean $decode   MIME decode description?
     * @param optional boolean $default  If the name parameter doesn't exist,
     *                                   should we use the default name from
     *                                   the description parameter?
     *
     * @return string  The description of this part.
     */
    function getDescription( $decode = false, $default = false )
    {
        $desc = $this->_description;

        if ( $default && empty( $desc ) ) 
		{
            $desc = $this->getName();
            
			if ( empty( $desc ) )
                $desc = MIME_DEFAULT_DESCRIPTION;
        }

        if ( $decode )
            return MIME::decode( $desc );
        else
            return $desc;
    }

    /**
     * Set the transfer encoding of the part.
     *
     * @access public
     *
     * @param string $encoding           The transfer encoding to use.
     * @param optional boolean $encoded  Have the contents already been
     *                                   transfer encoded?
     */
    function setTransferEncoding( $encoding, $encoded = false )
    {
        $this->_flags['alreadyEncoded'] = $encoded;

        if ( ( $mime_encoding = MIME::encoding( $encoding, MIME_STRING ) ) ) 
		{
            $this->_transferEncoding = $mime_encoding;
        } 
		else 
		{
            /* RFC 2045: Any entity with unrecognized encoding must be treated
               as if it has a Content-Type of "application/octet-stream"
               regardless of what the Content-Type field actually says. */
            $this->setType( 'application/octet-stream' );
            $this->_transferEncoding = 'x-unknown';
        }
    }

    /**
     * Get the transfer encoding for the part.
     *
     * @access public
     *
     * @return string  The transfer-encoding of this part.
     */
    function getTransferEncoding()
    {
        if ( is_null( $this->_transferEncoding ) )
            return $this->_getEncoding();
        else
            return $this->_transferEncoding;
    }

    /**
     * Add a MIME subpart.
     *
     * @access public
     *
     * @param object MIME_Part $mime_part  Add a MIME_Part subpart to the
     *                                     current MIME_Part.
     * @param optional string $index       The index of the added MIME_Part.
     */
    function addPart( $mime_part, $index = null )
    {
        /* Check to see if we need to add boundary strings. */
        $mime_part->_setBoundary();

        /* Add the part to the parts list. */
        if ( is_null( $index ) ) 
		{
            end( $this->_parts );
            $id  = key( $this->_parts ) + 1;
            $ptr = &$this->_parts;
        } 
		else 
		{
            $ptr = &$this->_partFind( $index, $this->_parts, true );
			
            if ( ( $pos = strrpos( $index, '.' ) ) )
                $id = substr( $index, $pos + 1 );
            else
                $id = $index;
        }

        /* Set the MIME ID if it has not already been set. */
        if ( $mime_part->getMIMEId() === null )
            $mime_part->setMIMEId( $id );

        /* Store the part now. */
        $ptr[$id] = $mime_part;

        /* Clear the ID -> Part mapping cache. */
        $this->_idmap = array();
    }

    /**
     * Get a list of all MIME subparts.
     *
     * @access public
     *
     * @return array  An array of the MIME_Part subparts.
     */
    function getParts()
    {
        return $this->_parts;
    }

    /**
     * Retrieve a specific MIME part.
     *
     * @access public
     *
     * @param string $id  The MIME_Part ID string.
     *
     * @return object MIME_Part  The MIME_Part requested.  Returns false if
     *                           the part doesn't exist.
     */
    function getPart( $id )
    {
        $orig = null;
        $mimeid = $this->getMIMEId();

        /* Return this part if:
           1) There is only one part (e.g. the MIME ID is 0, or the
              MIME ID is 1 and there are no subparts.
           2) $id matches this parts MIME ID. */
        if ( ( $id == 0 ) || ( ( $id == 1 ) && !count( $this->_parts ) ) || ( !empty( $mimeid ) && ( $id == $mimeid ) ) )
            return $this;

        return $this->_partFind( $id, $this->_parts );
    }

    /**
     * Remove a MIME_Part subpart.
     *
     * @access public
     *
     * @param string $id  The MIME Part to delete.
     */
    function removePart( $id )
    {
        if ( ( $ptr = &$this->_partFind( $id, $this->_parts ) ) ) 
		{
            unset( $ptr );
            $this->_idmap = array();
        }
    }

    /**
     * Alter a current MIME subpart.
     *
     * @access public
     *
     * @param string $id                   The MIME Part ID to alter.
     * @param object MIME_Part $mime_part  The MIME Part to store.
     */
    function alterPart( $id, $mime_part )
    {
        if ( ( $ptr = &$this->_partFind( $id, $this->_parts ) ) ) 
		{
            $ptr = $mime_part;
            $this->_idmap = array();
        }
    }

    /**
     * Add information about the MIME_Part.
     *
     * @access public
     *
     * @param string $label  The information label.
     * @param mixed $data    The information to store.
     */
    function setInformation( $label, $data )
    {
        $this->_information[$label] = $data;
    }

    /**
     * Retrieve information about the MIME_Part.
     *
     * @access public
     *
     * @param string $label  The information label.
     *
     * @return mixed  The information requested.
     *                Returns false if $label is not set.
     */
    function getInformation( $label )
    {
        return ( isset( $this->_information[$label] ) )? $this->_information[$label] : false;
    }

    /**
     * Add a disposition parameter to this part.
     *
     * @access public
     *
     * @param string $label  The disposition parameter label.
     * @param string $data   The disposition parameter data.
     */
    function setDispositionParameter( $label, $data )
    {
        $this->_dispositionParameters[$label] = $data;
    }

    /**
     * Get a disposition parameter from this part.
     *
     * @access public
     *
     * @param string $label  The disposition parameter label.
     *
     * @return string  The data requested.
     *                 Returns false if $label is not set.
     */
    function getDispositionParameter( $label )
    {
        return ( isset( $this->_dispositionParameters[$label] ) )? $this->_dispositionParameters[$label] : false;
    }

    /**
     * Get all parameters from the Content-Disposition header.
     *
     * @access public
     *
     * @return array  An array of all the parameters
     *                Returns the empty array if no parameters set.
     */
    function getAllDispositionParameters()
    {
        return $this->_dispositionParameters;
    }

    /**
     * Add a content type parameter to this part.
     *
     * @access public
     *
     * @param string $label  The disposition parameter label.
     * @param string $data   The disposition parameter data.
     */
    function setContentTypeParameter($label, $data)
    {
        $this->_contentTypeParameters[$label] = $data;
    }

    /**
     * Get a content type  parameter from this part.
     *
     * @access public
     *
     * @param string $label  The content type parameter label.
     *
     * @return string  The data requested.
     *                 Returns false if $label is not set.
     */
    function getContentTypeParameter($label)
    {
        return ( isset( $this->_contentTypeParameters[$label] ) )? $this->_contentTypeParameters[$label] : false;
    }

    /**
     * Get all parameters from the Content-Type header.
     *
     * @access public
     *
     * @return array  An array of all the parameters
     *                Returns the empty array if no parameters set.
     */
    function getAllContentTypeParameters()
    {
        return $this->_contentTypeParameters;
    }

    /**
     * Sets a new string to use for EOLs.
     *
     * @access public
     *
     * @param string $eol  The string to use for EOLs.
     */
    function setEOL( $eol )
    {
        $this->_eol = $eol;
    }

    /**
     * Get the string to use for EOLs.
     *
     * @access public
     *
     * @return string  The string to use for EOLs.
     */
    function getEOL()
    {
        return $this->_eol;
    }

    /**
     * Add the appropriate MIME headers for this part to an existing array.
     *
     * @access public
     *
     * @param array $headers  An array of any other headers for the part.
     *
     * @return array  The headers, with the MIME headers added.
     */
    function header( $headers )
    {
        $eol   = $this->getEOL();
        $ptype = $this->getPrimaryType();
        $stype = $this->getSubType();

        /* Get the character set for this part. */
        $charset = $this->getCharset( true );

        /* Get the Content-Type - this is ALWAYS required. */
        $ctype = $this->getType();

        foreach ( $this->getAllContentTypeParameters() as $key => $value ) 
		{
            /* Check to see if we need charset information. */
            if ( $key == 'charset' ) 
			{
                if ( empty( $charset ) )
                    continue;
                else
                    unset( $charset );
            }
			
            $ctype .= '; ' . $key . '="' . MIME::encode( $value, NLS::getCharset() ) . '"';
        }

        /* If $charset is still set, we need to add it to Content-Type. */
        if ( isset( $charset ) && $charset )
            $ctype .= '; ' . 'charset="' . $charset . '"';
        
        $headers['Content-Type'] = MIME::wrapHeaders( 'Content-Type', $ctype, $eol );

        /* Get the description, if any. */
        if ( ( $descrip = $this->getDescription() ) )
            $headers['Content-Description'] = MIME::wrapHeaders( 'Content-Description', MIME::encode( $descrip, NLS::getCharset()), $eol );

        /* RFC 2045 [4] - message/rfc822 and message/partial require the
           MIME-Version header only if they themselves claim to be MIME
           compliant. */
        if ( ( $ptype == 'message' ) && ( ( $stype == 'rfc822' ) || ( $stype == 'partial' ) ) && ( strstr( $this->getContents(), 'MIME-Version: 1.0' ) ) )
            $headers['MIME-Version'] = '1.0';

        /* message/* parts require no additional header information. */
        if ( $ptype == 'message' )
            return $headers;

        /* Don't show Content-Disposition for multipart messages unless
           there is a name parameter. */
        $name = $this->getName();
        
		if ( ( $ptype != 'multipart' ) || !empty( $name ) ) 
		{
            $disp = $this->getDisposition();

            /* Add any disposition parameter information, if available. */
            if ( !empty( $name ) )
                $disp .= '; ' . 'filename="' . MIME::encode( $name, NLS::getCharset() ) . '"';

            $headers['Content-Disposition'] = MIME::wrapHeaders( 'Content-Disposition', $disp, $eol );
        }

        /* Add transfer encoding information. */
        $headers['Content-Transfer-Encoding'] = $this->getTransferEncoding();

        return $headers;
    }

    /**
     * Return the entire part in MIME format. Includes headers on request.
     *
     * @access public
     *
     * @param optional boolean $headers  Include the MIME headers?
     *
     * @return string  The MIME string.
     */
    function toString( $headers = true )
    {
        $eol   = $this->getEOL();
        $ptype = $this->getPrimaryType();
        $text  = '';

        if ( $headers ) 
		{
            foreach ( $this->header( array() ) as $key => $val ) 
                $text .= $key . ': ' . $val . $eol;
            
            $text .= $eol;
        }

        /* Any information about a message/* is embedded in the message
           contents themself. Simply output the contents of the part
           directly and return. */
        if ( $ptype == 'message' )
            return $text . $this->getContents();

        $text .= $this->transferEncode();

        /* Deal with multipart messages. */
        if ( $ptype == 'multipart' ) 
		{
            $boundary = trim( $this->getContentTypeParameter( 'boundary' ), '"' );
			
            if ( !( $this->getContents() ) )
                $text .= 'This message is in MIME format.' . $eol;
            
            foreach ( $this->getParts() as $part ) 
			{
                $text   .= $eol . '--' . $boundary . $eol;
                $oldEOL  = $part->getEOL();
                $part->setEOL( $eol );
                $text .= $part->toString( true );
                $part->setEOL( $oldEOL );
            }
			
            $text .= $eol . '--' . $boundary . '--' . $eol;
        }

        return $text;
    }

    /**
     * Returns the encoded part in strict RFC 822 & 2045 output - namely, all
     * newlines end with the canonical <CR><LF> sequence.
     *
     * @access public
     *
     * @param optional boolean $headers  Include the MIME headers?
     *
     * @return string  The entire MIME part.
     */
    function toCanonicalString( $headers = true )
    {
        $oldEOL = $this->getEOL();
        $this->setEOL( MIME_PART_RFC_EOL );
        $part = $this->toString( $headers );
        $this->setEOL( $oldEOL );

        return $part;
    }

    /**
     * Should we make sure the message is encoded via 7-bit (e.g. to adhere
     * to mail delivery standards such as RFC 2821)?
     *
     * @access public
     *
     * @param boolean $use7bit  Use 7-bit encoding?
     */
    function strict7bit( $use7bit )
    {
        $this->_encode7bit = $use7bit;
    }
 
    /**
     * Encodes the contents with the part's transfer encoding.
     *
     * @access public
     *
     * @return string  The encoded text.
     */
    function transferEncode()
    {
        $contents = $this->getContents();
        $eol      = $this->getEOL();
        $encoding = $this->getTransferEncoding();

        /* If the contents have already been transfer encoded, return now. */
        if ( !empty( $this->_flags['alreadyEncoded'] ) )
            return $contents;

        switch ( $encoding ) 
		{
        	/* Base64 Encoding: See RFC 2045, section 6.8 */
        	case 'base64':
        	    /* Keeping these two lines separate seems to use much less
        	       memory than combining them (as of PHP 4.3). */
        	    $encoded_contents = base64_encode( $contents );
        	    return chunk_split( $encoded_contents, 76, $eol );

	        /* Quoted-Printable Encoding: See RFC 2045, section 6.7 */
	        case 'quoted-printable':
	            $output = '';
	
				foreach ( preg_split( "/\r?\n/", $contents) as $line ) 
				{
                	/* We need to go character by character through the line */
                	$length = strlen( $line );
                	$current_line = '';

                	for ( $i = 0; $i < $length; $i++ ) 
					{
                    	$char  = substr( $line, $i, 1 );
                    	$ascii = ord( $char );

	                    /* Spaces or tabs at the end of the line are NOT allowed.
	                       Also, Characters in ASCII below 32 or above 126 AND 61
	                       must be encoded. */
	                    if ( ( ( ( $ascii === 9 ) || ( $ascii === 32 ) ) && ( $i == ( $length - 1 ) ) ) || ( ($ascii < 32 ) || ( $ascii > 126 ) || ( $ascii === 61 ) ) )
                        	$char = '=' . StringUtil::upper( sprintf( '%02s', dechex( $ascii ) ) );
                    
	                    /* Lines must be 76 characters or less */
	                    if ( ( strlen( $current_line ) + strlen( $char ) ) > 76 ) 
						{
                        	$output .= $current_line . '=' . $eol;
                        	$current_line  = '';
                    	}

                    	$current_line .= $char;
                	}
                
					$output .= $current_line . $eol;
            	}
				
            	return $output;

	        default:
            	return $this->replaceEOL( $contents );
        }
    }

    /**
     * Decodes the contents of the part using the part's transfer encoding.
     *
     * @access public
     *
     * @param optional string $charset  The charset to use while decoding.
     *
     * @return string  The decoded text.
     *                 Returns the empty string if there is no text to decode.
     */
    function transferDecode( $charset = null )
    {
        $message  = '';
        $contents = $this->getContents();

        if ( !empty( $this->_flags['transferDecode'] ) )
            return $contents;
        
        if ( empty( $contents ) )
            return $message;

        $encoding = $this->getTransferEncoding();

        switch ( $encoding ) 
		{
        	case 'base64':
        	    $message = base64_decode( $contents );
        	    break;

	        case 'quoted-printable':
	            $message = preg_replace( "/=\r?\n/", '', $contents );
	            $message = $this->replaceEOL( $message );
	            $message = quoted_printable_decode( $message );
	            break;

	        default:
	            $message = $this->replaceEOL( $contents );
	            break;
        }

        if ( empty( $message ) )
            $message = $contents;

        /* Do character set conversions now. */
        if ( ( $msg_charset = $this->getCharset( true ) ) )
            $message = StringUtil::convertCharset( $message, $msg_charset, $charset );

        return $message;
    }

    /**
     * Split the contents of the current Part into its respective subparts,
     * if it is multipart MIME encoding. Unlike the imap_*() functions, this
     * will preserve all MIME header information.
     *
     * The boundary parameter must be set for this function to work correctly.
     *
     * @access public
     *
     * @return boolean  True if the contents were successfully split.
     *                  False if any error occurred.
     */
    function splitContents()
    {
        if ( !( $boundary = $this->getContentTypeParameter( 'boundary' ) ) )
            return false;

        if ( !( $contents = $this->getContents() ) )
            return false;

        $eol = $this->getEOL();

        foreach ( explode( $eol, $contents ) as $line ) 
		{
            $pos = strpos( $line, '--' . $boundary );
			
            if ( $pos === false && isset( $part_ptr ) ) 
			{
                $data[] = $line;
            } 
			else if ( $pos === 0 ) 
			{
                if ( isset( $part_ptr ) ) 
				{
                    $this->_parts[$part_ptr]->setContents( implode( $eol, $data ) );
                    $this->_parts[$part_ptr]->splitContents();
                    next( $this->_parts );
                } 
				else 
				{
                    reset( $this->_parts );
                }
				
                if ( isset( $data ) )
                    unset( $data );
                
                $data = array();
                $part_ptr = key( $this->_parts );
            }
        }

        return true;
    }

    /**
     * Replace newlines with those specified by the current setting of the
     * $eol varaible
     *
     * @access private
     *
     * @param string $text  The text to replace.
     *
     * @return string  The text with the newlines replaced by the desired
     *                 newline sequence.
     */
    function replaceEOL( $text )
    {
        return preg_replace( "/\r?\n/", $this->getEOL(), $text );
    }

    /**
     * Sets the MIME boundary string for all multipart sections in this
     * part and its subparts.
     *
     * @access private
     */
    function _setBoundary()
    {
        if ( ( $this->getPrimaryType() == 'multipart' ) && !( $this->getContentTypeParameter( 'boundary' ) ) )
            $this->setContentTypeParameter( 'boundary', $this->_createBoundaryString() );

        /* Recurse through any subparts and add boundary strings. */
        foreach ( $this->getParts() as $part )
            $part->_setBoundary();
    }

    /**
     * Creates a random string, useful for MIME boundaries.
     *
     * @access private
     *
     * @return string  A random string.
     */
    function _createBoundaryString()
    {
        return '=_' . base_convert( microtime(), 10, 36 );
    }

    /**
     * Determine the size of a MIME_Part and its child members.
     *
     * @access public
     *
     * @return integer  Size of the MIME_Part, in bytes.
     */
    function getBytes()
    {
        $bytes = 0;

        if ( empty( $this->_flags['contentsSet']) && $this->_bytes ) 
		{
            $bytes = $this->_bytes;
        } 
		else if ( $this->getPrimaryType() == 'multipart' ) 
		{
            foreach ( $this->getParts() as $part ) 
			{
                /* Skip multipart entries (since this may result in double
                   counting). */
                if ( $part->getPrimaryType() != 'multipart' )
                    $bytes += $part->getBytes();
            }
        } 
		else 
		{
            if ( $this->getPrimaryType() == 'text' )
                $bytes = StringUtil::length( $this->getContents(), $this->getCharset( true ) );
            else
                $bytes = strlen( $this->getContents() );
        }

        return $bytes;
    }

    /**
     * Explicitly set the size (in bytes) of this part. This value will only
     * be returned (via getBytes()) if there are no contents currently set.
     * This function is useful for setting the size of the part when the
     * contents of the part are not fully loaded (i.e. creating a MIME_Part
     * object from IMAP header information without loading the data of the
     * part).
     *
     * @access public
     *
     * @param integer $bytes  The size of this part in bytes.
     */
    function setBytes( $bytes )
    {
        $this->_bytes = $bytes;
    }

    /**
     * Output the size of this MIME_Part in KB.
     *
     * @access public
     *
     * @return string  Size of the MIME_Part, in string format.
     */
    function getSize()
    {
        $bytes = $this->getBytes();
        
		if ( empty( $bytes ) )
            return $bytes;

        $localeinfo = NLS::getLocaleInfo();
        return number_format( $bytes / 1024, 2, $localeinfo['decimal_point'], '' );
     }

    /**
     * Add to the list of CIDs for this part.
     *
     * @access public
     *
     * @param array $cids  A list of MIME IDs of the part.
     *                     Key - MIME ID
     *                     Value - CID for the part
     */
    function addCID( $cids = array() )
    {
        $this->_cids += $cids;
    }

    /**
     * Returns the list of CIDs for this part.
     *
     * @access public
     *
     * @return array  The list of CIDs for this part.
     */
    function getCIDList()
    {
        asort( $this->_cids, SORT_STRING );
        return $this->_cids;
    }

    /**
     * Alter the MIME ID of this part.
     *
     * @access public
     *
     * @param string $mimeid  The MIME ID.
     */
    function setMIMEId( $mimeid )
    {
        $this->_mimeid = $mimeid;
    }

    /**
     * Returns the MIME ID of this part.
     *
     * @access public
     *
     * @return string  The MIME ID.
     */
    function getMIMEId()
    {
        return $this->_mimeid;
    }

    /**
     * Return the unique MIME_Part identifcation string for this object.
     *
     * @access public
     *
     * @return string  The unique ID.
     */
    function getUniqueID()
    {
        return $this->_uniqueid;
    }

    /**
     * Returns the relative MIME ID of this part.
     * e.g., if the base part has MIME ID of 2, and you want the first
     * subpart of the base part, the relative MIME ID is 2.1.
     *
     * @access public
     *
     * @param string $id  The relative part ID.
     *
     * @return string  The relative MIME ID.
     */
    function getRelativeMIMEId( $id )
    {
        $rel = $this->getMIMEId();
        return ( empty( $rel ) )? $id : $rel . '.' . $id;
    }

    /**
     * Returns a mapping of all MIME IDs to their content-types.
     *
     * @access public
     *
     * @return array  KEY: MIME ID, VALUE: Content type
     */
    function contentTypeMap()
    {
        $map = array( $this->getMIMEId() => $this->getType() );
		
        foreach ( $this->_parts as $val )
            $map += $val->contentTypeMap();
        
        return $map;
    }


	// private methods
	
    /**
     * Function used to find a specific MIME Part by ID.
     *
     * @access private
     *
     * @param string $id                  The MIME_Part ID string.
     * @param array &$parts               A list of MIME_Part objects.
     * @param optional boolean $retarray  Return a pointer to the array that
     *                                    stores (would store) the part
     *                                    rather than the part itself?
     */
    function &_partFind( $id, &$parts, $retarray = false )
    {
        if ( empty( $this->_idmap ) )
            $this->_generateIdMap( $this->_parts );

        if ( $retarray ) 
		{
            if ( $pos = strrpos( $id, '.' ) )
                $id = substr( $id, 0, $pos );
            else
                return $parts;
        }

        if ( isset( $this->_idmap[$id] ) )
            return $this->_idmap[$id];
        else
            return false;
    }

    /**
     * Generates a mapping of MIME_Parts with their MIME IDs.
     *
     * @access private
     *
     * @param array &$parts  An array of MIME_Parts to map.
     */
    function _generateIdMap( &$parts )
    {
        if ( !empty( $parts ) ) 
		{
            foreach ( array_keys( $parts ) as $key ) 
			{
                $ptr = &$parts[$key];
                $this->_idmap[$ptr->getMIMEId()] = &$ptr;
                $this->_generateIdMap( $ptr->_parts );
            }
        }
    }
	
    /**
     * Get the encoding of the message and the charset based on the
     * current language and browser capabilities and the current contents
     * of the part.
     *
     * @access private
     *
     * @return string  The encoding to use.
     */
    function _getEncoding()
    {
        $encoding = MIME_DEFAULT_ENCODING;
        $ptype    = $this->getPrimaryType();
        $text     = str_replace( $this->getEOL(), ' ', $this->getContents() );

        switch ( $ptype ) 
		{
        	case 'message':
        	    /* RFC 2046 [5.2.1] - message/rfc822 messages only allow 7bit,
        	       8bit, and binary encodings. If the current encoding is either
        	       base64 or q-p, switch it to 8bit instead. 
        	       RFC 2046 [5.2.2, 5.2.3, 5.2.4] - All other message/* messages
        	       only allow 7bit encodings. */
        	    if ( $this->getSubType() == 'rfc822' )
        	        $encoding = '8bit';
        	    else
        	        $encoding = '7bit';
        	    
        	    break;

	        case 'text':
	            if ( MIME::is8bit( $text ) ) 
				{
	                if ( $this->_encode7bit )
	                    $encoding = 'quoted-printable';
	                else
	                    $encoding = '8bit';
	            }
	
	            /* Set the character set. */
	            $this->setCharset( $this->getCharset( true ) );
	            break;

	        default:
	            /* By default, if there is 8bit data, we MUST either 8bit or
	               base64 encode contents. */
	            if ( MIME::is8bit( $text ) ) 
				{
	                if ( $this->_encode7bit )
	                    $encoding = 'base64';
	                else
	                    $encoding = '8bit';
	            }
				
	            break;
        }

        return $encoding;
    }
} // END OF MIME_Part

?>
