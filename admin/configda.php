<?php
define('JIEQI_MODULE_NAME', 'system');
require_once ('../global.php');
include_once (JIEQI_ROOT_PATH . '/class/power.php');
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminblock'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
$_REQUEST['mod'] = 'system';
$_REQUEST['define'] = 1;
$allowchanges = array('JIEQI_BANNER', 'JIEQI_TOP_BAR', 'JIEQI_BOTTOM_BAR');
jieqi_loadlang('configs', JIEQI_MODULE_NAME);
jieqi_getconfigs($_REQUEST['mod'], 'configs', 'jieqiConfigs');

if (!isset($_REQUEST['file'])) {
	if (defined('JIEQI_MOBILE_LOCATION') && (7 < strlen(JIEQI_MOBILE_LOCATION)) && (substr(JIEQI_LOCAL_URL, 0, strlen(JIEQI_MOBILE_LOCATION)) == JIEQI_MOBILE_LOCATION)) {
		$_REQUEST['file'] = 1;
	}
	else {
		$_REQUEST['file'] = 0;
	}
}
else {
	$_REQUEST['file'] = intval($_REQUEST['file']);
}

include_once (JIEQI_ROOT_PATH . '/class/configs.php');
$configs_handler = JieqiConfigsHandler::getInstance('JieqiConfigsHandler');
$criteria = new CriteriaCompo(new Criteria('modname', $_REQUEST['mod'], '='));
if (!isset($_REQUEST['define']) || ($_REQUEST['define'] != 1)) {
	$_REQUEST['define'] = 0;
}

$criteria->add(new Criteria('cdefine', $_REQUEST['define'], '='));
$criteria->setSort('catorder ASC, cid');
$criteria->setOrder('ASC');
$configs_handler->queryObjects($criteria);
$v = $configs_handler->getObject();

