<?php

class PHPExcel_NamedRange
{
    /**
     * Range name
     *
     * @var string
     */
    private $_name;
    /**
     * Worksheet on which the named range can be resolved
     *
     * @var PHPExcel_Worksheet
     */
    private $_worksheet;
    /**
     * Range of the referenced cells
     *
     * @var string
     */
    private $_range;
    /**
     * Is the named range local? (i.e. can only be used on $this->_worksheet)
     *
     * @var bool
     */
    private $_localOnly;
    /**
     * Scope
     *
     * @var PHPExcel_Worksheet
     */
    private $_scope;
    public function __construct($pName = NULL, PHPExcel_Worksheet $pWorksheet, $pRange = 'A1', $pLocalOnly = false, $pScope = NULL)
    {
        if ($pName === NULL || $pWorksheet === NULL || $pRange === NULL) {
            throw new PHPExcel_Exception('Parameters can not be null.');
        }
        $this->_name = $pName;
        $this->_worksheet = $pWorksheet;
        $this->_range = $pRange;
        $this->_localOnly = $pLocalOnly;
        $this->_scope = $pLocalOnly == true ? $pScope == NULL ? $pWorksheet : $pScope : NULL;
    }
    public function getName()
    {
        return $this->_name;
    }
    public function setName($value = NULL)
    {
        if ($value !== NULL) {
            $oldTitle = $this->_name;
            if ($this->_worksheet !== NULL) {
                $this->_worksheet->getParent()->removeNamedRange($this->_name, $this->_worksheet);
            }
            $this->_name = $value;
            if ($this->_worksheet !== NULL) {
                $this->_worksheet->getParent()->addNamedRange($this);
            }
            $newTitle = $this->_name;
            PHPExcel_ReferenceHelper::getInstance()->updateNamedFormulas($this->_worksheet->getParent(), $oldTitle, $newTitle);
        }
        return $this;
    }
    public function getWorksheet()
    {
        return $this->_worksheet;
    }
    public function setWorksheet(PHPExcel_Worksheet $value = NULL)
    {
        if ($value !== NULL) {
            $this->_worksheet = $value;
        }
        return $this;
    }
    public function getRange()
    {
        return $this->_range;
    }
    public function setRange($value = NULL)
    {
        if ($value !== NULL) {
            $this->_range = $value;
        }
        return $this;
    }
    public function getLocalOnly()
    {
        return $this->_localOnly;
    }
    public function setLocalOnly($value = false)
    {
        $this->_localOnly = $value;
        $this->_scope = $value ? $this->_worksheet : NULL;
        return $this;
    }
    public function getScope()
    {
        return $this->_scope;
    }
    public function setScope(PHPExcel_Worksheet $value = NULL)
    {
        $this->_scope = $value;
        $this->_localOnly = $value == NULL ? false : true;
        return $this;
    }
    public static function resolveRange($pNamedRange = '', PHPExcel_Worksheet $pSheet)
    {
        return $pSheet->getParent()->getNamedRange($pNamedRange, $pSheet);
    }
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->{$key} = clone $value;
            } else {
                $this->{$key} = $value;
            }
        }
    }
}