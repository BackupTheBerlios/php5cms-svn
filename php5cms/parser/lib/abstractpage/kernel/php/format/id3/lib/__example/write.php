<?php

require( '../../../../../../prepend.php' );

using( 'format.id3.lib.ID3' );


$EditorFilename = ( isset( $_REQUEST['EditorFilename'] )? stripslashes( $_REQUEST['EditorFilename'] ) : '' );

if ( isset( $_POST['WriteOggCommentTagNow'] ) ) 
{
	$data['title']       = $_POST['EditorTitle'];
	$data['artist']      = $_POST['EditorArtist'];
	$data['album']       = $_POST['EditorAlbum'];
	$data['genre']       = ID3::lookupGenre( $_POST['EditorGenre'] );
	$data['tracknumber'] = $_POST['EditorTrack'];
	$data['comment']     = $_POST['EditorComment'];
	
	echo 'Ogg tag' . ( ID3_OGG::oggWrite( $EditorFilename, $data )? '' : ' NOT' ) . ' written successfully<HR>';
} 
else if ( isset( $_POST['WriteID3v2TagNow'] ) ) 
{
	echo 'starting to write tag<BR>';

	if ( $_POST['EditorTitle'] ) 
	{
		$data['id3']['id3v2']['TIT2']['encodingid'] = 0;
		$data['id3']['id3v2']['TIT2']['data'] = stripslashes( $_POST['EditorTitle'] );
	}
	
	if ( $_POST['EditorArtist'] ) 
	{
		$data['id3']['id3v2']['TPE1']['encodingid'] = 0;
		$data['id3']['id3v2']['TPE1']['data'] = stripslashes( $_POST['EditorArtist'] );
	}
	
	if ( $_POST['EditorAlbum'] ) 
	{
		$data['id3']['id3v2']['TALB']['encodingid'] = 0;
		$data['id3']['id3v2']['TALB']['data'] = stripslashes( $_POST['EditorAlbum'] );
	}
	
	if ( $_POST['EditorYear'] ) 
	{
		$data['id3']['id3v2']['TYER']['encodingid'] = 0;
		$data['id3']['id3v2']['TYER']['data'] = (int)stripslashes( $_POST['EditorYear'] );
	}
	
	if ( $_POST['EditorTrack'] ) 
	{
		$data['id3']['id3v2']['TRCK']['encodingid'] = 0;
		$data['id3']['id3v2']['TRCK']['data'] = (int)stripslashes( $_POST['EditorTrack'] );
	}
	
	if ( $_POST['EditorGenre'] ) 
	{
		$data['id3']['id3v2']['TCON']['encodingid'] = 0;
		$data['id3']['id3v2']['TCON']['data'] = '(' . $_POST['EditorGenre'] . ')';
	}
	
	if ( $_POST['EditorComment'] ) 
	{
		$data['id3']['id3v2']['COMM'][0]['encodingid']  = 0;
		$data['id3']['id3v2']['COMM'][0]['language']    = 'eng';
		$data['id3']['id3v2']['COMM'][0]['description'] = '';
		$data['id3']['id3v2']['COMM'][0]['data']        = stripslashes( $_POST['EditorComment'] );
	}

	if ( isset( $_FILES['userfile']['tmp_name'] ) && $_FILES['userfile']['tmp_name'] ) 
	{
		if ( is_uploaded_file( $_FILES['userfile']['tmp_name'] ) ) 
		{
			if ( $fd = @fopen( $_FILES['userfile']['tmp_name'], 'rb' ) ) 
			{
				$data['id3']['id3v2']['APIC'][0]['data'] = fread( $fd, filesize( $_FILES['userfile']['tmp_name'] ) );
				fclose( $fd );

				$data['id3']['id3v2']['APIC'][0]['encodingid']    = ( isset( $EditorAPICencodingID  )? $EditorAPICencodingID  : 0  );
				$data['id3']['id3v2']['APIC'][0]['picturetypeid'] = ( isset( $EditorAPICpictypeID   )? $EditorAPICpictypeID   : 0  );
				$data['id3']['id3v2']['APIC'][0]['description']   = ( isset( $EditorAPICdescription )? $EditorAPICdescription : '' );

				$imageinfo = ID3::getDataImageSize( $data['id3']['id3v2']['APIC'][0]['data'] );
				
				$imagetypes = array(
					1 => 'gif', 
					2 => 'jpeg', 
					3 => 'png'
				);
				
				if ( isset( $imageinfo[2] ) && ( $imageinfo[2] >= 1 ) && ( $imageinfo[2] <= 3 ) )
					$data['id3']['id3v2']['APIC'][0]['mime'] = 'image/' . $imagetypes[$imageinfo[2]];
				else
	    			echo '<B>invalid image format</B><BR>';
	    	} 
			else 
			{
	    		echo '<B>cannot open ' . $_FILES['userfile']['tmp_name'] . '</B><BR>';
	    	}
		} 
		else 
		{
	   		echo '<B>!is_uploaded_file(' . $_FILES['userfile']['tmp_name'] . ')</B><BR>';
		}
	}

	$data['id3']['id3v2']['TXXX'][0]['encodingid']  = 0;
	$data['id3']['id3v2']['TXXX'][0]['description'] = 'ID3v2-tagged by';

	// write tags
	if ( $_POST['WriteOrDelete'] == 'W' ) 
	{
		if ( isset( $_POST['VersionToEdit1'] ) && ( $_POST['VersionToEdit1'] == '1' ) ) 
		{
			if ( !is_numeric( $_POST['EditorGenre'] ) )
				$EditorGenre = 255; // ID3v1 only supports predefined numeric genres (255 = unknown)
			
			echo 'ID3v1 changes' . ( ID3::writeID3v1( $EditorFilename, $_POST['EditorTitle'], $_POST['EditorArtist'], $_POST['EditorAlbum'], $_POST['EditorYear'], $_POST['EditorComment'], $_POST['EditorGenre'], $_POST['EditorTrack'], true )? '' : ' NOT' ) . ' written successfully<HR>';
		}
		
		if ( isset( $_POST['VersionToEdit2'] ) && ( $_POST['VersionToEdit2'] == '2' ) )
			echo 'ID3v2 changes' . ( ID3::writeID3v2( $EditorFilename, $data, 3, 0, true, 0, true )? '' : ' NOT' ) . ' written successfully<HR>';
	}
	// delete tags 
	else 
	{
		if ( isset( $_POST['VersionToEdit1'] ) && ( $_POST['VersionToEdit1'] == '1' ) )
			echo 'ID3v1 tag' . ( ID3::removeID3v1( $EditorFilename, true )? '' : ' NOT' ) . ' successfully deleted<HR>';
		
		if ( isset( $_POST['VersionToEdit2'] ) && ( $_POST['VersionToEdit2'] == '2' ) )
			echo 'ID3v2 tag' . ( ID3::removeID3v2( $EditorFilename, true )? '' : ' NOT' ) . ' successfully deleted<HR>';
	}
}

