<?php
class JieqiDatabase extends JieqiObject
{
	static public $instance = array();

	public function __construct()
	{
		parent::__construct();
	}

	static public function retInstance()
	{
		return self::$instance;
	}

	static public function close($db = NULL)
	{
		if (is_object($db)) {
			$db->close();
		}
		else if (!empty(self::$instance)) {
			foreach (self::$instance as $db) {
				$db->close();
			}
		}
	}

	static public function getInstance($dbtype = '', $dbset = array())
	{
		if (!is_array($dbset)) {
			$dbset = array();
		}

		if (!isset($dbset['dbtype'])) {
			$dbset['dbtype'] = JIEQI_DB_TYPE;
		}

		if (!isset($dbset['dbhost'])) {
			$dbset['dbhost'] = JIEQI_DB_HOST;
		}

		if (!isset($dbset['dbuser'])) {
			$dbset['dbuser'] = JIEQI_DB_USER;
		}

		if (!isset($dbset['dbpass'])) {
			$dbset['dbpass'] = JIEQI_DB_PASS;
		}

		if (!isset($dbset['dbname'])) {
			$dbset['dbname'] = JIEQI_DB_NAME;
		}

		if (!isset($dbset['dbpconnect'])) {
			$dbset['dbpconnect'] = JIEQI_DB_PCONNECT;
		}

		if (!isset($dbset['dbcharset']) && defined('JIEQI_DB_CHARSET')) {
			$dbset['dbcharset'] = JIEQI_DB_CHARSET;
		}

		if (!isset($dbset['dbusage'])) {
			$dbset['dbusage'] = 0;
		}
		else {
			$dbset['dbusage'] = intval($dbset['dbusage']);
		}

		$inskey = md5(implode('|', $dbset));

		if (!isset(self::$instance[$inskey])) {
			switch ($dbset['dbtype']) {
			case 'mysqli':
				require_once 'mysqli/db.php';
				self::$instance[$inskey] = new JieqiMySQLIDatabase();
				break;

			case 'mysql':
				require_once 'mysql/db.php';
				self::$instance[$inskey] = new JieqiMySQLDatabase();
				break;

			case 'sqlite':
				require_once 'sqlite/db.php';
				self::$instance[$inskey] = new JieqiSQLiteDatabase();
				break;

			default:
				jieqi_printfail('The database type (' . $dbset['dbtype'] . ') is not exists!');
				return false;
			}

			self::$instance[$inskey]->setDbset($dbset);
		}

		return self::$instance[$inskey];
	}
}
class JieqiObjectData extends JieqiObject
{
	/**
	 * 保存时新整数据还是更新数据
	 */
	public $_isNew = false;

	public function __construct()
	{
		parent::__construct();
	}

	public function setNew()
	{
		$this->_isNew = true;
	}

	public function unsetNew()
	{
		$this->_isNew = false;
	}

	public function isNew()
	{
		return $this->_isNew;
	}

	public function initVar($key, $type, $value = NULL, $caption = '', $required = false, $maxlength = NULL, $isdirty = false)
	{
		$this->vars[$key] = array('type' => $type, 'value' => $value, 'caption' => $caption, 'required' => $required, 'maxlength' => $maxlength, 'isdirty' => $isdirty, 'default' => '', 'options' => '');
	}

	public function setOptions($key, $options)
	{
		$this->vars[$key]['options'] = $options;
	}

	public function setVar($key, $value, $isdirty = true)
	{
		if (!empty($key) && isset($value)) {
			if (!isset($this->vars[$key])) {
				$this->initVar($key, JIEQI_TYPE_TXTBOX);
			}

			$this->vars[$key]['value'] = $value;
			$this->vars[$key]['isdirty'] = $isdirty;
		}
	}

	public function setVars($var_arr, $isdirty = false)
	{
		if (is_array($var_arr)) {
			foreach ($var_arr as $key => $value) {
				$this->setVar($key, $value, $isdirty);
			}
		}
	}

	public function getVars($format = '')
	{
		if (in_array($format, array('s', 'e', 'q', 't', 'o', 'n'))) {
			$ret = array();

			foreach ($this->vars as $k => $v) {
				$ret[$k] = $this->getVar($k, $format);
			}

			return $ret;
		}
		else {
			return $this->vars;
		}
	}

