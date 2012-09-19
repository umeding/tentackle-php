<?php

if(!defined(GLOBAL_MODEL_LOADED))
    require_once('GenericModel.php');

class CPPGenericModel extends GenericModel {
    private var $knownTypes;

    // Map for the c++ types
    public function getMappedDataType($name, $mapping) {
        if($this->knownTypes == null) {
            $this->knownTypes['String'] = "const char*"; // not sure if we want const
            $this->knownTypes['void'] = "void";
            $this->knownTypes['byte'] = "unsigned char";
        }
        if(isset($this->knownTypes[$name])) {
            $mapped = $this->knownTypes[$name];
        } else {
            $mapped = parent::getMappedDataType($name, $mapping);
        }
        return($mapped);

    }
}
?>
