<?php require_once "abstractpage/kernel/php/util/text/gettext/lib/GetText.php";

function atl_1_0_e774f5b8c0d58d70fe527d2bbe23f1d2($__tpl__)
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
	<input type="hidden" name="cmd" value="delete"/>
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
    // TAG td AT LINE 12
    $_src_tag = "td"; $_src_line = 12;
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
    $__out__->writeStructure('</div>
				<ul>
				  ');
    // TAG li AT LINE 23
    $_src_tag = "li"; $_src_line = 23;
    // new loop
    $temp_5 = $__ctx__->get("form/objects");
    $temp_4 =& $temp_5;
    $temp_6 = & new ATL_LoopControler($__ctx__, "object", $temp_4);;
    if (PEAR::isError($temp_6->_error)) {
        $__out__->write($temp_6->_error);
    }
    else {
        while ($temp_6->isValid()) {
            $__out__->writeStructure('<li>
					');
            // TAG span AT LINE 24
            $_src_tag = "span"; $_src_line = 24;
            $temp_7 = $__ctx__->get("object");
            $__out__->write($temp_7);
            $temp_8 = $__ctx__->get("object");
            $__out__->writeStructure('
					<input');
            $value =& $temp_8;
            $__out__->writeStructure(' value="');
            $__out__->write($value);
            $__out__->writeStructure('" type="hidden" name="selection[]" />
					</li>');
            $temp_6->next();
        }
    }
    // end loop
    $temp_9 = " Execute";
    $__out__->writeStructure('
				</ul>
			</td>
		</tr>
		
		<tr>
		  <td>
			  <input');
    $value =& $temp_9;
    $__out__->writeStructure(' value="');
    $__out__->writeStructure($__tpl__->_translate(trim($value)));
    $temp_10 = " Cancel";
    $__out__->writeStructure('" type="submit" name="confirm" />
							 
			  <input');
    $value =& $temp_10;
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