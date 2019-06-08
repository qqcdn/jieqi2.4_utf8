<?php

class JieqiThemeForm extends JieqiForm
{
    public function insertBreak($extra = NULL)
    {
        if (!isset($extra)) {
            $extra = '<tr><td colspan="2"></td></tr>';
            $this->addElement($extra);
        } else {
            $extra = '<tr><td colspan="2">' . $extra . '</td></tr>';
            $this->addElement($extra);
        }
    }
    public function render($fwidth = '100%')
    {
        $required = $this->getRequired();
        $ret = '' . "\n" . '<form name="' . $this->getName() . '" id="' . $this->getName() . '" action="' . $this->getAction() . '" method="' . $this->getMethod() . '" onsubmit="return jieqiFormValidate_' . $this->getName() . '();"' . $this->getExtra() . '>' . "\n" . '<table width="' . $fwidth . '" class="grid" align="center">' . "\n" . '<caption>' . $this->getTitle() . '</caption>' . "\n" . '';
        foreach ($this->getElements() as $ele) {
            if (!is_object($ele)) {
                $ret .= $ele;
            } else {
                if (!$ele->isHidden()) {
                    $caption = $ele->getCaption();
                    if (empty($caption)) {
                        $ret .= '<tr>' . "\n" . '  <td colspan="2" class="head">' . $ele->render() . '</td>' . "\n" . '</tr>' . "\n" . '';
                    } else {
                        $ret .= '<tr valign="middle" align="left">' . "\n" . '  <td class="tdl" width="25%">' . $caption;
                        if ($ele->getIntro() != '') {
                            $ret .= ' <br /><span class="hot">' . $ele->getIntro() . '</span>';
                        }
                        $ret .= '</td>' . "\n" . '  <td class="tdr">' . $ele->render();
                        if ($ele->getDescription() != '') {
                            $ret .= ' <span class="hot">' . $ele->getDescription() . '</span>';
                        }
                        $ret .= '</td>' . "\n" . '</tr>' . "\n" . '';
                    }
                } else {
                    $ret .= $ele->render();
                }
            }
        }
        $js = '' . "\r\n" . '<script language="javascript" type="text/javascript">' . "\r\n" . '<!--' . "\r\n" . 'function jieqiFormValidate_' . $this->getName() . '(){' . "\r\n" . '';
        $required = $this->getRequired();
        $reqcount = count($required);
        if (!defined('LANG_PLEASE_ENTER')) {
            $lang_enter = '请输入%s';
        } else {
            $lang_enter = LANG_PLEASE_ENTER;
        }
        for ($i = 0; $i < $reqcount; $i++) {
            $js .= '  if(document.' . $this->getName() . '.' . $required[$i]->getName() . '.value == ""){' . "\r\n" . '    alert("' . sprintf($lang_enter, preg_replace(array('/\\<span[^\\<\\>]*\\>[^\\<\\>]*\\<\\/span\\>/is', '/\\<div[^\\<\\>]*\\>[^\\<\\>]*\\<\\/div\\>/is', '/\\<font[^\\<\\>]*\\>[^\\<\\>]*\\<\\/font\\>/is'), '', str_replace(array('\\', '"'), array('\\\\', '\\"'), $required[$i]->getCaption()))) . '");' . "\r\n" . '    document.' . $this->getName() . '.' . $required[$i]->getName() . '.focus();' . "\r\n" . '    return false;' . "\r\n" . '  }' . "\r\n" . '';
        }
        $js .= '}' . "\r\n" . '//-->' . "\r\n" . '</script>' . "\n" . '';
        $ret .= '</table>' . "\n" . '</form>' . "\n" . '';
        $ret .= $js;
        return $ret;
    }
}
include_once JIEQI_ROOT_PATH . '/lib/html/form/form.php';