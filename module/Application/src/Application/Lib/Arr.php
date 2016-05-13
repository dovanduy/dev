<?php

namespace Application\Lib;

/**
 * Array utility
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class Arr {

    /**
	 * Gets a dot-notated key from an array, with a default value if it does
	 * not exist.
	 *
	 * @param   array   $array    The search array
	 * @param   mixed   $key      The dot-notated key or array of keys
	 * @param   string  $default  The default value
	 * @return  mixed
	 */
	public static function get($array, $key, $default = null)
	{
		if ( ! is_array($array) and ! $array instanceof \ArrayAccess)
		{
			throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
		}

		if (is_null($key))
		{
			return $array;
		}

		if (is_array($key))
		{
			$return = array();
			foreach ($key as $k)
			{
				$return[$k] = static::get($array, $k, $default);
			}
			return $return;
		}

		is_object($key) and $key = (string) $key;

		if (array_key_exists($key, $array))
		{
			return $array[$key];
		}

		foreach (explode('.', $key) as $key_part)
		{
			if (($array instanceof \ArrayAccess and isset($array[$key_part])) === false)
			{
				if ( ! is_array($array) or ! array_key_exists($key_part, $array))
				{
					return $default;
				}
			}

			$array = $array[$key_part];
		}

		return $array;
	}

	/**
	 * Set an array item (dot-notated) to the value.
	 *
	 * @param   array   $array  The array to insert it into
	 * @param   mixed   $key    The dot-notated key to set or array of keys
	 * @param   mixed   $value  The value
	 * @return  void
	 */
	public static function set(&$array, $key, $value = null)
	{
		if (is_null($key))
		{
			$array = $value;
			return;
		}

		if (is_array($key))
		{
			foreach ($key as $k => $v)
			{
				static::set($array, $k, $v);
			}
		}
		else
		{
			$keys = explode('.', $key);

			while (count($keys) > 1)
			{
				$key = array_shift($keys);

				if ( ! isset($array[$key]) or ! is_array($array[$key]))
				{
					$array[$key] = array();
				}

				$array =& $array[$key];
			}

			$array[array_shift($keys)] = $value;
		}
	}
    
    /**
     * Method key_value - filter array with key and value   
     *  
     * @author thailh
     * @param array $arr Array need to filter
     * @param string $key Key to filter
     * @param string $value Value to filter
     * @return array Array after filtering
     */
    public static function keyValue($arr, $key, $value) {
        $result = array();
        if ($arr) {
            foreach ($arr as $item) {
                $result[$item[$key]] = $item[$value];
            }
        }
        return $result;
    }

    /**
     * Method key_values - filter array with key   
     *  
     * @author thailh
     * @param array $arr Array need to filter
     * @param string $key Key to filter
     * @return array Array after filtering
     */
    public static function keyValues($arr, $key) {
        $result = array();
        if ($arr) {
            foreach ($arr as $item) {
                $result[$item[$key]] = $item;
            }
        }
        return $result;
    }

    /**
     * Method field - filter array by field   
     *  
     * @author thailh
     * @param array $arr Array need to filter
     * @param string $field Field need to filter
     * @param bool $toString If true will return string, otherwise return an array
     * @return array/string Array/String after filtering
     */
    public static function field($arr, $field, $toString = false) {
        $result = array();
        if ($arr) {
            foreach ($arr as $item) {
                $result[] = $item[$field];
            }
        }
        $result = array_unique($result);
        if ($toString) {
            $result = implode(',', $result);
        }
        return $result;
    }

    /**
     * Method filter - filter array by field and value   
     *  
     * @author thailh
     * @param array $arr Array need to filter
     * @param string $field Field need to filter
     * @param string $value Value need to filter
     * @param bool $count If true will return array which including count number
     * @return array Array after filtering
     */
    public static function filter($arr, $field, $value, $count = false, $keepKey = true) {
        $result = array();
        $lenght = 0;
        if ($arr) {
            if ($keepKey) {
                foreach ($arr as $key => $item) {
                    if ($item[$field] == $value) {
                        $result[$key] = $item;
                        $lenght++;
                    }
                }
            } else {
                foreach ($arr as $item) {
                    if ($item[$field] == $value) {
                        $result[] = $item;
                        $lenght++;
                    }
                }
            }
        }
        if ($count) {
            return array($lenght, $result);
        }
        return $result;
    }
    
    /**
     * Method search - check if found an array by field and value   
     *  
     * @author thailh
     * @param array $arr Array need to search
     * @param string $field Field need to filter
     * @param string $value Value need to filter
     * @return bool
     */
    public static function search($arr, $field, $value) {
        return !empty(static::filter($arr, $field, $value)) ? true : false;
    }

    /**
     * Method count - count if found an array by field and value   
     *  
     * @author thailh
     * @param array $arr Array need to count
     * @param string $field Field to filter
     * @param string $value Value to filter
     * @return int
     */
    public static function count($arr, $field, $value) {
        $result = static::filter($arr, $field, $value);
        return !empty($result) ? count($result) : 0;
    }
    
    /**
     * Convert array to value array
     *    
     * @author thailvn
     * @param array $arr 2D input array
     * @param string $key Field key  
     * @return array  
     */
    public static function arrayValues($arr, $key) {
        $result = array();
        if ($arr) {
            foreach ($arr as $item) {
                $result[] = $item[$key];
            }
        }
        return $result;
    }

    /**
     * Method filter - filter array by field and value   
     *  
     * @author thailh
     * @param array $arr Array need to filter
     * @param string $field Field need to filter
     * @param string $value Value need to filter
     * @param bool $count If true will return array which including count number
     * @return array Array after filtering
     */
    public static function multiSearch($arr, $arrayCheck) {
        $result = array();
        $lenght = 0;
        if ($arr) {
            foreach ($arr as $item) {
                $lenght = 0;
                foreach($arrayCheck as $key => $value){
                    if ($item[$key] == $value) {
                        $lenght++;
                    }
                }
                if($lenght == sizeof($arrayCheck)){
                    return true;
                }
            }
        }
        return false;
    }
    
    public static function rand($arr, $num = 0) {
        if ($num == 0) {
            $num = count($arr);
        }
        $keys = array_keys($arr);
        shuffle($keys);
        $r = array();   
        for ($i = 0; $i < $num; $i++) {
            $r[] = $arr[$keys[$i]];
        }
        return $r;
    }

    public static function sort($array, $on, $order = SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }
           
            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }   
    
    public static function mergeKeyValue($array1, $array2, $array2Into1 = true){
        if ($array2Into1 == true) {
            foreach ($array2 as $k => $v) {
                $array1[$k] = $v; 
            }
            return $array1;
        } else {
            foreach ($array1 as $k => $v) {
                $array2[$k] = $v; 
            }
            return $array2;
        }        
    }
    
    /**
     * Sum if found an array by field and value   
     *  
     * @author thailh
     * @param array $arr Array need to sum
     * @param string $field Field to sum
     * @return int
     */
    public static function sum($arr, $field) {
        $result = 0;
        foreach ($result as $row) {
            if (!empty($row[$field])) {
                $result += $row[$field];
            }
        }
        return $result;
    }
    
}
