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
 * Provide the carrier and number, class returns an HTML link to the carrier's site.
 *
 * Carriers Currently Supported (these are the literal carrier codes used by the class - more on way!):
 * "not tested" means not used with valid shipping numbers, the links do work (some sites are slow, though!).
 *
 * UPS 		    UPS
 * FEDEX 		FEDEX
 * ROADWAY   	Roadway
 * BAX    		BAX Global
 * NEWPENN    	NEW PENN
 * ABF   		ABF
 * REDSTAR		Red Star
 * YELLOW		Yellow
 * DHL		    DHL
 * FFE		    Fedex Freight East
 * EMERY		Emery
 * GOD		    G.O.D.
 * OLDDOMINION	Old Dominion
 * USPS		    US Post Office (not tested)
 * CCX		    CCX (Cargo Connect - not tested) (must be in following format - AIRLINECODE_AIRWAYPREFIX_AIRWAYNUMBER
 *	      		example: EI_053_12345675
 *	      		see class CargoConnect file for more information
 *
 * Description of Usage:
 *
 * $myLink = new ShipTrack();
 * $myLink->printLink( $carrier, $tracking_number, $linktext = "", $shipping_type = "", $openwindow = "", $extracode = "" )
 *
 * Example:
 *
 * $myLink = new ShipTrack();
 * $myLink->printLink( "UPS", "1234324324", "1", "", "_top", " (your valid code here)" );
 * echo "<br>";
 * $myLink->printLink( "FEDEX", "22223333", "<font face=\"arial\"><B>FEDEX</B></font>", "", "_blank" );
 * echo "<br>";
 * $myLink->printLink( "CCX", "EI_053_12345675", "1", "", "_blank" );
 *
 * @package commerce
 */

class ShipTrack extends PEAR
{
	/**
	 * @access public
	 */
	var $carrier = "";
	
	/**
	 * @access public
	 */
	var $tracking_number = "";
	
	/**
	 * @access public
	 */
	var $linktext = "";
	
	/**
	 * @access public
	 */
	var $shipping_type = "";
	
	/**
	 * @access public
	 */
	var $openwindow = "";
	
	/**
	 * @access public
	 */
	var $extracode = "";

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ShipTrack()
	{
	    $this->carrier	       = "";
	    $this->tracking_number = "";
	    $this->shipping_type   = "";
	    $this->openwindow      = "";
	    $this->extracode       = "";
	}


	/**
	 * @access public
	 */
	function printLink( $carrier, $tracking_number, $linktext = "", $shipping_type = "", $openwindow = "", $extracode = "" )
	{
	    echo $this->returnLink( $carrier, $tracking_number, $linktext, $shipping_type, $openwindow, $extracode );
	}

	/**
	 * @access public
	 */
	function returnLink( $carrier, $tracking_number, $linktext = "", $shipping_type = "", $openwindow = "", $extracode = "" )
	{
	    $this->init( $carrier, $tracking_number, $linktext, $shipping_type, $openwindow, $extracode );
	    return $this->makeLink();

	}

	/**
	 * @access public
	 */
	function init( $carrier, $tracking_number, $linktext = "", $shipping_type = "", $openwindow = "", $extracode = "" )
	{
	    $this->carrier         = $carrier;
	    $this->tracking_number = $tracking_number;
		$this->shipping_type   = $shipping_type;
	    $this->extracode       = $extracode;
		
	    $this->setLinkText( $linktext );
	    $this->setOpenWindow( $openwindow );

	}

	/**
	 * @access public
	 */
	function setLinkText( $linktext = "" )
	{
		$this->linktext = "";

		if ( !strlen( $linktext ) ) 
		{
		    $this->linktext = $this->carrier;
		}
		else 
		{
		    switch ( $linktext ) 
			{
				case 1:
		    		$this->linktext = $this->carrier;
		    		break;

				case 2:
		    		$this->linktext = "Track";
		    		break;
		
				default:
		    		$this->linktext = $linktext;
		 	}
		}
	}

	/**
	 * @access public
	 */
	function setOpenWindow( $openwindow = "" )
	{
		$this->openwindow = "";

		if ( !strlen( $openwindow ) )
		    $this->openwindow = "";
		else
		    $this->openwindow = " target=\"$openwindow\"";
	}

	/**
	 * @access public
	 */
	function makeLink()    
	{
	    $link = "";
		
	    switch ( strtoupper( $this->carrier ) ) 
		{
			case "UPS":
		      	$link="<a href=\"http://wwwapps.ups.com/etracking/tracking.cgi?tracknums_displayed=1&TypeOfInquiryNumber=T&HTMLVersion=4.0&InquiryNumber1=".$this->tracking_number."&track=Track\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
    			break;

			case "FEDEX":
		     	$link="<a href=\"http://www.fedex.com/cgi-bin/tracking?tracknumbers=".$this->tracking_number."&language=english&action=track&cntry_code=us\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "ROADWAY":
		     	$link="<a href=\"http://www.quiktrak.roadway.com/cgi-bin/quiktrak?type=0&pro0=".$this->tracking_number."&zip0=&pro1=&zip1=&pro2=&zip2=&pro3=&zip3=&pro4=&zip4=&pro5=&zip5=&pro6=&zip6=&pro7=&zip7=&pro8=&zip8=&pro9=&zip9=&auth=0qmsUAkRe7M&submit.x=6&submit.y=22\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "BAX":
		    	$link = "<a href=\"http://www.baxglobal.com/win-cgi/cwstrack.dll?trackby=H&trackbyno=".$this->tracking_number."&org=&dst=&mnth1=&day1=&year1=&mnth2=&day2=&year2=&submit1.x=14&submit1.y=18\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "NEWPENN":
		    	$link = "<a href=\"http://www.newpenn.com/npweb/tracking.txt/process?p_input_typ=1&p_trak_1=".$this->tracking_number."\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "ABF":
		    	$link = "<a href=\"http://www.abfs.com/trace/abftrace.asp?blnBOL=TRUE&blnPO=TRUE&blnShipper=TRUE&blnConsignee=TRUE&blnABFGraphic=TRUE&blnOrigin=TRUE&blnDestination=TRUE&RefType=a&bhcp=1&Ref=".$this->tracking_number."\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "REDSTAR":
		    	$link="<a href=\"http://www.usfc.com/tools/truckingresultsdetail.asp?txtLookupNumber=".$this->tracking_number."&radLookupNumberType=H&SearchType=1\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "YELLOW":
		    	$link = "<a href=\"http://www2.yellowcorp.com/dynamic/services/servlet?diff=protrace&CONTROLLER=com.yell.ec.inter.yfsgentracking.http.controller.TrackPro&DESTINATION=%2Fyfsgentracking%2Ftrackingresults.jsp&SOURCE=%2Fyfsgentracking%2Ftrackpro.jsp&FBNUM2=&FBNUM3=&FBNUM4=&FBNUM5=&FBNUM1=".$this->tracking_number."\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "DHL":
		     	$link="<a href=\"http://www.dhl.com/cgi-bin/tracking.pl?AWB=".$this->tracking_number."&TID=CP_ENG\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "FFE":
		     	$link="<a href=\"http://www.fedexfreight.fedex.com/protrace.jsp?as_type=PRO&as_pro=".$this->tracking_number."\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "EMERY":
		     	$link="<a href=\"http://www.emeryworld.com/tracking/trackformaction.asp?optTYPE=SHIPNUM&PRO1=".$row->tracking_number."\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "GOD":
		     	$link="<a href=\"http://www.1800dialgod.com/quickpro.asp?cat=search&Prono=".$this->tracking_number."\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "OLDDOMINION":
		     	$link="<a href=\"http://www.odfl.com/trace/Trace.jsp?Type=P&pronum=".$this->tracking_number."\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "USPS":
		     	$link="<a href=\"http://trkcnfrm1.smi.usps.com/netdata-cgi/db2www/cbd_243.d2w/output?CAMEFROM=OK&strOrigTrackNum=".$this->tracking_number."\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";
				break;

			case "CCX":
		     	list( $apc, $awp, $awn ) = split( "_", $this->tracking_number );
		     	$link = "<a href=\"http://www.cargoserv.com/tracking.asp?Carrier=$apc&Pfx=$awp&Shipment=$awn\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";

		     	// THIS URL IS also valid - if you are looking to parse the info, this is the better URL to use
		     	// $link="<a href=\"http://www.ccx.com/cx/msfsr?ccd=$apc&awn=$awn&awp=$awp&id=R88888888\"".$this->openwindow.$this->extracode.">".$this->linktext."</a>";

				break;

			default:
		    	$link = new PEAR_Error( "Carrier Code not found." );
	    }
	    
		return $link;
	}
} // END OF ShipTrack

?>
