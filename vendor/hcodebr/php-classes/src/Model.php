<?php
namespace Hcode;

class Model {
    private $values = [];

    public function __call($name, $arguments)
    {
        $method = substr($name, 0, 3);
        $fieldName = substr($name, 3, strlen($name));

        switch ($method) {
            case 'get':
                return $this->values[$fieldName];
            break;
            
            default:
                $this->values[$fieldName] = $arguments[0];
            # code...
                break;
        }
    }
    
    public function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->{"set$key"}($value);
        }
    }
    
    public function getValues()
    {
        return $this->values;
    }
}