<?php
import('p5c.BaseObject');

using('io.FolderArray');

class p5c_model_FileList extends p5c_BaseObject {
	
	
	private $entries = array();
	
	private $meta = array(
		'dirsize'   => -1,
		'count'     => 0,
		'dircount'  => 0,
		'filecount' => 0
	);
	
	
	
	private $sKey = '';
	private $sOrder = -1;
	
	
	public function __construct($dir, $sKey = null, $sOrder = -1) {
		parent::__construct();
		
		
		
		$this->sKey   = (string)$sKey;
		$this->sOrder = (integer)$sOrder;
		
		
		$folder = new FolderArray($dir . '/parser/classes/p5c');
		$folder->setRecursive(false);
		$folder->setIgnore(array('.', '..', '.svn'));
		$folder->parseDir();
		
		$this->entries = $folder->getFolderArray();
		
		foreach ($this->meta as $key => $v) {
			if (isset($this->entries[$key])) {
				$this->meta[$key] = $this->entries[$key];
				unset($this->entries[$key]);
			}
		}
		
		
		#print "<pre>";print_r($this->getIterator());print "</pre>";
	}
	
	
	
	public function getIterator() {
		return new p5c_model_FileListIterator($this->entries, $this->sKey, $this->sOrder);
	} // end public function getIterator() 
	
	
} // end class p5c_model_FileList extends p5c_BaseObject



class p5c_model_FileListIterator extends p5c_BaseObject implements Iterator {
	
	
	private $files = array();
	
	
	private $sKey = '';
	private $sOrder = -1;
	
	
	public function __construct($files, $key = null, $order = -1) {
		
		
		$this->files = $files;
		
		
		if (!empty($files) && in_array($key, array_keys(reset($files)))) {
			$this->sKey = $key;
		}
		$this->sOrder = $order;
		
		uasort($this->files, array(&$this, 'sort'));
		
		$this->rewind();
	}
	
	
	public function valid() {
		return (current($this->files) !== false);
	}
	
	
	public function next() {
		return next($this->files);
	}

	
	public function rewind() {
		reset($this->files);
	}
	
	
	public function key() {
		return key($this->files);
	}
	
	
	public function current() {
		return current($this->files);
	}
	
	
	private function sort($file1, $file2) {
		
		if ($file1['type'] == 'dir' && $file2['type'] != 'dir') {
			return -1;
		} else if ($file1['type'] != 'dir' && $file2['type'] == 'dir') {
			return 1;
		} else if ($this->sKey != null) {
			
			if (is_numeric($file1[$this->sKey])) {
				$return = ($file1[$this->sKey] > $file2[$this->sKey] ? -1 : 1); 
			} else {
				$return = strcasecmp($file1[$this->sKey], $file2[$this->sKey]);
			}
		} else {
			$return = 0;
		}
		
		
		return ($return * ($this->sOrder == -1 ? 1 : -1));
	}

}
?>
