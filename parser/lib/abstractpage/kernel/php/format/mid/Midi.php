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
|Authors: Valentin Schmidt <fluxus@freenet.de>                         |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Class for reading, writing, analyzing, modifying, creating, downloading 
 * and playing (embedding) standard MIDI files (type 0 or 1). MIDI songs are 
 * internally represented as lists of tracks, where each track is a list of 
 * messages, and each message is a string. The message string format is 
 * exactly the same as the one used by the commandline tools MF2T/T2MF 
 * created by Piet van Oostrum (these tools are also integrated in the 
 * MIDI utility MIDI-OX, www.midiox.com).
 * 
 * Some examples for message strings:
 * 
 * 	0 Meta Text "Organ"
 * 	0 PrCh ch=2 p=46
 * 	0 Tempo 500000
 * 	96 On ch=2 n=67 v=64
 * 	6720 Pb ch=13 v=8192
 * 	6720 Par ch=13 c=64 v=0
 * 
 * The class provides methods for importing/exporting both binary midi files
 * (*.mid) and text representations in the MF2T/T2MF format and for generating 
 * and manipulating MIDI data.
 * 
 * APPLICATIONS
 * 
 * - audio toys
 * - mixers
 * - sequencers
 * - ringtone creators
 * - musical education/training
 *
 * @package format_mid
 */
 
class Midi extends PEAR
{
	/**
	 * array of tracks, where each track is array of message strings
	 * @var array
	 */
	var $tracks;
	
	/**
	 * timebase = ticks per frame (quarter note)
	 */
	var $timebase;

	/**
	 * tempo as integer (0 for unknown)
	 * @var integer
	 */
	var $tempo;
	
	/**
	 * position of tempo event in track 0
	 * @var integer
	 */
	var $tempoMsgNum;

	
	/**
	 * Creates (or resets to) new empty MIDI song.
	 *
	 * @access public
	 */
	function open( $timebase = 480 )
	{
		$this->tempo    = 0; // 125000 = 120 bpm
		$this->timebase = $timebase;
		$this->tracks   = array();
	}
	
	/**
	 * Sets tempo by replacing set tempo msg in track 0 (or adding new track 0).
	 *
	 * @access public
	 */
	function setTempo( $tempo )
	{
		$tempo = round( $tempo );
		
		if ( isset( $this->tempoMsgNum ) )
		{
			$this->tracks[0][$this->tempoMsgNum] = "0 Tempo $tempo";
		}
		else
		{
			$tempoTrack = array( 
				"0 TimeSig 4/4 24 8", 
				"0 Tempo $tempo", 
				"0 Meta TrkEnd" 
			);
			
			array_unshift( $this->tracks, $tempoTrack );
			$this->tempoMsgNum = 1;
		}
		
		$this->tempo = $tempo;
	}
	
	/**
	 * Returns tempo (0 if not set).
	 *
	 * @access public
	 */
	function getTempo()
	{
		return $this->tempo;
	}
	
	/**
	 * Sets tempo corresponding to given bpm.
	 *
	 * @access public
	 */
	function setBpm( $bpm )
	{
		$tempo = round( 60000000 / $bpm );
		$this->setTempo( $tempo );
	}
	
	/**
	 * Returns bpm corresponding to tempo.
	 *
	 * @access public
	 */
	function getBpm()
	{
		return 60000000 / $this->tempo;
	}
	
	/**
	 * Sets timebase.
	 *
	 * @access public
	 */
	function setTimebase( $tb )
	{
		$this->timebase = $tb;
	}
	
	/**
	 * Returns timebase.
	 *
	 * @access public
	 */
	function getTimebase()
	{
		return $this->timebase;
	}
	
	/**
	 * Adds new track.
	 *
	 * @access public
	 */
	function newTrack()
	{
		array_push( $this->tracks, array() );
		return count( $this->tracks );
	}
	
	/**
	 * Adds message to end of track $tn.
	 *
	 * @access public
	 */
	function addMsg( $tn, $msgStr )
	{
		$track = $this->tracks[$tn];
		$track[] = $msgStr;
		$this->tracks[$tn] = $track;
	}
	
	/**
	 * Adds message at adequate position of track $n (slower than addMsg).
	 *
	 * @access public
	 */
	function insertMsg( $tn, $msgStr )
	{
		$time  = (int)strtok( $msgStr, ' ' );
		$track = $this->tracks[$tn];
		$mc    = count( $track );
		
		for ( $i = 0; $i < $mc; $i++ )
		{
			$t = (int)strtok( $track[$i], ' ' );
			
			if ( $t >= $time ) 
				break;
		}
		
		array_splice( $this->tracks[$tn], $i, 0, $msgStr );
	}
	
	/**
	 * Returns message $mn of track $tn.
	 *
	 * @access public
	 */
	function getMsg( $tn, $mn )
	{
		return $this->$tracks[$tn][$mn];
	}
	
	/**
	 * Deletes message $mn of track $tn.
	 *
	 * @access public
	 */
	function deleteMsg( $tn, $mn )
	{
		array_splice( $this->tracks[$tn], $mn, 1 );
	}
	
	/**
	 * Deletes track $tn.
	 *
	 * @access public
	 */
	function deleteTrack( $tn )
	{
		array_splice( $this->tracks, $tn, 1 );
		return count( $this->tracks );
	}
	