if ($v) {
	if (isset($_POST['act']) && ($_POST['act'] == 'update')) {
		jieqi_checkpost();
		$cfgarray = array();
		$cfgdefine = '';

		do {
			$tmpkey = $v->getVar('cname', 'n');
			if (!in_array($tmpkey, $allowchanges) && isset($_POST[$tmpkey])) {
				unset($_POST[$tmpkey]);
			}

			switch ($v->getVar('ctype')) {
			case JIEQI_TYPE_TXTBOX:
			case JIEQI_TYPE_TXTAREA:
			case JIEQI_TYPE_HIDDEN:
				if (!isset($_POST[$tmpkey])) {
					$tmpval = $v->getVar('cvalue', 'n');
				}
				else {
					$tmpval = $_POST[$tmpkey];
				}

				break;

			case JIEQI_TYPE_INT:
				if (!isset($_POST[$tmpkey]) || !is_numeric($_POST[$tmpkey])) {
					$tmpval = $v->getVar('cvalue', 'n');
				}
				else {
					$tmpval = $_POST[$tmpkey];
				}

				$tmpval = intval($tmpval);
				break;

			case JIEQI_TYPE_NUM:
				if (!isset($_POST[$tmpkey]) || !is_numeric($_POST[$tmpkey])) {
					$tmpval = $v->getVar('cvalue', 'n');
				}
				else {
					$tmpval = $_POST[$tmpkey];
				}

				break;

			case JIEQI_TYPE_PASSWORD:
				if (!isset($_POST[$tmpkey]) || (strlen($_POST[$tmpkey]) == 0)) {
					$tmpval = $v->getVar('cvalue', 'n');
				}
				else {
					$tmpval = $_POST[$tmpkey];
				}

				break;

			case JIEQI_TYPE_SELECT:
			case JIEQI_TYPE_RADIO:
				$selectary = @jieqi_unserialize($v->getVar('options', 'n'));

				if (!is_array($selectary)) {
					$selectary = array();
				}

				if (!isset($_POST[$tmpkey]) || !isset($selectary[$_POST[$tmpkey]])) {
					$tmpval = $v->getVar('cvalue', 'n');
				}
				else {
					$tmpval = $_POST[$tmpkey];
				}

				break;

			case JIEQI_TYPE_MULSELECT:
			case JIEQI_TYPE_CHECKBOX:
				$selectary = @jieqi_unserialize($v->getVar('options', 'n'));

				if (!is_array($selectary)) {
					$selectary = array();
				}

				$tmparray = (is_array($_POST[$tmpkey]) ? $_POST[$tmpkey] : array());
				$tmpval = 0;

				foreach ($tmparray as $tmpv ) {
					if (isset($selectary[$tmpv])) {
						$tmpval = $tmpval | intval($tmpv);
					}
				}

				break;

			default:
				if (!isset($_POST[$tmpkey])) {
					$tmpval = $v->getVar('cvalue', 'n');
				}
				else {
					$tmpval = $_POST[$tmpkey];
				}

				break;
				break;
			}

			if (empty($_REQUEST['file']) && ($tmpval != $v->getVar('cvalue', 'n'))) {
				$v->setVar('cvalue', $tmpval);
				$configs_handler->insert($v);
			}

			if ($v->getVar('cdefine') == '1') {
				$cfgdefine .= '@define(\'' . jieqi_setslashes($tmpkey, '"') . '\',\'' . jieqi_setslashes($tmpval, '"') . '\');' . "\r\n" . '';
			}
			else {
				$cfgarray[$_REQUEST['mod']][$tmpkey] = $tmpval;
			}
		} while ($v = $configs_handler->getObject());

		if (0 < count($cfgarray)) {
			jieqi_setconfigs('configs', 'jieqiConfigs', $cfgarray, $_REQUEST['mod']);
		}

		if (!empty($cfgdefine)) {
			$isdefine = 1;
			$dir = JIEQI_ROOT_PATH . '/configs';

			if (!file_exists($dir)) {
				@mkdir($dir, 511);
			}

			@chmod($dir, 511);

			if ($_REQUEST['mod'] != 'system') {
				$dir .= '/' . $_REQUEST['mod'];

				if (!file_exists($dir)) {
					@mkdir($dir, 511);
				}

				@chmod($dir, 511);
			}

			$dir .= '/system.php';

			if (file_exists($dir)) {
				@chmod($dir, 511);
			}

			$cfgdefine = '<?php' . "\r\n" . '' . $cfgdefine . '' . "\r\n" . '?>';
			jieqi_writefile($dir, $cfgdefine);
			$publicdata = str_replace('?><?php', '', $cfgdefine . jieqi_readfile(JIEQI_ROOT_PATH . '/lang/lang_system.php') . jieqi_readfile(JIEQI_ROOT_PATH . '/configs/groups.php'));
			jieqi_writefile(JIEQI_ROOT_PATH . '/configs/define.php', $publicdata);
		}
		else {
			$isdefine = 0;
		}

		include_once (JIEQI_ROOT_PATH . '/class/logs.php');
		$logs_handler = JieqiLogsHandler::getInstance('JieqiLogsHandler');
		$logdata = array('logtype' => 2, 'logdata' => 'module:' . $_REQUEST['mod'] . ',define:' . $isdefine, 'todata' => serialize($_REQUEST));
		$logs_handler->addlog($logdata);
		jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['edit_config_success']);
	}
	else {
		include_once (JIEQI_ROOT_PATH . '/admin/header.php');
		include_once (JIEQI_ROOT_PATH . '/lib/html/formloader.php');
		$formcaption = $jieqiLang['system']['edit_config'];
		$formcaption .= (empty($_REQUEST['file']) ? $jieqiLang['system']['config_db_file'] : $jieqiLang['system']['config_only_file']);
		$self_fname = ($_SERVER['PHP_SELF'] ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']));
		$config_form = new JieqiThemeForm($formcaption, 'config', JIEQI_URL . '/admin/' . $self_fname);
		$catname = '';
		$catorder = 0;
		$catlink = '';

		do {
			if (!in_array($v->getVar('cname', 'n'), $allowchanges)) {
				continue;
			}

			$tmpvar = '';

			if ($catname != $tmpvar) {
				$catname = $tmpvar;
				$catorder++;
				$'catele' . $catorder = new JieqiFormLabel('', '<a name="catorder' . $catorder . '">' . $catname . '</a>');
				$config_form->addElement('catele' . $catorder, false);

				if (!empty($catlink)) {
					$catlink .= '&nbsp;&nbsp;';
				}

				$catlink .= '<a class="btnlink" href="#catorder' . $catorder . '">' . $catname . '</a>';
			}

			$modname = $v->getVar('modname', 'n');
			$cname = $v->getVar('cname', 'n');
			$cdefine = $v->getVar('cdefine', 'n');
			$cvalue = $v->getVar('cvalue', 'n');

			if (!empty($_REQUEST['file'])) {
				if ($_REQUEST['define'] == 1) {
					if (defined($cname)) {
						$cvalue = constant($cname);
					}
				}
				else if (isset($jieqiConfigs[$modname][$cname])) {
					$cvalue = $jieqiConfigs[$modname][$cname];
				}
			}

			$cvalue = jieqi_htmlchars($cvalue, ENT_QUOTES);

			switch ($v->getVar('ctype')) {
			case JIEQI_TYPE_INT:
			case JIEQI_TYPE_NUM:
				$tmpvar = $v->getVar('cname');
				$$tmpvar = new JieqiFormText($v->getVar('ctitle'), $v->getVar('cname'), 25, 100, $cvalue);
				$$tmpvar->setDescription($v->getVar('cdescription'));
				$config_form->addElement($tmpvar, false);
				break;

			case JIEQI_TYPE_TXTAREA:
				$tmpvar = $v->getVar('cname');
				$$tmpvar = new JieqiFormTextArea($v->getVar('ctitle'), $v->getVar('cname'), $cvalue, 5, 50);
				$$tmpvar->setDescription($v->getVar('cdescription'));
				$config_form->addElement($tmpvar, false);
				break;

			case JIEQI_TYPE_SELECT:
				$tmpvar = $v->getVar('cname');
				$$tmpvar = new JieqiFormSelect($v->getVar('ctitle'), $v->getVar('cname'), $cvalue);
				$$tmpvar->setDescription($v->getVar('cdescription'));
				$selectary = @jieqi_unserialize($v->getVar('options', 'n'));

				if (!is_array($selectary)) {
					$selectary = array();
				}

				foreach ($selectary as $val => $cap ) {
					$$tmpvar->addOption($val, $cap);
				}

				$config_form->addElement($tmpvar, false);
				break;

			case JIEQI_TYPE_RADIO:
				$tmpvar = $v->getVar('cname');
				$$tmpvar = new JieqiFormRadio($v->getVar('ctitle'), $v->getVar('cname'), $cvalue);
				$$tmpvar->setDescription($v->getVar('cdescription'));
				$selectary = @jieqi_unserialize($v->getVar('options', 'n'));

				if (!is_array($selectary)) {
					$selectary = array();
				}

				foreach ($selectary as $val => $cap ) {
					$$tmpvar->addOption($val, $cap);
				}

				$config_form->addElement($tmpvar, false);
				break;

			case JIEQI_TYPE_CHECKBOX:
				$tmpvar = $v->getVar('cname');
				$tmpvalue = decbin(intval($cvalue));
				$tmplen = strlen($tmpvalue);
				$tmparray = array();
				$tmpnum = 1;

				for ($p = $tmplen - 1; 0 <= $p; $p--) {
					if ($tmpvalue[$p] == '1') {
						$tmparray[] = $tmpnum;
					}

					$tmpnum *= 2;
				}

				$$tmpvar = new JieqiFormCheckBox($v->getVar('ctitle'), $v->getVar('cname'), $tmparray);
				$$tmpvar->setDescription($v->getVar('cdescription'));
				$selectary = @jieqi_unserialize($v->getVar('options', 'n'));

				if (!is_array($selectary)) {
					$selectary = array();
				}

				foreach ($selectary as $val => $cap ) {
					$$tmpvar->addOption($val, $cap);
				}

				$config_form->addElement($tmpvar, false);
				break;

			case JIEQI_TYPE_LABEL:
				$tmpvar = $v->getVar('cname');
				$$tmpvar = new JieqiFormLabel($v->getVar('ctitle'), $cvalue);
				$$tmpvar->setDescription($v->getVar('cdescription'));
				$config_form->addElement($tmpvar, false);
				break;

			case JIEQI_TYPE_PASSWORD:
				$tmpvar = $v->getVar('cname');
				$$tmpvar = new JieqiFormText($v->getVar('ctitle'), $v->getVar('cname'), 25, 30, '');
				$$tmpvar->setDescription($v->getVar('cdescription'));
				$config_form->addElement($tmpvar, false);
				break;

			case JIEQI_TYPE_TXTBOX:
			default:
				$tmpvar = $v->getVar('cname');
				$$tmpvar = new JieqiFormText($v->getVar('ctitle'), $v->getVar('cname'), 25, 100, $cvalue);
				$$tmpvar->setDescription($v->getVar('cdescription'));
				$config_form->addElement($tmpvar, false);
				break;
			}
		} while ($v = $configs_handler->getObject());

		$config_form->addElement(new JieqiFormHidden('mod', $_REQUEST['mod']));
		$config_form->addElement(new JieqiFormHidden('define', $_REQUEST['define']));
		$config_form->addElement(new JieqiFormHidden('file', $_REQUEST['file']));
		$config_form->addElement(new JieqiFormHidden('act', 'update'));
		$config_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
		$config_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['system']['save_config'], 'submit'));
		$jieqiTpl->assign('jieqi_contents', '<div style="text-align:center;line-height:200%;padding:10px;">' . $catlink . '</div>' . $config_form->render(JIEQI_FORM_MAX) . '<br />');
		include_once (JIEQI_ROOT_PATH . '/admin/footer.php');
	}
}
else {
	jieqi_msgwin(LANG_NOTICE, $jieqiLang['system']['no_usage_config']);
}

