<?php

function atl_1_0_93c1aa66914f264e355a8cd5d0701ace($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $__out__->writeStructure('<?xml version="1.0"?> 

<html>
<head>
  	<title>');
    // TAG title AT LINE 5
    $_src_tag = "title"; $_src_line = 5;
    $temp_0 = $__ctx__->get("title");
    $__out__->write($temp_0);
    $__out__->writeStructure('</title>
</head>

<body>

<h1>');
    // TAG h1 AT LINE 10
    $_src_tag = "h1"; $_src_line = 10;
    $temp_1 = $__ctx__->get("title");
    $__out__->write($temp_1);
    $__out__->writeStructure('</h1>

<table>
<tr>
  	<td>name</td>
    <td>phone</td>
</tr>

');
    // TAG tr AT LINE 18
    $_src_tag = "tr"; $_src_line = 18;
    // new loop
    $temp_3 = $__ctx__->get("result");
    $temp_2 =& $temp_3;
    $temp_4 = & new ATL_LoopControler($__ctx__, "item", $temp_2);;
    if (Base::isError($temp_4->_error)) {
        $__out__->write($temp_4->_error);
    }
    else {
        while ($temp_4->isValid()) {
            $__out__->writeStructure('<tr> 
    <td>');
            // TAG td AT LINE 19
            $_src_tag = "td"; $_src_line = 19;
            $temp_5 = $__ctx__->get("item/name");
            $__out__->write($temp_5);
            $__out__->writeStructure('</td>
    <td>');
            // TAG td AT LINE 20
            $_src_tag = "td"; $_src_line = 20;
            $temp_6 = $__ctx__->get("item/phone");
            $__out__->write($temp_6);
            $__out__->writeStructure('</td>
</tr>');
            $temp_4->next();
        }
    }
    // end loop
    $__out__->writeStructure('

');
    // TAG tr AT LINE 23
    $_src_tag = "tr"; $_src_line = 23;
    $__out__->writeStructure('

');
    // TAG tr AT LINE 28
    $_src_tag = "tr"; $_src_line = 28;
    $__out__->writeStructure('
</table>

</body> 
</html>
');
    return $__out__->toString();
}
?>