	/**
	 * Deletes track $tn.
	 *
	 * @access public
	 */
	function getTrackCount()
	{
		return count( $this->tracks );
	}
	
	/**
	 * Deletes all tracks except track $tn (and $track 0 which contains tempo info).
	 *
	 * @access public
	 */
	function soloTrack( $tn )
	{
		if ( $tn == 0 ) 
			$this->tracks = array( $this->tracks[0] );
		else 
			$this->tracks = array( $this->tracks[0], $this->tracks[$tn] );
	}
	
	/**
	 * Transposes song by $dn half tone steps.
	 *
	 * @access public
	 */
	function transpose( $dn )
	{
		$tc = count( $this->tracks );
		
		for ( $i = 0; $i < $tc; $i++ ) 
			$this->transposeTrack( $i, $dn );
	}
	
	/**
	 * Transposes track $tn by $dn half tone steps.
	 *
	 * @access public
	 */
	function transposeTrack( $tn, $dn )
	{
		$track = $this->tracks[$tn];
	  	$mc = count( $track );
	  
	  	for ( $i = 0; $i < $mc; $i++ )
		{
	  		$msg = explode( ' ', $track[$i] );
			
			if ( $msg[1] == 'On' || $msg[1] == 'Off' )
			{
				eval( "\$" . $msg[3] . ';' ); // $n
				$n = max( 0, min( 127, $n + $dn ) );
				$msg[3] = "n=$n";
				$track[$i] = join( ' ', $msg );
			}
		}
		
		$this->tracks[$tn] = $track;
	}
	
	/**
	 * Import whole MIDI song as text (mf2t-format).
	 *
	 * @access public
	 */
	function importTxt( $txt )
	{
		$txt = trim( $txt );

		// make unix text format
		if ( strpos( $txt, "\r" ) !== false && strpos( $txt, "\n" ) === false ) // MAC
			$txt = str_replace( "\r", "\n", $txt );
		else // PC?
			$txt = str_replace( "\r", '', $txt );
			
		$txt            = $txt . "\n";// makes things easier
		$headerStr      = strtok( $txt, "\n" );
		$header         = explode( ' ', $headerStr ); //"MFile $type $tc $timebase";
		$this->type     = $header[1];
		$this->timebase = $header[3];
		$this->tempo    = 0;
		$trackStrings   = explode( "MTrk\n", $txt );

		array_shift( $trackStrings );
		$tracks = array();
		
		foreach ( $trackStrings as $trackStr )
		{
			$track = explode( "\n", $trackStr );
			array_pop( $track );
			array_pop( $track );
			$tracks[] = $track;
		}
		
		$this->tracks = $tracks;
		$this->_findTempo();
	}
	
	/**
	 * Imports track as text (mf2t-format).
	 *
	 * @access public
	 */
	function importTrackTxt( $txt, $tn )
	{
		$txt = trim( $txt );
		
		// make unix text format
		if ( strpos( $txt, "\r" ) !== false && strpos( $txt, "\n" ) === false ) // MAC
			$txt = str_replace( "\r", "\n", $txt );
		else // PC?
			$txt = str_replace( "\r", '', $txt );
		
		$track = explode( "\n", $txt );
		
		if ( $track[0] == 'MTrk' ) 
			array_shift( $track );
			
		if ( $track[count( $track ) - 1] == 'TrkEnd' ) 
			array_pop( $track );
		
		$tn = isset( $tn )? $tn : count( $this.tracks );
		$this->tracks[$tn] = $track;	
		
		if ( $tn == 0 ) 
			$this->_findTempo();
	}
	
	/**
	 * Returns MIDI song as text.
	 *
	 * @access public
	 */
	function getTxt()
	{
		$timebase = $this->timebase;
		$tracks   = $this->tracks;
		$tc       = count( $tracks );
		$type     = ( $tc > 1 )? 1 : 0;
		$str      =  "MFile $type $tc $timebase\n";
		
		foreach ( $tracks as $track )
		{
			$str .= "MTrk\n";
			
			foreach ( $track as $msg ) 
				$str .= $msg."\n";
			
			$str .= "TrkEnd\n";
		}
		
		return $str;
	}
	
	/**
	 * Returns track as text.
	 *
	 * @access public
	 */
	function getTrackTxt( $tn )
	{
		$track = $this->tracks[$tn];
		$str   = "MTrk\n";
		
		foreach ( $track as $msg ) 
			$str .= $msg . "\n";
		
		$str .= "TrkEnd\n";
		return $str;
	}
	
