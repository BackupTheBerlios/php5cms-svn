<?php require_once "abstractpage/kernel/php/util/text/gettext/lib/GetText.php";

function atl_1_0_603d7f6bf82fa2c76e9f4220526e69cf($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $__out__->writeStructure('<table border="0" cellspacing="3" cellpadding="3" width="600">

	<tr>
		<td class="header-large">');
    // TAG td AT LINE 4
    $_src_tag = "td"; $_src_line = 4;
    $temp_0 = $__ctx__->get("form/title");
    $__out__->write($temp_0);
    $__out__->writeStructure('</td>
	</tr>
	
	<tr>
		<td>');
    // TAG td AT LINE 8
    $_src_tag = "td"; $_src_line = 8;
    $temp_1 = $__ctx__->get("form/workdir");
    $__out__->write($temp_1);
    $__out__->writeStructure('</td>
	</tr>
	
	<tr>
		<td>');
    $__out__->pushBuffer();
    $__out__->popBuffer();
    $temp_2 = 'macros.xml/top_toolbar';
    $__old_error = $__ctx__->_errorRaised;
    $__ctx__->_errorRaised = false;
    $temp_3 = new ATL_Macro($__tpl__, $temp_2);
    $temp_3 = $temp_3->execute($__tpl__);
    if (PEAR::isError($temp_3)) {
        $__ctx__->_errorRaised = $temp_3;
    }
    $__out__->writeStructure($temp_3);
    if (!$__ctx__->_errorRaised) {
        $__ctx__->_errorRaised = $__old_error;
    }
    $temp_4 = $__ctx__->get("form/action");
    $__out__->writeStructure('</td>
	</tr>
	
	<tr>
		<td>
			<form');
    $action =& $temp_4;
    $__out__->writeStructure(' action="');
    $__out__->write($action);
    $__out__->writeStructure('" method="post">
				<input type="hidden" name="phpcmsaction" value="FILEMANAGER"/>
				<input type="hidden" name="working_dir" value="'. $__ctx__->getToString("form/workdir") .'"/>
				<table border="0" cellspacing="0" cellpadding="2" style="width:100%;">
					<tr>
						<td class="header-small" style="width:20px;"></td>
						<td class="header-small" style="width:20px;"></td>
						<td class="header-small" style="width:20px;"></td>
						<td class="header-small">');
    $__out__->pushBuffer();
    $__out__->writeStructure('Name');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('Name'));
    $__out__->writeStructure('</td>
						<td class="header-small">');
    $__out__->pushBuffer();
    $__out__->writeStructure('Size');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('Size'));
    $__out__->writeStructure('</td>
						<td class="header-small">');
    $__out__->pushBuffer();
    $__out__->writeStructure('Date');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('Date'));
    $__out__->writeStructure('</td>
						<td class="header-small">');
    $__out__->pushBuffer();
    $__out__->writeStructure('Perms');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('Perms'));
    $temp_5 = " Open Folder ...";
    $__out__->writeStructure('</td>
					</tr>
					
					<tr>
						<td style="background-color:#cccccc;heigth:10px;width:20px;">^</td>
						<td style="background-color:#cccccc;heigth:10px;width:20px;">^</td>
						<td style="background-color:#cccccc;heigth:10px;width:20px;">^</td>
						<td style="background-color:#cccccc;heigth:10px;"></td>
						<td style="background-color:#cccccc;heigth:10px;"></td>
						<td style="background-color:#cccccc;heigth:10px;"></td>
						<td style="background-color:#cccccc;heigth:10px;"></td>
					</tr>
					
					<tr>
						<td style="width:20px;"></td>
						<td style="width:20px;">
							<input');
    $title =& $temp_5;
    $__out__->writeStructure(' title="');
    $__out__->writeStructure($__tpl__->_translate(trim($title)));
    $__out__->writeStructure('" type="image" name="open_dir" src="'. $__ctx__->getToString("page/baseuri") .'/parser/gif/filemanager/filetypes/parent.gif" value=".." />
						</td>
						<td style="width:20px;"></td>
						<td>..</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>

					');
    // TAG tr AT LINE 57
    $_src_tag = "tr"; $_src_line = 57;
    // new loop
    $temp_7 = $__ctx__->get("form/files");
    $temp_6 =& $temp_7;
    $temp_8 = & new ATL_LoopControler($__ctx__, "file", $temp_6);;
    if (PEAR::isError($temp_8->_error)) {
        $__out__->write($temp_8->_error);
    }
    else {
        while ($temp_8->isValid()) {
            $temp_9 = $__ctx__->get("file/name");
            $__out__->writeStructure('<tr>
						<td style="width:20px;">
							<input');
            $value =& $temp_9;
            $__out__->writeStructure(' value="');
            $__out__->write($value);
            $temp_11 = $__ctx__->get("file/folder");
            $__out__->writeStructure('" type="checkbox" name="selection[]" />
						</td>
						<td style="width:20px;">
							');
            $temp_10 =& $temp_11;
            if (!PEAR::isError($temp_10) && $temp_10) {
                $temp_12 = " Open folder ...";
                $__out__->writeStructure('<input');
                $title =& $temp_12;
                $__out__->writeStructure(' title="');
                $__out__->writeStructure($__tpl__->_translate(trim($title)));
                $__out__->writeStructure('" type="image" name="open_dir" src="'. $__ctx__->getToString("page/baseuri") .'/parser/gif/filemanager/filetypes/folder.gif" value="'. $__ctx__->getToString("file/name") .'" />');
            }
            $temp_14 = $__ctx__->get("file/text");
            $__out__->writeStructure('
							');
            $temp_13 =& $temp_14;
            if (!PEAR::isError($temp_13) && $temp_13) {
                $temp_15 = " Display file ...";
                $__out__->writeStructure('<input');
                $title =& $temp_15;
                $__out__->writeStructure(' title="');
                $__out__->writeStructure($__tpl__->_translate(trim($title)));
                $__out__->writeStructure('" type="image" name="cmd[show]" value="" src="'. $__ctx__->getToString("page/baseuri") .'/parser/gif/filemanager/filetypes/'. $__ctx__->getToString("file/type") .'.gif" />');
            }
            $temp_17 = $__ctx__->get("file/image");
            $__out__->writeStructure('
							');
            $temp_16 =& $temp_17;
            if (!PEAR::isError($temp_16) && $temp_16) {
                $temp_18 = " Display file ...";
                $__out__->writeStructure('<input');
                $title =& $temp_18;
                $__out__->writeStructure(' title="');
                $__out__->writeStructure($__tpl__->_translate(trim($title)));
                $__out__->writeStructure('" type="image" name="cmd[show]" value="" src="'. $__ctx__->getToString("page/baseuri") .'/parser/gif/filemanager/filetypes/'. $__ctx__->getToString("file/type") .'.gif" />');
            }
            $temp_20 = $__ctx__->get("file/binary");
            $__out__->writeStructure('
							');
            $temp_19 =& $temp_20;
            if (!PEAR::isError($temp_19) && $temp_19) {
                $temp_21 = " Display file ...";
                $__out__->writeStructure('<input');
                $title =& $temp_21;
                $__out__->writeStructure(' title="');
                $__out__->writeStructure($__tpl__->_translate(trim($title)));
                $__out__->writeStructure('" type="image" name="cmd[show]" value="" src="'. $__ctx__->getToString("page/baseuri") .'/parser/gif/filemanager/filetypes/'. $__ctx__->getToString("file/type") .'.gif" />');
            }
            $temp_23 = $__ctx__->get("file/text");
            $__out__->writeStructure('						
						</td>
						<td style="width:20px;">
							');
            $temp_22 =& $temp_23;
            if (!PEAR::isError($temp_22) && $temp_22) {
                $temp_24 = $__ctx__->get("file/name");
                $__out__->writeStructure('<input');
                $value =& $temp_24;
                $__out__->writeStructure(' value="');
                $__out__->writeStructure($__tpl__->_translate(trim($value)));
                $temp_25 = " Edit ...";
                $__out__->writeStructure('"');
                $title =& $temp_25;
                $__out__->writeStructure(' title="');
                $__out__->writeStructure($__tpl__->_translate(trim($title)));
                $__out__->writeStructure('" type="image" name="edit" src="'. $__ctx__->getToString("page/baseuri") .'/parser/gif/filemanager/edit.gif" />');
            }
            $__out__->writeStructure('
						</td>
						<td>');
            // TAG td AT LINE 96
            $_src_tag = "td"; $_src_line = 96;
            $temp_26 = $__ctx__->get("file/name");
            $__out__->write($temp_26);
            $__out__->writeStructure('</td>
						<td>
							');
            $temp_28 =& $__ctx__->get("file/folder");
            $temp_29 =  $temp_28 != -1;
            $temp_27 =& $temp_29;
            if (!PEAR::isError($temp_27) && $temp_27) {
                // TAG span AT LINE 100
                $_src_tag = "span"; $_src_line = 100;
                $temp_30 =& $__ctx__->get("file/size");
                $temp_31 =  FileUtil::filesizeAsString($temp_30) ;
                $__out__->write($temp_31);
            }
            $__out__->writeStructure('
						</td>
						<td>');
            // TAG td AT LINE 102
            $_src_tag = "td"; $_src_line = 102;
            $temp_32 =& $__ctx__->get("file/date");
            $temp_33 =  date('Y-m-d' , $temp_32) ;
            $__out__->write($temp_33);
            $__out__->writeStructure('</td>
						<td>');
            // TAG td AT LINE 103
            $_src_tag = "td"; $_src_line = 103;
            $temp_34 = $__ctx__->get("file/perms");
            $__out__->write($temp_34);
            $__out__->writeStructure('</td>					
					</tr>');
            $temp_8->next();
        }
    }
    // end loop
    $temp_35 = " Move or copy selected files and folders";
    $__out__->writeStructure('
					<tr>
						<td style="width:20px;">
							<img src="'. $__ctx__->getToString("page/baseuri") .'/parser/gif/filemanager/arrow.gif" width="16" height="24" border="0" alt=""/>
						</td>
						<td style="width:20px;">
							<input');
    $title =& $temp_35;
    $__out__->writeStructure(' title="');
    $__out__->writeStructure($__tpl__->_translate(trim($title)));
    $temp_36 = " Delete selected files and folders";
    $__out__->writeStructure('" type="image" src="'. $__ctx__->getToString("page/baseuri") .'/parser/gif/filemanager/move.gif" name="cmd[move]" value="move" />
						</td>
						<td style="width:20px;">
							<input');
    $title =& $temp_36;
    $__out__->writeStructure(' title="');
    $__out__->writeStructure($__tpl__->_translate(trim($title)));
    $__out__->writeStructure('" type="image" src="'. $__ctx__->getToString("page/baseuri") .'/parser/gif/filemanager/delete.gif" name="cmd[delete]" value="delete" />
						</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>					
					</tr>
				</table>
			</form>
		</td>
	</tr>
	
	<tr>
		<td class="header-small">Actions</td>
	</tr>
	
	<tr>
		<td>
		<pre>Upload: <input type="file"/> 
Folder: <input type="text"/>
File:   <input type="text"/></pre>   
		</td>
	</tr>
	
	
	<tr>
		<td class="header-small" style="text-align:right;">');
    // TAG td AT LINE 148
    $_src_tag = "td"; $_src_line = 148;
    $temp_37 = $__ctx__->get("form/status");
    $__out__->write($temp_37);
    $__out__->writeStructure('</td>
	</tr>

</table>');
    return $__out__->toString();
}
?>