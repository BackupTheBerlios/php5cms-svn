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
			
			if ($file['type'] == 'dir') {
				$file['icon']   = 'folder.png';
				$file['folder'] = true;
			} else {
				$info = pathinfo($file['name']);
				
				$ext = '';
				if (isset($info['extension'])) {
					$ext = $info['extension'];
				}
				$file['icon'] = $ext . '.png';
				
				if (in_array($ext, $this->config->getProperty('filemanager.texttypes'))) {
					$file['editable'] = true;	
				} else if (in_array($ext, $this->config->getProperty('filemanager.mediatypes'))) {
					$file['displayable'] = true;
				} else {
					$file['downloadable'] = true;	
				}
			}
			$folder[] = $file;
		}
		
		
		$this->set('form', array(
			'sort'    => $this->fileList->getSort(),
			'workdir' => $this->fileList->getWorkingDirectory()
		));
		
		
		$this->set('folder', $folder);

	} // end protected function doSetup()
	
	
}
?>