	/**
	 * Imports Standard MIDI File (typ 0 or 1).
	 *
	 * @access public
	 */
	function importMid( $smf_path )
	{
		$SMF  = fopen( $smf_path, "r" ); // Standard MIDI File, typ 0 or 1
		$song = fread( $SMF, filesize( $smf_path ) );
		fclose( $SMF );
		
		$header = substr( $song, 0, 14 );
		
		if ( substr( $header, 0, 8 ) != "MThd\0\0\0\6" ) 
			return PEAR::raiseError( 'Wrong MIDI-header.');
		
		$type = ord( $header[9] );
		
		if ( $type > 1 ) 
			return PEAR::raiseError( 'Only SMF Typ 0 and 1 supported.' );
			
		$trackCnt = ord( $header[10] ) * 256 + ord( $header[11] );
		$timebase = ord( $header[12] ) * 256 + ord( $header[13] );
		
		$this->type     = $type;
		$this->timebase = $timebase;
		$this->tempo    = 0;
		$trackStrings   = explode( 'MTrk', $song );

		array_shift( $trackStrings );
		$tracks = array();
		$tsc = count( $trackStrings );
		
		for ( $i = 0; $i < $tsc; $i++ )
		{
			$res = $this->_parseTrack( $trackStrings[$i], $i );
			
			if ( PEAR::isError( $res ) )
				return $res;
				
			$tracks[] = $res;
		}
		
		$this->tracks = $tracks;
	}
		
	/**
	 * Returns binary MIDI string.
	 *
	 * @access public
	 */
	function getMid()
	{
		$tracks = $this->tracks;
		$tc     = count($tracks);
		$type   = ( $tc > 1 )? 1 : 0;
		$midStr = "MThd\0\0\0\6\0" . chr( $type ) . Midi::_getBytes( $tc, 2 ) . Midi::_getBytes( $this->timebase, 2 );
		
		for ( $i = 0; $i < $tc; $i++ )
		{
			$track       = $tracks[$i];
			$mc          = count( $track );
			$time        = 0;
			$midStr     .= "MTrk";
			$trackStart  = strlen( $midStr );
			
			for ( $j = 0; $j < $mc; $j++ )
			{
				$line    = $track[$j];
				$t       = (int)strtok( $line, ' ' );
				$dt      = $t - $time;
				$time    = $t;
				$midStr .= Midi::_writeVarLen( $dt );
				
				$res = $this->_getMsgStr( $line );
				
				if ( PEAR::isError( $res ) )
					return $res;
					
				$midStr .= $res;
			}
			
			$trackLen = strlen( $midStr ) - $trackStart;
			$midStr   = substr( $midStr, 0, $trackStart ) . Midi::_getBytes( $trackLen, 4 ) . substr( $midStr, $trackStart );
		}
		
		return $midStr;
	}
	
	/**
	 * Saves MIDI song as Standard MIDI File.
	 *
	 * @access public
	 */
	function saveMidFile( $mid_path )
	{
		$SMF = fopen( $mid_path, "wb" ); // SMF
		$mid = $this->getMid();
		
		if ( PEAR::isError( $mid ) )
			return $mid;
			
		fwrite( $SMF, $mid );
		fclose( $SMF );
	}
	
	/**
	 * Embeds Standard MIDI File.
	 *
	 * @access public
	 */
	function playMidFile($file,$visible=1,$auto=1,$loop=1,$plug=''){
	
		switch($plug){
			case 'qt':
	?>
	<!-- QT -->
	<OBJECT CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" WIDTH="<?=($visible==0)?2:160?>" HEIGHT="<?=($visible==0)?2:16?>" CODEBASE="http://www.apple.com/qtactivex/qtplugin.cab">
	<PARAM NAME="SRC" VALUE="<?=$file?>">
	<PARAM NAME="AUTOPLAY" VALUE="<?=$auto?'true':'false'?>">
	<PARAM NAME="LOOP" VALUE="<?=$loop?'true':'false'?>">
	<PARAM NAME="CONTROLLER" VALUE="<?=($visible==0)?'false':'true'?>">
	<?=($visible==0)?'<PARAM NAME="HIDDEN" VALUE="true">':''?>
	<EMBED TYPE="video/quicktime" SRC="<?=$file?>" WIDTH="<?=($visible==0)?2:160?>" HEIGHT="<?=($visible==0)?2:16?>" AUTOPLAY="<?=$auto?'true':'false'?>" LOOP="<?=$loop?'true':'false'?>" CONTROLLER="<?=($visible==0)?'false':'true'?>" <?=($visible==0)?'HIDDEN="true" ':''?>PLUGINSPAGE="http://www.apple.com/quicktime/download/">
	</OBJECT>
	<?
			break;
			case 'wm':
	?>
	<!-- WMP -->
	<OBJECT CLASSID="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95" CODEBASE="http://www.microsoft.com/ntserver/netshow/download/en/nsmp2inf.cab#Version=5,1,51,415" type="application/x-oleobject" width=<?=($visible==0)?0:300?> height=<?=($visible==0)?0:44?>>
	<PARAM NAME="AutoStart" VALUE="<?=$auto?'true':'false'?>">
	<PARAM NAME="FileName" VALUE="<?=$file?>">
	<PARAM NAME="ControlType" VALUE="1">
	<PARAM NAME="Loop" VALUE="<?=$loop?'true':'false'?>">
	<PARAM NAME="ShowControls" VALUE="<?=($visible==0)?'false':'true'?>">
	<EMBED TYPE="video/x-ms-asf-plugin" PLUGINSPAGE="http://www.microsoft.com/windows/mediaplayer/download/default.asp" SRC="<?=$file?>" AutoStart="<?=$auto?1:0?>" ShowControls="<?=($visible==0)?0:1?>" Loop="<?=$loop?>" width=<?=($visible==0)?0:300?> height=<?=($visible==0)?0:44?>>
	</OBJECT>
	<?
			break;
			case 'bk':
	?>
	<OBJECT CLASSID="CLSID:B384F118-18EE-11D1-95C8-00A024330339" CODEBASE="http://dasdeck.de/beatnik/beatnik.cab" WIDTH=<?=($visible==0)?2:144?> HEIGHT=<?=($visible==0)?0:15?>>
	   <PARAM NAME="SRC" VALUE="<?=$file?>">
	   <PARAM NAME="TYPE" VALUE="audio/midi">
	   <PARAM NAME="WIDTH" VALUE="<?=($visible==0)?2:144?>">
	   <PARAM NAME="HEIGHT" VALUE="<?=($visible==0)?2:15?>">
	   <PARAM NAME="DISPLAY" VALUE="song">
	   <PARAM NAME="AUTOSTART" VALUE="<?=$auto?'true':'false'?>">
	   <PARAM NAME="LOOP" VALUE="<?=$loop?'true':'false'?>">
	   <?=($visible==0)?'<PARAM NAME="HIDDEN" VALUE="true">':''?>
	   <EMBED TYPE="audio/rmf" SRC="<?=$file?>" WIDTH="<?=($visible==0)?2:144?>" HEIGHT="<?=($visible==0)?2:15?>" DISPLAY="SONG" AUTOSTART="<?=$auto?'true':'false'?>" LOOP="<?=$loop?'true':'false'?>" PLUGINSPAGE="http://www.beatnik.com/to/?player"<?=($visible==0)?' HIDDEN="true"':''?>
	   >
	</OBJECT>
	<?
			break;
			default:
	?>
			<EMBED SRC="<?=$file?>" TYPE="audio/midi" AUTOSTART="<?=$auto?'TRUE':'FALSE'?>" LOOP="<?=$loop?'TRUE':'FALSE'?>"<?=($visible==0)?' HIDDEN="true"':''?>>
	<?
		}
	}

