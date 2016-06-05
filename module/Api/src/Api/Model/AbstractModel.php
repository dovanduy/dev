<?php

namespace Api\Model;

use Exception;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Log;
use Application\Lib\Arr;

/**
 * AbstractModel
 *
 * @package 	Model
 * @created 	2015-08-24
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
abstract class AbstractModel {

    /** @var array $errorCodeValidation Error validation */
    public static $errorCodeValidation = array();

    const ERROR_CODE_INVALED_PARAMETER = 400;
    const ERROR_CODE_AUTH_ERROR = 403;
    const ERROR_CODE_FIELD_NOT_EXIST = 1010;
    const ERROR_CODE_FIELD_DUPLICATE = 1011;
    const ERROR_CODE_OTHER_1 = 1021;
    const ERROR_CODE_OTHER_2 = 1022;
    const ERROR_CODE_OTHER_3 = 1023;
    const ERROR_CODE_OTHER_4 = 1024;
    const ERROR_CODE_OTHER_5 = 1025;
    
    const RETURN_TYPE_BOOLEAN = 'BOOLEAN';
    const RETURN_TYPE_ALL = 'ALL';
    const RETURN_TYPE_MULTIPLE_RESULTSET = 'MULTIPLE_RESULTSET';
    const RETURN_TYPE_ONE = 'ONE';
    const RETURN_TYPE_GENNERATE_VALUE = 'GENERATED_VALUE';
    const RETURN_TYPE_NONE = 'NONE';
    
    /** @var array $properties Table columns */
    protected static $properties;
    
    /** @var array $properties Table primary keys */
    protected static $primaryKey = array('id');
    
    /** @var object $db Zend\Db\Adapter\Adapter */
    protected static $defaultDb;
    
    /** @var object $db Zend\Db\Adapter\Adapter */
    protected static $db;

     /** @var object $sm Zend\ServiceManager\ServiceManager */
    protected static $sm;
    
    /**
     * Constructor
     * @author thailh 
     * return void
     */
    public function __construct($defaultDb = null, $sm = null) {        
        if (static::$defaultDb === null) {
            static::$defaultDb = $defaultDb;
        }
        if (static::$sm === null && $sm !== null) {
            static::$sm = $sm;
        }
    }

     /**
     * Get adapter database
     * @author thailh 
      * return Zend\Db\Adapter\Adapter
     */
    public static function getDb() {
        if (static::$db !== null) {           
            return static::$db;
        }
        return static::$defaultDb;
    }
    
    /**
     * Set adapter database
     * @author thailh 
      * return Zend\Db\Adapter\Adapter
     */
    public static function setDb($db) {
        static::$db = $db;
    }
    
    /**
	 * Fetches a property description array, or specific data from it
	 *
	 * @param   string  property or property.key
	 * @param   mixed   return value when key not present
	 * @return  mixed
	 */
	public static function property($key, $default = null)
	{
		return isset(static::$properties[$key]) ? static::$properties[$key] : $default;
	}
    
    /**
     * Function to set value for error_code cause INVALED_PARAMETER.
     * @param string $name Field of data (or not use this argument).
     * @param string $value The value of field.
     * @author thailh 
     */
    public static function errorParamInvalid($field = '', $value = '') {
        static::$errorCodeValidation[] = array(
            'code' => self::ERROR_CODE_INVALED_PARAMETER,
            'field' => $field,
            'value' => $value,
        );
    }

    /**
     * Function to set value for error_code cause FIELD_NOT_EXIST.
     * @param string $name Field of data.
     * @param string $value The value of field (or not use this argument).
     * @author thailh 
     */
    public static function errorNotExist($field = '', $value = '') {
        static::$errorCodeValidation[] = array(
            'code' => self::ERROR_CODE_FIELD_NOT_EXIST,
            'field' => $field,
            'value' => $value,
        );
    }

    /**
     * Function to set value for error_code cause FIELD_DUPLICATE.
     * @param string $name Field of data.
     * @param string $value The value of field (or not use this argument).
     * @author thailh 
     */
    public static function errorDuplicate($field, $value = '') {
        static::$errorCodeValidation[] = array(
            'code' => self::ERROR_CODE_FIELD_DUPLICATE,
            'field' => $field,
            'value' => $value,
        );
    }

    /**
     * Function to set value for error_code cause others.
     * @param string $code Input code.
     * @param string $name Field of data (or not use this argument).
     * @param string $value The value of field (or not use this argument).
     * @author thailh 
     */
    public static function errorOther($code, $field = null, $value = '') {
        static::$errorCodeValidation[] = array(
            'code' => $code,
            'field' => $field,
            'value' => $value,
        );
    }

    /**
     * Set error
     *
     * @author thailh 
     * @return void
     */
    public static function setError($error) {
        static::$errorCodeValidation = $error;
    }

    /**
     * Function to set value for errorCodeValidation.
     *
     * @author thailh 
     * @return array Returns the array.
     */
    public static function error($reset = false) {
        $errors = static::$errorCodeValidation;
        if ($reset === true) {
            static::$errorCodeValidation = array();
        }
        return $errors;
    }

    /**
     * Function to format date.
     * @param int $date Input date.
     * @author thailh 
     * @return int Returns integer.
     */
    public static function dateFromVal($date) {
        return strtotime($date);
    }

    /**
     * Function to format date time.
     * @param int $date Input date.
     * @author thailh 
     * @return int Returns the integer.
     */
    public static function dateToVal($date) {
        return strtotime($date . '23:59:59');
    }

    /**
     * Function to format date time.
     *
     * @param int $date Input time.
     * @author thailh
     * @return int Returns the integer.
     */
    public static function time2Val($time) {
        return strtotime($time);
    }
    
    /**
     * Convert date format to time value
     *
     * @param string $date Input date time.
     * @author thailh
     * @return int Returns the integer.
     */
    public static function str2time($datetime) {
        return strtotime($datetime);
    }
    
    /**
     * Get current date
     *
     * @author thailh
     * @return string Returns current date
     */
    public static function now() {
        return date('Y-m-d H:i');
    }
    
    /**
     * Get current date
     *
     * @author thailh
     * @return string Returns current date
     */
    public static function unix_timestamp() {
        return time();
    }
    
     /**
     * Convert result to array
     *     
     * @param object $result Result of select query       
     * @author thailh
     * @return array Result array
     */    
    public static function toArray($result) {       
        $data = array();
        while ($row = $result->current()) {
            if (!empty($row)) {
                $data[] = (array) $row;
            }
            $result->next();
        }
        return $data;
    }
    
     /**
     * Get offset by page
     *     
     * @param int $page Page   
     * @param int $limit Limit 
     * @author thailh
     * @return int Offset
     */
    public static function getOffset($page = 1, $limit = 10) {
        if (empty($page)) {
            $page = 1;
        }
        $offset = ($page - 1) * $limit;
        return $offset;
    }
    
    /**
	 * Count the number of records in query, without LIMIT or OFFSET applied.
	 * @return  integer
	 */
	public static function count($sql) {
        $sql = trim($sql);
        if (stripos($sql, 'SELECT') !== 0) {
            return false;
        }

        if (stripos($sql, 'LIMIT') !== false) {
            // Remove LIMIT from the SQL
            $sql = preg_replace('/\sLIMIT\s+[^a-z]+/i', ' ', $sql);
        }

        if (stripos($sql, 'OFFSET') !== false) {
            // Remove OFFSET from the SQL
            $sql = preg_replace('/\sOFFSET\s+\d+/i', '', $sql);
        }

        // Get the total rows from the last query executed
        $result = static::getDb()->query("SELECT COUNT(*) AS total_rows FROM ({$sql}) AS counted_results")
                ->execute()
                ->current();       

        // Return the total number of rows from the query
        return (int) $result['total_rows'];
    }

    public static function column($sql, $columnName) {
        $sql = trim($sql);
        if (stripos($sql, 'SELECT') !== 0) {
            return false;
        }

        if (stripos($sql, 'LIMIT') !== false) {
            // Remove LIMIT from the SQL
            $sql = preg_replace('/\sLIMIT\s+[^a-z]+/i', ' ', $sql);
        }

        if (stripos($sql, 'OFFSET') !== false) {
            // Remove OFFSET from the SQL
            $sql = preg_replace('/\sOFFSET\s+\d+/i', '', $sql);
        }

        // Get the total rows from the last query executed
        $data = static::getDb()->query("SELECT {$columnName} FROM ({$sql}) AS results")
                ->execute(); 
        $result = array();
        if (!empty($data)) {
            $columns = explode(',', $columnName);
            foreach ($data as $row) {
                foreach ($columns as $name) { 
                    //$name = trim($name);
                    if (!isset($result[$name])) {
                        $result[$name] = array();
                    }
                    if (!empty($row[$name]) && !in_array($row[$name], $result[$name])) {
                        $result[$name][] = $row[$name];
                    }
                } 
            }           
        }
        // Return the total number of rows from the query
        return $result;
    }
    
    /**
	 * Quote a value for an SQL query.
	 *
	 * @param	string	$string	the string to quote
	 * @return	string	the quoted value
	 */
	public static function quote($string) {
        if (is_array($string)) {
            foreach ($string as $k => $s) {
                if ($s instanceof \Zend\Db\Sql\Predicate\Expression) {
                    $string[$k] = $s->getExpression();
                } else {
                    $string[$k] = static::quote($s);
                }
            }
            return $string;
        }
        return static::getDb()->platform->quoteValue($string);
    }

    /**
	 * Execute a select query.
	 *
	 * @param	string	$sql select string
	 * @return	Array	Result 
	 */
    public static function query($sql, $getTotalRows = false) {
        $statement = static::getDb()->query($sql);         
        $data = static::toArray($statement->execute());
        if ($getTotalRows == true) {           
            return array(
                'count' => static::count($statement->getSql()),
                'data' => $data               
            );
        }
        return $data;
    }
    
    /**
     * Response result by type
     *     
     * @param object $result Zend\Db\Adapter\Driver\Pdo\Result 
     * @param string $returnType See declare
     * @author thailh
     * @return mixed Return sp result
     */
    public static function response($result, $returnType = self::RETURN_TYPE_ALL) 
    {
        switch ($returnType) {
            case self::RETURN_TYPE_ALL:
                return static::toArray($result);
                break;
            case self::RETURN_TYPE_ONE:
                $row = $result->current();
                return !empty($row) ? (array) $row : array();
                break;
            case self::RETURN_TYPE_GENNERATE_VALUE:
                return $result->getGeneratedValue();
                break;
            default:
                return $result ? true : false;
        }
        return false;  
    }
            
     /**
     * Execute stored procedure
     *     
     * @param string $spName SP name
     * @param array $param Array sp param
     * @author thailh
     * @return mixed Return sp result
     */
    public static function spQuery($spName, $param = array(), $returnType = self::RETURN_TYPE_ALL) {
        $stmt = static::getDb()->createStatement();
        $spParam = array();
        foreach ($param as $name => $value) {
            $spParam[] = ':' . $name;
        }
        $spParam = !empty($spParam) ? implode(',', $spParam) : '';
        $sql = "CALL {$spName}({$spParam});";
        $stmt->prepare($sql);
        $result = $stmt->execute($param);
        if ($returnType == self::RETURN_TYPE_MULTIPLE_RESULTSET) {
            $data = array();
            do {
                try {
                    $resultSet = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
                    if (!empty($resultSet)) {
                        $data[] = $resultSet;
                    }
                } catch (\Exception $e) {
                    break;
                }
            }
            while ($result->getResource()->nextRowset());
            return $data;
        }
        return self::response($result, $returnType);
    }
    
     /**
     * Execute select query
     *     
     * @param string $selectString Select query string    
     * @author thailh
     * @return array Return data array
     */
    public static function selectQuery($selectString) {
        $data = static::getDb()->query($selectString, Adapter::QUERY_MODE_EXECUTE); 
        return $data;
    }
 
     /**
     * Basic find record in database
     *     
     * @param array $options Options Where and order by ...
     * @param boolean $getOne True for get one record
     * @author thailh
     * @return array Return data array if success otherwise false
     */
    public static function find($options = array(), $returnType = self::RETURN_TYPE_ALL) {
        if (empty($options['table'])) {
            $options['table'] = static::$tableName; 
        }
        $sql = new Sql(static::getDb());
        $select = $sql->select()
            ->from($options['table']);       
        if (!empty($options['where'])) {
            $where = array();
            foreach ($options['where'] as $property => $value) {
                if (in_array($property, static::$properties)) {
                    $where[$property] = $value;               
                }
            } 
            if (!empty($where)) {
                $select->where($where);
            }
        }  
        if (!empty($options['order'])) {
            $select->order($options['order']); 
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = static::getDb()->query($selectString, Adapter::QUERY_MODE_EXECUTE); 
        if ($result->count() > 0) {
            return self::response($result, $returnType);            
        }
        return array();
    }
    
     /**
     * Insert a record to database
     *     
     * @param array $values Value for insert    
     * @author thailh
     * @return int | boolean Auto ID if success otherwise false
     */
    public static function insert($values, $tableName = '') { 
        $sql = new Sql(static::getDb());    
        foreach ($values as $property => $value) {
            if (!in_array($property, static::$properties)) {
                unset($values[$property]);                
            }
        }
        if (empty($values)) {
            static::errorParamInvalid();
            return false;
        }
        if (in_array('updated', static::$properties)) {
            $values['updated'] = new Expression('UNIX_TIMESTAMP()');
        }
        if (in_array('created', static::$properties)) {
            $values['created'] = new Expression('UNIX_TIMESTAMP()');
        }
        if (empty($tableName)) {
            $tableName = static::$tableName;
        }
        $insert = $sql->insert()
                ->into($tableName)
                ->columns(array_keys($values))
                ->values($values);
        $insertString = $sql->getSqlStringForSqlObject($insert);
        Log::info('Insert SQL', $insertString);
        $result = static::getDb()->query($insertString, Adapter::QUERY_MODE_EXECUTE);
        return $result->getGeneratedValue();
    }
    
    /**
     * Update a record in database
     *     
     * @param array $options Option set array & where array for update  
     * @author thailh
     * @return boolean True if success otherwise false
     */
    public static function update($options = array()) {
        if (empty($options['table'])) {
            $options['table'] = static::$tableName; 
        }
        $sql = new Sql(static::getDb());        
        $set = array();
        $where = array();
        foreach (static::$properties as $property) {
            if (isset($options['set'][$property])) {               
                $set[$property] = $options['set'][$property];
            }
            if (isset($options['where'][$property])) {               
                $where[$property] = $options['where'][$property];
            }
        }
        if (empty($set) || empty($options['where'])) {
            static::errorParamInvalid();
            return false;
        }       
        if (in_array('updated', static::$properties)) {
            $set['updated'] = new Expression('UNIX_TIMESTAMP()'); 
        }
        $update = $sql->update()
                ->table($options['table'])
                ->set($set)
                ->where($where);
        $updateString = $sql->getSqlStringForSqlObject($update);
        Log::info('Update SQL', $updateString);
        if (static::getDb()->query($updateString, Adapter::QUERY_MODE_EXECUTE)) {
            return true;
        }
        return false;
    }
    
    /**
     * batchInsert
     *     
     * @param array $data Data for insert/update
     * @param array $updates Data for update if duplicate keys
     * @param boolean $ignore Ignore duplicate or not
     * @author thailh
     * @return boolean True if success otherwise false
     */
    public static function batchInsert($data, $updates = array(), $ignore = true, $tableName = '') {
        if (empty($data)) {
            return false;
        }
        if (empty($data[0])) {
            $data = array($data);
        }
        if (!empty($ignore)) {
            $ignore = 'IGNORE';
        }        
        if (empty($tableName)) {
            $tableName = static::$tableName;
        }
        $inserts = $field = array();
        $data = static::quote($data); 
        foreach ($data as $i => $row) {
            $insert = array();
            foreach ($row as $key => $val) {
                if ($i == 0) {
                    $field[] = "`{$key}`";
                }                
                $insert[] = $val;
            }
            $inserts[] = "(" . implode(',', $insert) . ")";
        }
        if (!empty($inserts)) {            
            $sql = " INSERT {$ignore} INTO {$tableName} (" . implode(",", $field) . ")";
            $sql .= " VALUES " . implode(",", $inserts);
            if (!empty($updates)) {
                $updates = static::quote($updates);
                $updateSQL = array();                
                foreach ($updates as $field => $value) {                   
                    $updateSQL[] = "`{$field}`={$value}";
                }
                $sql .= " ON DUPLICATE KEY UPDATE " . implode(",", $updateSQL);
            }
            Log::info('BatchInsert SQL', $sql);
            if (static::getDb()->query($sql, Adapter::QUERY_MODE_EXECUTE)) {
                return true;
            }            
        }
        return false;
    }
    
    /**
     * Delete a record in database
     *     
     * @param array $options Option set array & where array for update  
     * @author thailh
     * @return boolean True if success otherwise false
     */
    public static function delete($options = array()) {
        if (empty($options['table'])) {
            $options['table'] = static::$tableName; 
        }
        $sql = new Sql(static::getDb());        
//        $where = array();
//        foreach (static::$properties as $property) {            
//            if (isset($options['where'][$property])) {               
//                $where[$property] = $options['where'][$property];
//            }
//        }
        $where = $options['where'];
        if (empty($where)) {
            static::errorParamInvalid();
            return false;
        } 
        $delete = $sql->delete()
                ->from($options['table'])                
                ->where($where);
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        Log::info('Delete SQL', $deleteString);
        if (static::getDb()->query($deleteString, Adapter::QUERY_MODE_EXECUTE)) {
            return true;
        }
        return false;
    }
    
    /**
     * Create condition for sp
     *     
     * @param array $options Option set array & where array for update  
     * @author thailh
     * @return boolean True if success otherwise false
     */
    public static function spCondition($name = array(), $param = array()) {
        $result = array();
        foreach ($param as $key => $value) {
            if (in_array($key, $name) && isset($value) && $value !== '') {
                if (!is_numeric($value) && is_string($value)) {
                    $result[] = $key . ':' . self::quote('%' . $value . '%');
                } else {
                    $result[] = $key . '=' . self::quote($value);
                }
            }
        }
        return !empty($result) ? '{' . implode(',', $result) . '}' : '';
    }
    
    /**
     * Create parameter for sp
     *     
     * @param array $spParam List params for SP 
     * @param array $param List params from request 
     * @author thailh
     * @return array List params
     */
    public static function spParameter($spParam = array(), $param = array()) {
        $result = array();
        foreach ($spParam as $key => $value) {
            if (isset($param[$key])) {
                $result[$key] = $param[$key];
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    
    /**
     * ON/OFF a field
     *     
     * @param array $param field, value and _id 
     * @author thailh
     * @return boolean True if success otherwise false
     */
    public static function updateOnOff($param) {   
        if (empty($param['field'])) {
            $param['field'] = 'active';
        }
        if (!self::update(array(
            'set' => array($param['field'] => $param['value']),
            'where' => array(
                '_id' => $param['_id']
            ),
        ))) {
            return false;
        }  
        return true;
    }
    
    public function updateSort($param) {
        if (is_array(self::$primaryKey)) {
            self::errorParamInvalid();
            return false;
        }
        if (empty($param['sort'])) {
            self::errorParamInvalid();
            return false;
        }
        $param['sort'] = \Zend\Json\Decoder::decode($param['sort'], \Zend\Json\Json::TYPE_ARRAY);        
        $values = array();
        foreach ($param['sort'] as $id => $sort) {
            $values[] = array(
                self::$primaryKey => $id,
                'sort' => $sort
            ); 
        }
        return self::batchInsert(
            $values,
            array(
                'sort' => new Expression('VALUES(`sort`)'),
            ),
            false
        );
    }
    
    public function max($options) {
        if (empty($options['field'])) {
            return false;
        }
        if (empty($options['default'])) {
            $default = 0;
        }
        if (empty($options['table'])) {
            $options['table'] = static::$tableName; 
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->columns(array(                
                new Expression("MAX({$options['field']}) AS max_value")
            ))
            ->from($options['table']);    
        if (!empty($options['where'])) {
            $where = array();
            foreach ($options['where'] as $property => $value) {
                if (in_array($property, static::$properties)) {
                    $where[$property] = $value;               
                }
            } 
            if (!empty($where)) {
                $select->where($where);
            }
        }
        $sqlString = $sql->getSqlStringForSqlObject($select);        
        $result = self::response(
            static::selectQuery($sqlString), 
            self::RETURN_TYPE_ONE
        );       
        return !empty($result['max_value']) ? $result['max_value'] : $default;
    }
    
}
