<?php require_once "abstractpage/kernel/php/util/text/gettext/lib/GetText.php";

function atl_1_0_b7bc81d9427cf04209aa8ff39dbf6371($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $temp_1 = "theme.css";
    $__out__->writeStructure('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
	<head>
		<meta name="generator" content="HTML Tidy, see www.w3.org"/>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1"/>
		<meta http-equiv="content-style-type" content="text/css"/>
		<meta http-equiv="content-script-type" content="text/javascript"/>
		<title>Parser - Einstellungen und Optionen :: phpCMS @ 192.168.0.101</title>
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
		  
		<script type="text/javascript" language="javascript">
		<--
			function reload_nav() {
			}
			
			function select(field, option, shadow) {
					if(document.getElementsByName) {
							if(document.getElementsByName(field)[option].checked == true) {
									document.getElementsByName(field)[shadow].checked = true;
							} else {
									document.getElementsByName(field)[option].checked = true;
							}
							check_all();
					}
			}
			
			function check_all() {
					if(document.getElementsByName) {
							if(document.getElementsByName("config_field[mail2crypt]")[0].checked == true) {
									document.getElementById("mail2crypt_js").className = "show";
							} else if(document.getElementsByName("config_field[mail2crypt]")[1].checked == true) {
									document.getElementById("mail2crypt_js").className = "hide";
							}
							if(document.getElementsByName("config_field[mail2crypt]")[0].checked == true) {
									document.getElementById("mail2crypt_img").className = "show";
							} else if(document.getElementsByName("config_field[mail2crypt]")[1].checked == true) {
									document.getElementById("mail2crypt_img").className = "hide";
							}
			
							if(document.getElementsByName("config_field[cache_state]")[0].checked == true) {
									document.getElementById("cache_dir").className = "show";
							} else if(document.getElementsByName("config_field[cache_state]")[1].checked == true) {
									document.getElementById("cache_dir").className = "hide";
							}
			
							if(document.getElementsByName("config_field[cache_client]")[0].checked == true) {
									document.getElementById("proxy_cache_time").className = "show";
							} else if(document.getElementsByName("config_field[cache_client]")[1].checked == true) {
									document.getElementById("proxy_cache_time").className = "hide";
							}
			
							if(document.getElementsByName("config_field[stealth]")[0].checked == true) {
									document.getElementById("stealth_secure").className = "show";
							} else if(document.getElementsByName("config_field[stealth]")[1].checked == true) {
									document.getElementById("stealth_secure").className = "hide";
							}
							if(document.getElementsByName("config_field[stealth]")[0].checked == true) {
									document.getElementById("nolinkchange").className = "hide";
							} else if(document.getElementsByName("config_field[stealth]")[1].checked == true) {
									document.getElementById("nolinkchange").className = "show";
							}
			
							if(document.getElementsByName("config_field[debug]")[0].checked == true) {
									document.getElementById("error_page").className = "hide";
							} else if(document.getElementsByName("config_field[debug]")[1].checked == true) {
									document.getElementById("error_page").className = "show";
							}
							if(document.getElementsByName("config_field[debug]")[0].checked == true) {
									document.getElementById("error_page_404").className = "hide";
							} else if(document.getElementsByName("config_field[debug]")[1].checked == true) {
									document.getElementById("error_page_404").className = "show";
							}
			
							if(document.getElementsByName("config_field[p3p_header]")[0].checked == true) {
									document.getElementById("p3p_policy").className = "show";
							} else if(document.getElementsByName("config_field[p3p_header]")[1].checked == true) {
									document.getElementById("p3p_policy").className = "hide";
							}
							if(document.getElementsByName("config_field[p3p_header]")[0].checked == true) {
									document.getElementById("p3p_href").className = "show";
							} else if(document.getElementsByName("config_field[p3p_header]")[1].checked == true) {
									document.getElementById("p3p_href").className = "hide";
							}
			
							if(document.getElementsByName("config_field[stats]")[0].checked == true) {
									document.getElementById("stats_dir").className = "show";
							} else if(document.getElementsByName("config_field[stats]")[1].checked == true) {
									document.getElementById("stats_dir").className = "hide";
							}
							if(document.getElementsByName("config_field[stats]")[0].checked == true) {
									document.getElementById("stats_current").className = "show";
							} else if(document.getElementsByName("config_field[stats]")[1].checked == true) {
									document.getElementById("stats_current").className = "hide";
							}
							if(document.getElementsByName("config_field[stats]")[0].checked == true) {
									document.getElementById("stats_file").className = "show";
							} else if(document.getElementsByName("config_field[stats]")[1].checked == true) {
									document.getElementById("stats_file").className = "hide";
							}
							if(document.getElementsByName("config_field[stats]")[0].checked == true) {
									document.getElementById("stats_backup").className = "show";
							} else if(document.getElementsByName("config_field[stats]")[1].checked == true) {
									document.getElementById("stats_backup").className = "hide";
							}
			
							if(document.getElementsByName("config_field[referrer]")[0].checked == true) {
									document.getElementById("referrer_dir").className = "show";
							} else if(document.getElementsByName("config_field[referrer]")[1].checked == true) {
									document.getElementById("referrer_dir").className = "hide";
							}
							if(document.getElementsByName("config_field[referrer]")[0].checked == true) {
									document.getElementById("referrer_file").className = "show";
							} else if(document.getElementsByName("config_field[referrer]")[1].checked == true) {
									document.getElementById("referrer_file").className = "hide";
							}
							if(document.getElementsByName("config_field[referrer]")[0].checked == true) {
									document.getElementById("ref_reload_lock").className = "show";
							} else if(document.getElementsByName("config_field[referrer]")[1].checked == true) {
									document.getElementById("ref_reload_lock").className = "hide";
							}
			
					}
			}
		//-->
		</script>
	</head>
	<body onload="check_all(); reload_nav()">
		<div id="page">
			<table border="0" cellspacing="3" cellpadding="3" width="600">
				<tr>
					<td class="header-large">');
    $__out__->pushBuffer();
    $__out__->writeStructure('
							Parser - Configuration and Options
					');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('Parser - Configuration and Options'));
    $temp_2 = $__ctx__->get("form/action");
    $__out__->writeStructure('</td>
				</tr>
				
				<tr>
					<td>
						<form');
    $action =& $temp_2;
    $__out__->writeStructure(' action="');
    $__out__->write($action);
    $temp_3 = $__ctx__->get("form/cache");
    $__out__->writeStructure('" name="form" method="post">
						
							<input type="hidden" name="action" value="CONF"/>
							<input type="hidden" name="phpcmsaction" value="OPTIONS"/>
							<input type="hidden" name="conf_action" value="WRITE"/>
							<input');
    $value =& $temp_3;
    $__out__->writeStructure(' value="');
    $__out__->write($value);
    $temp_4 = $__ctx__->get("form/language");
    $__out__->writeStructure('" type="hidden" name="cache_dir_set" />
							<input');
    $value =& $temp_4;
    $__out__->writeStructure(' value="');
    $__out__->write($value);
    $__out__->writeStructure('" type="hidden" name="language_set" />
							
							<table border="0" cellspacing="2" cellpadding="2" width="100%">
								<tr>
									<td bgcolor="#FFFFFF" width="60%"></td>
									<td bgcolor="#FFFFFF" width="40%"></td>
								</tr>
								
								<tr>
									<td class="header-small" bgcolor="#006600" colspan="2">');
    $__out__->pushBuffer();
    $__out__->writeStructure('
									Save phpCMS Settings
									');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('Save phpCMS Settings'));
    $temp_5 = "Save Settings";
    $__out__->writeStructure('</td>
								</tr>
								
								<tr bgcolor="#EEEEEE">
									<td valign="bottom">
									
									</td>
									
									<td valign="bottom">
										<input');
    $value =& $temp_5;
    $__out__->writeStructure(' value="');
    $__out__->writeStructure($__tpl__->_translate(trim($value)));
    $temp_7 = $__ctx__->get("status");
    $__out__->writeStructure('" type="submit" name="SUBMIT" />
									</td>
								</tr>
								
								');
    $temp_6 =& $temp_7;
    if (!PEAR::isError($temp_6) && $temp_6) {
        $__out__->writeStructure('<tr bgcolor="#006600">
									<td valign="bottom" colspan="2">
										<span style="font-family:Verdana,Helvetica,Arial,sans-serif;font-size:13px;color:#ffffff;">');
        // TAG span AT LINE 176
        $_src_tag = "span"; $_src_line = 176;
        $temp_8 = $__ctx__->get("status");
        $__out__->write($temp_8);
        $__out__->writeStructure('</span>
									</td>
								</tr>');
    }
    $__out__->writeStructure('
			
								<tr>
									<td colspan="2">
										');
    // TAG table AT LINE 182
    $_src_tag = "table"; $_src_line = 182;
    // new loop
    $temp_10 = $__ctx__->get("tabpanels");
    $temp_9 =& $temp_10;
    $temp_11 = & new ATL_LoopControler($__ctx__, "tabpanel", $temp_9);;
    if (PEAR::isError($temp_11->_error)) {
        $__out__->write($temp_11->_error);
    }
    else {
        while ($temp_11->isValid()) {
            $__out__->writeStructure('<table border="0" cellspacing="2" cellpadding="2" width="100%">
											<tr>
												<td bgcolor="#FFFFFF" width="60%"></td>
												<td bgcolor="#FFFFFF" width="40%"></td>
											</tr>
								
											<tr>
												<td class="header-small" colspan="2">');
            // TAG td AT LINE 189
            $_src_tag = "td"; $_src_line = 189;
            $temp_12 = $__ctx__->get("tabpanel/label");
            $__out__->write($temp_12);
            $__out__->writeStructure('</td>
											</tr>
										
								 
											');
            // TAG tr AT LINE 193
            $_src_tag = "tr"; $_src_line = 193;
            // new loop
            $temp_14 = $__ctx__->get("tabpanel/fields");
            $temp_13 =& $temp_14;
            $temp_15 = & new ATL_LoopControler($__ctx__, "field", $temp_13);;
            if (PEAR::isError($temp_15->_error)) {
                $__out__->write($temp_15->_error);
            }
            else {
                while ($temp_15->isValid()) {
                    $temp_16 = $__ctx__->get("field/id");
                    $__out__->writeStructure('<tr');
                    $id =& $temp_16;
                    $__out__->writeStructure(' id="');
                    $__out__->write($id);
                    $temp_17 = $__ctx__->get("field/label");
                    $__out__->writeStructure('" bgcolor="#EEEEEE">
												<td>
													');
                    $__ctx__->setRef("label", $temp_17);
                    $temp_18 = "view usage notes";
                    $__out__->writeStructure('
													<a');
                    $title =& $temp_18;
                    $__out__->writeStructure(' title="');
                    $__out__->writeStructure($__tpl__->_translate(trim($title)));
                    $__out__->writeStructure('" href="'. $__ctx__->getToString("base/url") .'#'. $__ctx__->getToString("field/id") .'" style="font-family:Verdana,Helvetica,Arial,sans-serif;font-size:12px">');
                    // TAG a AT LINE 198
                    $_src_tag = "a"; $_src_line = 198;
                    $temp_19 = $__ctx__->get("field/label");
                    $__out__->write($temp_19);
                    $temp_21 = $__ctx__->get("field/docroot");
                    $__out__->writeStructure('</a>:
														 ');
                    $temp_20 =& $temp_21;
                    if (!PEAR::isError($temp_20) && $temp_20) {
                        $__out__->writeStructure('<div style="font-family:Verdana,Helvetica,Arial,sans-serif;font-size:10px">
															 (');
                        // TAG span AT LINE 200
                        $_src_tag = "span"; $_src_line = 200;
                        $temp_22 = $__ctx__->get("labels/document-root");
                        $__out__->write($temp_22);
                        $temp_23 = $__ctx__->get("base/DocRoot");
                        $__out__->writeStructure('
															 <span');
                        $title =& $temp_23;
                        $__out__->writeStructure(' title="');
                        $__out__->write($title);
                        $__out__->writeStructure('">
																 DOCUMENT_ROOT <img src="'. $__ctx__->getToString("base/scriptPath") .'/gif/info.gif" align="top"/>
															 </span>)
														</div>');
                    }
                    $temp_25 = $__ctx__->get("field/message");
                    $__out__->writeStructure('
														');
                    $temp_24 =& $temp_25;
                    if (!PEAR::isError($temp_24) && $temp_24) {
                        $__out__->writeStructure('<div style="font-family:Verdana,Helvetica,Arial,sans-serif;font-size:10px">');
                        // TAG div AT LINE 207
                        $_src_tag = "div"; $_src_line = 207;
                        $temp_26 = $__ctx__->get("field/message");
                        $__out__->write($temp_26);
                        $__out__->writeStructure('</div>');
                    }
                    $temp_28 = $__ctx__->get("field/select");
                    $__out__->writeStructure('
												</td>
												');
                    $temp_27 =& $temp_28;
                    if (!PEAR::isError($temp_27) && $temp_27) {
                        $__out__->writeStructure('<td valign="top">
													<select id="field_'. $__ctx__->getToString("field/id") .'" name="config_field['. $__ctx__->getToString("field/id") .']" size="1" style="width:128px;">
													  ');
                        // TAG span AT LINE 211
                        $_src_tag = "span"; $_src_line = 211;
                        // new loop
                        $temp_30 = $__ctx__->get("field/options");
                        $temp_29 =& $temp_30;
                        $temp_31 = & new ATL_LoopControler($__ctx__, "option", $temp_29);;
                        if (PEAR::isError($temp_31->_error)) {
                            $__out__->write($temp_31->_error);
                        }
                        else {
                            while ($temp_31->isValid()) {
                                $__out__->writeStructure('
														');
                                $temp_33 =& $__ctx__->get("option/value");
                                $temp_34 =& $__ctx__->get("field/selected");
                                $temp_35 = $temp_33 == $temp_34;
                                $temp_32 =& $temp_35;
                                if (!PEAR::isError($temp_32) && $temp_32) {
                                    $temp_36 = $__ctx__->get("option/value");
                                    $__out__->writeStructure('<option');
                                    $value =& $temp_36;
                                    $__out__->writeStructure(' value="');
                                    $__out__->write($value);
                                    $__out__->writeStructure('" selected="selected">');
                                    // TAG option AT LINE 215
                                    $_src_tag = "option"; $_src_line = 215;
                                    $temp_37 = $__ctx__->get("option/label");
                                    $__out__->write($temp_37);
                                    $__out__->writeStructure('</option>');
                                }
                                $__out__->writeStructure('
														');
                                $temp_39 =& $__ctx__->get("option/value");
                                $temp_40 =& $__ctx__->get("field/selected");
                                $temp_41 = $temp_39 != $temp_40;
                                $temp_38 =& $temp_41;
                                if (!PEAR::isError($temp_38) && $temp_38) {
                                    $temp_42 = $__ctx__->get("option/value");
                                    $__out__->writeStructure('<option');
                                    $value =& $temp_42;
                                    $__out__->writeStructure(' value="');
                                    $__out__->write($value);
                                    $__out__->writeStructure('">');
                                    // TAG option AT LINE 218
                                    $_src_tag = "option"; $_src_line = 218;
                                    $temp_43 = $__ctx__->get("option/label");
                                    $__out__->write($temp_43);
                                    $__out__->writeStructure('</option>');
                                }
                                $__out__->writeStructure('
														');
                                $temp_31->next();
                            }
                        }
                        // end loop
                        $__out__->writeStructure('
													</select>
												</td>');
                    }
                    $temp_45 = $__ctx__->get("field/text");
                    $__out__->writeStructure('
												');
                    $temp_44 =& $temp_45;
                    if (!PEAR::isError($temp_44) && $temp_44) {
                        $__out__->writeStructure('<td valign="top">
													<input id="field_'. $__ctx__->getToString("field/id") .'" type="text" name="config_field['. $__ctx__->getToString("field/id") .']" value="'. $__ctx__->getToString("field/value") .'" size="15" style="width:150px;"/>
												</td>');
                    }
                    $temp_47 = $__ctx__->get("field/password");
                    $__out__->writeStructure('
												');
                    $temp_46 =& $temp_47;
                    if (!PEAR::isError($temp_46) && $temp_46) {
                        $__out__->writeStructure('<td valign="top">
													<input id="field_'. $__ctx__->getToString("field/id") .'" type="password" name="config_field['. $__ctx__->getToString("field/id") .']" value="'. $__ctx__->getToString("field/value") .'" size="15" style="width:150px;"/>
												</td>');
                    }
                    $temp_49 = $__ctx__->get("field/radio");
                    $__out__->writeStructure('
												');
                    $temp_48 =& $temp_49;
                    if (!PEAR::isError($temp_48) && $temp_48) {
                        $temp_51 = $__ctx__->get("field/on");
                        $__out__->writeStructure('<td valign="top">
													');
                        $temp_50 =& $temp_51;
                        if (!PEAR::isError($temp_50) && $temp_50) {
                            $__out__->writeStructure('<input id="field_'. $__ctx__->getToString("field/id") .'" name="config_field['. $__ctx__->getToString("field/id") .']" type="radio" value="on" style="background-color: #eeeeee;" checked="checked"/>');
                        }
                        $temp_53 = $__ctx__->get("field/off");
                        $__out__->writeStructure('
													');
                        $temp_52 =& $temp_53;
                        if (!PEAR::isError($temp_52) && $temp_52) {
                            $__out__->writeStructure('<input id="field_'. $__ctx__->getToString("field/id") .'" name="config_field['. $__ctx__->getToString("field/id") .']" type="radio" value="on" style="background-color: #eeeeee;"/>');
                        }
                        $__out__->writeStructure('
													');
                        // TAG span AT LINE 240
                        $_src_tag = "span"; $_src_line = 240;
                        $temp_54 = $__ctx__->get("labels/on");
                        $__out__->write($temp_54);
                        $temp_56 = $__ctx__->get("field/on");
                        $__out__->writeStructure('
													');
                        $temp_55 =& $temp_56;
                        if (!PEAR::isError($temp_55) && $temp_55) {
                            $__out__->writeStructure('<input id="field_'. $__ctx__->getToString("field/id") .'" name="config_field['. $__ctx__->getToString("field/id") .']" type="radio" value="off" style="background-color: #eeeeee;"/>');
                        }
                        $temp_58 = $__ctx__->get("field/off");
                        $__out__->writeStructure('
													');
                        $temp_57 =& $temp_58;
                        if (!PEAR::isError($temp_57) && $temp_57) {
                            $__out__->writeStructure('<input id="field_'. $__ctx__->getToString("field/id") .'" name="config_field['. $__ctx__->getToString("field/id") .']" type="radio" value="off" style="background-color: #eeeeee;" checked="checked"/>');
                        }
                        $__out__->writeStructure('
													');
                        // TAG span AT LINE 254
                        $_src_tag = "span"; $_src_line = 254;
                        $temp_59 = $__ctx__->get("labels/off");
                        $__out__->write($temp_59);
                        $__out__->writeStructure('
												</td>');
                    }
                    $__out__->writeStructure('
											</tr>');
                    $temp_15->next();
                }
            }
            // end loop
            $__out__->writeStructure('
										</table>');
            $temp_11->next();
        }
    }
    // end loop
    $__out__->writeStructure('
									</td>
								</tr>
								
								<tr>
									<td class="header-small" colspan="2">');
    $__out__->pushBuffer();
    $__out__->writeStructure('
									Save phpCMS Settings
									');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('Save phpCMS Settings'));
    $temp_60 = "Save Settings";
    $__out__->writeStructure('</td>
								</tr>
								
								<tr bgcolor="#EEEEEE">
									<td valign="bottom">
									
									</td>
									
									<td valign="bottom">
										<input');
    $value =& $temp_60;
    $__out__->writeStructure(' value="');
    $__out__->writeStructure($__tpl__->_translate(trim($value)));
    $__out__->writeStructure('" type="submit" name="SUBMIT" />
									</td>
								</tr>
								
							</table>
						</form>
					</td>
				</tr>
				
				<tr>
					<td class="header-small">');
    $__out__->pushBuffer();
    $__out__->writeStructure('
							Parser - Configuration and Options
					');
    $__out__->popBuffer();
    $__out__->writeStructure($__tpl__->_translate('Parser - Configuration and Options'));
    $__out__->writeStructure('</td>
				</tr>
				
			</table>
		</div>
	</body>
</html>');
    return $__out__->toString();
}
?>