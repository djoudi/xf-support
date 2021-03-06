<?php namespace Robbo\Support;

use XenForo_DataWriter as DW;

class DataWriterField {

	protected $_name;

	protected $_table;

	protected $_definition = array();

	public function __construct($name, $table)
	{
		$this->_name = $name;
		$this->_table = $table;
	}

	protected function _addDefinition($key, $value)
	{
		$this->_definition[$key] = $value;

		return $this;
	}

	public function setIncrements()
	{
		return $this->uint()->autoIncrement();
	}

	public function setDefault($value)
	{
		return $this->_addDefinition('default', $value);
	}

	public function setAllowedValues($values)
	{
		$values = is_array($values) ? $values : func_get_args();

		return $this->_addDefinition('allowedValues', $values);
	}

	public function getName()
	{
		return $this->_name;
	}

	public function getTable()
	{
		return $this->_table;
	}

	public function toArray()
	{
		return $this->_definition;
	}

	public function __call($method, $args)
	{
		if (method_exists($this, 'set'.ucwords($method)))
		{
			return call_user_func_array(array($this, 'set'.ucwords($method)), $args);
		}

		$types = array(
			'uint' 		=> DW::TYPE_UINT,
			'uintForced' => DW::TYPE_UINT_FORCED,
			'string' 	=> DW::TYPE_STRING,
			'serialized' => DW::TYPE_SERIALIZED,
		);

		if (isset($types[$method]))
		{
			return $this->_addDefinition('type', $types[$method]);
		}

		$youCantHandleTheTruth = array(
			'required', 'autoIncrement'
		);

		if (in_array($method, $youCantHandleTheTruth))
		{
			return $this->_addDefinition($method, true);
		}

		$className = get_class($this);
		throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
	}
}
