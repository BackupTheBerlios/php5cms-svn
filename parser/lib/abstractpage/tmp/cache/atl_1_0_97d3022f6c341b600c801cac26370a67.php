<?php require_once "abstractpage/kernel/php/util/text/gettext/lib/GetText.php";

function atl_1_0_97d3022f6c341b600c801cac26370a67($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $temp_0 = $__ctx__->get("form/action");
    $__out__->writeStructure('<form');
    $action =& $temp_0;
    $__out__->writeStructure(' action="');
    $__out__->write($action);
    $__out__->writeStructure('" method="post">
	<input type="hidden" name="phpcmsaction" value="FILEMANAGER"/>
	<input type="hidden" name="working_dir" value="'. $__ctx__->getToString("form/workdir") .'"/>
	<table border="0" cellspacing="3" cellpadding="3" width="600">
	
		<tr>
			<td class="header-large">');
    $__out__->pushBuffer();
    $__out__->writeStructure('FileManager');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('FileManager'));
    $__out__->writeStructure('</td>
		</tr>
		
		<tr>
			<td>');
    // TAG td AT LINE 11
    $_src_tag = "td"; $_src_line = 11;
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
    $__out__->writeStructure('</td>
		</tr>
		
		<tr>
		  <td>
			  <div>');
    $__out__->pushBuffer();
    $__out__->writeStructure('Are you sure you want to delete these files?');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('Are you sure you want to delete these files?'));
    $temp_4 = " Execute";
    $__out__->writeStructure('</div>
			</td>
		</tr>
		
		<tr>
		  <td>
			  <input');
    $value =& $temp_4;
    $__out__->writeStructure(' value="');
    $__out__->writeStructure($__tpl__->_translate(trim($value)));
    $temp_5 = " Cancel";
    $__out__->writeStructure('" type="submit" name="confirm" />
							 
			  <input');
    $value =& $temp_5;
    $__out__->writeStructure(' value="');
    $__out__->writeStructure($__tpl__->_translate(trim($value)));
    $__out__->writeStructure('" type="submit" name="cancel" />				
			</td>
		</tr>
		
		<tr>
			<td class="header-small">');
    $__out__->pushBuffer();
    $__out__->writeStructure('Dummy');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('Dummy'));
    $__out__->writeStructure('</td>
		</tr>
		
	</table>
</form>');
    return $__out__->toString();
}
?>