	public function getVar($key, $format = 's')
	{
		if (isset($this->vars[$key]['value'])) {
			if (is_string($this->vars[$key]['value'])) {
				switch (strtolower($format)) {
				case 's':
					return jieqi_htmlstr($this->vars[$key]['value']);
				case 'e':
					return preg_replace('/&amp;#(\\d+);/isU', '&#\\1;', jieqi_htmlchars($this->vars[$key]['value'], ENT_QUOTES));
				case 'q':
					return jieqi_dbslashes($this->vars[$key]['value']);
				case 't':
					return $this->vars[$key]['caption'];
				case 'o':
					return !empty($this->vars[$key]['options'][$this->vars[$key]['value']]) ? $this->vars[$key]['options'][$this->vars[$key]['value']] : '';
				case 'n':
				default:
					return $this->vars[$key]['value'];
				}
			}
			else {
				return $this->vars[$key]['value'];
			}
		}
		else {
			return false;
		}
	}
}
class JieqiQueryHandler extends JieqiObject
{
	/**
	 * 数据库对象
	 *
	 * @var object
	 */
	public $db;
	/**
	 * 查询结果资源
	 *
	 * @var resource
	 */
	public $sqlres;

	public function __construct($db = '')
	{
		parent::__construct();
		if (empty($db) || !is_object($db)) {
			$this->db = JieqiDatabase::getInstance();
		}
		else {
			$this->db = &$db;
		}
	}

	public function setdb($db)
	{
		$this->db = &$db;
	}

	public function getdb()
	{
		return $this->db;
	}

	public function execute($criteria = NULL, $full = false, $nobuffer = false)
	{
		if (is_object($criteria)) {
			$sql = $criteria->getSql();

			if (!$full) {
				$sql .= ' ' . $criteria->renderWhere();
			}

			$this->sqlres = $this->db->query($sql, 0, 0, $nobuffer);
			return $this->sqlres;
		}
		else if (!empty($criteria)) {
			$this->sqlres = $this->db->query($criteria, 0, 0, $nobuffer);
			return $this->sqlres;
		}

		return false;
	}

	public function queryObjects($criteria = NULL, $nobuffer = false)
	{
		$limit = $start = 0;
		$sql = 'SELECT ' . $criteria->getFields() . ' FROM ' . $criteria->getTables() . ' ' . $criteria->renderWhere();

		if ($criteria->getGroupby() != '') {
			$sql .= ' GROUP BY ' . $criteria->getGroupby();
		}

		if ($criteria->getSort() != '') {
			$sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
		}

		$limit = $criteria->getLimit();
		$start = $criteria->getStart();
		$this->sqlres = $this->db->query($sql, $limit, $start, $nobuffer);
		return $this->sqlres;
	}

	public function getObject($result = '')
	{
		if ($result == '') {
			$result = $this->sqlres;
		}

		if (!$result) {
			return false;
		}
		else {
			$myrow = $this->db->fetchArray($result);

			if (!$myrow) {
				return false;
			}
			else {
				$dbrowobj = new JieqiObjectData();
				$dbrowobj->setVars($myrow);
				return $dbrowobj;
			}
		}
	}

	public function getRow($result = '')
	{
		if ($result == '') {
			$result = $this->sqlres;
		}

		if (!$result) {
			return false;
		}
		else {
			$myrow = $this->db->fetchArray($result);

			if (!$myrow) {
				return false;
			}
			else {
				return $myrow;
			}
		}
	}

	public function getCount($criteria = NULL)
	{
		if (is_object($criteria)) {
			if ($criteria->getGroupby() == '') {
				$sql = 'SELECT COUNT(*) FROM ' . $criteria->getTables() . ' ' . $criteria->renderWhere();
				$nobuffer = true;
			}
			else {
				$sql = 'SELECT COUNT(' . $criteria->getGroupby() . ') FROM ' . $criteria->getTables() . ' ' . $criteria->renderWhere() . ' GROUP BY ' . $criteria->getGroupby();
				$nobuffer = false;
			}

			$result = $this->db->query($sql, 0, 0, $nobuffer);

			if (!$result) {
				return 0;
			}

			if ($criteria->getGroupby() == '') {
				list($count) = $this->db->fetchRow($result);
			}
			else {
				$count = $this->db->getRowsNum($result);
			}

			return $count;
		}

		return 0;
	}

