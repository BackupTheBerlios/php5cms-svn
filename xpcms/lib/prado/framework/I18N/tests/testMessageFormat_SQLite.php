<?php

require_once('../core/MessageFormat.php');

class testMessageFormat_SQLite extends UnitTestCase
{
	
	var $type = 'SQLite';
	var $source = 'sqlite:///./messages/messages.db';
		
	function testMessageFormat_SQLite()
	{
		$this->UnitTestCase();
	}
	
	function test1()
	{
		$source = MessageSource::factory($this->type, $this->source);
		$source->setCulture('en_AU');
		$source->setCache(new MessageCache('./tmp'));

		$formatter = new MessageFormat($source);
		$this->assertEqual($formatter->format('Hello'),'G\'day Mate!');
	
		$this->assertEqual($formatter->format('Goodbye'), 'Goodbye');
		
		$formatter->setUntranslatedPS(array('[T]','[/T]'));	
		$this->assertEqual($formatter->format('Hi'), '[T]Hi[/T]');
		
		//save the untranslated
		
	}
	
	function getAllContents($file)
	{
		$db = sqlite_open($file);
		$rs = sqlite_query('SELECT * FROM trans_unit',$db);
		$result = '';
		while($row = sqlite_fetch_array($rs,SQLITE_NUM))
		{
			$result .= implode(', ',$row)."\n";
		}
		sqlite_close($db);
		return $result;
	}
	
	function testSaveUpdateDelete()
	{
		$backup = './messages/messages.db.bak';
		$dbfile = './messages/messages.db';
		
		//restore using the back file
		copy($backup,$dbfile);
		
		//test that the back file doesn't contain the 'Testing123' string.
		$contents = $this->getAllContents($dbfile);
		$this->assertNoUnwantedPattern('/Testing123/',$contents);
		
		$source = MessageSource::factory($this->type, $this->source);
		$source->setCulture('en_AU');
		$source->setCache(new MessageCache('./tmp'));
		
		$formatter = new MessageFormat($source);
		
		$formatter->setUntranslatedPS(array('[t]','[/t]'));
		
		//add a untranslated string
		$this->assertEqual($formatter->format('Testing123'), '[t]Testing123[/t]');

		//save it
		$this->assertTrue($formatter->getSource()->save());
		
		//check the contents
		$contents = $this->getAllContents($dbfile);
		$this->assertWantedPattern('/Testing123/',$contents);
		
		//testing for update.		
		$this->assertTrue($formatter->getSource()->update(
						'Testing123', '123Test', 'update comments'));
						
		$contents = $this->getAllContents($dbfile);		
		$this->assertWantedPattern('/123Test/',$contents);			
		$this->assertWantedPattern('/update comments/',$contents);					
		
		//var_dump(htmlspecialchars($contents));
				
		//now doing some delete		
		//doesn't detect missing source
		$this->assertTrue($formatter->getSource()->delete('Test123'));
		$this->assertTrue($formatter->getSource()->delete('Testing123'));
		
		$contents = $this->getAllContents($dbfile);		
		$this->assertNoUnwantedPattern('/Testing123/',$contents);	
		
		//restore using the backup file.
		copy($backup,$dbfile);
	}
	
	function testCatalogueList()
	{
		$source = MessageSource::factory($this->type, $this->source);
		$result[] = array('messages',NULL);
		$result[] = array('messages', 'en');
		$result[] = array('messages','en_AU');
		$result[] = array('tests',NULL);
		$result[] = array('tests','en');
		$result[] = array('tests','en_AU');

		$this->assertEqual($result, $source->catalogues());
	}
	
	function testAltCatalogue()
	{
		$source = MessageSource::factory($this->type, $this->source);
		$source->setCulture('en_AU');
		$source->setCache(new MessageCache('./tmp'));	
		
		$formatter = new MessageFormat($source);
		$formatter->Catalogue = 'tests';
		
		//from a different catalogue
		$this->assertEqual($formatter->format('Hello'), 'Howdy!');	
		$this->assertEqual($formatter->format('Welcome'), 'Ho Ho!');	
		$this->assertEqual($formatter->format('Goodbye'), 'Sayonara');	
		
		//switch to 'messages' catalogue
		$this->assertEqual($formatter->format('Hello',null,'messages'),'G\'day Mate!');
	}
}

?>