<?php

class PHPExcel_CalcEngine_CyclicReferenceStack
{
    /**
     *  The call stack for calculated cells
     *
     *  @var mixed[]
     */
    private $_stack = array();
    public function count()
    {
        return count($this->_stack);
    }
    public function push($value)
    {
        $this->_stack[] = $value;
    }
    public function pop()
    {
        return array_pop($this->_stack);
    }
    public function onStack($value)
    {
        return in_array($value, $this->_stack);
    }
    public function clear()
    {
        $this->_stack = array();
    }
    public function showStack()
    {
        return $this->_stack;
    }
}