	/**
	 * Starts download of Standard MIDI File.
	 *
	 * @access public
	 */
	function downloadMidFile( $file, $output )
	{
		// $mime_type = 'audio/midi';
		$mime_type = 'application/octetstream'; // force download
		header( 'Content-Type: ' . $mime_type );
		header( 'Expires: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Content-Disposition: attachment; filename="' . $output . '"' );
		header( 'Pragma: no-cache' );
		
		$d = fopen( $file, "r" );
		fpassthru( $d );
		@fclose( $d );
		
		exit();
	}
	
	/**
	 * Returns list of standard instrument names.
	 *
	 * @access public
	 */
	function getInstrumentList()
	{
		return array(
			'Piano',
			'Bright Piano',
			'Electric Grand',
			'Honky Tonk Piano',
			'Electric Piano 1',
			'Electric Piano 2',
			'Harpsichord',
			'Clavinet',
			'Celesta',
			'Glockenspiel',
			'Music Box',
			'Vibraphone',
			'Marimba',
			'Xylophone',
			'Tubular Bell',
			'Dulcimer',
			'Hammond Organ',
			'Perc Organ',
			'Rock Organ',
			'Church Organ',
			'Reed Organ',
			'Accordion',
			'Harmonica',
			'Tango Accordion',
			'Nylon Str Guitar',
			'Steel String Guitar',
			'Jazz Electric Gtr',
			'Clean Guitar',
			'Muted Guitar',
			'Overdrive Guitar',
			'Distortion Guitar',
			'Guitar Harmonics',
			'Acoustic Bass',
			'Fingered Bass',
			'Picked Bass',
			'Fretless Bass',
			'Slap Bass 1',
			'Slap Bass 2',
			'Syn Bass 1',
			'Syn Bass 2',
			'Violin',
			'Viola',
			'Cello',
			'Contrabass',
			'Tremolo Strings',
			'Pizzicato Strings',
			'Orchestral Harp',
			'Timpani',
			'Ensemble Strings',
			'Slow Strings',
			'Synth Strings 1',
			'Synth Strings 2',
			'Choir Aahs',
			'Voice Oohs',
			'Syn Choir',
			'Orchestra Hit',
			'Trumpet',
			'Trombone',
			'Tuba',
			'Muted Trumpet',
			'French Horn',
			'Brass Ensemble',
			'Syn Brass 1',
			'Syn Brass 2',
			'Soprano Sax',
			'Alto Sax',
			'Tenor Sax',
			'Baritone Sax',
			'Oboe',
			'English Horn',
			'Bassoon',
			'Clarinet',
			'Piccolo',
			'Flute',
			'Recorder',
			'Pan Flute',
			'Bottle Blow',
			'Shakuhachi',
			'Whistle',
			'Ocarina',
			'Syn Square Wave',
			'Syn Saw Wave',
			'Syn Calliope',
			'Syn Chiff',
			'Syn Charang',
			'Syn Voice',
			'Syn Fifths Saw',
			'Syn Brass and Lead',
			'Fantasia',
			'Warm Pad',
			'Polysynth',
			'Space Vox',
			'Bowed Glass',
			'Metal Pad',
			'Halo Pad',
			'Sweep Pad',
			'Ice Rain',
			'Soundtrack',
			'Crystal',
			'Atmosphere',
			'Brightness',
			'Goblins',
			'Echo Drops',
			'Sci Fi',
			'Sitar',
			'Banjo',
			'Shamisen',
			'Koto',
			'Kalimba',
			'Bag Pipe',
			'Fiddle',
			'Shanai',
			'Tinkle Bell',
			'Agogo',
			'Steel Drums',
			'Woodblock',
			'Taiko Drum',
			'Melodic Tom',
			'Syn Drum',
			'Reverse Cymbal',
			'Guitar Fret Noise',
			'Breath Noise',
			'Seashore',
			'Bird',
			'Telephone',
			'Helicopter',
			'Applause',
			'Gunshot'
		);
	}
	
