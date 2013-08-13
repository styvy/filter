<?php

class Filter implements ArrayAccess
{
	const PLACEHOLDER_VALUE = '#value';
	
	protected $_data = array();
	
	protected $_filters = array();
	protected $_all_filters = array();
	
	
	
	/**
	 * return new filter object
	 * 
	 * @param array		$data
	 * 
	 * @return object
	 */
	public static function create(array $data)
	{
		return new self($data);
	}
	
	public function __construct(array $data)
	{
		$this->_data = $data;
	}
	
	
	/**
	 * ArrayAccess methods
	 */
	public function offsetSet($offset, $value)
	{
		throw new Exception('Filter objects are read-only');
	}
	
	public function offsetUnset($offset)
	{
		throw new Exception('Filter objects are read-only');
	}
	
	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]);
	}
	
	public function offsetGet($offset)
	{
		return $this->_data[$offset];
	}
	
	
	/**
	 * return data array
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	}
	
	
	/**
	 * filters checker
	 * 
	 * @param array 	$filters
	 * 
	 * @return array
	 */
	protected function _check_filters(array $filters)
	{
		$_filters = array();
		
		foreach($filters as $filter)
		{
			if(is_string($filter))
			{
				$params = array();
			}
			else if(is_array($filter) && count($filter))
			{
				$params = !empty($filter[1]) && is_array($filter[1]) ? $filter[1] : array();
				$filter = $filter[0];
			}
			else continue;
			
			if(empty($filter)) continue;
			
			$_filters[] = array($filter, $params);
		}
		
		return $_filters;
	}
	
	
	/**
	 * merge filters
	 * 
	 * @return array
	 */
	protected function _merge_filters()
	{
		$filters = array();
		
		foreach($this->_data as $field => $value)
		{
			if(isset($this->_filters[$field]))
			{
				$filters[$field] = array_merge($this->_all_filters, $this->_filters[$field]);
			}
			else
			{
				$filters[$field] = $this->_all_filters;
			}
		}
		
		return $filters;
	}
	
	
	/**
	 * add filter rule for all fields
	 * 
	 * @param array 	$filters
	 * @return $this
	 */
	public function all(array $filters)
	{
		$this->_all_filters = $this->_check_filters($filters);
		
		return $this;
	}
	
	
	/**
	 * add filter rule
	 * 
	 * @param  string|array  $fields 	
	 * @param  array  $filters
	 * @return $this
	 */
	public function add($fields, array $filters)
	{
		if( !is_array($fields)) $fields = array($fields);
		
		$filters = $this->_check_filters($filters);
		
		foreach($fields as $field)
		{
			if( !isset($this->_data[$field])) continue;
			
			$this->_filters[$field] = $filters;
		}
		
		return $this;
	}
	
	
	/**
	 * execute filters
	 * 
	 * @return $this
	 */
	public function filter()
	{
		$filters = $this->_merge_filters();
		
		foreach($this->_data as $field => $value)
		{
			if(isset($filters[$field]))
			{
				foreach($filters[$field] as $filter)
				{
					list($filter, $params) = $filter;
					
					if( !is_string($filter))
						throw new Exception("Filter: Filter name must be a string.");
					
					// find value placeholder
					if(empty($params))
					{
						$params = array($value);
					}
					else
					{
						foreach($params as $key => $param)
							if(self::PLACEHOLDER_VALUE === $param) $params[$key] = $value;
					}
					
					// execute filter
					if(false === strpos($filter, '::'))
					{
						if( !is_callable($filter))
							throw new Exception("Filter: Function '{$filter}' not found.");
						
						$value = call_user_func_array($filter, $params);
					}
					else
					{
						list($class, $method) = explode('::', $filter, 2);
						
						if( !method_exists($class, $method))
							throw new Exception("Filter: Method '{$method}' not found in class '{$class}'.");
						
						$method = new ReflectionMethod($class, $method);
						
						$value = $method->invokeArgs(NULL, $params);
					}
				}
			}
			
			$this->_data[$field] = $value;
		}
		
		return $this;
	}
}
