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
 * @package locale
 */
 
/**
 * Constructor
 *
 * @access public
 */
Localization = function()
{
	this.Dictionary = Dictionary;
	this.Dictionary();
	
	this._populate();
};


Localization.prototype = new Dictionary();
Localization.prototype.constructor = Localization;
Localization.superclass = Dictionary.prototype;


// private methods

/**
 * @access private
 */
Localization.prototype._populate = function()
{
	this.add( "Czech",
		{
			lang             : "cs",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "cs_CZ",
			charset          : "iso-8859-2",
			fully_translated : true,
			keymap           : "csy",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Danish",
		{
			lang             : "da",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "da_DK",
			charset          : "iso-8859-1",
			fully_translated : true,
			keymap           : "weu",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "English",
		{
			lang             : "en",
			font             : "",
			map              : "",
			lc_all           : "en_GB",
			charset          : "iso-8859-1",
			fully_translated : false,
			keymap           : "en",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Estonian",
		{
			lang             : "et",
			font             : "lat2u-16",
			map              : "",
			lc_all           : "et_EE",
			charset          : "iso-8859-1",
			fully_translated : false,
			keymap           : "et",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Finnish",
		{
			lang             : "fi",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "fi_FI",
			charset          : "iso-8859-1",
			fully_translated : false,
			keymap           : "fi",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "French",
		{
			lang             : "fr",
			font             : "lat0-sun16",
			map              : "iso15",
			lc_all           : "fr_FR",
			charset          : "iso-8859-1",
			fully_translated : false,
			keymap           : "fr",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "German",
		{
			lang             : "de",
			font             : "lat0-sun16",
			map              : "iso15",
			lc_all           : "de_DE",
			charset          : "iso-8859-1",
			fully_translated : false,
			keymap           : "de",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Hungarian",
		{
			lang             : "hu",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "hu_HU",
			charset          : "iso-8859-2",
			fully_translated : true,
			keymap           : "hu",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Icelandic",
		{
			lang             : "is",
			font             : "lat0-sun16",
			map              : "iso15",
			lc_all           : "is_IS",
			charset          : "iso-8859-1",
			fully_translated : true,
			keymap           : "is",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Indonesian",
		{
			lang             : "in",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "in_ID",
			charset          : "iso-8859-1",
			fully_translated : true,
			keymap           : "dva",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Italian",
		{
			lang             : "it",
			font             : "lat0-sun16",
			map              : "iso15",
			lc_all           : "it_IT",
			charset          : "iso-8859-1",
			fully_translated : true,
			keymap           : "it",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Norwegian",
		{
			lang             : "no",
			font             : "lat0-sun16",
			map              : "iso15",
			lc_all           : "no_NO",
			charset          : "iso-8859-1",
			fully_translated : true,
			keymap           : "no",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Polish",
		{
			lang             : "pl",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "pl_PL",
			charset          : "iso-8859-2",
			fully_translated : true,
			keymap           : "pl",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "BrazilianPortuguese",
		{
			lang             : "pt_BR",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "pt_BR",
			charset          : "iso-8859-1",
			fully_translated : true,
			keymap           : "pt",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Romanian",
		{
			lang             : "ro",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "ro_RO",
			charset          : "iso-8859-2",
			fully_translated : true,
			keymap           : "ro",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Russian",
		{
			lang             : "ru",
			font             : "Cyr_a8x16",
			map              : "koi2alt",
			lc_all           : "ru_RU.KOI8-R",
			charset          : "koi8-r",
			fully_translated : false,
			keymap           : "rum",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Serbian",
		{
			lang             : "sr",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "sr_YU",
			charset          : "iso-8859-2",
			fully_translated : true,
			keymap           : "weu",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Slovak",
		{
			lang             : "sk",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "sk_SK",
			charset          : "iso-8859-2",
			fully_translated : true,
			keymap           : "sky",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Spanish",
		{
			lang             : "es",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "es_ES",
			charset          : "iso-8859-1",
			fully_translated : false,
			keymap           : "es",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Swedish",
		{
			lang             : "sv",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "sv_SE",
			charset          : "iso-8859-1",
			fully_translated : false,
			keymap           : "se",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Turkish",
		{
			lang             : "tr",
			font             : "lat2-sun16",
			map              : "iso02",
			lc_all           : "tr_TR",
			charset          : "iso-8859-9",
			fully_translated : false,
			keymap           : "trq",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
	this.add( "Ukrainian",
		{
			lang             : "uk_UA",
			font             : "RUSCII_8x16",
			map              : "koi2alt",
			lc_all           : "uk_UA",
			charset          : "koi8-u",
			fully_translated : true,
			keymap           : "uam",
			currency:		 : "",
			percent:		 : "",
			decimal:		 : ""
		}
	);
};
