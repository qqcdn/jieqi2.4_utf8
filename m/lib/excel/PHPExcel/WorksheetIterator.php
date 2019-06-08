<?php

class PHPExcel_WorksheetIterator implements Iterator
{
    /**
     * Spreadsheet to iterate
     *
     * @var PHPExcel
     */
    private $_subject;
    /**
     * Current iterator position
     *
     * @var int
     */
    private $_position = 0;
    public function __construct(PHPExcel $subject = NULL)
    {
        $this->_subject = $subject;
    }
    public function __destruct()
    {
        unset($this->_subject);
    }
    public function rewind()
    {
        $this->_position = 0;
    }
    public function current()
    {
        return $this->_subject->getSheet($this->_position);
    }
    public function key()
    {
        return $this->_position;
    }
    public function next()
    {
        ++$this->_position;
    }
    public function valid()
    {
        return $this->_position < $this->_subject->getSheetCount();
    }
}