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
 * MP3 class
 *
 * retrieve mp3 files info (bitrate, duration, layer, etc) and id3v1 tags (artist,title, etc.),
 * send file playlist, stream file with icecast headers url a few methods are broken,
 * and need to be completed (I have no time for this). File info and properties reading works ok.
 *
 * @package format_mp3
 */

class MP3 extends PEAR
{ 
	/**
	 * @access public
	 */
	var $id3_genres_array = array( 
		'Blues',
		'Classic Rock',
		'Country', 
		'Dance', 
		'Disco', 
		'Funk', 
		'Grunge', 
		'Hip-Hop', 
		'Jazz', 
		'Metal', 
		'New Age', 
		'Oldies', 
		'Other', 
		'Pop', 
		'R&B', 
		'Rap', 
		'Reggae', 
		'Rock', 
		'Techno', 
		'Industrial', 
		'Alternative', 
		'Ska', 
		'Death Metal', 
		'Pranks', 
		'Soundtrack', 
		'Euro-Techno', 
		'Ambient', 
		'Trip-Hop', 
		'Vocal', 
		'Jazz+Funk', 
		'Fusion', 
		'Trance', 
		'Classical', 
		'Instrumental', 
		'Acid', 
		'House', 
		'Game', 
		'Sound Clip', 
		'Gospel', 
		'Noise', 
		'AlternRock', 
		'Bass', 
		'Soul', 
		'Punk', 
		'Space', 
		'Meditative', 
		'Instrumental Pop', 
		'Instrumental Rock', 
		'Ethnic', 
		'Gothic', 
		'Darkwave', 
		'Techno-Industrial', 
		'Electronic', 
		'Pop-Folk', 
		'Eurodance', 
		'Dream', 
		'Southern Rock', 
		'Comedy', 
		'Cult', 
		'Gangsta', 
		'Top 40', 
		'Christian Rap', 
		'Pop/Funk', 
		'Jungle', 
		'Native American', 
		'Cabaret', 
		'New Wave', 
		'Psychadelic', 
		'Rave', 
		'Showtunes', 
		'Trailer', 
		'Lo-Fi', 
		'Tribal', 
		'Acid Punk', 
		'Acid Jazz', 
		'Polka', 
		'Retro', 
		'Musical', 
		'Rock & Roll', 
		'Hard Rock', 
		'Folk', 
		'Folk/Rock', 
		'National Folk', 
		'Swing', 
		'Fast Fusion', 
		'Bebob', 
		'Latin', 
		'Revival', 
		'Celtic', 
		'Bluegrass', 
		'Avantgarde', 
		'Gothic Rock', 
		'Progressive Rock', 
		'Psychedelic Rock', 
		'Symphonic Rock', 
		'Slow Rock', 
		'Big Band', 
		'Chorus', 
		'Easy Listening', 
		'Acoustic', 
		'Humour', 
		'Speech', 
		'Chanson', 
		'Opera', 
		'Chamber Music', 
		'Sonata', 
		'Symphony', 
		'Booty Bass', 
		'Primus', 
		'Porn Groove', 
		'Satire', 
		'Slow Jam', 
		'Club', 
		'Tango', 
		'Samba', 
		'Folklore', 
		'Ballad', 
		'Power Ballad', 
		'Rhythmic Soul', 
		'Freestyle', 
		'Duet', 
		'Punk Rock', 
		'Drum Solo', 
		'Acapella', 
		'Euro-house', 
		'Dance Hall' 
	);
	
	/**
	 * @access public
	 */
	var $info_bitrates = array( 
		1 => array( 
			1 => array(
				0   => 0,
				16  => 32,
				32  => 64,
				48  => 96,
				64  => 128,
				80  => 160,
				96  => 192,
				112 => 224,
				128 => 256,
				144 => 288,
				160 => 320,
				176 => 352,
				192 => 384,
				208 => 416,
				224 => 448,
				240 => false
			), 
 			2 => array(
				0   => 0,
				16  => 32,
				32  => 48,
				48  => 56,
				64  => 64,
				80  => 80,
				96  => 96,
				112 => 112,
				128 => 128,
				144 => 160,
				160 => 192,
				176 => 224,
				192 => 256,
				208 => 320,
				224 => 384,
				240 => false
			), 
  			3 => array(
				0   => 0,
				16  => 32,
				32  => 40,
				48  => 48,
				64  => 56,
				80  => 64,
				96  => 80,
				112 => 96,
				128 => 112,
				144 => 128,
				160 => 160,
				176 => 192,
				192 => 224,
				208 => 256,
				224 => 320,
				240 => false
			) 
		), 
		2 => array( 
			1 => array(
				0   => 0,
				16  => 32,
				32  => 48,
				48  => 56,
				64  => 64,
				80  => 80,
				96  => 96,
				112 => 112,
				128 => 128,
				144 => 144,
				160 => 160,
				176 => 176,
				192 => 192,
				208 => 224,
				224 => 256,
				240 => false
			), 
			2 => array(
				0   => 0,
				16  => 8,
				32  => 16,
				48  => 24,
				64  => 32,
				80  => 40,
				96  => 48,
				112 => 56,
				128 => 64,
				144 => 80,
				160 => 96,
				176 => 112,
				192 => 128,
				208 => 144,
				224 => 160,
				240 => false
			), 
			3 => array(
				0   => 0,
				16  => 8,
				32  => 16,
				48  => 24,
				64  => 32,
				80  => 40,
				96  => 48,
				112 => 56,
				128 => 64,
				144 => 80,
				160 => 96,
				176 => 112,
				192 => 128,
				208 => 144,
				224 => 160,
				240 => false
			) 
		) 
 	);

