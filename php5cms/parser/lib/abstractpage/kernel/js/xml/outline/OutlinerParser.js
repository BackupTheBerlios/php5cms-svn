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
 * @package xml_outline
 */
 
/**
 * Constructor
 *
 * @access public
 */
OutlinerParser = function( name, doc )
{
	this.Base = Base;
	this.Base();
	
	this.doc  = doc;
	this.node = new OutlinerNode( name );
	
	this.start( 'base' );

	while ( this.state )
	{
		try
		{
    		var c  = doc.getChar();
    		this.f = this.stt[this.state][c];
    
			if ( this.f )
			{
				this.f();
			}
			else
			{
      			if ( c == '\0' )
					throw 'Unexpected end of file.';
      			else
					this[this.state] += c;
    		}
		}
		catch ( e )
		{
			return Base.raiseError( name );
		}
	}
};


OutlinerParser.prototype = new Base();
OutlinerParser.prototype.constructor = OutlinerParser;
OutlinerParser.superclass = Base.prototype;

/**
 * @access public
 */
OutlinerParser.prototype = 
{
	start : function( s )
	{
		this.state = s;
		this[s] = '';
	},
	cont : function( s )
	{
		this.state = s;
	},
	stop : function()
	{
		this.state = '';
	},
	entlist :
	{
		lt : '<', gt : '>', amp : '&'
	},
	stt :
	{
		base :
		{
    		'<' : function()
			{
				this.start( 'tag' );
			},
    		'&' : function()
			{
				this.start( 'ent' );
			},
    		'\0' : function()
			{
				if ( this.node._name != '{document}' )
					throw 'Unexpected end of file.';
				else
					this.stop();
			}
		},
   		tag :
		{
    		'>' : function()
			{
      			if ( !this.tag )
					throw 'Tag may not be empty.';
      			
				var n = new OutlinerParser( this.tag, this.doc ).node;
      
	  			if ( this.tag == '_' )
				{
					this.node._children[this.node._children.length] = n;
					this.node._text += n._text;
				} 
      			else
				{
					if ( this.node[this.tag] )
						throw 'Double property: ' + this.tag;
					
					this.node[this.tag] = n;
				}

				this.cont( 'base' );
			},
			'/' : function()
			{
				if ( this.tag )
					throw 'Unexpected / in tag: ' + this.tag;
				
				this.start( 'endtag' );
			},
			'?' : function()
			{
				this.start( 'ignoretag' );
			},
			'!' : function()
			{
				this.start( 'ignoretag' );
			}
		},
		endtag :
		{
			'>' : function()
			{
				if ( this.endtag != this.node._name )
					throw 'End-tag (' + this.endtag + ') does not match start-tag (' + this.node._name + ').';
				
				if ( !this.node._text )
					this.node._text = this.base;
					
				this.stop();
			}
		},
		ent : 
		{
			'#' : function()
			{
				this.start( 'nument' );
			},
			';' : function()
			{
				if ( !this.entlist[this.ent] )
					throw 'Unknown entity: ' + this.ent;
					
				this.base += this.entlist[this.ent];
				this.cont('base');
			}
		},
		nument :
		{
			';' : function()
			{
				this.base += String.fromCharCode( this.nument );
				this.cont( 'base' );
			}
		},
		ignoretag :
		{
			'>' : function()
			{
				this.cont( 'base' );
			}
		}
	}
};
