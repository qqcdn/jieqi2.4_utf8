<?php

class JieqiFormDhtmlTextArea extends JieqiFormTextArea
{
    public $_hiddenText;
    public function __construct($caption, $name, $value, $rows = 10, $cols = 50, $hiddentext = 'jieqiHiddenText')
    {
        parent::__construct($caption, $name, $value, $rows, $cols);
        $this->_hiddenText = $hiddentext;
    }
    public function render()
    {
        $ret = '<textarea class="textarea" name="' . $this->getName() . '" id="' . $this->getName() . '" rows="' . $this->getRows() . '" cols="' . $this->getCols() . '"' . $this->getExtra() . '>' . $this->getValue() . '</textarea>';
        if (file_exists(JIEQI_ROOT_PATH . '/scripts/ubbeditor_' . JIEQI_CHAR_SET . '.js')) {
            $jsfile = JIEQI_URL . '/scripts/ubbeditor_' . JIEQI_CHAR_SET . '.js';
        } else {
            $jsfile = JIEQI_URL . '/scripts/ubbeditor.js';
        }
        $ret .= '<script type="text/javascript">loadJs("' . $jsfile . '", function(){UBBEditor.Create("' . $this->getName() . '");});</script>';
        return $ret;
    }
}
include_once JIEQI_ROOT_PATH . '/lib/html/form/formtextarea.php';