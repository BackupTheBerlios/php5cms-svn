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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


/**
 * A JavaScript implementation of the Secure Hash Algorithm, SHA-1,
 * as defined in FIPS PUB 180-1
 *
 * USAGE:
 *      
 * Simple text hash:
 *      
 * $sha = new SHA;
 * $hasharray = $sha->hash_string( 'hash me!' );
 *
 * This returns an array of 5 32-bit integers.
 * The SHA.hash_bytes function does the same thing, but requires
 * an array of bytes as input.  Note that the input values will be
 * truncated if they are larger than 8 bits.
 *
 * There are also some hash to string conversion functions.  The
 * naming convention admittedly could be better, but it works :).
 *
 * $sha->hash_to_string( $hasharray )
 *      
 * Converts the hash array to an uppercase hex string.
 *
 * Hashing very large blocks a piece at a time:
 *
 * $sha = new SHA;
 * $sha->init();
 *
 * while ( blocks_to_process )
 *		$sha->update( next_byte_array )
 *      
 * $hasharray = $sha->finalize()
 *
 * @package security_checksum
 */

/**
 * Constructor
 *
 * @access public
 */
SHA = function()
{
	this.Base = Base;
	this.Base();

    // initialize all variables
    this.init();
};


SHA.prototype = new Base();
SHA.prototype.constructor = SHA;
SHA.superclass = Base.prototype;

/**
 * @access public
 */
SHA.prototype.init = function()
{
	this.A = 0x67452301;
	this.B = 0xefcdab89;
	this.C = 0x98badcfe;
	this.D = 0x10325476;
	this.E = 0xc3d2e1f0;
	
	this.a = this.A;
	this.b = this.B;
	this.c = this.C;
	this.d = this.D;
	this.e = this.E;
	
	this.K0_19  = 0x5a827999;
	this.K20_39 = 0x6ed9eba1;
	this.K40_59 = 0x8f1bbcdc;
	this.K60_79 = 0xca62c1d6;

	// Buffer for padding and updating.
	this.buffer = new Array();
	
	// Current number of bytes in the buffer.
	this.buffsize = 0;
	
	// Total size processed so far.
	this.totalsize = 0;
};

/**
 * @access public
 */
SHA.prototype.f00_19 = function( x, y, z )
{
	return (x & y) | (~x & z);
};

/**
 * @access public
 */
SHA.prototype.f20_39 = function( x, y, z )
{
	return (x ^ y ^ z);
};

/**
 * @access public
 */
SHA.prototype.f40_59 = function( x, y, z )
{
	return (x & y) | (x & z) | (y & z);
};

/**
 * @access public
 */
SHA.prototype.f60_79 = function( x, y, z )
{
	return this.f20_39( x, y, z );
};

/**
 * @access public
 */
SHA.prototype.circ_shl = function( n, amt )
{
	var leftmask    = 0xFFFFFFFF;
	leftmask      <<= 32 - amt;

	var rightmask   = 0xFFFFFFFF;
	rightmask     <<= amt;
	rightmask       = ~rightmask;

	var remains     = n & leftmask;
	remains       >>= 32 - amt;
	remains        &= rightmask;

	return ( n << amt ) | remains;
};

/**
 * This padding function is limited to a size of 2^32 bits, rather than 2^64 
 * as dictated by the spec.
 *
 * @access public
 */
SHA.prototype.pad_block = function( last_block, size )
{
	// Returns a block that is a multiple of 512 bits (64 bytes) long.
	// 'size' is the total number of bytes in the message.
	var newblock = new Array();
	
	// Adds padding to a block.
	var blksize = last_block.length
	var bits = size * 8
	
	// Always pad with 0x80, then add as many zeros as necessary to
	// make the message 64 bits short of 512.  Then add the 64-bit size.
	var i;
	for ( i = 0; i < blksize; ++i )
		newblock[i] = last_block[i];
	
	// Add 0x80
	newblock[blksize] = 0x80;
	
	// Add the zeros.
	while ( ( newblock.length % 64 ) != 56 )
		newblock[newblock.length] = 0;
	
	// Add the size (in bytes).
	for ( i = 0; i < 8; ++i )
		newblock[newblock.length] = ( i < 4 )? 0 : ( bits >> ( ( 7 - i ) * 8 ) ) & 0xff;

	return newblock;
};

/**
 * Converts the message block of 16 words into a block of 80 words.
 *
 * @access public
 */
SHA.prototype.expand_block = function( block )
{
	var nblk = new Array();
	var i;

	for ( i = 0; i < 16; ++i )
		nblk[i] = block[i];
	
	for ( i = 16; i < 80; ++i )
		nblk[i] = this.circ_shl( nblk[i-3] ^ nblk[i-8] ^ nblk[i-14] ^ nblk[i-16], 1 );
	
	return nblk;
};

