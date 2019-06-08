<?php

class JieqiForm
{
    public $_action;
    public $_method;
    public $_name;
    public $_title;
    public $_elements = array();
    public $_extra;
    public $_required = array();
    public function __construct($title, $name, $action, $method = 'post')
    {
        $this->_title = $title;
        $this->_name = $name;
        $this->_action = $action;
        $this->_method = $method;
    }
    public function getTitle()
    {
        return $this->_title;
    }
    public function getName()
    {
        return $this->_name;
    }
    public function getAction()
    {
        if (strstr($this->_action, '?')) {
            return $this->_action . '&do=submit';
        } else {
            return $this->_action . '?do=submit';
        }
    }
    public function getMethod()
    {
        return $this->_method;
    }
    public function addElement(&$formElement, $required = false)
    {
        $this->_elements[] =& $formElement;
        if ($required) {
            $this->_required[] =& $formElement;
        }
    }
    public function getElements()
    {
        return $this->_elements;
    }
    public function setExtra($extra)
    {
        $this->_extra = ' ' . $extra;
    }
    public function getExtra()
    {
        if (isset($this->_extra)) {
            return $this->_extra;
        }
    }
    public function setRequired(&$formElement)
    {
        $this->_required[] =& $formElement;
    }
    public function getRequired()
    {
        return $this->_required;
    }
    public function insertBreak($extra = NULL)
    {
    }
    public function render()
    {
    }
    public function display()
    {
        echo $this->render();
    }
    public function assign(&$tpl)
    {
        $i = 0;
        foreach ($this->getElements() as $ele) {
            if (!$ele->isHidden()) {
                $elements[$i]['caption'] = $ele->getCaption();
                $elements[$i]['body'] = $ele->render();
                $elements[$i]['hidden'] = false;
            } else {
                $elements[$i]['caption'] = '';
                $elements[$i]['body'] = $ele->render();
                $elements[$i]['hidden'] = true;
            }
            $i++;
        }
        $js = '' . "\r\n" . '		<!-- Start Form Vaidation JavaScript //-->' . "\r\n" . '		<script type=\'text/javascript\'>' . "\r\n" . '		<!--//' . "\r\n" . '		function jieqiFormValidate_' . $this->getName() . '(){' . "\r\n" . '		';
        $required =& $this->getRequired();
        $reqcount = count($required);
        for ($i = 0; $i < $reqcount; $i++) {
            $js .= 'if ( window.document.' . $this->getName() . '.' . $required[$i]->getName() . '.value == "" ) {alert( "' . sprintf(LANG_PLEASE_ENTER, $required[$i]->getCaption()) . '" );window.document.' . $this->getName() . '.' . $required[$i]->getName() . '.focus();return false;' . "\n" . '}' . "\r\n" . '				';
        }
        $js .= '}' . "\r\n" . '		//-->' . "\r\n" . '		</script>' . "\r\n" . '		<!-- End Form Vaidation JavaScript //-->' . "\r\n" . '		';
        $tpl->assign($this->getName(), array('title' => $this->getTitle(), 'name' => $this->getName(), 'action' => $this->getAction(), 'method' => $this->getMethod(), 'extra' => 'onsubmit="return jieqiFormValidate_' . $this->getName() . '();"' . $this->getExtra(), 'javascript' => $js, 'elements' => $elements));
    }
}