	/**
	 * @access public
	 */
	var $info_sampling_rates = array( 
		0 => array(
			0  => false,
			4  => false,
			8  => false,
			12 => false
		), 
		1 => array(
			0  => "44100 Hz",
			4  => "48000 Hz",
			8  => "32000 Hz",
			12 => false
		), 
		2 => array(
			0  => "22050 Hz",
			4  => "24000 Hz",
			8  => "16000 Hz",
			12 => false
		), 
		2.5	=> array(
			0  => "11025 Hz",
			4  => "12000 Hz",
			8  => "8000 Hz", 
			12 => false
		) 
	); 
	 
	/**
	 * @access public
	 */
 	var $info_versions = array(
		0	=> "reserved",
		1	=> "MPEG Version 1",
		2	=> "MPEG Version 2",
		2.5	=> "MPEG Version 2.5"
	);
	 
	/**
	 * @access public
	 */
 	var $info_layers = array(
		"reserved",
		"Layer I",
		"Layer II",
		"Layer III"
	); 

	/**
	 * @access public
	 */
	var $info_channel_modes = array(
		0	=> "stereo",
		64	=> "joint stereo",
		128	=> "dual channel",
		192	=> "single channel"
	); 

	/**
	 * @access public
	 */
	var $id3 = array(
		/* 
		"tag"		=> "", 
		"title"		=> "unknown", 
		"author"	=> "unknown", 
		"album"		=> "unknown", 
		"year"		=> "unknown", 
		"comment"	=> "unknown", 
		"genre_id"	=> 0, 
		"genre"		=> "unknown" 
		*/        
	);
	
	/**
	 * @access public
	 */
	var $file = ""; 
	
	/**
	 * @access public
	 */
	var $fh = false; 
	
	/**
	 * @access public
	 */
	var $id3_parsed = false;
	
	/**
	 * @access public
	 */
	var $url = "";
	
	/**
	 * @access public
	 */
	var $info = array(); 
    
	
	/**
	 * Constructor
	 *
	 * @access public
	 */      
	function MP3( $file )
	{
		if ( file_exists( $file ) )
		{ 
			$this->file = $file; 
			$this->fh   = fopen( $this->file, "r" ); 
			
			$this->url = "http://" . $_SERVER['HTTP_HOST'] . "/" . $_SERVER["PHP_SELF"];
		}
		else
		{
			$this = new PEAR_Error( "No such file." );
			return;
		} 
	} 


	/**
	 * @access public
	 */	
	function set_id3( $title = "", $author = "", $album = "", $year = "", $comment = "", $genre_id = 0 )
	{ 
		$this->wfh = fopen( $this->file, "a" ); 
		fseek( $this->wfh, -128, SEEK_END ); 
		fwrite( $this->wfh, pack( "a3a30a30a30a4a30C1", "TAG", $title, $author, $album, $year, $comment, $genre_id ), 128 ); 
		fclose( $this->wfh ); 
	} 

	/**
	 * @access public
	 */
	function get_id3()
	{ 
		$this->id3_parsed = true; 
		fseek( $this->fh, -128, SEEK_END ); 
		$line = fread( $this->fh, 10000 );
		 
		if ( preg_match( "/^TAG/", $line ) )
		{ 
			$this->id3 = unpack( "a3tag/a30title/a30author/a30album/a4year/a30comment/C1genre_id", $line ); 
 			$this->id3["genre"] = $this->id3_genres_array[$this->id3["genre_id"]]; 
  			
			return true; 
		}
		else
		{
			return PEAR::raiseError( "No idv3 tag found." );
		} 
	}
	 
	/**
	 * @access public
	 */
	function calculate_length( $id3v2_tagsize = 0 )
	{ 
		$length = floor( ( $this->info["filesize"] - $id3v2_tagsize ) / $this->info["bitrate"] * 0.008 ); 
		$min    = floor( $length / 60 ); 
		$min    = ( strlen( $min ) == 1 )? "0$min" : $min; 
		$sec    = $length % 60; 
		$sec    = ( strlen( $sec ) == 1 )? "0$sec" : $sec; 

		return( "$min:$sec" ); 
	} 

