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
 * @package xml
 */
 
class XMLLite extends PEAR
{
	/**
	 * @access public
	 */
	var $line_number;
	
	/**
	 * @access public
	 */
	var $new_line;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function XMLLite()
	{
		$this->line_number   = 0;
		$this->new_line      = "\n";
		$this->error_string  = '';

		$this->elem_start_handler = '';
		$this->elem_stop_handler  = '';
		$this->elem_cdata_handler = '';
	}

	
	/**
	 * @access public
	 */
	function AllocParser()
	{
		return true;
	}

	/**
	 * @access public
	 */
	function DeallocParser()
	{
		return true;
	}

	/**
	 * @access public
	 */
	function SetElementHandler( $start, $end )
	{
		$this->elem_start_handler = $start;
		$this->elem_stop_handler  = $end;
	}

	/**
	 * @access public
	 */
	function SetCharacterDataHandler( $function )
	{
		$this->elem_cdata_handler = $function;
	}

	/**
	 * @access public
	 */
	function ParseXmlFile( $file )
	{
		return true;
	}

	/**
	 * @access public
	 */
	function XmlStartElemHandler( $tag )
	{
		if ( $this->elem_start_handler != '' )
		{
			list( $tag, $attributes ) = $this->ParseXmlTagElem( $tag );
			return $this->{ $this->elem_start_handler }( $this, $tag, $attributes );
		}
	}

	/**
	 * @access public
	 */	
	function XmlStopElemHandler( $tag )
	{
		if ( $this->elem_stop_handler != '' )
		{
			list( $tag, $attributes ) = $this->ParseXmlTagElem( $tag );
			return $this->{ $this->elem_stop_handler }( $this, $tag, $attributes );
		}
	}
	
	/**
	 * @access public
	 */
	function XmlCdataElemHandler( $cdata )
	{
		if ( $this->elem_cdata_handler != '' )
			return $this->{ $this->elem_cdata_handler }( $this, $cdata );
	}

	/**
	 * @access public
	 */
	function ParseXmlTagElem( $intag )
	{
		$tag = '';
		$attributes = '';

		if ( ereg( ' ', $intag ) )
		{
			$elems = explode( ' ', $intag );
			$tag   = strtoupper( $elems[ 0 ] );
			
			for ( $i = 1; $i < count( $elems ); $i++ )
			{
				if ( eregi( '(.+)=(\'|\")(.+)(\'|\")', $elems[ $i ], $regs ) )
					$attributes[ strtoupper( $regs[ 1 ] ) ] = $regs[ 3 ];
			}
		}
		else
		{
			$tag = strtoupper( $intag );
		}

		return Array( $tag, $attributes );
	}

	/**
	 * @access public
	 */
	function ParseXml( $data, $is_final = 0 )
	{
		// $is_final is a libxpat specific thingy
		$data = explode( $this->new_line, $data );

		for ( $i = 0; $i < count( $data ); $i++ )
		{
			$this->line_number = $i;
			$data[ $i ] = ereg_replace( '\<\!\-\-(.+)\-\-\>', '', $data[ $i ] );

			if ( $data[ $i ] == '' )
			{
				next;
			}
			else if ( eregi( '\<(.+)\>(.+)\<\/(.+)\>', $data[ $i ], $regs ) )
			{
				$xml_start_tag = $regs[ 1 ];
				$cdata = $regs[ 2 ];
				$xml_stop_tag  = $regs[ 3 ];

				$this->XmlStartElemHandler( $xml_start_tag );
				$this->XmlCdataElemHandler( $cdata );
				$this->XmlStopElemHandler( $xml_stop_tag );
			}
			else if ( eregi( '\<(.+)\>\<\/(.+)\>', $data[ $i ], $regs ) )
			{
				$xml_start_tag = $regs[ 1 ];
				$cdata = '';
				$xml_stop_tag  = $regs[ 2 ];

				$this->XmlStartElemHandler( $xml_start_tag );
				$this->XmlCdataElemHandler( $cdata );
				$this->XmlStopElemHandler( $xml_stop_tag );
			}
			else if ( eregi( '\<\/(.+)\>', $data[ $i ], $regs ) )
			{
				$xml_stop_tag = $regs[ 1 ];
				$this->XmlStopElemHandler( $xml_stop_tag );
			}
			else if ( eregi( '\<(.+)\>', $data[ $i ], $regs ) )
			{
				$xml_start_tag = $regs[ 1 ];
				$this->XmlStartElemHandler( $xml_start_tag );
			}
			else
			{
				return PEAR::raiseError( "Unhandled Error." );
			}
		}
		
		// Well i really don't like returning one by default
		// but since we really can't say there wasn't something wrong...
		return true;
	}
} // END OF XMLLite

?>