	/**
	 * Returns list of drumset instrument names.
	 *
	 * @access public
	 */
	function getDrumset()
	{
		return array(
			35 => 'Acoustic Bass Drum',
			36 => 'Bass Drum 1',
			37 => 'Side Stick',
			38 => 'Acoustic Snare',
			39 => 'Hand Clap',
			40 => 'Electric Snare',
			41 => 'Low Floor Tom',
			42 => 'Closed Hi-Hat',
			43 => 'High Floor Tom',
			44 => 'Pedal Hi-Hat',
			45 => 'Low Tom',
			46 => 'Open Hi-Hat',
			47 => 'Low Mid Tom',
			48 => 'High Mid Tom',
			49 => 'Crash Cymbal 1',
			50 => 'High Tom',
			51 => 'Ride Cymbal 1',
			52 => 'Chinese Cymbal',
			53 => 'Ride Bell',
			54 => 'Tambourine',
			55 => 'Splash Cymbal',
			56 => 'Cowbell',
			57 => 'Crash Cymbal 2',
			58 => 'Vibraslap',
			59 => 'Ride Cymbal 2',
			60 => 'High Bongo',
			61 => 'Low Bongo',
			62 => 'Mute High Conga',
			63 => 'Open High Conga',
			64 => 'Low Conga',
			65 => 'High Timbale',
			66 => 'Low Timbale',
			// 35..66
			67 => 'High Agogo',
			68 => 'Low Agogo',
			69 => 'Cabase',
			70 => 'Maracas',
			71 => 'Short Whistle',
			72 => 'Long Whistle',
			73 => 'Short Guiro',
			74 => 'Long Guiro',
			75 => 'Claves',
			76 => 'High Wood Block',
			77 => 'Low Wood Block',
			78 => 'Mute Cuica',
			79 => 'Open Cuica',
			80 => 'Mute Triangle',
			81 => 'Open Triangle'
		);
	}
	
	/**
	 * Returns list of standard drum kit names.
	 *
	 * @access public
	 */
	function getDrumkitList()
	{
		return array(
			1   => 'Dry',
			9   => 'Room',
			19  => 'Power',
			25  => 'Electronic',
			33  => 'Jazz',
			41  => 'Brush',
			57  => 'SFX',
			128 => 'Default'
		);
	}
	
	/**
	 * Returns list of note names.
	 *
	 * @access public
	 */
	function getNoteList()
	{
		// note 69 (A6) = A440
	  	// note 60 (C6) = Middle C
		return array(
			//Do          Re           Mi    Fa           So           La           Ti 
			'C0', 'Cs0', 'D0', 'Ds0', 'E0', 'F0', 'Fs0', 'G0', 'Gs0', 'A0', 'As0', 'B0',
			'C1', 'Cs1', 'D1', 'Ds1', 'E1', 'F1', 'Fs1', 'G1', 'Gs1', 'A1', 'As1', 'B1',
			'C2', 'Cs2', 'D2', 'Ds2', 'E2', 'F2', 'Fs2', 'G2', 'Gs2', 'A2', 'As2', 'B2',
			'C3', 'Cs3', 'D3', 'Ds3', 'E3', 'F3', 'Fs3', 'G3', 'Gs3', 'A3', 'As3', 'B3',
			'C4', 'Cs4', 'D4', 'Ds4', 'E4', 'F4', 'Fs4', 'G4', 'Gs4', 'A4', 'As4', 'B4',
			'C5', 'Cs5', 'D5', 'Ds5', 'E5', 'F5', 'Fs5', 'G5', 'Gs5', 'A5', 'As5', 'B5',
			'C6', 'Cs6', 'D6', 'Ds6', 'E6', 'F6', 'Fs6', 'G6', 'Gs6', 'A6', 'As6', 'B6',
			'C7', 'Cs7', 'D7', 'Ds7', 'E7', 'F7', 'Fs7', 'G7', 'Gs7', 'A7', 'As7', 'B7',
			'C8', 'Cs8', 'D8', 'Ds8', 'E8', 'F8', 'Fs8', 'G8', 'Gs8', 'A8', 'As8', 'B8',
			'C9', 'Cs9', 'D9', 'Ds9', 'E9', 'F9', 'Fs9', 'G9', 'Gs9', 'A9', 'As9', 'B9',
			'C10','Cs10','D10','Ds10','E10','F10','Fs10','G10'
		);
	}
	
	
	// private methods
		
