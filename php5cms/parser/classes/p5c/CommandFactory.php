<?php
import('p5c.BaseObject');

import('p5c.command.FileManagerDefault');

class p5c_CommandFactory extends p5c_BaseObject {
	
	
	
	
	public function createCommand($module, $action) {
		
		return new p5c_command_FileManagerDefault();
	} // end public function createCommand($module, $action)
	
	
	
} // end class p5c_CommandFactory extends p5c_BaseObject


class p5c_CommandNotFoundException extends Exception {}
?>
