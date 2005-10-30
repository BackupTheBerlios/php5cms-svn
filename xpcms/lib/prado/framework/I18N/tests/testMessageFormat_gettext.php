<?php

require_once('../core/MessageFormat.php');

class testMessageFormat_gettext extends UnitTestCase
{
	var $type = 'gettext';
	var $source = './messages';
		
	function testMessageFormat_gettext()
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
	
	function testSaveUpdateDelete()
	{
		$MObackup = './messages/messages.en_AU.mo.bak';
		$MOfile = './messages/messages.en_AU.mo';
		$PObackup = './messages/messages.en_AU.po.bak';
		$POfile = './messages/messages.en_AU.po';		
		
		//restore using the back file
		copy($MObackup,$MOfile);
		copy($PObackup,$POfile);
		
		//test that the back file doesn't contain the 'Testing123' string.
		$this->assertNoUnwantedPattern('/Testing123/',file_get_contents($MOfile));		
		$this->assertNoUnwantedPattern('/Testing123/',file_get_contents($POfile));	
		
		$source = MessageSource::factory($this->type, $this->source);
		$source->setCulture('en_AU');
		$source->setCache(new MessageCache('./tmp'));
		
		$formatter = new MessageFormat($source);
		
		//add a untranslated string
		$this->assertEqual($formatter->format('Testing123'), 'Testing123');

		//save it
		$this->assertTrue($formatter->getSource()->save());
		
		//check the contents
		//$this->assertWantedPattern('/Testing123/',file_get_contents($MOfile));
		$this->assertWantedPattern('/Testing123/',file_get_contents($POfile));		
		
		//testing for update.		
		$this->assertTrue($formatter->getSource()->update(
						'Testing123', '123Test', 'update comments'));
						
		$this->assertWantedPattern('/123Test/',file_get_contents($MOfile));			
		
		
		//var_dump(htmlspecialchars($contents));
				
		//now doing some delete		
		$this->assertFalse($formatter->getSource()->delete('Test123'));
		$this->assertTrue($formatter->getSource()->delete('Testing123'));
		
		$this->assertNoUnwantedPattern('/Testing123/',file_get_contents($MOfile));	
		$this->assertNoUnwantedPattern('/Testing123/',file_get_contents($POfile));	
		
		//restore using the backup file.
		copy($MObackup,$MOfile);
		copy($PObackup,$POfile);
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
	
	function testDirectoryTypeSaveUpdateDelete()
	{
		$MObackup = './messages/en_AU/tests.mo.bak';
		$MOfile = './messages/en_AU/tests.mo';
		$PObackup = './messages/en_AU/tests.po.bak';
		$POfile = './messages/en_AU/tests.po';		
		
		//restore using the back file
		copy($MObackup,$MOfile);
		copy($PObackup,$POfile);
		
		//test that the back file doesn't contain the 'Testing123' string.
		$this->assertNoUnwantedPattern('/Testing123/',file_get_contents($MOfile));		
		$this->assertNoUnwantedPattern('/Testing123/',file_get_contents($POfile));		
		
		$source = MessageSource::factory($this->type, $this->source);
		$source->setCulture('en_AU');
		$source->setCache(new MessageCache('./tmp'));
		
		$formatter = new MessageFormat($source);

		//add a untranslated string, note, doesn't matter which catalogue
		$this->assertEqual($formatter->format('Testing123'), 'Testing123');
		
		//save it to the 'tests' catalgoue
		$this->assertTrue($formatter->getSource()->save('tests'));
		
		//check the contents
		//$this->assertWantedPattern('/Testing123/',file_get_contents($MOfile));
		$this->assertWantedPattern('/Testing123/',file_get_contents($POfile));		
		
		//testing for update. Update it to the 'tests' catalogue	
		$this->assertTrue($formatter->getSource()->update(
						'Testing123', '123Test', 'update comments','tests'));
						
		$this->assertWantedPattern('/123Test/',file_get_contents($MOfile));					
		
		//now doing some delete	from the 'tests' catalogue
		$this->assertFalse($formatter->getSource()->delete('Test123','tests'));
		$this->assertTrue($formatter->getSource()->delete('Testing123','tests'));
		
		$this->assertNoUnwantedPattern('/Testing123/',file_get_contents($MOfile));	
		$this->assertNoUnwantedPattern('/Testing123/',file_get_contents($POfile));
		
		//restore using the backup file.
		copy($MObackup,$MOfile);
		copy($PObackup,$POfile);	
	}
}

?>