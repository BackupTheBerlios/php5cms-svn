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
|Authors: Dave Shapiro <dave@ohdave.com>                               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package security_crypt_rsa_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
RSAKeyPair = function( encryptionExponent, decryptionExponent, modulus )
{
	this.e = BigInt.fromHex( encryptionExponent );
	this.d = BigInt.fromHex( decryptionExponent );
	this.m = BigInt.fromHex( modulus );
	
	// We can do two bytes per digit, so
	// chunkSize = 2 * (number of digits in modulus - 1).
	// Note that BigInt.numDigits actually returns the high index, not the
	// number of digits, so since it's zero-based, 1 has already been
	// subtracted. Poor naming convention, admittedly. I'll fix that
	// one of these days.
	this.chunkSize = 2 * BigInt.numDigits( this.m );
};
