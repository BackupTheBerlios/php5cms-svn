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
TimezoneProfiles = function()
{
	this.Dictionary = Dictionary;
	this.Dictionary();
	
	this._populate();
};


TimezoneProfiles.prototype = new Dictionary();
TimezoneProfiles.prototype.constructor = TimezoneProfiles;
TimezoneProfiles.superclass = Dictionary.prototype;

/**
 * @access public
 */
TimezoneProfiles.prototype.getBySystemTime = function()
{
	var d = new Date();
	// TODO
};

/**
 * @access public
 */
TimezoneProfiles.prototype.getByGMTOffset = function( offset )
{
	// TODO
};


// private methods

/**
 * @access private
 */
TimezoneProfiles.prototype._populate = function()
{
	// Standard Time Zones
	
	this.add( "GMT",
		{
			zone              : "Z",
			military          : "Zulu",
			civilianTimeZones : [ "GMT: Greenwich Mean", "UT: Universal", "UTC: Universal Co-ordinated", "WET: Western European London, England" ],
			cities            : [ "Dublin, Ireland", "Edinburgh, Scotland", "Lisbon, Portugal", "Reykjavik, Iceland", "Casablanca, Morocco" ],
			
			// daylight savings time
			gmt_summer        : "GMT+1",
			civilianTime      : [ "BST: British Summer Time" ]
		}
	);
	
	
	// East of Greenwich
	
	this.add( "GMT+1",
		{
			zone              : "A",
			military          : "Alpha",
			civilianTimeZones : [ "CET: Central European Paris, France" ],
			cities            : [ "Berlin, Germany", "Amsterdam, The Netherlands", "Brussels, Belgium", "Vienna, Austria", "Madrid, Spain", "Rome, Italy", "Bern, Switzerland", "Stockholm, Sweden", "Oslo, Norway" ],
			
			// daylight savings time
			gmt_summer        : "GMT+2",
			civilianTime      : [ "MEST: Middle European Summer", "MESZ: Middle European Summer", "SST: Swedish Summer", "FST: French Summer" ]
		}
	);
	this.add( "GMT+2",
		{
			zone              : "B",
			military          : "Bravo",
			civilianTimeZones : [ "EET: Eastern European Athens, Greece" ],
			cities            : [ "Helsinki, Finland", "Istanbul, Turkey", "Jerusalem, Israel", "Harare, Zimbabwe" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+3",
		{
			zone              : "C",
			military          : "Charlie",
			civilianTimeZones : [ "BT: Baghdad Kuwait" ],
			cities            : [ "Nairobi, Kenya", "Riyadh, Saudi Arabia", "Moscow, Russia" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+3:30",
		{
			zone              : "C*",
			military          : "",
			civilianTimeZones : [],
			cities            : [ "Tehran, Iran" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+4",
		{
			zone              : "D",
			military          : "Delta",
			civilianTimeZones : [],
			cities            : [ "Abu Dhabi, UAE", "Muscat", "Tblisi", "Volgograd", "Kabul" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+4:30",
		{
			zone              : "D*",
			military          : "",
			civilianTimeZones : [],
			cities            : [ "Afghanistan" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+5",
		{
			zone              : "E",
			military          : "Echo",
			civilianTimeZones : [],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+5:30",
		{
			zone              : "E*",
			military          : "",
			civilianTimeZones : [],
			cities            : [ "India" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	
	
	this.add( "GMT+6",
		{
			zone              : "F",
			military          : "Foxtrott",
			civilianTimeZones : [],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+6:30",
		{
			zone              : "F*",
			military          : "",
			civilianTimeZones : [],
			cities            : [ "Cocos Islands" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+7",
		{
			zone              : "G",
			military          : "Golf",
			civilianTimeZones : [ "WAST: West Australian Standard" ],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+8",
		{
			zone              : "H",
			military          : "Hotel",
			civilianTimeZones : [ "CCT: China Coast" ],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+9",
		{
			zone              : "I",
			military          : "India",
			civilianTimeZones : [ "JST: Japan Standard" ],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+9:30",
		{
			zone              : "I*",
			military          : "",
			civilianTimeZones : [ "Australia Central Standard Darwin, Australia" ],
			cities            : [ "Adelaide, Australia" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+10",
		{
			zone              : "K",
			military          : "Kilo",
			civilianTimeZones : [ "GST: Guam Standard" ],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+10:30",
		{
			zone              : "K*",
			military          : "",
			civilianTimeZones : [],
			cities            : [ "Lord Howe Island" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+11",
		{
			zone              : "L",
			military          : "Lima",
			civilianTimeZones : [],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+11:30",
		{
			zone              : "L*",
			military          : "",
			civilianTimeZones : [],
			cities            : [ "Norfolk Island" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+12",
		{
			zone              : "M",
			military          : "Mike",
			civilianTimeZones : [ "IDLE: International Date Line East", "NZST: New Zealand Standard Wellington, New Zealand" ],
			cities            : [ "Fiji", "Marshall Islands" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	
	
	this.add( "GMT+13:00",
		{
			zone              : "M*",
			military          : "",
			civilianTimeZones : [],
			cities            : [ "Rawaki Islands: Enderbury Kiribati" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT+14:00",
		{
			zone              : "M±",
			military          : "",
			civilianTimeZones : [],
			cities            : [ "Line Islands: Kiritibati" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	
	
	// West of Greenwich

	this.add( "GMT-1",
		{
			zone              : "N",
			military          : "November",
			civilianTimeZones : [ "WAT: West Africa" ],
			cities            : [ "Azores", "Cape Verde Islands" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT-2",
		{
			zone              : "O",
			military          : "Oscar",
			civilianTimeZones : [ "AT: Azores" ],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT-3",
		{
			zone              : "P",
			military          : "Papa",
			civilianTimeZones : [],
			cities            : [ "Brasilia, Brazil", "Buenos Aires, Argentina", "Georgetown, Guyana" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT-3:30",
		{
			zone              : "P*",
			military          : "",
			civilianTimeZones : [],
			cities            : [ "Newfoundland" ],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT-4",
		{
			zone              : "Q",
			military          : "Quebec",
			civilianTimeZones : [ "AST: Atlantic Standard" ],
			cities            : [ "Caracas", "La Paz" ],
			
			// daylight savings time
			gmt_summer        : "GMT-3",
			civilianTime      : [ "ADT: Atlantic Daylight" ]
		}
	);
	this.add( "GMT-5",
		{
			zone              : "R",
			military          : "Romeo",
			civilianTimeZones : [ "EST: Eastern Standard" ],
			cities            : [ "Bogota", "Lima, Peru", "New York, NY, USA" ],
			
			// daylight savings time
			gmt_summer        : "GMT-4",
			civilianTime      : [ "EDT: Eastern Daylight" ]
		}
	);
	this.add( "GMT-6",
		{
			zone              : "S",
			military          : "Sierra",
			civilianTimeZones : [ "CST: Central Standard" ],
			cities            : [ "Mexico City, Mexico", "Saskatchewan, Canada" ],
			
			// daylight savings time
			gmt_summer        : "GMT-5",
			civilianTime      : [ "CDT: Central Daylight" ]
		}
	);
	this.add( "GMT-7",
		{
			zone              : "T",
			military          : "Tango",
			civilianTimeZones : [ "MST: Mountain Standard" ],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "GMT-6",
			civilianTime      : [ "MDT: Mountain Daylight" ]
		}
	);
	this.add( "GMT-8",
		{
			zone              : "U",
			military          : "Uniform",
			civilianTimeZones : [ "PST: Pacific Standard" ],
			cities            : [ "Los Angeles, CA, USA" ],
			
			// daylight savings time
			gmt_summer        : "GMT-7",
			civilianTime      : [ "PDT: Pacific Daylight" ]
		}
	);
	this.add( "GMT-8:30",
		{
			zone              : "U*",
			military          : "",
			civilianTimeZones : [],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT-9",
		{
			zone              : "V",
			military          : "Victor",
			civilianTimeZones : [ "YST:  Yukon Standard" ],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "GMT-8",
			civilianTime      : [ "YDT: Yukon Daylight" ]
		}
	);
	this.add( "GMT-9:30",
		{
			zone              : "V*",
			military          : "",
			civilianTimeZones : [],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT-10",
		{
			zone              : "W",
			military          : "Whiskey",
			civilianTimeZones : [ "AHST: Alaska-Hawaii Standard", "CAT: Central Alaska", "HST: Hawaii Standard" ],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "GMT-9",
			civilianTime      : [ "HDT: Hawaii Daylight" ]
		}
	);
	this.add( "GMT-11",
		{
			zone              : "X",
			military          : "X-ray",
			civilianTimeZones : [ "NT: Nome" ],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
	this.add( "GMT-12",
		{
			zone              : "Y",
			military          : "Yankee",
			civilianTimeZones : [ "IDLW: International Date Line West" ],
			cities            : [],
			
			// daylight savings time
			gmt_summer        : "",
			civilianTime      : []
		}
	);
};
