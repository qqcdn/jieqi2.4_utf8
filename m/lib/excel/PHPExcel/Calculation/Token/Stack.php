<?php

class PHPExcel_Calculation_Token_Stack
{
    /**
     *  The parser stack for formulae
     *
     *  @var mixed[]
     */
    private $_stack = array();
    /**
     *  Count of entries in the parser stack
     *
     *  @var integer
     */
    private $_count = 0;
    public function count()
    {
        return $this->_count;
    }
    public function push($type, $value, $reference = NULL)
    {
        $this->_stack[$this->_count++] = array('type' => $type, 'value' => $value, 'reference' => $reference);
        if ($type == 'Function') {
            $localeFunction = PHPExcel_Calculation::_localeFunc($value);
            if ($localeFunction != $value) {
                $this->_stack[$this->_count - 1]['localeValue'] = $localeFunction;
            }
        }
    }
    public function pop()
    {
        if (0 < $this->_count) {
            return $this->_stack[--$this->_count];
        }
        return NULL;
    }
    public function last($n = 1)
    {
        if ($this->_count - $n < 0) {
            return NULL;
        }
        return $this->_stack[$this->_count - $n];
    }
    public function clear()
    {
        $this->_stack = array();
        $this->_count = 0;
    }
}