	/**
	 * Returns binary code for message string.
	 *
	 * @access private
	 */
	function _getMsgStr( $line )
	{
		$msg = explode( ' ', $line );
		
		switch ( $msg[1] )
		{
			case 'PrCh': // 0x0C
				eval( "\$" . $msg[2] . ';' ); // chan
				eval( "\$" . $msg[3] . ';' ); // prog
				return chr( 0xC0 + $ch - 1 ) . chr( $p );
				break;
				
			case 'On': // 0x09
				eval( "\$" . $msg[2] . ';' ); // chan
				eval( "\$" . $msg[3] . ';' ); // note
				eval( "\$" . $msg[4] . ';' ); // vel
				return chr( 0x90 + $ch - 1 ) . chr( $n ) . chr( $v );
				break;
				
			case 'Off': // 0x08
				eval( "\$" . $msg[2] . ';' ); // chan
				eval( "\$" . $msg[3] . ';' ); // note
				eval( "\$" . $msg[4] . ';' ); // vel
				return chr( 0x80 + $ch - 1 ) . chr( $n ) . chr( $v );
				break;
				
			case 'PoPr': // 0x0A = PolyPressure
				eval( "\$" . $msg[2] . ';' ); // chan
				eval( "\$" . $msg[3] . ';' ); // note
				eval( "\$" . $msg[4] . ';' ); // val
				return chr( 0xA0 + $ch - 1 ) . chr( $n ) . chr( $v );
				break;
				
			case 'Par': // 0x0B = ControllerChange
				eval( "\$" . $msg[2] . ';' ); // chan
				eval( "\$" . $msg[3] . ';' ); // controller
				eval( "\$" . $msg[4] . ';' ); // val
				return chr( 0xB0 + $ch - 1 ) . chr( $c ) . chr( $v );
				break;
				
			case 'ChPr': // 0x0D = ChannelPressure
				eval( "\$" . $msg[2] . ';' ); // chan
				eval( "\$" . $msg[3] . ';' ); // val
				return chr( 0xD0 + $ch - 1 ) . chr( $v );
				break;
				
			case 'Pb': // 0x0E = PitchBend
				eval( "\$" . $msg[2] . ';' ); // chan
				eval( "\$" . $msg[3] . ';' ); // val (2 Bytes!)
				return chr( 0xE0 + $ch - 1) . Midi::_getBytes( $v, 2 );
				break;
	
			// META EVENTS
			case 'Seqnr': // 0x00 = sequence_number
				$num = chr( $msg[2] );
				return "\xFF\x00\x02$num";
				break;
	
			case 'Meta':
				$type = $msg[2];
				
				switch ( $type )
				{
					case 'Text': //0x01: // Meta Text
					
					case 'Copyright': //0x02: // Meta Copyright
					
					case 'TrkName': //0x03: // Meta TrackName ???SeqName???
					
					case 'InstrName': //0x04: // Meta InstrumentName
					
					case 'Lyric': //0x05: // Meta Lyrics
					
					case 'Marker': //0x06: // Meta Marker
					
					case 'Cue': //0x07: // Meta Cue
						$texttypes = array(
							'Text',
							'Copyright',
							'TrkName',
							'InstrName',
							'Lyric',
							'Marker',
							'Cue'
						);
						
						$byte  = chr( array_search( $type, $texttypes ) + 1 );
						$start = strpos( $line, '"' ) + 1;
						$end   = strrpos( $line, '"' );
						$txt   = substr( $line, $start, $end - $start );
						$len   = chr( strlen( $txt ) );
						return "\xFF$byte$len$txt";
						break;
						
					case 'TrkEnd': //0x2F
						return "\xFF\x2F\x00";
						break;
					
					case '0x20': // 0x20 = ChannelPrefix
						$v = chr( $msg[3] );
						return "\xFF\x20\x01$v";
						break;
						
					default:
						return PEAR::raiseError( 'Unknown meta event ' . $type );
				}
				
				break;
			
			case 'Tempo': // 0x51
				$tempo = Midi::_getBytes( (int)$msg[2], 3 );
				return "\xFF\x51\x03$tempo";
				break;
				
			case 'SMPTE': // 0x54 = SMPTE offset
				$h  = chr( $msg[2] );
				$m  = chr( $msg[3] );
				$s  = chr( $msg[4] );
				$f  = chr( $msg[5] );
				$fh = chr( $msg[6] );
				return "\xFF\x54\x05$h$m$s$f$fh";
				break;
				
			case 'TimeSig': // 0x58
				$zt = explode( '/', $msg[2] );
				$z  = chr( $zt[0]  );
				$t  = chr( $zt[1]  );
				$mc = chr( $msg[3] );
				$c  = chr( $msg[4] );
				return "\xFF\x58\x04$z$t$mc$c";
				break;
				
			case 'KeySig': // 0x59
				$vz = chr( $msg[2] );
				$g  = chr( ( $msg[3] == 'major' )? 0 : 1 );
				return "\xFF\x59\x02$vz$g";
				break;
	
			case 'SeqSpec': // 0x7F = Sequencer specific data (Bs: 0 SeqSpec 00 00 41)
				$cnt  = count( $msg ) - 2;
				$data = '';
				
				for ( $i = 0; $i < $cnt; $i++ )
					$data.=Midi::_hex2bin( $msg[$i + 2] );
					
				$len = chr( strlen( $data ) );
				return "\xFF\x7F$len$data";
				break;
				
			case 'SysEx': // 0xF0 = SysEx
				$start = strpos( $line, 'f0' );
				$end   = strrpos( $line, 'f7' );
				$data  = substr( $line, $start + 3, $end - $start - 1 );
				$data  = Midi::_hex2bin( str_replace( ' ', '', $data ) );
				$len   = chr( strlen( $data ) );
				return "\xF0$len".$data;
				break;
	
			default:
				return PEAR::raiseError( 'Unknown event ' . $msg[1] );
		}
	}
	
