<?php
import('p5c.View');

using('io.FileUtil');

class p5c_view_FileManager extends p5c_View {
	
	
	private $fileList = null;
	
	
	public function __construct(p5c_model_FileList $fileList) {
		parent::__construct('filemanager.htm');
		
		$this->fileList = $fileList;
	} // end public function __construct(p5c_model_FileList $fileList)


	protected function doSetup() {
		
		$folder = array();
		
		$it = $this->fileList->getIterator();
		for ($it->rewind(); $it->valid(); $it->next()) {
			
			$file = $it->current();
			$file['time'] = date('d.m.Y - H:i', $file['mtime']);
			$file['size'] = FileUtil::filesizeAsString($file['size']);
			
			$folder[] = $file;
		}
		
		
		$this->set('folder', $folder);

	} // end protected function doSetup()
	
	
}
?>