	public function uptablefields($table, $fields, $criteria = NULL)
	{
		$sql = 'UPDATE ' . $table . ' SET ';
		$start = true;

		if (is_array($fields)) {
			foreach ($fields as $k => $v) {
				if (!$start) {
					$sql .= ', ';
				}
				else {
					$start = false;
				}

				if (is_numeric($v)) {
					$sql .= $k . '=' . $this->db->quoteString($v);
				}
				else {
					$sql .= $k . '=' . $this->db->quoteString($v);
				}
			}
		}
		else {
			$sql .= $fields;
		}

		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' ' . $criteria->renderWhere();
		}

		if (!($result = $this->db->query($sql))) {
			return false;
		}

		return true;
	}

	public function makeupsql($table, $prows, $mode = 'INSERT', $where = '')
	{
		switch ($mode) {
		case 'INSERT':
		case 'insert':
		case 'REPLACE':
		case 'replace':
			$fields = '';
			$values = '';

			foreach ($prows as $k => $v) {
				if ($fields != '') {
					$fields .= ', ';
				}

				$fields .= '`' . $k . '`';

				if ($values != '') {
					$values .= ', ';
				}

				$values .= '\'' . jieqi_dbslashes($v) . '\'';
			}

			$sql = strtoupper($mode) . (' INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ')');
			break;

		case 'UPDATE':
		case 'update':
			$fields = '';

			foreach ($prows as $k => $v) {
				if ($fields != '') {
					$fields .= ', ';
				}

				$fields .= '`' . $k . '` = \'' . jieqi_dbslashes($v) . '\'';
			}

			$sql = strtoupper($mode) . (' ' . $table . ' SET ' . $fields . ' WHERE ');

			if (is_array($where)) {
				$limits = '';

				foreach ($where as $k => $v) {
					if ($limits != '') {
						$limits .= ' AND ';
					}

					$limits .= '`' . $k . '` = \'' . jieqi_dbslashes($v) . '\'';
				}

				$sql .= $limits;
			}
			else {
				$sql .= $where;
			}

			break;

		default:
			return $sql = '';
		}

		return $sql;
	}
}
class JieqiObjectHandler extends JieqiQueryHandler
{
	/**
	 * 类的基本名称
	 *
	 * @var string
	 */
	public $basename;
	/**
	 * 唯一序号字段
	 *
	 * @var string
	 */
	public $autoid;
	/**
	 * 数据表表名
	 *
	 * @var string
	 */
	public $dbname;
	/**
	 * 是否完整的表名，默认“否”，要用dbprefix获得完整表名
	 *
	 * @var bool
	 */
	public $fullname = false;

	public function __construct($db = '')
	{
		parent::__construct($db);
	}

	public function create($isNew = true)
	{
		$tmpvar = 'Jieqi' . ucfirst($this->basename);
		${$this->basename} = new $tmpvar();

		if ($isNew) {
			${$this->basename}->setNew();
		}

		return ${$this->basename};
	}

	public function get($fieldvalue, $fieldname = '')
	{
		if (0 < strlen($fieldvalue)) {
			if ($fieldname == '') {
				$fieldname = $this->autoid;
			}

			$sql = 'SELECT * FROM ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' WHERE ' . $fieldname . '=\'' . jieqi_dbslashes($fieldvalue) . '\'';

			if (!($result = $this->db->query($sql, 1, 0, true))) {
				return false;
			}

			$datarow = $this->db->fetchArray($result);

			if (is_array($datarow)) {
				$tmpvar = 'Jieqi' . ucfirst($this->basename);
				${$this->basename} = new $tmpvar();
				${$this->basename}->setVars($datarow);
				return ${$this->basename};
			}
		}

		return false;
	}

