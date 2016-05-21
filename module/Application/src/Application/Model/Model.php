<?php

namespace Application\Model;

class Model {    
    
    public function __construct($array = array())
    {
        if (is_array($array)) {
            foreach ($array as $field => $value) {
                $this->{$field} = $value;
            }
        }
    }
    
    public function exchangeArray($array)
    {
       if (is_array($array)) {
            foreach ($array as $field => $value) {
                $this->{$field} = $value;
            }
        }
    }
    
    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