/**
 * @access public
 */
SHA.prototype.to_string = function( val )
{
	var str = "";
	
	for ( var i = 0; i < 8; ++i )
	{
		var shift  = ( 7 - i ) * 4;
		var nibble = ( val >> shift ) & 0x0f;
		str += nibble.toString( 16 );
	}
	
	return str.toUpperCase();
};

/**
 * @access public
 */
SHA.prototype.block_to_string = function( val )
{
	var str = "";
	
	for ( var v in val )
		str += this.to_string( val[v] );
	
	return str;
};

/**
 * Requires the block to be 64 bytes long.
 *
 * @access public
 */
SHA.prototype.bytes_to_words = function( block )
{
	var nblk = new Array();
	var i;
	
	for ( i = 0; i < 16; ++i )
	{
		var index = i*4;
		nblk[i]   = 0;
		nblk[i]  |= ( block[index]     & 0xff ) << 24;
		nblk[i]  |= ( block[index + 1] & 0xff ) << 16;
		nblk[i]  |= ( block[index + 2] & 0xff ) << 8;
		nblk[i]  |= ( block[index + 3] & 0xff );
	}
	
	return nblk;
};

/**
 * Each message block is 16 32-bit words.
 * It is expected that the array will be of 32-bit words, not of bytes.
 *
 * @access public
 */
SHA.prototype.process_block = function( block )
{
	var blk = this.expand_block( block );
	var i;

	for ( i = 0; i < 80; ++i )
	{
		with ( this )
		{
			var temp = circ_shl( a, 5 );
			
			if ( i < 20 )
				temp += f00_19( b, c, d ) + e + blk[i] + K0_19;
			else if ( i < 40 )
				temp += f20_39( b, c, d ) + e + blk[i] + K20_39;
			else if ( i < 60 )
				temp += f40_59( b, c, d ) + e + blk[i] + K40_59;
			else
				temp += f60_79( b, c, d ) + e + blk[i] + K60_79;

			e = d;
			d = c;
			c = circ_shl( b, 30 );
			b = a;
			a = temp;
		}
	}
	
	with ( this )
	{
		A += a;
		B += b;
		C += c;
		D += d;
		E += e;
	}
};

/**
 * Pass in a byte array, and update will call process_block as needed.
 *
 * @access public
 */
SHA.prototype.update = function( bytes )
{
	// If there are bytes in the buffer and the number of bytes here
	// is sufficient to make a block, then process that initial block.
	var index = 0;
	
	// Process each full block.
	while ( ( bytes.length - index ) + this.buffsize >= 64 )
	{
		// Copy the needed parts into the hash buffer.
		for ( var i = this.buffsize; i<64; ++i )
			this.buffer[i] = bytes[index + i - this.buffsize];
		
		this.process_block( this.bytes_to_words( this.buffer ) );
		index += 64 - this.buffsize;
		this.buffsize = 0;
	}
	
	// Add the remaining bytes into the buffer.
	var remaining = bytes.length - index;
	
	for ( var i = 0; i < remaining; ++i)
		this.buffer[this.buffsize + i] = bytes[index + i];
	
	this.buffsize  += remaining;
	this.totalsize += bytes.length;
};

/**
 * Finalize returns the hash value after doing things like padding.
 *
 * @access public
 */
SHA.prototype.finalize = function()
{
	// Clear out the hash buffer.
	var last_block = new Array();
	
	for ( var i = 0; i < this.buffsize; ++i )
		last_block[i] = this.buffer[i];
	
	this.buffsize = 0;
	
	// Pad the last block.
	last_block = this.pad_block( last_block, this.totalsize );
	
	// Process this last piece.
	// We do NOT call update here, since it updates the total size count,
	// and that is not desired.  The process_block function is called instead.
	var index = 0;
	
	while ( index < last_block.length )
	{
		this.process_block( this.bytes_to_words( last_block.slice( index, index + 64 ) ) );
		index += 64;
	}
	
	var temp = new Array();
	
	with (this)
	{
		temp[0] = A;
		temp[1] = B;
		temp[2] = C;
		temp[3] = D;
		temp[4] = E;
	}

	return temp;
};

/**
 * @access public
 */
SHA.prototype.hash_bytes = function( bytes )
{
	this.init();
	this.update( bytes );
	
	return this.finalize();
};

/**
 * @access public
 */
SHA.prototype.hash_text = function( text )
{
	// Create a byte array from the text (chop off the MSB of each char).
	var bytes = new Array();
	
	for ( var i = 0; i<text.length; ++i )
		bytes[i] = text.charCodeAt(i) & 0xff;
	
	return this.hash_bytes( bytes );
};
