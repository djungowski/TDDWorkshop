<?php
namespace Config;

class Web
{
    private $_config;
    
    public function __construct($configFile)
    {
        $this->_config = parse_ini_file($configFile, true);
    }
    
    public function get($section = null)
    {
        if (is_null($section)) {
           return $this->_config; 
        }
        if (isset($this->_config[$section])) {
            return $this->_config[$section];
        }
        return null;
    }
}