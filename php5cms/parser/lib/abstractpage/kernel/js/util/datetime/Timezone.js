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
 * @package util_datetime
 */
 
/**
 * Constructor
 *
 * @access public
 */
Timezone = function()
{
	this.Dictionary = Dictionary;
	this.Dictionary();
	
	this._populate();
};


Timezone.prototype = new Dictionary();
Timezone.prototype.constructor = Timezone;
Timezone.superclass = Dictionary.prototype;

/**
 * @access private
 */
Timezone.prototype._populate = function()
{
    this.add( 'Etc/GMT+12', {
        'offset'       : -43200000,
        'longname'     : "GMT-12:00",
        'shortname'    : 'GMT-12:00',
        'hasdst'       : false 
	} );
	this.add( 'Etc/GMT+11', {
        'offset'       : -39600000,
        'longname'     : "GMT-11:00",
        'shortname'    : 'GMT-11:00',
        'hasdst'       : false 
	} );
    this.add( 'MIT', {
        'offset'       : -39600000,
        'longname'     : "West Samoa Time",
        'shortname'    : 'WST',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Apia', {
        'offset'       : -39600000,
        'longname'     : "West Samoa Time",
        'shortname'    : 'WST',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Midway', {
        'offset'       : -39600000,
        'longname'     : "Samoa Standard Time",
        'shortname'    : 'SST',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Niue', {
        'offset'       : -39600000,
        'longname'     : "Niue Time",
        'shortname'    : 'NUT',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Pago_Pago', {
        'offset'       : -39600000,
        'longname'     : "Samoa Standard Time",
        'shortname'    : 'SST',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Samoa', {
        'offset'       : -39600000,
        'longname'     : "Samoa Standard Time",
        'shortname'    : 'SST',
        'hasdst'       : false 
	} );
    this.add( 'US/Samoa', {
        'offset'       : -39600000,
        'longname'     : "Samoa Standard Time",
        'shortname'    : 'SST',
        'hasdst'       : false 
	} );
    this.add( 'America/Adak', {
        'offset'       : -36000000,
        'longname'     : "Hawaii-Aleutian Standard Time",
       	 'shortname'    : 'HAST',
        'hasdst'       : true,
        'dstlongname'  : "Hawaii-Aleutian Daylight Time",
        'dstshortname' : 'HADT' 
	} );
    this.add( 'America/Atka', {
        'offset'       : -36000000,
        'longname'     : "Hawaii-Aleutian Standard Time",
        'shortname'    : 'HAST',
        'hasdst'       : true,
        'dstlongname'  : "Hawaii-Aleutian Daylight Time",
        'dstshortname' : 'HADT' 
	} );
    this.add( 'Etc/GMT+10', {
        'offset'       : -36000000,
        'longname'     : "GMT-10:00",
        'shortname'    : 'GMT-10:00',
        'hasdst'       : false 
	} );
    this.add( 'HST', {
        'offset'       : -36000000,
        'longname'     : "Hawaii Standard Time",
        'shortname'    : 'HST',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Fakaofo', {
        'offset'       : -36000000,
        'longname'     : "Tokelau Time",
        'shortname'    : 'TKT',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Honolulu', {
        'offset'       : -36000000,
        'longname'     : "Hawaii Standard Time",
        'shortname'    : 'HST',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Johnston', {
        'offset'       : -36000000,
        'longname'     : "Hawaii Standard Time",
        'shortname'    : 'HST',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Rarotonga', {
        'offset'       : -36000000,
        'longname'     : "Cook Is. Time",
        'shortname'    : 'CKT',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Tahiti', {
        'offset'       : -36000000,
        'longname'     : "Tahiti Time",
        'shortname'    : 'TAHT',
        'hasdst'       : false 
	} );
    this.add( 'SystemV/HST10', {
        'offset'       : -36000000,
        'longname'     : "Hawaii Standard Time",
        'shortname'    : 'HST',
        'hasdst'       : false 
	} );
    this.add( 'US/Aleutian', {
        'offset'       : -36000000,
        'longname'     : "Hawaii-Aleutian Standard Time",
        'shortname'    : 'HAST',
        'hasdst'       : true,
        'dstlongname'  : "Hawaii-Aleutian Daylight Time",
        'dstshortname' : 'HADT' 
	} );
    this.add( 'US/Hawaii', {
        'offset'       : -36000000,
        'longname'     : "Hawaii Standard Time",
        'shortname'    : 'HST',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Marquesas', {
        'offset'       : -34200000,
        'longname'     : "Marquesas Time",
        'shortname'    : 'MART',
        'hasdst'       : false 
	} );
    this.add( 'AST', {
        'offset'       : -32400000,
        'longname'     : "Alaska Standard Time",
        'shortname'    : 'AKST',
        'hasdst'       : true,
        'dstlongname'  : "Alaska Daylight Time",
        'dstshortname' : 'AKDT' 
	} );
    this.add( 'America/Anchorage', {
        'offset'       : -32400000,
        'longname'     : "Alaska Standard Time",
        'shortname'    : 'AKST',
        'hasdst'       : true,
        'dstlongname'  : "Alaska Daylight Time",
        'dstshortname' : 'AKDT' 
	} );
    this.add( 'America/Juneau', {
        'offset'       : -32400000,
        'longname'     : "Alaska Standard Time",
        'shortname'    : 'AKST',
        'hasdst'       : true,
        'dstlongname'  : "Alaska Daylight Time",
        'dstshortname' : 'AKDT' 
	} );
    this.add( 'America/Nome', {
        'offset'       : -32400000,
        'longname'     : "Alaska Standard Time",
        'shortname'    : 'AKST',
        'hasdst'       : true,
        'dstlongname'  : "Alaska Daylight Time",
        'dstshortname' : 'AKDT' 
	} );
    this.add( 'America/Yakutat', {
        'offset'       : -32400000,
        'longname'     : "Alaska Standard Time",
        'shortname'    : 'AKST',
        'hasdst'       : true,
        'dstlongname'  : "Alaska Daylight Time",
        'dstshortname' : 'AKDT' 
	} );
    this.add( 'Etc/GMT+9', {
        'offset'       : -32400000,
        'longname'     : "GMT-09:00",
        'shortname'    : 'GMT-09:00',
        'hasdst'       : false 
	} );
    this.add( 'Pacific/Gambier', {
        'offset'       : -32400000,
        'longname'     : "Gambier Time",
        'shortname'    : 'GAMT',
        'hasdst'       : false 
	} );
    this.add( 'SystemV/YST9', {
        'offset'       : -32400000,
        'longname'     : "Gambier Time",
        'shortname'    : 'GAMT',
        'hasdst'       : false 
	} );
    this.add( 'SystemV/YST9YDT', {
        'offset'       : -32400000,
        'longname'     : "Alaska Standard Time",
        'shortname'    : 'AKST',
        'hasdst'       : true,
        'dstlongname'  : "Alaska Daylight Time",
        'dstshortname' : 'AKDT' 
	} );
    this.add( 'US/Alaska', {
        'offset'       : -32400000,
        'longname'     : "Alaska Standard Time",
        'shortname'    : 'AKST',
        'hasdst'       : true,
        'dstlongname'  : "Alaska Daylight Time",
        'dstshortname' : 'AKDT' 
	} );
    this.add( 'America/Dawson', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'America/Ensenada', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT'
	} );
    this.add( 'America/Los_Angeles', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'America/Tijuana', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'America/Vancouver', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'America/Whitehorse', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'Canada/Pacific', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'Canada/Yukon', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'Etc/GMT+8', {
        'offset'       : -28800000,
        'longname'     : "GMT-08:00",
        'shortname'    : 'GMT-08:00',
        'hasdst'       : false 
	} );
    this.add( 'Mexico/BajaNorte', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'PST', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'PST8PDT', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'Pacific/Pitcairn', {
        'offset'       : -28800000,
        'longname'     : "Pitcairn Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : false 
	} );
    this.add( 'SystemV/PST8', {
        'offset'       : -28800000,
        'longname'     : "Pitcairn Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : false 
	} );
    this.add( 'SystemV/PST8PDT', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'US/Pacific', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'US/Pacific-New', {
        'offset'       : -28800000,
        'longname'     : "Pacific Standard Time",
        'shortname'    : 'PST',
        'hasdst'       : true,
        'dstlongname'  : "Pacific Daylight Time",
        'dstshortname' : 'PDT' 
	} );
    this.add( 'America/Boise', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'America/Cambridge_Bay', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'America/Chihuahua', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'America/Dawson_Creek', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : false 
	} );
    this.add( 'America/Denver', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'America/Edmonton', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'America/Hermosillo', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : false 
	} );
    this.add( 'America/Inuvik', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'America/Mazatlan', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'America/Phoenix', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
       	 'hasdst'       : false 
	} );
    this.add( 'America/Shiprock', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'America/Yellowknife', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'Canada/Mountain', {
        'offset'       : -25200000,
       	 'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'Etc/GMT+7', {
        'offset'       : -25200000,
        'longname'     : "GMT-07:00",
        'shortname'    : 'GMT-07:00',
        'hasdst'       : false 
	} );
    this.add( 'MST', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'MST7MDT', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
       	 'dstshortname' : 'MDT' 
	} );
    this.add( 'Mexico/BajaSur', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'Navajo', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'PNT', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : false 
	} );
    this.add( 'SystemV/MST7', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : false 
	} );
    this.add( 'SystemV/MST7MDT', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'US/Arizona', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : false 
	} );
    this.add( 'US/Mountain', {
        'offset'       : -25200000,
        'longname'     : "Mountain Standard Time",
        'shortname'    : 'MST',
        'hasdst'       : true,
        'dstlongname'  : "Mountain Daylight Time",
        'dstshortname' : 'MDT' 
	} );
    this.add( 'America/Belize', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'America/Cancun', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
        'dstlongname'  : "Central Daylight Time",
        'dstshortname' : 'CDT' 
	} );
    this.add( 'America/Chicago', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
        'dstlongname'  : "Central Daylight Time",
        'dstshortname' : 'CDT' 
	} );
    this.add( 'America/Costa_Rica', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'America/El_Salvador', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'America/Guatemala', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'America/Managua', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'America/Menominee', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
        'dstlongname'  : "Central Daylight Time",
        'dstshortname' : 'CDT' 
	} );
    this.add( 'America/Merida', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
        'dstlongname'  : "Central Daylight Time",
        'dstshortname' : 'CDT' 
	} );
    this.add( 'America/Mexico_City', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'America/Monterrey', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
        'dstlongname'  : "Central Daylight Time",
        'dstshortname' : 'CDT' 
	} );
    this.add( 'America/North_Dakota/Center', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
       	'dstlongname'  : "Central Daylight Time",
        'dstshortname' : 'CDT' 
	} );
    this.add( 'America/Rainy_River', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
        'dstlongname'  : "Central Daylight Time",
        'dstshortname' : 'CDT' 
	} );
    this.add( 'America/Rankin_Inlet', {
        'offset'       : -21600000,
        'longname'     : "Eastern Standard Time",
        'shortname'    : 'EST',
        'hasdst'       : true,
        'dstlongname'  : "Eastern Daylight Time",
        'dstshortname' : 'EDT' 
	} );
    this.add( 'America/Regina', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'America/Swift_Current', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'America/Tegucigalpa', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'America/Winnipeg', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
        'dstlongname'  : "Central Daylight Time",
        'dstshortname' : 'CDT' 
	} );
    this.add( 'CST', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
        'dstlongname'  : "Central Daylight Time",
        'dstshortname' : 'CDT' 
	} );
    this.add( 'CST6CDT', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
        'dstlongname'  : "Central Daylight Time",
       	 'dstshortname' : 'CDT' 
	} );
    this.add( 'Canada/Central', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : true,
        'dstlongname'  : "Central Daylight Time",
        'dstshortname' : 'CDT' 
	} );
    this.add( 'Canada/East-Saskatchewan', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'Canada/Saskatchewan', {
        'offset'       : -21600000,
        'longname'     : "Central Standard Time",
        'shortname'    : 'CST',
        'hasdst'       : false 
	} );
    this.add( 'Chile/EasterIsland', {
        'offset'       : -21600000,
        'longname'     : "Easter Is. Time",
        'shortname'    : 'EAST',
       	 'hasdst'       : true,
        'dstlongname'  : "Easter Is. Summer Time",
        'dstshortname' : 'EASST'
} );
    this.add( 'Etc/GMT+6', {
        'offset'       : -21600000,
        'longname'     : "GMT-06:00",
        'shortname'    : 'GMT-06:00',
        'hasdst'       : false
	} );
    this.add( 'Mexico/General', {
		'offset'       : -21600000,
        'longname'     : "Central Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
    this.add( 'Pacific/Easter', {
		'offset'       : -21600000,
        'longname'     : "Easter Is. Time",
		'shortname'    : 'EAST',
		'hasdst'       : true,
		'dstlongname'  : "Easter Is. Summer Time",
		'dstshortname' : 'EASST' 
	} );
    this.add( 'Pacific/Galapagos', {
		'offset'       : -21600000,
        'longname'     : "Galapagos Time",
		'shortname'    : 'GALT',
		'hasdst'       : false 
	} );
    this.add( 'SystemV/CST6', {
		'offset'       : -21600000,
        'longname'     : "Central Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
    this.add( 'SystemV/CST6CDT', {
		'offset'       : -21600000,
        'longname'     : "Central Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : true,
		'dstlongname'  : "Central Daylight Time",
		'dstshortname' : 'CDT' 
	} );
    this.add( 'US/Central', {
		'offset'       : -21600000,
        'longname'     : "Central Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : true,
		'dstlongname'  : "Central Daylight Time",
		'dstshortname' : 'CDT' } );
    this.add( 'America/Bogota', {
		'offset'       : -18000000,
        'longname'     : "Colombia Time",
		'shortname'    : 'COT',
		'hasdst'       : false 
	} );
    this.add( 'America/Cayman', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'America/Detroit', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT'
	} );
    this.add( 'America/Eirunepe', {
		'offset'       : -18000000,
        'longname'     : "Acre Time",
		'shortname'    : 'ACT',
		'hasdst'       : false 
	} );
    this.add( 'America/Fort_Wayne', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'America/Grand_Turk', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'America/Guayaquil', {
		'offset'       : -18000000,
        'longname'     : "Ecuador Time",
		'shortname'    : 'ECT',
		'hasdst'       : false 
	} );
    this.add( 'America/Havana', {
		'offset'       : -18000000,
        'longname'     : "Central Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : true,
		'dstlongname'  : "Central Daylight Time",
		'dstshortname' : 'CDT' 
	} );
    this.add( 'America/Indiana/Indianapolis', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'America/Indiana/Knox', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'America/Indiana/Marengo', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'America/Indiana/Vevay', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'America/Indianapolis', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'America/Iqaluit', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT'
	} );
    this.add( 'America/Jamaica', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false
	} );
    this.add( 'America/Kentucky/Louisville', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT'
	} );
    this.add( 'America/Kentucky/Monticello', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'America/Knox_IN', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'America/Lima', {
		'offset'       : -18000000,
        'longname'     : "Peru Time",
		'shortname'    : 'PET',
		'hasdst'       : false
	} );
    this.add( 'America/Louisville', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT'
	} );
    this.add( 'America/Montreal', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT'
	} );
    this.add( 'America/Nassau', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT'
	} );
    this.add( 'America/New_York', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'America/Nipigon', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'America/Panama', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
   	 this.add( 'America/Pangnirtung', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'America/Port-au-Prince', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'America/Porto_Acre', {
		'offset'       : -18000000,
        'longname'     : "Acre Time",
		'shortname'    : 'ACT',
		'hasdst'       : false 
	} );
    this.add( 'America/Rio_Branco', {
		'offset'       : -18000000,
        'longname'     : "Acre Time",
		'shortname'    : 'ACT',
		'hasdst'       : false 
	} );
    this.add( 'America/Thunder_Bay', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'Brazil/Acre', {
		'offset'       : -18000000,
        'longname'     : "Acre Time",
		'shortname'    : 'ACT',
		'hasdst'       : false 
	} );
    this.add( 'Canada/Eastern', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'Cuba', {
		'offset'       : -18000000,
        'longname'     : "Central Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : true,
		'dstlongname'  : "Central Daylight Time",
		'dstshortname' : 'CDT' 
	} );
    this.add( 'EST', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'EST5EDT', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'Etc/GMT+5', {
		'offset'       : -18000000,
        'longname'     : "GMT-05:00",
		'shortname'    : 'GMT-05:00',
		'hasdst'       : false 
	} );
    this.add( 'IET', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'Jamaica', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'SystemV/EST5', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'SystemV/EST5EDT', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'US/East-Indiana', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'US/Eastern', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'US/Indiana-Starke', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
    this.add( 'US/Michigan', {
		'offset'       : -18000000,
        'longname'     : "Eastern Standard Time",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Daylight Time",
		'dstshortname' : 'EDT' 
	} );
    this.add( 'America/Anguilla', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Antigua', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Aruba', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Asuncion', {
		'offset'       : -14400000,
        'longname'     : "Paraguay Time",
		'shortname'    : 'PYT',
		'hasdst'       : true,
		'dstlongname'  : "Paraguay Summer Time",
		'dstshortname' : 'PYST' 
	} );
    this.add( 'America/Barbados', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Boa_Vista', {
		'offset'       : -14400000,
        'longname'     : "Amazon Standard Time",
		'shortname'    : 'AMT',
		'hasdst'       : false 
	} );
    this.add( 'America/Caracas', {
		'offset'       : -14400000,
        'longname'     : "Venezuela Time",
		'shortname'    : 'VET',
		'hasdst'       : false 
	} );
    this.add( 'America/Cuiaba', {
		'offset'       : -14400000,
        'longname'     : "Amazon Standard Time",
		'shortname'    : 'AMT',
		'hasdst'       : true,
		'dstlongname'  : "Amazon Summer Time",
		'dstshortname' : 'AMST' 
	} );
    this.add( 'America/Curacao', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Dominica', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Glace_Bay', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : true,
		'dstlongname'  : "Atlantic Daylight Time",
		'dstshortname' : 'ADT' 
	} );
    this.add( 'America/Goose_Bay', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : true,
		'dstlongname'  : "Atlantic Daylight Time",
		'dstshortname' : 'ADT' 
	} );
    this.add( 'America/Grenada', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Guadeloupe', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Guyana', {
		'offset'       : -14400000,
        'longname'     : "Guyana Time",
		'shortname'    : 'GYT',
		'hasdst'       : false 
	} );
    this.add( 'America/Halifax', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : true,
		'dstlongname'  : "Atlantic Daylight Time",
		'dstshortname' : 'ADT' 
	} );
    this.add( 'America/La_Paz', {
		'offset'       : -14400000,
        'longname'     : "Bolivia Time",
		'shortname'    : 'BOT',
		'hasdst'       : false 
	} );
    this.add( 'America/Manaus', {
		'offset'       : -14400000,
        'longname'     : "Amazon Standard Time",
		'shortname'    : 'AMT',
		'hasdst'       : false 
	} );
    this.add( 'America/Martinique', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Montserrat', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Port_of_Spain', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Porto_Velho', {
		'offset'       : -14400000,
        'longname'     : "Amazon Standard Time",
		'shortname'    : 'AMT',
		'hasdst'       : false 
	} );
    this.add( 'America/Puerto_Rico', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
    this.add( 'America/Santiago', {
		'offset'       : -14400000,
        'longname'     : "Chile Time",
		'shortname'    : 'CLT',
		'hasdst'       : true,
		'dstlongname'  : "Chile Summer Time",
		'dstshortname' : 'CLST' 
	} );
	this.add( 'America/Santo_Domingo', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'America/St_Kitts', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'America/St_Lucia', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'America/St_Thomas', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'America/St_Vincent', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'America/Thule', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'America/Tortola', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'America/Virgin', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'Antarctica/Palmer', {
		'offset'       : -14400000,
        'longname'     : "Chile Time",
		'shortname'    : 'CLT',
		'hasdst'       : true,
		'dstlongname'  : "Chile Summer Time",
		'dstshortname' : 'CLST' 
	} );
	this.add( 'Atlantic/Bermuda', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : true,
		'dstlongname'  : "Atlantic Daylight Time",
		'dstshortname' : 'ADT' 
	} );
	this.add( 'Atlantic/Stanley', {
		'offset'       : -14400000,
        'longname'     : "Falkland Is. Time",
		'shortname'    : 'FKT',
		'hasdst'       : true,
		'dstlongname'  : "Falkland Is. Summer Time",
		'dstshortname' : 'FKST' 
	} );
	this.add( 'Brazil/West', {
		'offset'       : -14400000,
        'longname'     : "Amazon Standard Time",
		'shortname'    : 'AMT',
		'hasdst'       : false 
	} );
	this.add( 'Canada/Atlantic', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : true,
		'dstlongname'  : "Atlantic Daylight Time",
		'dstshortname' : 'ADT' 
	} );
	this.add( 'Chile/Continental', {
		'offset'       : -14400000,
        'longname'     : "Chile Time",
		'shortname'    : 'CLT',
		'hasdst'       : true,
		'dstlongname'  : "Chile Summer Time",
		'dstshortname' : 'CLST' 
	} );
	this.add( 'Etc/GMT+4', {
		'offset'       : -14400000,
        'longname'     : "GMT-04:00",
		'shortname'    : 'GMT-04:00',
		'hasdst'       : false 
	} );
	this.add( 'PRT', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'SystemV/AST4', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'SystemV/AST4ADT', {
		'offset'       : -14400000,
        'longname'     : "Atlantic Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : true,
		'dstlongname'  : "Atlantic Daylight Time",
		'dstshortname' : 'ADT' 
	} );
	this.add( 'America/St_Johns', {
		'offset'       : -12600000,
        'longname'     : "Newfoundland Standard Time",
		'shortname'    : 'NST',
		'hasdst'       : true,
		'dstlongname'  : "Newfoundland Daylight Time",
		'dstshortname' : 'NDT' 
	} );
    this.add( 'CNT', {
		'offset'       : -12600000,
        'longname'     : "Newfoundland Standard Time",
		'shortname'    : 'NST',
		'hasdst'       : true,
		'dstlongname'  : "Newfoundland Daylight Time",
		'dstshortname' : 'NDT'
	} );
	this.add( 'Canada/Newfoundland', {
		'offset'       : -12600000,
        'longname'     : "Newfoundland Standard Time",
		'shortname'    : 'NST',
		'hasdst'       : true,
		'dstlongname'  : "Newfoundland Daylight Time",
		'dstshortname' : 'NDT' 
	} );
    this.add( 'AGT', {
		'offset'       : -10800000,
        'longname'     : "Argentine Time",
		'shortname'    : 'ART',
		'hasdst'       : false 
	} );
	this.add( 'America/Araguaina', {
		'offset'       : -10800000,
        'longname'     : "Brazil Time",
		'shortname'    : 'BRT',
		'hasdst'       : true,
		'dstlongname'  : "Brazil Summer Time",
		'dstshortname' : 'BRST'
	} );
	this.add( 'America/Belem', {
		'offset'       : -10800000,
        'longname'     : "Brazil Time",
		'shortname'    : 'BRT',
		'hasdst'       : false 
	} );
	this.add( 'America/Buenos_Aires', {
		'offset'       : -10800000,
        'longname'     : "Argentine Time",
		'shortname'    : 'ART',
		'hasdst'       : false 
	} );
	this.add( 'America/Catamarca', {
		'offset'       : -10800000,
        'longname'     : "Argentine Time",
		'shortname'    : 'ART',
		'hasdst'       : false
	} );
	this.add( 'America/Cayenne', {
		'offset'       : -10800000,
        'longname'     : "French Guiana Time",
		'shortname'    : 'GFT',
		'hasdst'       : false 
	} );
	this.add( 'America/Cordoba', {
		'offset'       : -10800000,
        'longname'     : "Argentine Time",
		'shortname'    : 'ART',
		'hasdst'       : false 
	} );
	this.add( 'America/Fortaleza', {
		'offset'       : -10800000,
        'longname'     : "Brazil Time",
		'shortname'    : 'BRT',
		'hasdst'       : true,
		'dstlongname'  : "Brazil Summer Time",
		'dstshortname' : 'BRST' 
	} );
	this.add( 'America/Godthab', {
		'offset'       : -10800000,
        'longname'     : "Western Greenland Time",
		'shortname'    : 'WGT',
		'hasdst'       : true,
		'dstlongname'  : "Western Greenland Summer Time",
		'dstshortname' : 'WGST' 
	} );
	this.add( 'America/Jujuy', {
		'offset'       : -10800000,
        'longname'     : "Argentine Time",
		'shortname'    : 'ART',
		'hasdst'       : false 
	} );
	this.add( 'America/Maceio', {
		'offset'       : -10800000,
        'longname'     : "Brazil Time",
		'shortname'    : 'BRT',
		'hasdst'       : true,
		'dstlongname'  : "Brazil Summer Time",
		'dstshortname' : 'BRST' 
	} );
	this.add( 'America/Mendoza', {
		'offset'       : -10800000,
        'longname'     : "Argentine Time",
		'shortname'    : 'ART',
		'hasdst'       : false 
	} );
	this.add( 'America/Miquelon', {
		'offset'       : -10800000,
        'longname'     : "Pierre & Miquelon Standard Time",
		'shortname'    : 'PMST',
		'hasdst'       : true,
		'dstlongname'  : "Pierre & Miquelon Daylight Time",
		'dstshortname' : 'PMDT' 
	} );
	this.add( 'America/Montevideo', {
		'offset'       : -10800000,
        'longname'     : "Uruguay Time",
		'shortname'    : 'UYT',
		'hasdst'       : false 
	} );
	this.add( 'America/Paramaribo', {
		'offset'       : -10800000,
        'longname'     : "Suriname Time",
		'shortname'    : 'SRT',
		'hasdst'       : false 
	} );
	this.add( 'America/Recife', {
		'offset'       : -10800000,
        'longname'     : "Brazil Time",
		'shortname'    : 'BRT',
		'hasdst'       : true,
		'dstlongname'  : "Brazil Summer Time",
		'dstshortname' : 'BRST' 
	} );
	this.add( 'America/Rosario', {
		'offset'       : -10800000,
        'longname'     : "Argentine Time",
		'shortname'    : 'ART',
		'hasdst'       : false 
	} );
	this.add( 'America/Sao_Paulo', {
		'offset'       : -10800000,
        'longname'     : "Brazil Time",
		'shortname'    : 'BRT',
		'hasdst'       : true,
		'dstlongname'  : "Brazil Summer Time",
		'dstshortname' : 'BRST' 
	} );
    this.add( 'BET', {
		'offset'       : -10800000,
        'longname'     : "Brazil Time",
		'shortname'    : 'BRT',
		'hasdst'       : true,
		'dstlongname'  : "Brazil Summer Time",
		'dstshortname' : 'BRST' 
	} );
	this.add( 'Brazil/East', {
		'offset'       : -10800000,
        'longname'     : "Brazil Time",
		'shortname'    : 'BRT',
		'hasdst'       : true,
		'dstlongname'  : "Brazil Summer Time",
		'dstshortname' : 'BRST' 
	} );
	this.add( 'Etc/GMT+3', {
		'offset'       : -10800000,
        'longname'     : "GMT-03:00",
		'shortname'    : 'GMT-03:00',
		'hasdst'       : false 
	} );
	this.add( 'America/Noronha', {
		'offset'       : -7200000,
        'longname'     : "Fernando de Noronha Time",
		'shortname'    : 'FNT',
		'hasdst'       : false 
	} );
	this.add( 'Atlantic/South_Georgia', {
		'offset'       : -7200000,
        'longname'     : "South Georgia Standard Time",
		'shortname'    : 'GST',
		'hasdst'       : false 
	} );
	this.add( 'Brazil/DeNoronha', {
		'offset'       : -7200000,
        'longname'     : "Fernando de Noronha Time",
		'shortname'    : 'FNT',
		'hasdst'       : false 
	} );
	this.add( 'Etc/GMT+2', {
		'offset'       : -7200000,
        'longname'     : "GMT-02:00",
		'shortname'    : 'GMT-02:00',
		'hasdst'       : false 
	} );
	this.add( 'America/Scoresbysund', {
		'offset'       : -3600000,
        'longname'     : "Eastern Greenland Time",
		'shortname'    : 'EGT',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Greenland Summer Time",
		'dstshortname' : 'EGST' 
	} );
	this.add( 'Atlantic/Azores', {
		'offset'       : -3600000,
        'longname'     : "Azores Time",
		'shortname'    : 'AZOT',
		'hasdst'       : true,
		'dstlongname'  : "Azores Summer Time",
		'dstshortname' : 'AZOST' 
	} );
	this.add( 'Atlantic/Cape_Verde', {
		'offset'       : -3600000,
        'longname'     : "Cape Verde Time",
		'shortname'    : 'CVT',
		'hasdst'       : false 
	} );
	this.add( 'Etc/GMT+1', {
		'offset'       : -3600000,
        'longname'     : "GMT-01:00",
		'shortname'    : 'GMT-01:00',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Abidjan', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Accra', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Bamako', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Banjul', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Bissau', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Casablanca', {
		'offset'       : 0,
        'longname'     : "Western European Time",
		'shortname'    : 'WET',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Conakry', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Dakar', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/El_Aaiun', {
		'offset'       : 0,
        'longname'     : "Western European Time",
		'shortname'    : 'WET',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Freetown', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Lome', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Monrovia', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Nouakchott', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Ouagadougou', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Sao_Tome', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Timbuktu', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
	this.add( 'America/Danmarkshavn', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
	this.add( 'Atlantic/Canary', {
		'offset'       : 0,
        'longname'     : "Western European Time",
		'shortname'    : 'WET',
		'hasdst'       : true,
		'dstlongname'  : "Western European Summer Time",
		'dstshortname' : 'WEST' 
	} );
	this.add( 'Atlantic/Faeroe', {
		'offset'       : 0,
        'longname'     : "Western European Time",
		'shortname'    : 'WET',
		'hasdst'       : true,
		'dstlongname'  : "Western European Summer Time",
		'dstshortname' : 'WEST' 
	} );
	this.add( 'Atlantic/Madeira', {
		'offset'       : 0,
        'longname'     : "Western European Time",
		'shortname'    : 'WET',
		'hasdst'       : true,
		'dstlongname'  : "Western European Summer Time",
		'dstshortname' : 'WEST' 
	} );
	this.add( 'Atlantic/Reykjavik', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
	this.add( 'Atlantic/St_Helena', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Eire', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : true,
		'dstlongname'  : "Irish Summer Time",
		'dstshortname' : 'IST' 
	} );
	this.add( 'Etc/GMT', {
		'offset'       : 0,
        'longname'     : "GMT+00:00",
		'shortname'    : 'GMT+00:00',
		'hasdst'       : false 
	} );
	this.add( 'Etc/GMT+0', {
		'offset'       : 0,
        'longname'     : "GMT+00:00",
		'shortname'    : 'GMT+00:00',
		'hasdst'       : false 
	} );
	this.add( 'Etc/GMT-0', {
		'offset'       : 0,
        'longname'     : "GMT+00:00",
		'shortname'    : 'GMT+00:00',
		'hasdst'       : false 
	} );
	this.add( 'Etc/GMT0', {
		'offset'       : 0,
        'longname'     : "GMT+00:00",
		'shortname'    : 'GMT+00:00',
		'hasdst'       : false 
	} );
	this.add( 'Etc/Greenwich', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
	this.add( 'Etc/UCT', {
		'offset'       : 0,
        'longname'     : "Coordinated Universal Time",
		'shortname'    : 'UTC',
		'hasdst'       : false 
	} );
	this.add( 'Etc/UTC', {
		'offset'       : 0,
        'longname'     : "Coordinated Universal Time",
		'shortname'    : 'UTC',
		'hasdst'       : false 
	} );
	this.add( 'Etc/Universal', {
		'offset'       : 0,
        'longname'     : "Coordinated Universal Time",
		'shortname'    : 'UTC',
		'hasdst'       : false 
	} );
	this.add( 'Etc/Zulu', {
		'offset'       : 0,
        'longname'     : "Coordinated Universal Time",
		'shortname'    : 'UTC',
		'hasdst'       : false 
	} );
	this.add( 'Europe/Belfast', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : true,
		'dstlongname'  : "British Summer Time",
		'dstshortname' : 'BST' 
	} );
	this.add( 'Europe/Dublin', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : true,
		'dstlongname'  : "Irish Summer Time",
		'dstshortname' : 'IST' 
	} );
	this.add( 'Europe/Lisbon', {
		'offset'       : 0,
        'longname'     : "Western European Time",
		'shortname'    : 'WET',
		'hasdst'       : true,
		'dstlongname'  : "Western European Summer Time",
		'dstshortname' : 'WEST' 
	} );
	this.add( 'Europe/London', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : true,
		'dstlongname'  : "British Summer Time",
		'dstshortname' : 'BST' 
	} );
    this.add( 'GB', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : true,
		'dstlongname'  : "British Summer Time",
		'dstshortname' : 'BST' 
	} );
    this.add( 'GB-Eire', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : true,
		'dstlongname'  : "British Summer Time",
		'dstshortname' : 'BST' 
	} );
    this.add( 'GMT', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'GMT0', {
		'offset'       : 0,
        'longname'     : "GMT+00:00",
		'shortname'    : 'GMT+00:00',
		'hasdst'       : false 
	} );
    this.add( 'Greenwich', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
    this.add( 'Iceland', {
		'offset'       : 0,
        'longname'     : "Greenwich Mean Time",
		'shortname'    : 'GMT',
		'hasdst'       : false 
	} );
	this.add( 'Portugal', {
		'offset'       : 0,
        'longname'     : "Western European Time",
		'shortname'    : 'WET',
		'hasdst'       : true,
		'dstlongname'  : "Western European Summer Time",
		'dstshortname' : 'WEST' 
	} );
	this.add( 'UCT', {
		'offset'       : 0,
        'longname'     : "Coordinated Universal Time",
		'shortname'    : 'UTC',
		'hasdst'       : false 
	} );
	this.add( 'UTC', {
		'offset'       : 0,
        'longname'     : "Coordinated Universal Time",
		'shortname'    : 'UTC',
		'hasdst'       : false 
	} );
	this.add( 'Universal', {
		'offset'       : 0,
        'longname'     : "Coordinated Universal Time",
		'shortname'    : 'UTC',
		'hasdst'       : false 
	} );
	this.add( 'WET', {
		'offset'       : 0,
        'longname'     : "Western European Time",
		'shortname'    : 'WET',
		'hasdst'       : true,
		'dstlongname'  : "Western European Summer Time",
		'dstshortname' : 'WEST' 
	} );
	this.add( 'Zulu', {
		'offset'       : 0,
        'longname'     : "Coordinated Universal Time",
		'shortname'    : 'UTC',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Algiers', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : false
	} );
    this.add( 'Africa/Bangui', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Brazzaville', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Ceuta', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
    this.add( 'Africa/Douala', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Kinshasa', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Lagos', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Libreville', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Luanda', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Malabo', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Ndjamena', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Niamey', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Porto-Novo', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Tunis', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Windhoek', {
		'offset'       : 3600000,
        'longname'     : "Western African Time",
		'shortname'    : 'WAT',
		'hasdst'       : true,
		'dstlongname'  : "Western African Summer Time",
		'dstshortname' : 'WAST' 
	} );
	this.add( 'Arctic/Longyearbyen', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Atlantic/Jan_Mayen', {
		'offset'       : 3600000,
        'longname'     : "Eastern Greenland Time",
		'shortname'    : 'EGT',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Greenland Summer Time",
		'dstshortname' : 'EGST' 
	} );
	this.add( 'CET', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'ECT', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Etc/GMT-1', {
		'offset'       : 3600000,
        'longname'     : "GMT+01:00",
		'shortname'    : 'GMT+01:00',
		'hasdst'       : false 
	} );
	this.add( 'Europe/Amsterdam', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Andorra', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Belgrade', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Berlin', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Bratislava', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Brussels', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Budapest', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Copenhagen', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Gibraltar', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Ljubljana', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Luxembourg', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Madrid', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Malta', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST'
	} );
	this.add( 'Europe/Monaco', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Oslo', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Paris', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Prague', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Rome', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST'
	} );
	this.add( 'Europe/San_Marino', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Sarajevo', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Skopje', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Stockholm', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Tirane', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Vaduz', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Vatican', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Vienna', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Warsaw', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Zagreb', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'Europe/Zurich', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'MET', {
		'offset'       : 3600000,
        'longname'     : "Middle Europe Time",
		'shortname'    : 'MET',
		'hasdst'       : true,
		'dstlongname'  : "Middle Europe Summer Time",
		'dstshortname' : 'MEST' 
	} );
	this.add( 'Poland', {
		'offset'       : 3600000,
        'longname'     : "Central European Time",
		'shortname'    : 'CET',
		'hasdst'       : true,
		'dstlongname'  : "Central European Summer Time",
		'dstshortname' : 'CEST' 
	} );
	this.add( 'ART', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
    this.add( 'Africa/Blantyre', {
		'offset'       : 7200000,
        'longname'     : "Central African Time",
		'shortname'    : 'CAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Bujumbura', {
		'offset'       : 7200000,
        'longname'     : "Central African Time",
		'shortname'    : 'CAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Cairo', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
    this.add( 'Africa/Gaborone', {
		'offset'       : 7200000,
        'longname'     : "Central African Time",
		'shortname'    : 'CAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Harare', {
		'offset'       : 7200000,
        'longname'     : "Central African Time",
		'shortname'    : 'CAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Johannesburg', {
		'offset'       : 7200000,
        'longname'     : "South Africa Standard Time",
		'shortname'    : 'SAST',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Kigali', {
		'offset'       : 7200000,
        'longname'     : "Central African Time",
		'shortname'    : 'CAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Lubumbashi', {
		'offset'       : 7200000,
        'longname'     : "Central African Time",
		'shortname'    : 'CAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Lusaka', {
		'offset'       : 7200000,
        'longname'     : "Central African Time",
		'shortname'    : 'CAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Maputo', {
		'offset'       : 7200000,
        'longname'     : "Central African Time",
		'shortname'    : 'CAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Maseru', {
		'offset'       : 7200000,
        'longname'     : "South Africa Standard Time",
		'shortname'    : 'SAST',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Mbabane', {
		'offset'       : 7200000,
        'longname'     : "South Africa Standard Time",
		'shortname'    : 'SAST',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Tripoli', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Amman', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Asia/Beirut', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Asia/Damascus', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Asia/Gaza', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Asia/Istanbul', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Asia/Jerusalem', {
		'offset'       : 7200000,
        'longname'     : "Israel Standard Time",
		'shortname'    : 'IST',
		'hasdst'       : true,
		'dstlongname'  : "Israel Daylight Time",
		'dstshortname' : 'IDT' 
	} );
	this.add( 'Asia/Nicosia', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Asia/Tel_Aviv', {
		'offset'       : 7200000,
        'longname'     : "Israel Standard Time",
		'shortname'    : 'IST',
		'hasdst'       : true,
		'dstlongname'  : "Israel Daylight Time",
		'dstshortname' : 'IDT' 
	} );
	this.add( 'CAT', {
		'offset'       : 7200000,
        'longname'     : "Central African Time",
		'shortname'    : 'CAT',
		'hasdst'       : false 
	} );
	this.add( 'EET', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Egypt', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Etc/GMT-2', {
		'offset'       : 7200000,
        'longname'     : "GMT+02:00",
		'shortname'    : 'GMT+02:00',
		'hasdst'       : false 
	} );
	this.add( 'Europe/Athens', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Bucharest', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Chisinau', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Helsinki', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Istanbul', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Kaliningrad', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Kiev', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Minsk', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Nicosia', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Riga', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Simferopol', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Sofia', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Tallinn', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : false 
	} );
	this.add( 'Europe/Tiraspol', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Uzhgorod', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Europe/Vilnius', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : false 
	} );
	this.add( 'Europe/Zaporozhye', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
	this.add( 'Israel', {
		'offset'       : 7200000,
        'longname'     : "Israel Standard Time",
		'shortname'    : 'IST',
		'hasdst'       : true,
		'dstlongname'  : "Israel Daylight Time",
		'dstshortname' : 'IDT' 
	} );
	this.add( 'Libya', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : false 
	} );
	this.add( 'Turkey', {
		'offset'       : 7200000,
        'longname'     : "Eastern European Time",
		'shortname'    : 'EET',
		'hasdst'       : true,
		'dstlongname'  : "Eastern European Summer Time",
		'dstshortname' : 'EEST' 
	} );
    this.add( 'Africa/Addis_Ababa', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Asmera', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Dar_es_Salaam', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Djibouti', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Kampala', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Khartoum', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Mogadishu', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
    this.add( 'Africa/Nairobi', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
	this.add( 'Antarctica/Syowa', {
		'offset'       : 10800000,
        'longname'     : "Syowa Time",
		'shortname'    : 'SYOT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Aden', {
		'offset'       : 10800000,
        'longname'     : "Arabia Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Baghdad', {
		'offset'       : 10800000,
        'longname'     : "Arabia Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : true,
		'dstlongname'  : "Arabia Daylight Time",
		'dstshortname' : 'ADT' 
	} );
	this.add( 'Asia/Bahrain', {
		'offset'       : 10800000,
        'longname'     : "Arabia Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Kuwait', {
		'offset'       : 10800000,
        'longname'     : "Arabia Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Qatar', {
		'offset'       : 10800000,
        'longname'     : "Arabia Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Riyadh', {
		'offset'       : 10800000,
        'longname'     : "Arabia Standard Time",
		'shortname'    : 'AST',
		'hasdst'       : false 
	} );
	this.add( 'EAT', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
	this.add( 'Etc/GMT-3', {
		'offset'       : 10800000,
        'longname'     : "GMT+03:00",
		'shortname'    : 'GMT+03:00',
		'hasdst'       : false 
	} );
	this.add( 'Europe/Moscow', {
		'offset'       : 10800000,
        'longname'     : "Moscow Standard Time",
		'shortname'    : 'MSK',
		'hasdst'       : true,
		'dstlongname'  : "Moscow Daylight Time",
		'dstshortname' : 'MSD' 
	} );
	this.add( 'Indian/Antananarivo', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
	this.add( 'Indian/Comoro', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
	this.add( 'Indian/Mayotte', {
		'offset'       : 10800000,
        'longname'     : "Eastern African Time",
		'shortname'    : 'EAT',
		'hasdst'       : false 
	} );
	this.add( 'W-SU', {
		'offset'       : 10800000,
        'longname'     : "Moscow Standard Time",
		'shortname'    : 'MSK',
		'hasdst'       : true,
		'dstlongname'  : "Moscow Daylight Time",
		'dstshortname' : 'MSD' 
	} );
	this.add( 'Asia/Riyadh87', {
		'offset'       : 11224000,
        'longname'     : "GMT+03:07",
		'shortname'    : 'GMT+03:07',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Riyadh88', {
		'offset'       : 11224000,
        'longname'     : "GMT+03:07",
		'shortname'    : 'GMT+03:07',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Riyadh89', {
		'offset'       : 11224000,
        'longname'     : "GMT+03:07",
		'shortname'    : 'GMT+03:07',
		'hasdst'       : false 
	} );
	this.add( 'Mideast/Riyadh87', {
		'offset'       : 11224000,
        'longname'     : "GMT+03:07",
		'shortname'    : 'GMT+03:07',
		'hasdst'       : false 
	} );
	this.add( 'Mideast/Riyadh88', {
		'offset'       : 11224000,
        'longname'     : "GMT+03:07",
		'shortname'    : 'GMT+03:07',
		'hasdst'       : false 
	} );
	this.add( 'Mideast/Riyadh89', {
		'offset'       : 11224000,
        'longname'     : "GMT+03:07",
		'shortname'    : 'GMT+03:07',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Tehran', {
		'offset'       : 12600000,
        'longname'     : "Iran Time",
		'shortname'    : 'IRT',
		'hasdst'       : true,
		'dstlongname'  : "Iran Sumer Time",
		'dstshortname' : 'IRST' 
	} );
	this.add( 'Iran', {
		'offset'       : 12600000,
        'longname'     : "Iran Time",
		'shortname'    : 'IRT',
		'hasdst'       : true,
		'dstlongname'  : "Iran Sumer Time",
		'dstshortname' : 'IRST' 
	} );
	this.add( 'Asia/Aqtau', {
		'offset'       : 14400000,
        'longname'     : "Aqtau Time",
		'shortname'    : 'AQTT',
		'hasdst'       : true,
		'dstlongname'  : "Aqtau Summer Time",
		'dstshortname' : 'AQTST' 
	} );
	this.add( 'Asia/Baku', {
		'offset'       : 14400000,
        'longname'     : "Azerbaijan Time",
		'shortname'    : 'AZT',
		'hasdst'       : true,
		'dstlongname'  : "Azerbaijan Summer Time",
		'dstshortname' : 'AZST' 
	} );
	this.add( 'Asia/Dubai', {
		'offset'       : 14400000,
        'longname'     : "Gulf Standard Time",
		'shortname'    : 'GST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Muscat', {
		'offset'       : 14400000,
        'longname'     : "Gulf Standard Time",
		'shortname'    : 'GST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Tbilisi', {
		'offset'       : 14400000,
        'longname'     : "Georgia Time",
		'shortname'    : 'GET',
		'hasdst'       : true,
		'dstlongname'  : "Georgia Summer Time",
		'dstshortname' : 'GEST' 
	} );
	this.add( 'Asia/Yerevan', {
		'offset'       : 14400000,
        'longname'     : "Armenia Time",
		'shortname'    : 'AMT',
		'hasdst'       : true,
		'dstlongname'  : "Armenia Summer Time",
		'dstshortname' : 'AMST' 
	} );
	this.add( 'Etc/GMT-4', {
		'offset'       : 14400000,
        'longname'     : "GMT+04:00",
		'shortname'    : 'GMT+04:00',
		'hasdst'       : false 
	} );
	this.add( 'Europe/Samara', {
		'offset'       : 14400000,
        'longname'     : "Samara Time",
		'shortname'    : 'SAMT',
		'hasdst'       : true,
		'dstlongname'  : "Samara Summer Time",
		'dstshortname' : 'SAMST' 
	} );
	this.add( 'Indian/Mahe', {
		'offset'       : 14400000,
        'longname'     : "Seychelles Time",
		'shortname'    : 'SCT',
		'hasdst'       : false 
	} );
	this.add( 'Indian/Mauritius', {
		'offset'       : 14400000,
        'longname'     : "Mauritius Time",
		'shortname'    : 'MUT',
		'hasdst'       : false 
	} );
	this.add( 'Indian/Reunion', {
		'offset'       : 14400000,
        'longname'     : "Reunion Time",
		'shortname'    : 'RET',
		'hasdst'       : false 
	} );
	this.add( 'NET', {
		'offset'       : 14400000,
        'longname'     : "Armenia Time",
		'shortname'    : 'AMT',
		'hasdst'       : true,
		'dstlongname'  : "Armenia Summer Time",
		'dstshortname' : 'AMST' 
	} );
	this.add( 'Asia/Kabul', {
		'offset'       : 16200000,
        'longname'     : "Afghanistan Time",
		'shortname'    : 'AFT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Aqtobe', {
		'offset'       : 18000000,
        'longname'     : "Aqtobe Time",
		'shortname'    : 'AQTT',
		'hasdst'       : true,
		'dstlongname'  : "Aqtobe Summer Time",
		'dstshortname' : 'AQTST' 
	} );
	this.add( 'Asia/Ashgabat', {
		'offset'       : 18000000,
        'longname'     : "Turkmenistan Time",
		'shortname'    : 'TMT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Ashkhabad', {
		'offset'       : 18000000,
        'longname'     : "Turkmenistan Time",
		'shortname'    : 'TMT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Bishkek', {
		'offset'       : 18000000,
        'longname'     : "Kirgizstan Time",
		'shortname'    : 'KGT',
		'hasdst'       : true,
		'dstlongname'  : "Kirgizstan Summer Time",
		'dstshortname' : 'KGST' 
	} );
	this.add( 'Asia/Dushanbe', {
		'offset'       : 18000000,
        'longname'     : "Tajikistan Time",
		'shortname'    : 'TJT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Karachi', {
		'offset'       : 18000000,
        'longname'     : "Pakistan Time",
		'shortname'    : 'PKT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Samarkand', {
		'offset'       : 18000000,
        'longname'     : "Turkmenistan Time",
		'shortname'    : 'TMT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Tashkent', {
		'offset'       : 18000000,
        'longname'     : "Uzbekistan Time",
		'shortname'    : 'UZT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Yekaterinburg', {
		'offset'       : 18000000,
        'longname'     : "Yekaterinburg Time",
		'shortname'    : 'YEKT',
		'hasdst'       : true,
		'dstlongname'  : "Yekaterinburg Summer Time",
		'dstshortname' : 'YEKST' 
	} );
	this.add( 'Etc/GMT-5', {
		'offset'       : 18000000,
        'longname'     : "GMT+05:00",
		'shortname'    : 'GMT+05:00',
		'hasdst'       : false 
	} );
	this.add( 'Indian/Kerguelen', {
		'offset'       : 18000000,
        'longname'     : "French Southern & Antarctic Lands Time",
		'shortname'    : 'TFT',
		'hasdst'       : false 
	} );
	this.add( 'Indian/Maldives', {
		'offset'       : 18000000,
        'longname'     : "Maldives Time",
		'shortname'    : 'MVT',
		'hasdst'       : false 
	} );
	this.add( 'PLT', {
		'offset'       : 18000000,
        'longname'     : "Pakistan Time",
		'shortname'    : 'PKT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Calcutta', {
		'offset'       : 19800000,
        'longname'     : "India Standard Time",
		'shortname'    : 'IST',
		'hasdst'       : false 
	} );
	this.add( 'IST', {
		'offset'       : 19800000,
        'longname'     : "India Standard Time",
		'shortname'    : 'IST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Katmandu', {
		'offset'       : 20700000,
        'longname'     : "Nepal Time",
		'shortname'    : 'NPT',
		'hasdst'       : false 
	} );
	this.add( 'Antarctica/Mawson', {
		'offset'       : 21600000,
        'longname'     : "Mawson Time",
		'shortname'    : 'MAWT',
		'hasdst'       : false 
	} );
	this.add( 'Antarctica/Vostok', {
		'offset'       : 21600000,
        'longname'     : "Vostok time",
		'shortname'    : 'VOST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Almaty', {
		'offset'       : 21600000,
        'longname'     : "Alma-Ata Time",
		'shortname'    : 'ALMT',
		'hasdst'       : true,
		'dstlongname'  : "Alma-Ata Summer Time",
		'dstshortname' : 'ALMST' 
	} );
	this.add( 'Asia/Colombo', {
		'offset'       : 21600000,
        'longname'     : "Sri Lanka Time",
		'shortname'    : 'LKT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Dacca', {
		'offset'       : 21600000,
        'longname'     : "Bangladesh Time",
		'shortname'    : 'BDT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Dhaka', {
		'offset'       : 21600000,
        'longname'     : "Bangladesh Time",
		'shortname'    : 'BDT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Novosibirsk', {
		'offset'       : 21600000,
        'longname'     : "Novosibirsk Time",
		'shortname'    : 'NOVT',
		'hasdst'       : true,
		'dstlongname'  : "Novosibirsk Summer Time",
		'dstshortname' : 'NOVST' 
	} );
	this.add( 'Asia/Omsk', {
		'offset'       : 21600000,
        'longname'     : "Omsk Time",
		'shortname'    : 'OMST',
		'hasdst'       : true,
		'dstlongname'  : "Omsk Summer Time",
		'dstshortname' : 'OMSST' 
	} );
	this.add( 'Asia/Thimbu', {
		'offset'       : 21600000,
        'longname'     : "Bhutan Time",
		'shortname'    : 'BTT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Thimphu', {
		'offset'       : 21600000,
        'longname'     : "Bhutan Time",
		'shortname'    : 'BTT',
		'hasdst'       : false 
	} );
	this.add( 'BST', {
		'offset'       : 21600000,
        'longname'     : "Bangladesh Time",
		'shortname'    : 'BDT',
		'hasdst'       : false 
	} );
	this.add( 'Etc/GMT-6', {
		'offset'       : 21600000,
        'longname'     : "GMT+06:00",
		'shortname'    : 'GMT+06:00',
		'hasdst'       : false 
	} );
	this.add( 'Indian/Chagos', {
		'offset'       : 21600000,
        'longname'     : "Indian Ocean Territory Time",
		'shortname'    : 'IOT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Rangoon', {
		'offset'       : 23400000,
        'longname'     : "Myanmar Time",
		'shortname'    : 'MMT',
		'hasdst'       : false 
	} );
	this.add( 'Indian/Cocos', {
		'offset'       : 23400000,
        'longname'     : "Cocos Islands Time",
		'shortname'    : 'CCT',
		'hasdst'       : false 
	} );
	this.add( 'Antarctica/Davis', {
		'offset'       : 25200000,
        'longname'     : "Davis Time",
		'shortname'    : 'DAVT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Bangkok', {
		'offset'       : 25200000,
        'longname'     : "Indochina Time",
		'shortname'    : 'ICT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Hovd', {
		'offset'       : 25200000,
        'longname'     : "Hovd Time",
		'shortname'    : 'HOVT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Jakarta', {
		'offset'       : 25200000,
        'longname'     : "West Indonesia Time",
		'shortname'    : 'WIT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Krasnoyarsk', {
		'offset'       : 25200000,
        'longname'     : "Krasnoyarsk Time",
		'shortname'    : 'KRAT',
		'hasdst'       : true,
		'dstlongname'  : "Krasnoyarsk Summer Time",
		'dstshortname' : 'KRAST' 
	} );
	this.add( 'Asia/Phnom_Penh', {
		'offset'       : 25200000,
        'longname'     : "Indochina Time",
		'shortname'    : 'ICT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Pontianak', {
		'offset'       : 25200000,
        'longname'     : "West Indonesia Time",
		'shortname'    : 'WIT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Saigon', {
		'offset'       : 25200000,
        'longname'     : "Indochina Time",
		'shortname'    : 'ICT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Vientiane', {
		'offset'       : 25200000,
        'longname'     : "Indochina Time",
		'shortname'    : 'ICT',
		'hasdst'       : false 
	} );
	this.add( 'Etc/GMT-7', {
		'offset'       : 25200000,
        'longname'     : "GMT+07:00",
		'shortname'    : 'GMT+07:00',
		'hasdst'       : false 
	} );
	this.add( 'Indian/Christmas', {
		'offset'       : 25200000,
        'longname'     : "Christmas Island Time",
		'shortname'    : 'CXT',
		'hasdst'       : false 
	} );
	this.add( 'VST', {
		'offset'       : 25200000,
        'longname'     : "Indochina Time",
		'shortname'    : 'ICT',
		'hasdst'       : false 
	} );
	this.add( 'Antarctica/Casey', {
		'offset'       : 28800000,
        'longname'     : "Western Standard Time (Australia)",
		'shortname'    : 'WST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Brunei', {
		'offset'       : 28800000,
        'longname'     : "Brunei Time",
		'shortname'    : 'BNT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Chongqing', {
		'offset'       : 28800000,
        'longname'     : "China Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Chungking', {
		'offset'       : 28800000,
        'longname'     : "China Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Harbin', {
		'offset'       : 28800000,
        'longname'     : "China Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Hong_Kong', {
		'offset'       : 28800000,
        'longname'     : "Hong Kong Time",
		'shortname'    : 'HKT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Irkutsk', {
		'offset'       : 28800000,
        'longname'     : "Irkutsk Time",
		'shortname'    : 'IRKT',
		'hasdst'       : true,
		'dstlongname'  : "Irkutsk Summer Time",
		'dstshortname' : 'IRKST' 
	} );
	this.add( 'Asia/Kashgar', {
		'offset'       : 28800000,
        'longname'     : "China Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Kuala_Lumpur', {
		'offset'       : 28800000,
        'longname'     : "Malaysia Time",
		'shortname'    : 'MYT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Kuching', {
		'offset'       : 28800000,
        'longname'     : "Malaysia Time",
		'shortname'    : 'MYT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Macao', {
		'offset'       : 28800000,
        'longname'     : "China Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Manila', {
		'offset'       : 28800000,
        'longname'     : "Philippines Time",
		'shortname'    : 'PHT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Shanghai', {
		'offset'       : 28800000,
        'longname'     : "China Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Singapore', {
		'offset'       : 28800000,
        'longname'     : "Singapore Time",
		'shortname'    : 'SGT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Taipei', {
		'offset'       : 28800000,
        'longname'     : "China Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Ujung_Pandang', {
		'offset'       : 28800000,
        'longname'     : "Central Indonesia Time",
		'shortname'    : 'CIT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Ulaanbaatar', {
		'offset'       : 28800000,
        'longname'     : "Ulaanbaatar Time",
		'shortname'    : 'ULAT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Ulan_Bator', {
		'offset'       : 28800000,
        'longname'     : "Ulaanbaatar Time",
		'shortname'    : 'ULAT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Urumqi', {
		'offset'       : 28800000,
        'longname'     : "China Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Australia/Perth', {
		'offset'       : 28800000,
        'longname'     : "Western Standard Time (Australia)",
		'shortname'    : 'WST',
		'hasdst'       : false 
	} );
	this.add( 'Australia/West', {
		'offset'       : 28800000,
        'longname'     : "Western Standard Time (Australia)",
		'shortname'    : 'WST',
		'hasdst'       : false 
	} );
	this.add( 'CTT', {
		'offset'       : 28800000,
        'longname'     : "China Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Etc/GMT-8', {
		'offset'       : 28800000,
        'longname'     : "GMT+08:00",
		'shortname'    : 'GMT+08:00',
		'hasdst'       : false 
	} );
	this.add( 'Hongkong', {
		'offset'       : 28800000,
        'longname'     : "Hong Kong Time",
		'shortname'    : 'HKT',
		'hasdst'       : false 
	} );
	this.add( 'PRC', {
		'offset'       : 28800000,
        'longname'     : "China Standard Time",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Singapore', {
		'offset'       : 28800000,
        'longname'     : "Singapore Time",
		'shortname'    : 'SGT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Choibalsan', {
		'offset'       : 32400000,
        'longname'     : "Choibalsan Time",
		'shortname'    : 'CHOT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Dili', {
		'offset'       : 32400000,
        'longname'     : "East Timor Time",
		'shortname'    : 'TPT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Jayapura', {
		'offset'       : 32400000,
        'longname'     : "East Indonesia Time",
		'shortname'    : 'EIT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Pyongyang', {
		'offset'       : 32400000,
        'longname'     : "Korea Standard Time",
		'shortname'    : 'KST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Seoul', {
		'offset'       : 32400000,
        'longname'     : "Korea Standard Time",
		'shortname'    : 'KST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Tokyo', {
		'offset'       : 32400000,
        'longname'     : "Japan Standard Time",
		'shortname'    : 'JST',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Yakutsk', {
		'offset'       : 32400000,
        'longname'     : "Yakutsk Time",
		'shortname'    : 'YAKT',
		'hasdst'       : true,
		'dstlongname'  : "Yaktsk Summer Time",
		'dstshortname' : 'YAKST' 
	} );
	this.add( 'Etc/GMT-9', {
		'offset'       : 32400000,
        'longname'     : "GMT+09:00",
		'shortname'    : 'GMT+09:00',
		'hasdst'       : false 
	} );
	this.add( 'JST', {
		'offset'       : 32400000,
        'longname'     : "Japan Standard Time",
		'shortname'    : 'JST',
		'hasdst'       : false 
	} );
	this.add( 'Japan', {
		'offset'       : 32400000,
        'longname'     : "Japan Standard Time",
		'shortname'    : 'JST',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Palau', {
		'offset'       : 32400000,
        'longname'     : "Palau Time",
		'shortname'    : 'PWT',
		'hasdst'       : false 
	} );
	this.add( 'ROK', {
		'offset'       : 32400000,
        'longname'     : "Korea Standard Time",
		'shortname'    : 'KST',
		'hasdst'       : false 
	} );
	this.add( 'ACT', {
		'offset'       : 34200000,
        'longname'     : "Central Standard Time (Northern Territory)",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Australia/Adelaide', {
		'offset'       : 34200000,
        'longname'     : "Central Standard Time (South Australia)",
		'shortname'    : 'CST',
		'hasdst'       : true,
		'dstlongname'  : "Central Summer Time (South Australia)",
		'dstshortname' : 'CST' 
	} );
	this.add( 'Australia/Broken_Hill', {
		'offset'       : 34200000,
        'longname'     : "Central Standard Time (South Australia/New South Wales)",
		'shortname'    : 'CST',
		'hasdst'       : true,
		'dstlongname'  : "Central Summer Time (South Australia/New South Wales)",
		'dstshortname' : 'CST' 
	} );
	this.add( 'Australia/Darwin', {
		'offset'       : 34200000,
        'longname'     : "Central Standard Time (Northern Territory)",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Australia/North', {
		'offset'       : 34200000,
        'longname'     : "Central Standard Time (Northern Territory)",
		'shortname'    : 'CST',
		'hasdst'       : false 
	} );
	this.add( 'Australia/South', {
		'offset'       : 34200000,
        'longname'     : "Central Standard Time (South Australia)",
		'shortname'    : 'CST',
		'hasdst'       : true,
		'dstlongname'  : "Central Summer Time (South Australia)",
		'dstshortname' : 'CST' 
	} );
	this.add( 'Australia/Yancowinna', {
		'offset'       : 34200000,
        'longname'     : "Central Standard Time (South Australia/New South Wales)",
		'shortname'    : 'CST',
		'hasdst'       : true,
		'dstlongname'  : "Central Summer Time (South Australia/New South Wales)",
		'dstshortname' : 'CST' 
	} );
	this.add( 'AET', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (New South Wales)",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Summer Time (New South Wales)",
		'dstshortname' : 'EST' 
	} );
	this.add( 'Antarctica/DumontDUrville', {
		'offset'       : 36000000,
        'longname'     : "Dumont-d'Urville Time",
		'shortname'    : 'DDUT',
		'hasdst'       : false 
	} );
	this.add( 'Asia/Sakhalin', {
		'offset'       : 36000000,
        'longname'     : "Sakhalin Time",
		'shortname'    : 'SAKT',
		'hasdst'       : true,
		'dstlongname'  : "Sakhalin Summer Time",
		'dstshortname' : 'SAKST' 
	} );
	this.add( 'Asia/Vladivostok', {
		'offset'       : 36000000,
        'longname'     : "Vladivostok Time",
		'shortname'    : 'VLAT',
		'hasdst'       : true,
		'dstlongname'  : "Vladivostok Summer Time",
		'dstshortname' : 'VLAST' 
	} );
	this.add( 'Australia/ACT', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (New South Wales)",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Summer Time (New South Wales)",
		'dstshortname' : 'EST' 
	} );
	this.add( 'Australia/Brisbane', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (Queensland)",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
	this.add( 'Australia/Canberra', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (New South Wales)",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Summer Time (New South Wales)",
		'dstshortname' : 'EST' 
	} );
	this.add( 'Australia/Hobart', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (Tasmania)",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Summer Time (Tasmania)",
		'dstshortname' : 'EST' 
	} );
	this.add( 'Australia/Lindeman', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (Queensland)",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
	this.add( 'Australia/Melbourne', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (Victoria)",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Summer Time (Victoria)",
		'dstshortname' : 'EST' 
	} );
	this.add( 'Australia/NSW', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (New South Wales)",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Summer Time (New South Wales)",
		'dstshortname' : 'EST' 
	} );
	this.add( 'Australia/Queensland', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (Queensland)",
		'shortname'    : 'EST',
		'hasdst'       : false 
	} );
	this.add( 'Australia/Sydney', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (New South Wales)",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Summer Time (New South Wales)",
		'dstshortname' : 'EST' 
	} );
	this.add( 'Australia/Tasmania', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (Tasmania)",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Summer Time (Tasmania)",
		'dstshortname' : 'EST' 
	} );
	this.add( 'Australia/Victoria', {
		'offset'       : 36000000,
        'longname'     : "Eastern Standard Time (Victoria)",
		'shortname'    : 'EST',
		'hasdst'       : true,
		'dstlongname'  : "Eastern Summer Time (Victoria)",
		'dstshortname' : 'EST' 
	} );
	this.add( 'Etc/GMT-10', {
		'offset'       : 36000000,
        'longname'     : "GMT+10:00",
		'shortname'    : 'GMT+10:00',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Guam', {
		'offset'       : 36000000,
        'longname'     : "Chamorro Standard Time",
		'shortname'    : 'ChST',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Port_Moresby', {
		'offset'       : 36000000,
        'longname'     : "Papua New Guinea Time",
		'shortname'    : 'PGT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Saipan', {
		'offset'       : 36000000,
        'longname'     : "Chamorro Standard Time",
		'shortname'    : 'ChST',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Truk', {
		'offset'       : 36000000,
        'longname'     : "Truk Time",
		'shortname'    : 'TRUT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Yap', {
		'offset'       : 36000000,
        'longname'     : "Yap Time",
		'shortname'    : 'YAPT',
		'hasdst'       : false 
	} );
	this.add( 'Australia/LHI', {
		'offset'       : 37800000,
        'longname'     : "Load Howe Standard Time",
		'shortname'    : 'LHST',
		'hasdst'       : true,
		'dstlongname'  : "Load Howe Summer Time",
		'dstshortname' : 'LHST' 
	} );
	this.add( 'Australia/Lord_Howe', {
		'offset'       : 37800000,
        'longname'     : "Load Howe Standard Time",
		'shortname'    : 'LHST',
		'hasdst'       : true,
		'dstlongname'  : "Load Howe Summer Time",
		'dstshortname' : 'LHST' 
	} );
	this.add( 'Asia/Magadan', {
		'offset'       : 39600000,
        'longname'     : "Magadan Time",
		'shortname'    : 'MAGT',
		'hasdst'       : true,
		'dstlongname'  : "Magadan Summer Time",
		'dstshortname' : 'MAGST' 
	} );
	this.add( 'Etc/GMT-11', {
		'offset'       : 39600000,
        'longname'     : "GMT+11:00",
		'shortname'    : 'GMT+11:00',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Efate', {
		'offset'       : 39600000,
        'longname'     : "Vanuatu Time",
		'shortname'    : 'VUT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Guadalcanal', {
		'offset'       : 39600000,
        'longname'     : "Solomon Is. Time",
		'shortname'    : 'SBT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Kosrae', {
		'offset'       : 39600000,
        'longname'     : "Kosrae Time",
		'shortname'    : 'KOST',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Noumea', {
		'offset'       : 39600000,
        'longname'     : "New Caledonia Time",
		'shortname'    : 'NCT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Ponape', {
		'offset'       : 39600000,
        'longname'     : "Ponape Time",
		'shortname'    : 'PONT',
		'hasdst'       : false 
	} );
	this.add( 'SST', {
		'offset'       : 39600000,
        'longname'     : "Solomon Is. Time",
		'shortname'    : 'SBT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Norfolk', {
		'offset'       : 41400000,
        'longname'     : "Norfolk Time",
		'shortname'    : 'NFT',
		'hasdst'       : false 
	} );
	this.add( 'Antarctica/McMurdo', {
		'offset'       : 43200000,
        'longname'     : "New Zealand Standard Time",
		'shortname'    : 'NZST',
		'hasdst'       : true,
		'dstlongname'  : "New Zealand Daylight Time",
		'dstshortname' : 'NZDT' 
	} );
	this.add( 'Antarctica/South_Pole', {
		'offset'       : 43200000,
        'longname'     : "New Zealand Standard Time",
		'shortname'    : 'NZST',
		'hasdst'       : true,
		'dstlongname'  : "New Zealand Daylight Time",
		'dstshortname' : 'NZDT' 
	} );
	this.add( 'Asia/Anadyr', {
		'offset'       : 43200000,
        'longname'     : "Anadyr Time",
		'shortname'    : 'ANAT',
		'hasdst'       : true,
		'dstlongname'  : "Anadyr Summer Time",
		'dstshortname' : 'ANAST' 
	} );
	this.add( 'Asia/Kamchatka', {
		'offset'       : 43200000,
        'longname'     : "Petropavlovsk-Kamchatski Time",
		'shortname'    : 'PETT',
		'hasdst'       : true,
		'dstlongname'  : "Petropavlovsk-Kamchatski Summer Time",
		'dstshortname' : 'PETST' 
	} );
	this.add( 'Etc/GMT-12', {
		'offset'       : 43200000,
        'longname'     : "GMT+12:00",
		'shortname'    : 'GMT+12:00',
		'hasdst'       : false 
	} );
	this.add( 'Kwajalein', {
		'offset'       : 43200000,
        'longname'     : "Marshall Islands Time",
		'shortname'    : 'MHT',
		'hasdst'       : false 
	} );
	this.add( 'NST', {
		'offset'       : 43200000,
        'longname'     : "New Zealand Standard Time",
		'shortname'    : 'NZST',
		'hasdst'       : true,
		'dstlongname'  : "New Zealand Daylight Time",
		'dstshortname' : 'NZDT' 
	} );
	this.add( 'NZ', {
		'offset'       : 43200000,
        'longname'     : "New Zealand Standard Time",
		'shortname'    : 'NZST',
		'hasdst'       : true,
		'dstlongname'  : "New Zealand Daylight Time",
		'dstshortname' : 'NZDT' 
	} );
	this.add( 'Pacific/Auckland', {
		'offset'       : 43200000,
        'longname'     : "New Zealand Standard Time",
		'shortname'    : 'NZST',
		'hasdst'       : true,
		'dstlongname'  : "New Zealand Daylight Time",
		'dstshortname' : 'NZDT' 
	} );
	this.add( 'Pacific/Fiji', {
		'offset'       : 43200000,
        'longname'     : "Fiji Time",
		'shortname'    : 'FJT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Funafuti', {
		'offset'       : 43200000,
        'longname'     : "Tuvalu Time",
		'shortname'    : 'TVT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Kwajalein', {
		'offset'       : 43200000,
        'longname'     : "Marshall Islands Time",
		'shortname'    : 'MHT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Majuro', {
		'offset'       : 43200000,
        'longname'     : "Marshall Islands Time",
		'shortname'    : 'MHT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Nauru', {
		'offset'       : 43200000,
        'longname'     : "Nauru Time",
		'shortname'    : 'NRT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Tarawa', {
		'offset'       : 43200000,
        'longname'     : "Gilbert Is. Time",
		'shortname'    : 'GILT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Wake', {
		'offset'       : 43200000,
        'longname'     : "Wake Time",
		'shortname'    : 'WAKT',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Wallis', {
		'offset'       : 43200000,
	    'longname'     : "Wallis & Futuna Time",
		'shortname'    : 'WFT',
		'hasdst'       : false
	} );
	this.add( 'NZ-CHAT', {
		'offset'       : 45900000,
        'longname'     : "Chatham Standard Time",
		'shortname'    : 'CHAST',
		'hasdst'       : true,
		'dstlongname'  : "Chatham Daylight Time",
		'dstshortname' : 'CHADT' 
	} );
	this.add( 'Pacific/Chatham', {
		'offset'       : 45900000,
        'longname'     : "Chatham Standard Time",
		'shortname'    : 'CHAST',
		'hasdst'       : true,
		'dstlongname'  : "Chatham Daylight Time",
		'dstshortname' : 'CHADT' 
	} );
	this.add( 'Etc/GMT-13', {
		'offset'       : 46800000,
        'longname'     : "GMT+13:00",
		'shortname'    : 'GMT+13:00',
		'hasdst'       : false
	} );
	this.add( 'Pacific/Enderbury', {
		'offset'       : 46800000,
        'longname'     : "Phoenix Is. Time",
		'shortname'    : 'PHOT',
		'hasdst'       : false
	} );
	this.add( 'Pacific/Tongatapu', {
		'offset'       : 46800000,
        'longname'     : "Tonga Time",
		'shortname'    : 'TOT',
		'hasdst'       : false 
	} );
	this.add( 'Etc/GMT-14', {
		'offset'       : 50400000,
        'longname'     : "GMT+14:00",
		'shortname'    : 'GMT+14:00',
		'hasdst'       : false 
	} );
	this.add( 'Pacific/Kiritimati', {
		'offset'       : 50400000,
        'longname'     : "Line Is. Time",
		'shortname'    : 'LINT',
		'hasdst'       : false
	} );
};
