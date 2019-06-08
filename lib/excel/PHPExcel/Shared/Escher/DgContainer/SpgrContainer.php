<?php

class PHPExcel_Shared_Escher_DgContainer_SpgrContainer
{
	/**
	 * Parent Shape Group Container
	 *
	 * @var PHPExcel_Shared_Escher_DgContainer_SpgrContainer
	 */
	private $_parent;
	/**
	 * Shape Container collection
	 *
	 * @var array
	 */
	private $_children = array();

	public function setParent($parent)
	{
		$this->_parent = $parent;
	}

	public function getParent()
	{
		return $this->_parent;
	}

	public function addChild($child)
	{
		$this->_children[] = $child;
		$child->setParent($this);
	}

	public function getChildren()
	{
		return $this->_children;
	}

	public function getAllSpContainers()
	{
		$allSpContainers = array();

		foreach ($this->_children as $child) {
			if ($child instanceof PHPExcel_Shared_Escher_DgContainer_SpgrContainer) {
				$allSpContainers = array_merge($allSpContainers, $child->getAllSpContainers());
			}
			else {
				$allSpContainers[] = $child;
			}
		}

		return $allSpContainers;
	}
}


?>
