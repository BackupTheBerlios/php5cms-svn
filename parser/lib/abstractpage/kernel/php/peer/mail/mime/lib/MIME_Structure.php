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
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/** 
 * The character set to use if none is specified. 
 */
define( 'MIME_DEFAULT_CHARSET', 'us-ascii' );

/** 
 * The encoding to use if none is specified. 
 */
define( 'MIME_DEFAULT_ENCODING', ENC7BIT );

/** 
 * The description to use for parts with none set. 
 */
define( 'MIME_DEFAULT_DESCRIPTION', 'unnamed' );


/**
 * The MIME_Structure class provides methods for dealing with MIME mail.
 *
 * @package peer_mail_mime_lib
 */
 
class MIME_Structure extends PEAR
{
    /**
     * Given the results of imap_fetchstructure(), parse the structure
     * of the message, figuring out correct bodypart numbers, etc.
     *
     * @access public
     *
     * @param object stdClass $body The result of imap_fetchstructure().
     * @param int $index            The IMAP UID of the message being parsed.
     * @param array &$MimeID        An array of Mime IDs to write any CIDs found to.
     *
     * @return array                An array containing all parts of the message.
     */
    function parse( $body, $index, &$MimeID )
    {
        if ( !is_array( $MimeID ) ) 
			$MimeID = array();
        
		$attachments = array();
		return MIME_Structure::_parse( $body, $index, $MimeID, $attachments );
    }

    /**
     * Given a mime part from imap_fetchstructure(), munge it into a
     * useful form and make sure that any parameters which are missing
     * are given default values.
     * @access private
     *
     * @param object stdClass $part The original part info.
     * @param array &$MimeID        An array of Mime IDs to write this part's CID to.
     * @param string $ref           The number of this part.
     *
     * @return object stdClass      The populated object.
     */
    function setInfo( $part, &$MimeID, $ref )
    {
        global $mime_types, $mime_actions;

        $object = new stdClass;
        $object->ref      = $ref;
        $object->type     = isset( $part->type )? $part->type : TYPETEXT;
        $object->subtype  = ( $part->ifsubtype )? strtolower( $part->subtype ) : 'x-unknown';
        $object->encoding = isset( $part->encoding )? $part->encoding : MIME_DEFAULT_ENCODING;
        $object->bytes    = isset( $part->bytes )? $part->bytes : '?';
        
		$localeinfo = localeconv();
        $object->size = ( $object->bytes != '?' )? number_format( $object->bytes / 1024, 2, $localeinfo['decimal_point'], '' ) : '?';
        $object->disposition = $part->ifdisposition? strtolower( $part->disposition ) : 'inline';

        if ( $part->ifid ) 
		{
            $object->id   = $part->id;
            $MimeID[$ref] = $object->id;
        } 
		else 
		{
            $object->id = false;
        }

        // Set default value of charset 
        $object->charset = MIME_DEFAULT_CHARSET;

        // go through the parameters, if any
        if ( $part->ifparameters ) 
		{
            while ( list(,$param) = each( $part->parameters ) ) 
			{
                $field = strtolower( $param->attribute );
				
                if ( $field == 'type' )
				{
                    if ( isset( $mime_types[strtolower($param->value)] ) )
                        $object->type = $mime_types[strtolower( $param->value )];
                } 
				else 
				{
                    $object->$field = $param->value;
                }
            }
        }

        // go through the disposition parameters, if any
        if ( $part->ifdparameters ) 
		{
            while ( list(,$param) = each( $part->dparameters ) ) 
			{
                $field = strtolower( $param->attribute );
                $object->$field = $param->value;
            }
        }

        // make sure a name is set
        if ( empty( $object->name ) && !empty( $object->filename ) )
            $object->name = $object->filename;

        // make sure there's a description
        if ( !empty( $part->description ) ) 
		{
            $object->description = $part->description;
        } 
		else 
		{
            if ( !empty( $object->name ) )
                $object->description = $object->name;
            else
                $object->description = MIME_DEFAULT_DESCRIPTION;
        }

        if ( empty( $object->name ) ) 
			$object->name = preg_replace( '|\W|', '_', $object->description );

        $object->TYPE = isset( $mime_types[$object->type] )? $mime_types[$object->type] : $mime_types[TYPETEXT];
        return $object;
    }


	// private methods
	
    /**
     * Given the results of imap_fetchstructure(), parse the structure
     * of the message, figuring out correct bodypart numbers, etc.
     * @access private
     *
     * @param object stdClass $body The result of imap_fetchstructure().
     * @param int $index            The IMAP UID of the message being parsed.
     * @param array &$MimeID        An array of Mime IDs to write any CIDs found to.
     * @param array &$attachments   The array of attachments that is being built up.
     * @param string $ref           The current bodypart.
     * @param string $alternative   Whether or not the part is one of several alternatives.
     *
     * @return array                An array containing all parts of the message
     *                              that have been parsed so far.
     */
    function _parse( $body, $index, &$MimeID, &$attachments, $ref = '', $alternative = '' )
    {
		// top multiparts don't get their own line
        if ( !strlen( $ref ) )
            $ref = ( isset( $body->type ) && $body->type == TYPEMULTIPART )? '' : 1;

        if ( isset( $body->subtype ) && $body->subtype == 'RFC822' ) 
		{
            $href = "$ref.0";
            $attachments[$href] = new stdClass;
            $attachments[$href]->part = true;
            $attachments[$href]->alternative = $alternative;
            $attachments[$href]->header = true;
            $attachments[$href]->header_imap_id = $href;
            $attachments[$href]->index = $index;
        }
		
        if ( !empty( $body->bytes ) && ( $body->subtype != 'MIXED' ) && ( $body->subtype != 'ALTERNATIVE' ) ) 
		{
            $attachments[$ref] = MIME_Structure::setInfo( $body, $MimeID, $ref );
            $attachments[$ref]->alternative = $alternative;
            $attachments[$ref]->header  = false;
            $attachments[$ref]->imap_id = $ref;
            $attachments[$ref]->index   = $index;
        }

        if ( isset( $body->parts ) ) 
		{
            $alternative_id = ( isset( $body->subtype ) && strtolower( $body->subtype ) == 'alternative' )? ( strlen( $ref )? $ref : '-' ) : '';
            $parts = $body->parts;
            $i = 0;
			
            foreach ( $parts as $sub_part ) 
			{
                if ( isset( $body->type ) && ( $body->type == TYPEMESSAGE ) && isset( $sub_part->type ) && ( $sub_part->type == TYPEMULTIPART ) )
                    $sub_ref = $ref;
                else
                    $sub_ref = ( !strlen( $ref ) )? '' . ( $i + 1 ) : $ref . '.' . ( $i + 1 );

                MIME_Structure::_parse( $sub_part, $index, $MimeID, $attachments, $sub_ref, $alternative_id );
                $i++;
            }
        }

        return $attachments;
    }
} // END OF MIME_Structure

?>
