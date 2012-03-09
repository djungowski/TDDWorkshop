<?php
namespace Storage;

class MysqlStorage implements StorageInterface
{
    protected $_table;
    protected $_className;
    protected $_dbHandler;
    
    public function __construct($table, $className)
    {
        $this->_table = $table;
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('class "' . $className . '" does not exist');
        }
        $this->_className = $className;
    }
    
    public function setDbHandler(\PDO $dbHandler)
    {
        $this->_dbHandler = $dbHandler;
    }
    
    /**
     * 
     * @return \PDO
     */
    private function getDbHandler()
    {
        if (!isset($this->_dbHandler)) {
            $this->_dbHandler = new \PDO("mysql:host=localhost;dbname=tddworkshop", "root", "");
            $this->_dbHandler->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return $this->_dbHandler;
    }
    
    public function load($id)
    {
        $query = $this->getDbHandler()->prepare('SELECT * FROM ' . $this->_table . ' WHERE id = :id');
        $query->bindValue(':id', $id);
        $query->setFetchMode(\PDO::FETCH_ASSOC);
        $query->execute();
        // we queried by id, so this basically should only return one set of data.
        $data = $query->fetch();
        return $this->toObject($data);
    }
    
    public function store($object)
    {
        
    }
    
    public function getAll()
    {
        $query = $this->getDbHandler()->prepare('SELECT * FROM ' . $this->_table);
        $query->setFetchMode(\PDO::FETCH_ASSOC);
        $query->execute();
        
        $result = Array();
        while (($row = $query->fetch()) !== FALSE) {
            $result[] = $this->toObject($row);
        }
        return $result;
    }
    
    protected function toObject($data)
    {
        $object = new $this->_className;
        
        foreach ($data as $key => $value) {
            $object->$key = $value;
        }
        
        return $object;
    }
}