	/**
	 * @access public
	 */
	function get_info()
	{ 
		$second = $this->synchronize(); 
		$third = ord( fread( $this->fh, 1 ) ); 
		$fourth = ord( fread( $this->fh, 1 ) );
		
		$this->info["version_id"]    = ( ( $second & 16 ) > 0 )? ( ( ( $second & 8 ) > 0 )? 1 : 2 ) : ( ( ( $second & 8 ) > 0 )? 0 : 2.5 );
		$this->info["version"]       = $this->info_versions[$this->info["version_id"]]; 
		$this->info["layer_id"]      = ( ( $second & 4 ) > 0 )? ( ( ( $second & 2 ) > 0 )? 1 : 2 ) : ( ( ( $second & 2 ) > 0 )? 3 : 0 );
		$this->info["layer"]         = $this->info_layers[$this->info["layer_id"]]; 
		$this->info["protection"]    = ( ( $second & 1 ) > 0 )? "no CRC" : "CRC"; 
		$this->info["bitrate"]       = $this->info_bitrates[$this->info["version_id"]][$this->info["layer_id"]][($third & 240)]; 
		$this->info["sampling_rate"] = $this->info_sampling_rates[$this->info["version_id"]][($third & 12)]; 
 		$this->info["padding"]       = ( ( $third & 2 ) > 0 )? "on" : "off"; 
		$this->info["private"]       = ( ( $third & 1 ) > 0 )? "on" : "off"; 
		$this->info["channel_mode"]  = $this->info_channel_modes[$fourth & 192]; 
		$this->info["copyright"]     = ( ( $fourth & 8 ) > 0 )? "on" : "off"; 
		$this->info["original"]      = ( ( $fourth & 4 ) > 0 )? "on" : "off"; 
		$this->info["filesize"]      = filesize( $this->file ); 
		$this->info["length"]        = $this->calculate_length(); 
	}
	 
	/**
	 * @access public
	 */
	function synchronize()
	{
		$finished = false; 
		rewind( $this->fh );
		
		while ( !$finished )
		{ 
			$skip = ord( fread( $this->fh, 1 ) );
			
			while ( ( $skip != 255 ) && !feof( $this->fh ) )
				$skip = ord( fread( $this->fh, 1 ) ); 

			if ( feof( $this->fh ) )
				return PEAR::raiseError( "No Info header found." );
			 
			$store = ord( fread( $this->fh, 1 ) ); 

			if ( $store >= 225 )
				$finished = true; 
			else if ( feof($this->fh ) )
				return PEAR::raiseError( "No Info header found." );
		} 
		
		return( $store ); 
	} 

	/**
	 * @access public
	 */
	function get_id3v2header()
	{ 
		$bytes = fread( $this->fh, 3 ); 

		if ( $bytes != "ID3" )
			return false; 
		
		// get major and minor versions 
		$major = fread( $this->fh, 1 ); 
		$minor = fread( $this->fh, 1 ); 
		// echo( "ID3v$major.$minor" ); 
	}

	/**
	 * @access public
	 */	 
	function stream()
	{ 
		if ( !$this->id3_parsed )
		{ 
			$this->get_id3(); 
			$this->get_info(); 
		}
		
		header( "ICY 200 OK\r\n" ); 
		header( "icy-notice1:This stream requires a shoutcast/icecast compatible player.<br>\r\n" ); 
		header( "icy-notice2:php MP3 class<br>\r\n" ); 
		header( "icy-name:"  . ( ( count( $this->id3 ) > 0 )? $this->id3["title"] . " - " . $this->id3["author"] . " - " . $this->id3["album"] . " - " . $this->id3["year"] : $this->file ) . "\r\n" ); 
		header( "icy-genre:" . ( ( count( $this->id3 ) > 0 )? $this->id3["genre"] : "unspecified") . "\r\n"); 
		header( "icy-url:bbb\r\n" ); 
 		header( "icy-pub:1\r\n" ); 
		header( "icy-br:" . $this->info["bitrate"] . "\r\n" );
		
		rewind( $this->fh ); 
		fpassthru( $this->fh ); 
	}

	/**
	 * @access public
	 */	
	function send_playlist_header( $numentries = 1 )
	{ 
		header( "Content-Type: audio/mpegurl;" ); 
		echo( "[playlist]\r\n\r\n" ); 
		echo( "NumberOfEntries=$numentries\r\n" ); 
	} 

	/**
	 * @access public
	 */
	function send_pls( $server, $script )
	{ 
		$this->send_playlist_header(); 
		$path = "/"; 
		$path_array = explode( "/", dirname( $this->file ) );
		
		while ( list( $key, $val ) = each( $path_array ) )
			$path .= empty( $val ) ? "" : rawurlencode( $val ); 

		$path .= "/"; 
		$file  = rawurlencode( basename( $this->file ) );
		
		echo( "File1=http://$server/$script.mps?file=$path$file\r\n" );
	} 

	/**
	 * @access public
	 */
	function close()
	{
		@fclose( $this->fh ); 
	} 
} // END OF MP3
 
?> 