	public function insert(&$baseobj)
	{
		if (strcasecmp(get_class($baseobj), 'jieqi' . $this->basename) != 0) {
			return false;
		}

		if ($baseobj->isNew()) {
			if (is_numeric($baseobj->getVar($this->autoid, 'n'))) {
				${$this->autoid} = intval($baseobj->getVar($this->autoid, 'n'));
			}
			else {
				${$this->autoid} = $this->db->genId($this->dbname . '_' . $this->autoid . '_seq');
			}

			$sql = 'INSERT INTO ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' (';
			$values = ') VALUES (';
			$start = true;

			foreach ($baseobj->vars as $k => $v) {
				if (!$start) {
					$sql .= ', ';
					$values .= ', ';
				}
				else {
					$start = false;
				}

				$sql .= $k;

				if ($v['type'] == JIEQI_TYPE_INT) {
					if ($k != $this->autoid) {
						if (!is_numeric($v['value'])) {
							$v['value'] = @intval($v['value']);
						}

						$values .= $this->db->quoteString($v['value']);
					}
					else {
						$values .= ${$this->autoid};
					}
				}
				else {
					$values .= $this->db->quoteString($v['value']);
				}
			}

			$sql .= $values . ')';
			unset($values);
		}
		else {
			$sql = 'UPDATE ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' SET ';
			$start = true;

			foreach ($baseobj->vars as $k => $v) {
				if ($k != $this->autoid && $v['isdirty']) {
					if (!$start) {
						$sql .= ', ';
					}
					else {
						$start = false;
					}

					if ($v['type'] == JIEQI_TYPE_INT) {
						if (!is_numeric($v['value'])) {
							$v['value'] = @intval($v['value']);
						}

						$sql .= $k . '=' . $this->db->quoteString($v['value']);
					}
					else {
						$sql .= $k . '=' . $this->db->quoteString($v['value']);
					}
				}
			}

			if ($start) {
				return true;
			}

			$sql .= ' WHERE ' . $this->autoid . '=' . intval($baseobj->vars[$this->autoid]['value']);
		}

		$result = $this->db->query($sql);

		if (!$result) {
			return false;
		}

		if ($baseobj->isNew()) {
			$baseobj->setVar($this->autoid, $this->db->getInsertId());
		}

		return true;
	}

	public function delete($criteria = 0, $fieldname = '')
	{
		$sql = '';

		if (is_numeric($criteria)) {
			$criteria = intval($criteria);

			if ($fieldname == '') {
				$fieldname = $this->autoid;
			}

			$sql = 'DELETE FROM ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' WHERE ' . $fieldname . '=' . $criteria;
		}
		else {
			if (is_object($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
				$tmpstr = $criteria->renderWhere();

				if (!empty($tmpstr)) {
					$sql = 'DELETE FROM ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' ' . $tmpstr;
				}
			}
		}

		if (empty($sql)) {
			return false;
		}

		$result = $this->db->query($sql);

		if (!$result) {
			return false;
		}

		return true;
	}

	public function queryObjects($criteria = NULL, $nobuffer = false)
	{
		$limit = $start = 0;
		$sql = 'SELECT ' . $criteria->getFields() . ' FROM ' . jieqi_dbprefix($this->dbname, $this->fullname);
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' ' . $criteria->renderWhere();

			if ($criteria->getGroupby() != '') {
				$sql .= ' GROUP BY ' . $criteria->getGroupby();
			}

			if ($criteria->getSort() != '') {
				$sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
			}

			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}

		$this->sqlres = $this->db->query($sql, $limit, $start, $nobuffer);
		return $this->sqlres;
	}

	public function getObject($result = '')
	{
		if ($result == '') {
			$result = $this->sqlres;
		}

		if (!$result) {
			return false;
		}
		else {
			$tmpvar = 'Jieqi' . ucfirst($this->basename);
			$myrow = $this->db->fetchArray($result);

			if (!$myrow) {
				return false;
			}
			else {
				$dbrowobj = new $tmpvar();
				$dbrowobj->setVars($myrow);
				return $dbrowobj;
			}
		}
	}

	public function getCount($criteria = NULL)
	{
		$sql = 'SELECT COUNT(*) FROM ' . jieqi_dbprefix($this->dbname, $this->fullname);
		$nobuffer = true;
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' ' . $criteria->renderWhere();

			if ($criteria->getGroupby() != '') {
				$sql = 'SELECT COUNT(' . $criteria->getGroupby() . ') FROM ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' ' . $criteria->renderWhere() . ' GROUP BY ' . $criteria->getGroupby();
				$nobuffer = false;
			}
		}

		$result = $this->db->query($sql, 0, 0, $nobuffer);

		if (!$result) {
			return 0;
		}

		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement') && $criteria->getGroupby() != '') {
			$count = $this->db->getRowsNum($result);
		}
		else {
			list($count) = $this->db->fetchRow($result);
		}