echo '<A HREF="' . $_SERVER['PHP_SELF'] . '">Start Over</A><BR>';
echo '<TABLE BORDER="0"><FORM ACTION="' . $_SERVER['PHP_SELF'] . '" METHOD="POST" ENCTYPE="multipart/form-data">';
echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><B>Sample ID3v1/ID3v2/OggComment editor</B></TD></TR>';
echo '<TR><TD ALIGN="RIGHT"><B>Filename</B></TD><TD><INPUT TYPE="HIDDEN" NAME="EditorFilename" VALUE="' . ID3::fixTextFields( $EditorFilename ) . '"><I>' . $EditorFilename . '</I></TD></TR>';

if ( $EditorFilename ) 
{
	if ( file_exists( $EditorFilename ) ) 
	{
		$OldMP3fileInfo = ID3::getAllMP3info( $EditorFilename );
		
		echo '<TR><TD ALIGN="RIGHT"><B>Title</B></TD><TD><INPUT TYPE="TEXT" SIZE="40" NAME="EditorTitle" VALUE="'   . ID3::fixTextFields( isset( $OldMP3fileInfo['title']  )? $OldMP3fileInfo['title']  : '' ) . '"></TD></TR>';
		echo '<TR><TD ALIGN="RIGHT"><B>Artist</B></TD><TD><INPUT TYPE="TEXT" SIZE="40" NAME="EditorArtist" VALUE="' . ID3::fixTextFields( isset( $OldMP3fileInfo['artist'] )? $OldMP3fileInfo['artist'] : '' ) . '"></TD></TR>';
		echo '<TR><TD ALIGN="RIGHT"><B>Album</B></TD><TD><INPUT TYPE="TEXT" SIZE="40" NAME="EditorAlbum" VALUE="'   . ID3::fixTextFields( isset( $OldMP3fileInfo['album']  )? $OldMP3fileInfo['album']  : '' ) . '"></TD></TR>';
		
		if ( $OldMP3fileInfo['fileformat'] == 'mp3' )
			echo '<TR><TD ALIGN="RIGHT"><B>Year</B></TD><TD><INPUT TYPE="TEXT" SIZE="4" NAME="EditorYear" VALUE="'  . ID3::fixTextFields( isset( $OldMP3fileInfo['year']   )? $OldMP3fileInfo['year']   : '' ) . '"></TD></TR>';
		
		echo '<TR><TD ALIGN="RIGHT"><B>Track</B></TD><TD><INPUT TYPE="TEXT" SIZE="2" NAME="EditorTrack" VALUE="'    . ID3::fixTextFields( isset( $OldMP3fileInfo['track']  )? $OldMP3fileInfo['track']   : '' ) . '"></TD></TR>';
		echo '<TR><TD ALIGN="RIGHT"><B>Genre</B></TD><TD><SELECT NAME="EditorGenre">';

		$ArrayOfGenres = ID3::arrayOfGenres();	// get the array of genres
		unset( $ArrayOfGenres['CR'] );				// take off these special cases
		unset( $ArrayOfGenres['RX'] );
		unset( $ArrayOfGenres[255]  );
		asort( $ArrayOfGenres );					// sort into alphabetical order
		$ArrayOfGenres[255]  = '-Unknown-';			// and put the special cases back on the end
		$ArrayOfGenres['CR'] = '-Cover-';
		$ArrayOfGenres['RX'] = '-Remix-';
		$EditorGenre = ( isset( $OldMP3fileInfo['genre'] )? ID3::lookupGenre( $OldMP3fileInfo['genre'], true ) : 255 );
		
		foreach ( $ArrayOfGenres as $key => $value )
			echo '<OPTION VALUE="' . $key . '"' . ( ( $EditorGenre == $key )? ' SELECTED' : '' ) . '>' . $value . '</OPTION>';
		
		echo '</SELECT></TD></TR>';
		echo '<TR><TD ALIGN="RIGHT"><B>Comment</B></TD><TD><TEXTAREA COLS="30" ROWS="3" NAME="EditorComment" WRAP="VIRTUAL">' . ( isset( $OldMP3fileInfo['comment'] )? $OldMP3fileInfo['comment'] : '' ) . '</TEXTAREA></TD></TR>';

		if ( $OldMP3fileInfo['fileformat'] == 'mp3' ) 
		{
			echo '<TR><TD ALIGN="RIGHT"><B>Picture</B></TD><TD><INPUT TYPE="FILE" NAME="userfile" ACCEPT="image/jpeg, image/gif, image/png"></TD></TR>';
			echo '<INPUT TYPE="HIDDEN" NAME="WriteID3v2TagNow" VALUE="1">';
			echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="RADIO" NAME="WriteOrDelete" VALUE="W" CHECKED> Write <INPUT TYPE="RADIO" NAME="WriteOrDelete" VALUE="D"> Delete</TD></TR>';
			echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="CHECKBOX" NAME="VersionToEdit1" VALUE="1"> ID3v1 <INPUT TYPE="CHECKBOX" NAME="VersionToEdit2" VALUE="2" CHECKED> ID3v2</TD></TR>';
		} 
		else if ( $OldMP3fileInfo['fileformat'] == 'ogg' ) 
		{
			echo '<INPUT TYPE="HIDDEN" NAME="WriteOggCommentTagNow" VALUE="1">';
		}
		
		echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="SUBMIT" VALUE="Save Changes"> <INPUT TYPE="RESET" VALUE="Reset"></TD></TR>';
	} 
	else 
	{
		echo '<TR><TD ALIGN="RIGHT"><B>Error</B></TD><TD>' . ID3::fixTextFields( $EditorFilename ) . ' does not exist</TD></TR>';
		echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="SUBMIT" VALUE="Find File"></TD></TR>';
	}
} 
else 
{
	echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><INPUT TYPE="SUBMIT" VALUE="Find File"></TD></TR>';
}

echo '</FORM></TABLE>';

?>