	/**
	 * Converts binary track string to track (list of msg strings).
	 *
	 * @access private
	 */
	function _parseTrack( $binStr, $tn )
	{
		$trackLen = strlen( $binStr );
		$p        = 4;
		$time     = 0;
		$track    = array();

		while ( $p < $trackLen )
		{
			// timedelta
			$dt    = Midi::_readVarLen( $binStr, $p );
			$time += $dt;
			$byte  = ord( $binStr[$p] );
			$high  = $byte >> 4;
			$low   = $byte - $high * 16;
	
			switch ( $high )
			{
				case 0x0C: // PrCh = ProgramChange
					$chan    = $low + 1;
					$prog    = ord( $binStr[$p + 1] );
					$track[] = "$time PrCh ch=$chan p=$prog";
					
					$p += 2;
					break;
					
				case 0x09: // On
					$chan    = $low + 1;
					$note    = ord( $binStr[$p + 1] );
					$vel     = ord( $binStr[$p + 2] );
					$last    = 'On';
					$track[] = "$time On ch=$chan n=$note v=$vel";
					
					$p += 3;
					break;
					
				case 0x08: // Off
					$chan    = $low + 1;
					$note    = ord( $binStr[$p + 1] );
					$vel     = ord( $binStr[$p + 2] );
					$last    = 'Off';
					$track[] = "$time Off ch=$chan n=$note v=$vel";
					
					$p += 3;
					break;
					
				case 0x0A: // PoPr = PolyPressure
					$chan    = $low + 1;
					$note    = ord( $binStr[$p + 1] );
					$val     = ord( $binStr[$p + 2] );
					$last    = 'PoPr';
					$track[] = "$time PoPr ch=$chan n=$note v=$val";
					
					$p += 3;
					break;
					
				case 0x0B: // Par = ControllerChange
					$chan    = $low + 1;
					$c       = ord( $binStr[$p + 1] );
					$val     = ord( $binStr[$p + 2] );
					$last    = 'Par';
					$track[] = "$time Par ch=$chan c=$c v=$val";
					
					$p += 3;
					break;
					
				case 0x0D: // ChPr = ChannelPressure
					$chan    = $low + 1;
					$val     = ord( $binStr[$p + 1] );
					$last    = 'ChPr';
					$track[] = "$time ChPr ch=$chan v=$val";
					
					$p += 2;
					break;
					
				case 0x0E: // Pb = PitchBend
					$chan    = $low + 1;
					$val     = ord( $binStr[$p + 1] ) + ord( $binStr[$p + 2] ) * 128;
					$last    = 'Pb';
					$track[] = "$time Pb ch=$chan v=$val";
					
					$p += 3;
					break;
					
				default:
					switch ( $byte )
					{
						case 0xFF: // Meta
							$meta = ord( $binStr[$p + 1] );
							
							switch ( $meta )
							{
								case 0x00: // sequence_number
									$num = ord( $binStr[$p + 2] );
									$track[] = "$time Seqnr $num";
									
									$p += 2;
									break;
	
								case 0x01: // Meta Text

								case 0x02: // Meta Copyright

								case 0x03: // Meta TrackName ???sequence_name???

								case 0x04: // Meta InstrumentName

								case 0x05: // Meta Lyrics

								case 0x06: // Meta Marker

								case 0x07: // Meta Cue
									$texttypes = array(
										'Text',
										'Copyright',
										'TrkName',
										'InstrName',
										'Lyric',
										'Marker',
										'Cue'
									);
									
									$type    = $texttypes[$meta - 1];
									$len     = ord( $binStr[$p + 2] );
									$txt     = substr( $binStr, $p + 3, $len );
									$track[] = "$time Meta $type \"$txt\"";
									
									$p += $len + 3;
									break;
									
								case 0x20: // ChannelPrefix
								  	$chan    = ord( $binStr[$p + 3] );
									$track[] = "$time Meta 0x20 $chan";
									
									$p += 4;
									break;
									
								case 0x2F: // Meta TrkEnd
									$track[] = "$time Meta TrkEnd";
									
									$p += 3;
									break;
									
								case 0x51: // Tempo
									$tempo   = ord( $binStr[$p + 3] ) * 256 * 256 + ord( $binStr[$p + 4] ) * 256 + ord( $binStr[$p + 5] );
									$track[] = "$time Tempo $tempo";

									if ( $tn == 0 && $time == 0 ) 
									{
										$this->tempo = $tempo; // ???
										$this->tempoMsgNum = count( $track ) - 1;
									}
									
									$p += 6;
									break;
									
								case 0x54: // SMPTE offset
									$h       = ord( $binStr[$p + 3] );
									$m       = ord( $binStr[$p + 4] );
									$s       = ord( $binStr[$p + 5] );
									$f       = ord( $binStr[$p + 6] );
									$fh      = ord( $binStr[$p + 7] );
									$track[] = "$time SMPTE $h $m $s $f $fh";
									
									$p += 8;
									break;
									
								case 0x58: // TimeSig
									$z       = ord( $binStr[$p + 3] );
									$t       = pow( 2, ord( $binStr[$p + 4] ) );
									$mc      = ord( $binStr[$p + 5] );
									$c       = ord( $binStr[$p + 6] );
									$track[] = "$time TimeSig $z/$t $mc $c";
									
									$p += 7;
									break;
									
								case 0x59: // KeySig
									$vz      = ord( $binStr[$p + 3] );
									$g       = ord( $binStr[$p + 4] ) == 0? 'major' : 'minor';
									$track[] = "$time KeySig $vz $g";
									
									$p += 5;
									break;
									
								case 0x7F: // Sequencer specific data (string or hexString???)
									$len     = ord( $binStr[$p + 2] );
									$data    = substr( $binStr, $p + 3, $len );
									$track[] = "$time SeqSpec \"$data\"";
									
									$p += $len + 3;
									break;
									
								default:
									return PEAR::raiseError( 'Unknown meta event ' . $time . ' ' . $byte );
							}
							
							break;
	
						case 0xF0: // SysEx
							$len = ord( $binStr[$p + 1] );
							$str = 'f0';
							
							for ( $i = 0; $i < $len; $i++ ) 
								$str .= ' ' . sprintf( "%02x", ord( $binStr[$p + 2 + $i] ) );
								
							$track[] = "$time SysEx $str";
							
							$p += $len + 2;
							break;
	
						default:
							switch ( $last )
							{
								case 'On':
								
								case 'Off':
									$note    = ord( $binStr[$p] );
									$vel     = ord( $binStr[$p + 1] );
									$track[] = "$time $last ch=$chan n=$note v=$vel";

									$p += 2;
									break;
									
								case 'PoPr':
									$note    = ord( $binStr[$p + 1] );
									$val     = ord( $binStr[$p + 2] );
									$track[] = "$time PoPr ch=$chan n=$note v=$val";
									
									$p += 2;
									break;
									
								case 'ChPr':
									$val     = ord( $binStr[$p] );
									$track[] = "$time ChPr ch=$chan v=$val";
									
									$p += 1;
									break;
									
								case 'Par':
									$c       = ord( $binStr[$p] );
									$val     = ord( $binStr[$p + 1] );
									$track[] = "$time Par ch=$chan c=$c v=$val";
									
									$p += 2;
									break;
									
								case 'Pb':
									$val     = ord( $binStr[$p] ) + ord( $binStr[$p + 1] ) * 128;
									$track[] = "$time Pb ch=$chan v=$val";
									
									$p += 2;
									break;
									
								default:
									return PEAR::raiseError( 'Unknown repetition ' . $last );
							}
					}
			}
		}
		
		return $track;
	}
	
