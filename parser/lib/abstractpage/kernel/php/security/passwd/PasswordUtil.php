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
 * Static helper functions.
 *
 * @package security_passwd
 */
 
class PasswordUtil
{
	/**
	 * Returns a unique time-stamp (microtime) based password/ID (8-digits).
	 *
	 * @access public
	 * @static
	 */
	function getXID()
	{ 
		$uab  = 57; 
		$lab  = 48; 

		$mic  = microtime(); 
		$smic = substr( $mic, 1, 2 ); 
		$emic = substr( $mic, 4, 6 ); 

		mt_srand( (double)microtime() * 1000000 );
		
		$ch    = ( mt_rand() % ( $uab - $lab ) ) + $lab; 
		$po    = strpos( $emic, chr( $ch ) ); 
		$emica = substr( $emic, 0, $po ); 
		$emicb = substr( $emic, $po, strlen( $emic ) ); 
		$out   = $emica . $smic . $emicb; 

		return $out; 
	}
	
	/**
	 * This simple function returns a randomly generated password.
	 *
	 * @access public
	 * @static
	 */
	function getPassword()
	{ 
		// set password length 
		$pw_length = 8; 

		// set ASCII range for random character generation 
		$lower_ascii_bound = 50;
		$upper_ascii_bound = 122;
		
    	// Exclude special characters and some confusing alphanumerics 
    	// o,O,0,I,1,l etc
		$notuse = array(
			58,
			59,
			60,
			61,
			62,
			63,
			64,
			73,
			79,
			91,
			92,
			93,
			94,
			95,
			96,
			108,
			111
		); 

		while ( $i < $pw_length )
		{ 
			mt_srand( (double)microtime() * 1000000 ); 
                
			// random limits within ASCII table 
        	$randnum = mt_rand( $lower_ascii_bound, $upper_ascii_bound ); 
                
			if ( !in_array( $randnum, $notuse ) )
			{ 
           		$password = $password . chr( $randnum ); 
				$i++; 
			} 
		} 

		return $password;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function getSimplePassword()
	{
    	$passwdlength = 8;
		
    	$passwdkey .= "1234567890";
    	$passwdkey .= "abcdefghijklmnopqrstuvwxyz";
    	$passwdkey .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    
		srand( (double)microtime() * 1000000 );
	
    	while ( $passwdlength )
		{
        	$result .= substr( $passwdkey, rand( 0, strlen( $passwdkey ) ), 1 );
        	$passwdlength--;
    	}
	
    	return( $result );
	}

	/**
	 * @access public
	 * @static
	 */
	function getCryptedPassword( $type = "strong", $length = 8 )
	{
		if ( $type == "weak" )
		{
			$password = substr(
				ereg_replace( "[^A-Z]", "", crypt( time() ) ) .
				ereg_replace( "[^A-Z]", "", crypt( time() ) ) .
				ereg_replace( "[^A-Z]", "", crypt( time() ) ),
            	0, $length );
		}
		else if ( $type == "medium" )
		{
			$password = substr(
				ereg_replace( "[^A-Z0-9]", "", crypt( time() ) ) .
				ereg_replace( "[^A-Z0-9]", "", crypt( time() ) ) .
				ereg_replace( "[^A-Z0-9]", "", crypt( time() ) ),
				0, $length );
		}
		else if ( $type == "strong" )
		{
			$password = substr(
				ereg_replace( "[^A-Za-z0-9]", "", crypt( time() ) ) .
				ereg_replace( "[^A-Za-z0-9]", "", crypt( time() ) ) .
				ereg_replace( "[^A-Za-z0-9]", "", crypt( time() ) ),
          		0, $length );
		}

		return $password;
	}
	
	/**
	 * getPasswordFromDict will generate human readable passwords.
	 * It has been designed to generate them on the fly, by using  
	 * random numbers to manipulate the file pointer location.                      
	 * It uses dict.txt and random numbers to paste together a password 
	 * between min_length and max_length chars long.
	 *
	 * @access public
	 * @static
	 */
	function getPasswordFromDict( $dict_path = "dict.txt", $min_length = 7, $max_length = 10 )
	{ 
		mt_srand( (double)microtime() * 1000000 );
		
    	$fp   = fopen( $dict_path, "r" ); 
    	$size = filesize( $dict_path ); 
     
    	while ( strlen( $password ) < $min_length )
		{ 
			// move to a random spot in the file 
			fseek( $fp, mt_rand( 0, $size - 8 ) ); 
      
	  		// finish off the current word 
      		fgets( $fp, 4096 ); 
      		$word = trim( fgets( $fp, 4096 ) ); 
      
	  		if ( ( strlen( $word ) + strlen( $password ) ) <= $max_length ) 
        		$password .= $word; 
    	} 
     
		fclose( $fp );
		return $password;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function securePasswordGenerate()
	{ 
    	mt_srand( (float)microtime() * 1000000 ); 
    	$securePassword = "";
    	$safeEnglishWords = PasswordUtil::_getEnglishWords(); 
    	$count = count( $safeEnglishWords ); 
    
		for ( $i = 0; $i < $this->numberOfWords; $i++ )
		{ 
        	$securePassword .= $safeEnglishWords[mt_rand( 0, $count )]; 
        
			if ( $this->digitsAfterLastWord || $i + 1 != $this->numberOfWords )
				$securePassword .= mt_rand( 0, pow( 10, $this->maxNumberOfDigitBetweenWords ) -1 ); 
    	} 

    	return $securePassword; 
	} 

	/**
	 * @access public
	 * @static
	 */
	function randomizer( $length )
	{ 
    	$arr = array(
			"1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "q", "w", "e",
			"r", "t", "y", "u", "i", "o", "p", "a", "s", "d", "f", "g", "h",
			"j", "k", "l", "z", "x", "c", "v", "b", "n", "m", "Q", "W", "E",
			"R", "T", "Y", "U", "I", "O", "P", "A", "S", "D", "F", "G", "H",
			"J", "K", "L", "Z", "X", "C", "V", "B", "N", "M"
		);
		 
		srand( (float)microtime() * 1000000 ); 
    
		for ( $i = $length; $i > 0; $i-- ) 
        	$str .= $arr[rand( 0, sizeof( $arr ) )];         
    
    	return $str; 
	}

	/**
	 * The function will return one of three values: 
	 * -2 if there was a file reading error 
	 * -1 if the password is incorrect 
	 *  0 if the username doesn't exist 
	 *  1 if the password is correct 
	 *
	 * @access public
	 * @static
	 */
	function verify( $username, $password, $passwd_file )
	{
		$fhandle  = $passwd_file;
		$fd       = fopen( $fhandle, "r" ); 
		$contents = fread( $fd, filesize( $fhandle ) ); 
		
		fclose( $fd ); 

		if ( !$contents )
			return -2; 
		
		$lines  = split( "\n", $contents ); 
		$passwd = array(); 

		for ( $count = 0; $count < count( $lines ); $count++ )
		{ 
			list( $user, $pass ) = split( ":", $lines[$count] ); 

			if ( $user == $username ) 
       			break; 
		} 

		if ( !$user )
			return false; 

		$cryptedpass = $pass; 
		$salt = substr( $cryptedpass, 0, 2 );
		$Pass = crypt( $password, $salt ); 

		if ( $Pass == $cryptedpass ) 
			return true; 
		else
			return -1;
	}
	
	
	// private

	/**
	 * @access private
	 * @static
	 */	
	function _getEnglishWords()
	{ 
    	$array = array(
			'a', 'able', 'about', 'above', 'accept', 'accident', 'accuse', 'across', 'act',  
			'activist', 'actor', 'add', 'administration', 'admit', 'advise', 'affect', 'afraid', 'after', 'again', 
			'against', 'age', 'agency', 'aggression', 'ago', 'agree', 'agriculture', 'aid', 'aim', 'air',  
			'airplane', 'airport', 'alive', 'all', 'ally', 'almost', 'alone', 'along', 'already', 'also',  
			'although', 'always', 'ambassador', 'amend', 'ammunition', 'among', 'amount', 'an', 'anarchy',  
			'ancient', 'and', 'anger', 'animal', 'anniversary', 'announce', 'another', 'answer', 'any',  
			'apologize', 'appeal', 'appear', 'appoint', 'approve', 'area', 'argue', 'arms', 'army', 'around',  
			'arrest', 'arrive', 'art', 'artillery', 'as', 'ash', 'ask', 'assist', 'astronaut', 'asylum', 'at',  
			'atmosphere', 'atom', 'attack', 'attempt', 'attend', 'automobile', 'autumn', 'awake', 'award', 'away',  
			'back', 'bad', 'balance', 'ball', 'balloon', 'ballot', 'ban', 'bank', 'bar', 'base', 'battle', 'be', 
			'beach', 'beat', 'beauty', 'because', 'become', 'bed', 'beg', 'begin', 'behind', 'believe', 'bell', 
			'belong', 'below', 'best', 'betray', 'better', 'between', 'big', 'bill', 'bird', 'bite', 'bitter', 
			'black', 'blame', 'blanket', 'bleed', 'blind', 'block', 'blood', 'blow', 'blue', 'boat', 'body', 
			'boil', 'bomb', 'bone', 'book', 'border', 'born', 'borrow', 'both', 'bottle', 'bottom', 'box', 
			'boy', 'brain', 'brave', 'bread', 'break', 'breathe', 'bridge', 'brief', 'bright', 'bring', 
			'broadcast', 'brother', 'brown', 'build', 'bullet', 'burn', 'burst', 'bury', 'bus', 'business', 
			'busy', 'but', 'buy', 'by', 'cabinet', 'call', 'calm', 'camera', 'campaign', 'can', 'cancel', 
			'cancer', 'candidate', 'cannon', 'capital', 'capture', 'car', 'care', 'careful', 'carry', 'case', 
			'cat', 'catch', 'cattle', 'cause', 'ceasefire', 'celebrate', 'cell', 'center', 'century', 
			'ceremony', 'chairman', 'champion', 'chance', 'change', 'charge', 'chase', 'cheat', 'check', 
			'cheer', 'chemicals', 'chieg', 'child', 'choose', 'church', 'circle', 'citizen', 'city', 
			'civil', 'civilian', 'clash', 'clean', 'clear', 'clergy', 'climb', 'clock', 'close', 'cloth', 
			'clothes', 'cloud', 'coal', 'coalition', 'coast', 'coffee', 'cold', 'collect', 'colony', 'color', 
			'come', 'comedy', 'command', 'comment', 'committee', 'common', 'communicate', 'company', 'compete', 
			'complete', 'compromise', 'computer', 'concern', 'condemn', 'condition', 'conference', 'confirm', 
			'conflict', 'congratulate', 'congress', 'connect', 'conservative', 'consider', 'contain', 
			'continent', 'continue', 'control', 'convention', 'cook', 'cool', 'cooperate', 'copy', 'correct', 
			'cost', 'costitution', 'cotton', 'count', 'country', 'court', 'cover', 'cow', 
			'coward', 'crash', 'create', 'creature', 'credit', 'crew', 'crime', 'criminal', 'crisis', 
			'criticize', 'crops', 'cross', 'crowd', 'cruel', 'crush', 'cry', 'culture', 'cure', 'current', 
			'custom', 'cut', 'dam', 'damage', 'dance', 'danger', 'dark', 'date', 'daughter', 'day', 'dead', 
			'deaf', 'deal', 'debate', 'decide', 'declare', 'deep', 'defeat', 'defend', 'deficit', 'degree', 
			'delay', 'delegate', 'demand', 'democracy', 'demonstrate', 'denounce', 'deny', 'depend', 'deplore', 
			'deploy', 'describe', 'desert', 'design', 'desire', 'destroy', 'details', 'develop', 'device', 'dictator', 'die',  
			'different', 'difficult', 'dig', 'dinner', 'diplomat', 'direct', 'direction', 'dirty', 'disappear', 'disarm', 'discover',  
			'discuss', 'disease', 'dismiss', 'dispute', 'dissident', 'distance', 'distant', 'dive', 'divide', 'do', 'doctor', 'document', 
 			'dollar', 'door', 'down', 'draft', 'dream', 'drink', 'drive', 'drown', 'drugs', 'dry', 'during', 'dust', 'duty', 'each',  
			'early', 'earn', 'earth', 'earthquake', 'ease', 'east', 'easy', 'eat', 'economy', 'edge', 'educate', 'effect', 'effort',  
			'egg', 'either', 'elect', 'electricity', 'electron', 'element', 'embassy', 'emergency', 'emotion', 'employ', 'empty', 'end',  
			'enemy', 'energy', 'enforce', 'engine', 'engineer', 'enjoy', 'enough', 'enter', 'eqipment', 'equal', 'escape', 'especially',  
			'establish', 'even', 'event', 'ever', 'every', 'evidence', 'evil', 'evironment', 'exact', 'examine', 'example', 'excellent', 
 			'except', 'exchange', 'excite', 'excuse', 'execute', 'exile', 'exist', 'expand', 'expect', 'expel', 'experiment', 'expert',  
			'explain', 'explode', 'explore', 'export', 'express', 'extend', 'extra', 'extreme', 'face', 'fact', 'factory', 'fail',  
			'fair', 'fall', 'family', 'famous', 'fanatic', 'far', 'farm', 'fast', 'fat', 'father', 'fear', 'feast', 'federal', 'feed',  
			'feel', 'female', 'fertile', 'few', 'field', 'fierce', 'fight', 'fill', 'film', 'final', 'find', 'fine', 'finish', 'fire',  
			'firm', 'first', 'fish', 'fix', 'flag', 'flat', 'flee', 'float', 'flood', 'floor', 'flow', 'flower', 'fluid', 'fly', 'fog',  
			'follow', 'food', 'fool', 'foot', 'for', 'force', 'foreign', 'forget', 'forgive', 'form', 'former', 'forward', 'free',  
			'freeze', 'fresh', 'friend', 'frighten', 'from', 'front', 'fruit', 'fuel', 'funeral', 'furious', 'future', 'gain', 'game',  
			'gas', 'gather', 'general', 'gentle', 'get', 'gift', 'girl', 'give', 'glass', 'go', 'goal', 'God', 'gold', 'good',  
			'good-bye', 'goods', 'govern', 'government', 'grain', 'grandfather', 'grandmother', 'grass', 'gray', 'great', 'green',  
			'grind', 'ground', 'group', 'grow', 'guarantee', 'guard', 'guerilla', 'guide', 'guilty', 'gun', 'hair', 'half', 'halt',  
			'hang', 'happen', 'happy', 'harbor', 'hard', 'harm', 'hat', 'hate', 'he', 'head', 'headquarters', 'health', 'hear', 'heart', 
 			'heat', 'heavy', 'helicopter', 'help', 'here', 'hero', 'hide', 'high', 'hijack', 'hill', 'history', 'hit', 'hold', 'hole', 
 			'holiday', 'holy', 'home', 'honest', 'honor', 'hope', 'horrible', 'horse', 'hospital', 'hostage', 'hostile', 'hostilities', 
 			'hot', 'hotel', 'hour', 'house', 'how', 'however', 'huge', 'human', 'humor', 'hunger', 'hunt', 'hurry', 'hurt', 'husband',  
			'I', 'ice', 'idea', 'if', 'illegal', 'imagine', 'immediate', 'import', 'important', 'improve', 'in', 'incident', 'incite',  
			'include', 'increase', 'independent', 'industry', 'inflation', 'influence', 'inform', 'injure', 'innocent', 'insane',  
			'insect', 'inspect', 'instead', 'instrument', 'insult', 'intelligent', 'intense', 'interest', 'interfere', 'international',  
			'intervene', 'invade', 'invent', 'invest', 'investigate', 'invite', 'involve', 'iron', 'island', 'issue', 'it', 'jail',  
			'jewel', 'job', 'join', 'joint', 'joke', 'judge', 'jump', 'jungle', 'jury', 'just', 'keep', 'kick', 'kidnap', 'kill', 'kind', 
 			'kiss', 'knife', 'know', 'labor', 'laboratory', 'lack', 'lake', 'land', 'language', 'large', 'last', 'late', 'laugh',  
			'launch', 'law', 'lead', 'leak', 'learn', 'leave', 'left', 'legal', 'lend', 'less', 'let', 'letter', 'level', 'liberal',  
			'lie', 'life', 'light', 'lightning', 'like', 'limit', 'line', 'link', 'liquid', 'list', 'listen', 'little', 'live', 'load',  
			'local', 'lonely', 'long', 'look', 'lose', 'loud', 'love', 'low', 'loyal', 'luck', 'machine', 'mad', 'mail', 'main', 'major', 
 			'majority', 'make', 'male', 'man', 'map', 'march', 'mark', 'marker', 'mass', 'material', 'may', 'mayor', 'mean', 'measure',  
			'meat', 'medicine', 'meet', 'melt', 'member', 'memorial', 'memory', 'mercenary', 'mercy', 'message', 'metal', 'method',  
			'microscope', 'middle', 'militant', 'military', 'milk', 'mind', 'mine', 'mineral', 'minister', 'minor', 'minority', 'minute', 
 			'miss', 'missile', 'missing', 'mistake', 'mix', 'mob', 'moderate', 'modern', 'money', 'month', 'moon', 'more', 'morning',  
			'most', 'mother', 'motion', 'mountain', 'mourn', 'move', 'much', 'murder', 'music', 'must', 'mystery', 'naked', 'name',  
			'nation', 'navy', 'near', 'necessary', 'need', 'negotiate', 'neither', 'nerve', 'neutral', 'never', 'new', 'news', 'next',  
			'nice', 'night', 'no', 'noise', 'nominate', 'noon', 'normal', 'north', 'not', 'note', 'nothing', 'now', 'nowhere', 'nuclear', 
 			'number', 'nurse', 'obey', 'object', 'observe', 'occupy', 'ocean', 'of', 'off', 'offensive', 'offer', 'officer', 'official', 
 			'often', 'oil', 'old', 'on', 'once', 'only', 'open', 'operate', 'opinion', 'oppose', 'opposite', 'oppress', 'or', 'orbit',  
			'orchestra', 'order', 'organize', 'other', 'oust', 'out', 'over', 'overthrow', 'owe', 'own', 'pain', 'paint', 'palace',  
			'pamphlet', 'pan', 'paper', 'parachute', 'parade', 'pardon', 'parent', 'parliament', 'part', 'party', 'pass', 'passenger',  
			'passport', 'past', 'path', 'pay', 'peace', 'people', 'percent', 'perfect', 'perhaps', 'period', 'permanent', 'permit',  
			'person', 'physics', 'piano', 'picture', 'piece', 'pilot', 'pipe', 'pirate', 'place', 'planet', 'plant', 'play', 'please',  
			'plenty', 'plot', 'poem', 'point', 'poison', 'police', 'policy', 'politics', 'pollute', 'poor', 'popular', 'population',  
			'port', 'position', 'possess', 'possible', 'postpone', 'pour', 'power', 'praise', 'pray', 'pregnant', 'prepare', 'present',  
			'president', 'press', 'pressure', 'prevent', 'price', 'priest', 'prison', 'private', 'prize', 'probably', 'problem',  
			'produce', 'professor', 'program', 'progress', 'project', 'promise', 'propaganda', 'property', 'propose', 'protect',  
			'protest', 'proud', 'prove', 'provide', 'public', 'publication', 'publish', 'pull', 'pump', 'punish', 'purchase', 'pure',  
			'purpose', 'push', 'put', 'question', 'quick', 'quiet', 'rabbi', 'race', 'radar', 'radiation', 'radio', 'raid', 'railroad',  
			'rain', 'raise', 'rapid', 'rare', 'rate', 'reach', 'read', 'ready', 'real', 'realistic', 'reason', 'reasonable', 'rebel',  
			'receive', 'recent', 'recession', 'recognize', 'record', 'red', 'reduce', 'reform', 'refugee', 'refuse', 'regret',  
			'relations', 'release', 'religion', 'remain', 'remember', 'remove', 'repair', 'repeat', 'report', 'repress', 'request',  
			'rescue', 'resign', 'resolution', 'responsible', 'rest', 'restrain', 'restrict', 'result', 'retire', 'return', 'revolt',  
			'rice', 'rich', 'ride', 'right', 'riot', 'rise', 'river', 'road', 'rob', 'rock', 'rocket', 'roll', 'room', 'root', 'rope',  
			'rough', 'round', 'rub', 'rubber', 'ruin', 'rule', 'run', 'sabotage', 'sacrifice', 'sad', 'safe', 'sail', 'salt', 'same',  
			'satellite', 'satisfy', 'save', 'say', 'school', 'science', 'scream', 'sea', 'search', 'season', 'seat', 'second', 'secret',  
			'security', 'see', 'seek', 'seem', 'seize', 'self', 'sell', 'senate', 'send', 'sense', 'sentence', 'separate', 'series',  
			'serious', 'sermon', 'serve', 'set', 'settle', 'several', 'severe', 'sex', 'shake', 'shape', 'share', 'sharp', 'she', 'shell',  
			'shine', 'ship', 'shock', 'shoe', 'shoot', 'short', 'should', 'shout', 'show', 'shrink', 'shut', 'sick', 'side', 
 			'sign', 'signal', 'silence', 'silver', 'similar', 'simple', 'since', 'sing', 'sink', 'sister', 'sit', 'situation', 'size',  
			'skeleton', 'skill', 'skull', 'sky', 'slave', 'sleep', 'slide', 'slow', 'small', 'smash', 'smell', 'smile', 'smoke',  
			'smooth', 'snow', 'so', 'social', 'soft', 'soldier', 'solid', 'solve', 'some', 'son', 'soon', 'sorry', 'sort', 'sound',  
			'south', 'space', 'speak', 'special', 'speed', 'spend', 'spill', 'spilt', 'spirit', 'split', 'sports', 'spread', 'spring',  
			'spy', 'stab', 'stamp', 'stand', 'star', 'start', 'starve', 'state', 'station', 'statue', 'stay', 'steal', 'steam', 'steel',  
			'step', 'stick', 'still', 'stomach', 'stone', 'stop', 'store', 'storm', 'story', 'stove', 'straight', 'strange', 'street',  
			'stretch', 'strike', 'strong', 'struggle', 'stubborn', 'study', 'stupid', 'submarine', 'substance', 'substitute',  
			'subversion', 'succeed', 'such', 'sudden', 'suffer', 'sugar', 'summer', 'sun', 'supervise', 'supply', 'support', 'suppose',  
			'suppress', 'sure', 'surplus', 'surprise', 'surrender', 'surround', 'survive', 'suspect', 'suspend', 'swallow', 'swear',  
			'sweet', 'swim', 'sympathy', 'system', 'take', 'talk', 'tall', 'tank', 'target', 'task', 'taste', 'tax', 'teach', 'team',  
			'tear', 'tears', 'technical', 'telephone', 'telescope', 'television', 'tell', 'temperature', 'temporary', 'tense', 'term',  
			'terrible', 'territory', 'terror', 'test', 'textiles', 'than', 'thank', 'that', 'the', 'theater', 'then', 'there', 'thick',  
			'thin', 'thing', 'think', 'third', 'this', 'threaten', 'through', 'throw', 'tie', 'time', 'tired', 'tissue', 'to', 'today',  
			'together', 'tomorrow', 'tonight', 'too', 'tool', 'top', 'torture', 'touch', 'toward', 'town', 'trade', 'tradition',  
			'tragic', 'train', 'traitor', 'transport', 'trap', 'travel', 'treason', 'treasure', 'treat', 'treaty', 'tree', 'trial',  
			'tribe', 'trick', 'trip', 'troops', 'trouble', 'truce', 'truck', 'trust', 'try', 'turn', 'under', 'understand', 'unite',  
			'universe', 'university', 'unless', 'until', 'up', 'urge', 'urgent', 'use', 'usual', 'valley', 'value', 'vehicle', 'version',  
			'veto', 'vicious', 'victim', 'victory', 'village', 'violate', 'violence', 'violin', 'virus', 'visit', 'voice', 'volcano',  
			'vote', 'voyage', 'wages', 'wait', 'walk', 'wall', 'want', 'war', 'warm', 'warn', 'wash', 'waste', 'watch', 'water', 'wave',  
			'way', 'weak', 'wealth', 'weapon', 'wear', 'weather', 'weigh', 'welcome', 'well', 'west', 'wet', 'what', 'wheat', 'wheel',  
			'when', 'where', 'which', 'while', 'white', 'who', 'why', 'wide', 'wife', 'wild', 'will', 'willing', 'win', 'wind', 'window', 
 			'wire', 'wise', 'wish', 'with', 'withdraw', 'without', 'woman', 'wonder', 'wood', 'woods', 'word', 'work', 'world', 'worry', 
 			'worse', 'wound', 'wreck', 'write', 'wrong', 'year', 'yellow', 'yes', 'yesterday', 'yet', 'you', 'young'
		); 
    
		return $array; 
	}
} // END OF PasswordUtil

?>
