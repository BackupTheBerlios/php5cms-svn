<?php

function atl_1_0_e93514e206f37b5fb3ce73a362753872($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $temp_1 = "/style.css";
    $__out__->writeStructure('<html>
  <head>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1"/>
    <meta http-equiv="content-style-type" content="text/css"/>
		<style type="text/css" media="screen">');
    $temp_0 =& $temp_1;
    $temp_0 = $__tpl__->realpath($temp_0);
    if (PEAR::isError($temp_0)) {
        $__ctx__->_errorRaised = true;
        $__out__->write($temp_0);
    }
    else {
        $__out__->writeStructure(join("", file($temp_0)));
    }
    $__out__->writeStructure('</style>
	</head>
	<body>
	  <h1>Manuel</h1>
		
		<form action="" method="post">
		  <input type="hidden" name="sort" value="'. $__ctx__->getToString("form/sort") .'"/>
			<input type="hidden" name="workdir" value="'. $__ctx__->getToString("form/workdir") .'"/>
			
		<div>');
    // TAG div AT LINE 15
    $_src_tag = "div"; $_src_line = 15;
    $temp_2 = $__ctx__->get("form/workdir");
    $__out__->write($temp_2);
    $__out__->writeStructure('</div>
		
		<table border="0">
		  <tr style="background-color: green;color: white;">
			  <td></td>
				<td></td>
				<td></td>
			  <td>Name</td>
			  <td>Größe</td>
			  <td>Zeit</td>	
			  <td>Rechte</td>
			</tr>
			
			
		  <tr class="fm-sort-row">
			  <td class="fm-sort-button"></td>
				<td class="fm-sort-button"></td>
				<td class="fm-sort-button"></td>
			  <td class="fm-sort-button">
				  <input type="image" name="sort[name-asc]" class="fm-sort-button" border="0" width="10" height="7" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/up.png"/>
				  <input type="image" name="sort[name-desc]" class="fm-sort-button" border="0" width="10" height="7" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/down.png"/>
				</td>
			  <td class="fm-sort-button">
				  <input type="image" name="sort[size-asc]" class="fm-sort-button" border="0" width="10" height="7" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/up.png"/>
				  <input type="image" name="sort[size-desc]" class="fm-sort-button" border="0" width="10" height="7" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/down.png"/>
				</td>
			  <td class="fm-sort-button">
				  <input type="image" name="sort[mtime-asc]" class="fm-sort-button" border="0" width="10" height="7" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/up.png"/>
				  <input type="image" name="sort[mtime-desc]" class="fm-sort-button" border="0" width="10" height="7" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/down.png"/>
				</td>	
			  <td class="fm-sort-button" height="">
				  <input type="image" name="sort[perms-asc]" class="fm-sort-button" border="0" width="10" height="7" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/up.png"/>
				  <input type="image" name="sort[perms-desc]" class="fm-sort-button" border="0" width="10" height="7" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/down.png"/>
				</td>
			</tr>
			
			
		  <tr>
			  <td> </td>
				<td>
				  <input type="image" name="chdir[..]" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/parent.png"/>
				</td>
				<td> </td>
			  <td> </td>
			  <td> </td>
			  <td> </td>	
			  <td> </td>
			</tr>
			
			
		  ');
    // TAG tr AT LINE 99
    $_src_tag = "tr"; $_src_line = 99;
    // new loop
    $temp_4 = $__ctx__->get("folder");
    $temp_3 =& $temp_4;
    $temp_5 = & new ATL_LoopControler($__ctx__, "file", $temp_3);;
    if (PEAR::isError($temp_5->_error)) {
        $__out__->write($temp_5->_error);
    }
    else {
        while ($temp_5->isValid()) {
            $temp_6 = $__ctx__->get("file/name");
            $__out__->writeStructure('<tr>
			  <td>
				  <input');
            $value =& $temp_6;
            $__out__->writeStructure(' value="');
            $__out__->write($value);
            $temp_8 = $__ctx__->get("file/folder");
            $__out__->writeStructure('" type="checkbox" name="selection[]" />
				</td>
				<td>
				  ');
            $temp_7 =& $temp_8;
            if (!PEAR::isError($temp_7) && $temp_7) {
                $__out__->writeStructure('<input type="image" name="chdir['. $__ctx__->getToString("file/name") .']" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/filetypes/'. $__ctx__->getToString("file/icon") .'"/>');
            }
            $temp_10 = $__ctx__->get("file/editable");
            $__out__->writeStructure('
				  ');
            $temp_9 =& $temp_10;
            if (!PEAR::isError($temp_9) && $temp_9) {
                $__out__->writeStructure('<input type="image" name="action[show]" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/filetypes/'. $__ctx__->getToString("file/icon") .'"/>');
            }
            $temp_12 = $__ctx__->get("file/displayable");
            $__out__->writeStructure('
				  ');
            $temp_11 =& $temp_12;
            if (!PEAR::isError($temp_11) && $temp_11) {
                $__out__->writeStructure('<input type="image" name="action[show]" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/filetypes/'. $__ctx__->getToString("file/icon") .'"/>');
            }
            $temp_14 = $__ctx__->get("file/downloadable");
            $__out__->writeStructure('
          ');
            $temp_13 =& $temp_14;
            if (!PEAR::isError($temp_13) && $temp_13) {
                $__out__->writeStructure('<a href="">
					  <img src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/filetypes/'. $__ctx__->getToString("file/icon") .'" border="0" width="16" height="16" alt=""/>
					</a>');
            }
            $temp_16 = $__ctx__->get("file/editable");
            $__out__->writeStructure('
				</td>
				<td>
				  ');
            $temp_15 =& $temp_16;
            if (!PEAR::isError($temp_15) && $temp_15) {
                $__out__->writeStructure('<input type="image" name="action[edit]" src="'. $__ctx__->getToString("base/directory") .'/img/filemanager/edit.png"/>');
            }
            $__out__->writeStructure('
				</td>
			  <td>');
            // TAG td AT LINE 129
            $_src_tag = "td"; $_src_line = 129;
            $temp_17 = $__ctx__->get("file/name");
            $__out__->write($temp_17);
            $__out__->writeStructure('</td>
				<td>');
            // TAG td AT LINE 130
            $_src_tag = "td"; $_src_line = 130;
            $temp_18 = $__ctx__->get("file/size");
            $__out__->write($temp_18);
            $__out__->writeStructure('</td>
				<td>');
            // TAG td AT LINE 131
            $_src_tag = "td"; $_src_line = 131;
            $temp_19 = $__ctx__->get("file/time");
            $__out__->write($temp_19);
            $__out__->writeStructure('</td>
				<td>');
            // TAG td AT LINE 132
            $_src_tag = "td"; $_src_line = 132;
            $temp_20 = $__ctx__->get("file/perms");
            $__out__->write($temp_20);
            $__out__->writeStructure('</td>
			</tr>');
            $temp_5->next();
        }
    }
    // end loop
    $__out__->writeStructure('
		</table>
		
		</form>
		
	</body>
</html>');
    return $__out__->toString();
}
?>