	/**
	 * Search track 0 for set tempo msg.
	 *
	 * @access private
	 */
	function _findTempo()
	{
		$track = $this->tracks[0];
		$mc    = count( $track );
		
		for ( $i = 0; $i < $mc; $i++ )
		{
			$msg = explode( ' ', $track[$i] );
			
			if ( (int)$msg[0] > 0 ) 
				break;
			
			if ( $msg[1] == 'Tempo' )
			{
				$this->tempo = $msg[2];
				$this->tempoMsgNum = $i;

				break;
			}
		}
	}
	
	/**
	 * hexstr to binstr
	 *
	 * @access private
	 * @static
	 */
	function _hex2bin( $hex_str )
	{
		$bin_str = '';
  
  		for ( $i = 0; $i < strlen( $hex_str ); $i += 2 )
    		$bin_str .= chr( hexdec( substr( $hex_str, $i, 2 ) ) );
  
  		return $bin_str;
	}

	/**
	 * int to bytes (length $len)
	 *
	 * @access private
	 * @static
	 */
	function _getBytes( $n, $len )
	{
		$str = '';
	
		for ( $i = $len - 1; $i >= 0; $i-- )
			$str .= chr( floor( $n / pow( 256, $i ) ) );
	
		return $str;
	}

	/**
	 * variable length string to int (+repositioning)
	 *
	 * @access private
	 * @static
	 */
	function _readVarLen( $str, &$pos )
	{
		if ( ( $value = ord( $str[$pos++] ) ) & 0x80 )
		{
			$value &= 0x7F;
		
			do 
			{
	  			$value = ( $value << 7 ) + ( ( $c = ord( $str[$pos++] ) ) & 0x7F );
			} while ( $c & 0x80 );
		}
	
		return $value;
	}

	/**
	 * int to variable length string
	 *
	 * @access private
	 * @static
	 */
	function _writeVarLen( $value )
	{
		$buf = $value & 0x7F;
		$str = '';
	
		while ( ( $value >>= 7 ) )
		{
	  		$buf <<= 8;
	  		$buf  |= ( ( $value & 0x7F ) | 0x80 );
		}
	
		while ( true )
		{
			$str .= chr( $buf % 256 );
		
			if ( $buf & 0x80 ) 
				$buf >>= 8;
			else 
				break;
		}
	
		return $str;
	}
} // END OF MIDI

?>
