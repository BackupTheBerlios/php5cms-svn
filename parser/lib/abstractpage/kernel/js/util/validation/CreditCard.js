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
 * @package util_validation
 */
 
/**
 * Constructor
 *
 * @access public
 */
CreditCard = function()
{
	this.Base = Base;
	this.Base();
};


CreditCard.prototype = new Base();
CreditCard.prototype.constructor = CreditCard;
CreditCard.superclass = Base.prototype;

/**
 * @access public
 */
CreditCard.prototype.isCreditCard = function( st )
{
	// Encoding only works on cards with less than 19 digits.
	if ( st.length > 19 )
		return ( false );

	sum = 0;
	mul = 1;
	l = st.length;
	
	for ( i = 0; i < l; i++ )
	{
		digit    = st.substring( l - i - 1, l - i );
		tproduct = parseInt( digit ,10 ) * mul;
			
		if ( tproduct >= 10 )
			sum += ( tproduct % 10 ) + 1;
		else
			sum += tproduct;
		
		if ( mul == 1 )
			mul++;
		else
			mul--;
	}

	if ( ( sum % 10 ) == 0 )
		return ( true );
	else
		return ( false );
};

/**
 * @access public
 */
CreditCard.prototype.isAnyCard = function( cc )
{
	if ( !this.isCreditCard( cc ) )
		return false;
		
	if ( !this.isMasterCard( cc ) && !this.isVisa( cc ) && !this.isAmericanExpress( cc ) && !this.isDinersClub( cc ) && !this.isDiscover( cc ) && !this.isEnRoute( cc ) && !this.isJCB( cc ) )
		return false;
		
	return true;
};

/**
 * @access public
 */
CreditCard.prototype.isCardMatch = function( cardType, cardNumber )
{
	cardType = cardType.toUpperCase();
	var doesMatch = true;

	if ( ( cardType == "VISA" ) && ( !this.isVisa( cardNumber ) ) )
		doesMatch = false;
	
	if ( ( cardType == "MASTERCARD" ) && ( !this.isMasterCard( cardNumber ) ) )
		doesMatch = false;
	
	if ( ( ( cardType == "AMERICANEXPRESS" ) || ( cardType == "AMEX" ) ) && ( !this.isAmericanExpress( cardNumber ) ) )
		doesMatch = false;
	
	if ( ( cardType == "DISCOVER" ) && ( !this.isDiscover( cardNumber ) ) )
		doesMatch = false;
	
	if ( ( cardType == "JCB" ) && ( !this.isJCB( cardNumber ) ) )
		doesMatch = false;
	
	if ( ( cardType == "DINERS" ) && ( !this.isDinersClub( cardNumber ) ) )
		doesMatch = false;

	if ( ( cardType == "CARTEBLANCHE" ) && ( !this.isCarteBlanche( cardNumber ) ) )
		doesMatch = false;
	
	if ( ( cardType == "ENROUTE" ) && ( !this.isEnRoute( cardNumber ) ) )
		doesMatch = false;
	
	return doesMatch;
};

/**
 * @access public
 */
CreditCard.prototype.isVisa = function( cc )
{
	if ( ( ( cc.length == 16 ) || ( cc.length == 13 ) ) && ( cc.substring( 0, 1 ) == 4 ) )
		return this.isCreditCard( cc );
	
	return false;
};

/**
 * @access public
 */
CreditCard.prototype.isMasterCard = function( cc )
{
	firstdig  = cc.substring( 0, 1 );
	seconddig = cc.substring( 1, 2 );
	
	if ( ( cc.length == 16 ) && ( firstdig == 5 ) && ( ( seconddig >= 1 ) && ( seconddig <= 5 ) ) )
		return this.isCreditCard( cc );
		
	return false;
};

/**
 * @access public
 */
CreditCard.prototype.isAmericanExpress = function( cc )
{
	firstdig  = cc.substring(0,1);
	seconddig = cc.substring(1,2);

	if ( ( cc.length == 15 ) && ( firstdig == 3 ) && ( ( seconddig == 4 ) || ( seconddig == 7 ) ) )
		return this.isCreditCard( cc );
	
	return false;
};

/**
 * @access public
 */
CreditCard.prototype.isDinersClub = function( cc )
{
	firstdig  = cc.substring(0,1);
	seconddig = cc.substring(1,2);

	if ( ( cc.length == 14 ) && ( firstdig == 3 ) && ( ( seconddig == 0 ) || ( seconddig == 6 ) || ( seconddig == 8 ) ) )
		return this.isCreditCard( cc );
	
	return false;
};

/**
 * @access public
 */
CreditCard.prototype.isCarteBlanche = function( cc )
{
	return this.isDinersClub( cc );
};

/**
 * @access public
 */
CreditCard.prototype.isDiscover = function( cc )
{
	first4digs = cc.substring( 0, 4 );

	if ( ( cc.length == 16 ) && ( first4digs == "6011" ) )
		return this.isCreditCard( cc );

	return false;
};

/**
 * @access public
 */
CreditCard.prototype.isEnRoute = function( cc )
{
	first4digs = cc.substring( 0, 4 );

	if ( ( cc.length == 15 ) && ( ( first4digs == "2014" ) || ( first4digs == "2149" ) ) )
		return this.isCreditCard( cc );
	
	return false;
};

/**
 * @access public
 */
CreditCard.prototype.isJCB = function( cc )
{
	first4digs = cc.substring( 0, 4 );

	if ( ( cc.length  == 16 ) && (
		 ( first4digs == "3088" ) ||
		 ( first4digs == "3096" ) ||
		 ( first4digs == "3112" ) ||
		 ( first4digs == "3158" ) ||
		 ( first4digs == "3337" ) ||
		 ( first4digs == "3528" ) ) ) return this.isCreditCard( cc );

	return false;
};