		return $count;
	}

	public function updatefields($fields, $criteria = NULL)
	{
		$sql = 'UPDATE ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' SET ';
		$start = true;

		if (is_array($fields)) {
			foreach ($fields as $k => $v) {
				if (!$start) {
					$sql .= ', ';
				}
				else {
					$start = false;
				}

				if (is_numeric($v)) {
					$sql .= $k . '=' . $this->db->quoteString($v);
				}
				else {
					$sql .= $k . '=' . $this->db->quoteString($v);
				}
			}
		}
		else {
			$sql .= $fields;
		}

		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' ' . $criteria->renderWhere();
		}

		if (!($result = $this->db->query($sql))) {
			return false;
		}

		return true;
	}
}
class CriteriaElement extends JieqiObject
{
	public $order = 'ASC';
	public $sort = '';
	public $limit = 0;
	public $start = 0;
	public $groupby = '';
	public $sql = '';
	public $fields = '*';
	public $tables = '';

	public function __construct()
	{
		parent::__construct();
	}

	public function setSql($sql)
	{
		$this->sql = $sql;
	}

	public function getSql()
	{
		return $this->sql;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setTables($tables)
	{
		$this->tables = $tables;
	}

	public function getTables()
	{
		return $this->tables;
	}

	public function setSort($sort)
	{
		$this->sort = $sort;
	}

	public function getSort()
	{
		return $this->sort;
	}

	public function setOrder($order)
	{
		if ('DESC' == strtoupper($order)) {
			$this->order = 'DESC';
		}
		else if ('ASC' == strtoupper($order)) {
			$this->order = 'ASC';
		}
	}

	public function getOrder()
	{
		return $this->order;
	}

	public function setLimit($limit = 0)
	{
		if (isset($limit) && is_numeric($limit)) {
			$this->limit = intval($limit);
		}
		else {
			$this->limit = 1;
		}
	}

	public function getLimit()
	{
		return $this->limit;
	}

	public function setStart($start = 0)
	{
		$this->start = intval($start);
	}

	public function getStart()
	{
		return $this->start;
	}

	public function setGroupby($group)
	{
		$this->groupby = $group;
	}

	public function getGroupby()
	{
		return $this->groupby;
	}
}
class CriteriaCompo extends CriteriaElement
{
	public $criteriaElements = array();
	public $conditions = array();

	public function __construct($ele = NULL, $condition = 'AND')
	{
		if (isset($ele) && is_object($ele)) {
			$this->add($ele, $condition);
		}
	}

	public function add(&$criteriaElement, $condition = 'AND')
	{
		$this->criteriaElements[] = &$criteriaElement;
		$this->conditions[] = $condition;
		return $this;
	}

	public function render()
	{
		$ret = '';
		$count = count($this->criteriaElements);

		if (0 < $count) {
			$ret = '(' . $this->criteriaElements[0]->render();

			for ($i = 1; $i < $count; $i++) {
				$ret .= ' ' . $this->conditions[$i] . ' ' . $this->criteriaElements[$i]->render();
			}

			$ret .= ')';
		}

		return $ret;
	}

	public function renderWhere()
	{
		$ret = $this->render();
		$ret = $ret != '' ? 'WHERE ' . $ret : $ret;
		return $ret;
	}
}
class Criteria extends CriteriaElement
{
	public $column;
	public $operator;
	public $value;

	public function __construct($column, $value = '', $operator = '=')
	{
		$this->column = $column;
		$this->value = $value;
		$this->operator = $operator;
	}

	public function render()
	{
		if (!empty($this->column)) {
			$clause = $this->column . ' ' . $this->operator;
		}
		else {
			$clause = '';
		}

		if (isset($this->value)) {
			if ($this->column == '' && $this->operator == '') {
				$clause .= ' ' . trim($this->value);
			}
			else if (strtoupper($this->operator) == 'IN') {
				$clause .= ' ' . $this->value;
			}
			else {
				$clause .= ' \'' . jieqi_dbslashes(trim($this->value)) . '\'';
			}
		}

		return $clause;
	}

	public function renderWhere()
	{
		$ret = $this->render();
		$ret = $ret != '' ? 'WHERE ' . $ret : $ret;
		return $ret;
	}
}

?>
