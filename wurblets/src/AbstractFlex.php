<?php

if(!defined('GENERIC_MODEL_LOADED'))
    require_once("GenericModel.php");

class AbstractFlex extends AbstractWurblet {

    private $args;
    private $attrs;

    public function __construct() {
        parent::__construct();
        $this->attrs = array();
        $this->args = array();
    }

    private function extractArg($arg) {
        if($arg[0] != '-') {
            return(false);
        }
        $pos = strpos($arg, '=');
        if($pos === false) {
            $name = substr($arg, 1);
            $value = true;
        } else {
            $name = substr($arg, 1, $pos-1);
            $value = substr($arg, $pos+1);
            $last = strlen($value)-1;
            if($value[0] == $value[$last]) {
                switch($value[0]) {
                    case '"': case "'":
                              $value = substr($value, 1, $last-1);
                              break;
                }
            }
        }
        $this->attrs[$name] = $value;
        return(true);
    }

    /**
     *  Load the model.
     */
    public function run() {

        $av = $this->getContainer()->getArgs();
        $ac = count($av);
        for($i = 0; $i < $ac; $i++) {
            if(!$this->extractArg($av[$i])) {
                $this->args[] = $av[$i];
            }
        } 

        // run the parent 
        parent::run();

    }

    protected function formatParameter($name, $prefix="", $suffix="") {
        if(isset($this->attrs[$name])) {
            return($name."=\"".$prefix.$this->attrs[$name].$suffix."\"");
        } else {
            return("");
        }
    }
    
    protected function haveParameter($name) {
        return(isset($this->attrs[$name]));
    }

    protected function getParameter($name) {
        return($this->attrs[$name]);
    }

    protected function getArgCount() {
        return(count($this->args));
    }

    protected function getArg($pos) {
        if(isset($this->args[$pos])) {
            return($this->args[$pos]);
        } else {
            return(null);
        }
    }

    protected function newGenericModelInstance($modelName) {
        return($this->newGeneric4ColModelInstance($modelName));
    }

    protected function newGeneric4ColModelInstance($modelName) {
        $gm = new GenericModel();
        $gm->setModelName($modelName);
        $gm->setNCols(4);
        $gm->loadModel();
        return($gm);
    }

    protected function newGeneric2ColModelInstance($modelName) {
        $gm = new GenericModel();
        $gm->setModelName($modelName);
        $gm->setNCols(2);
        $gm->loadModel();
        return($gm);
    }

    public function getGuardName() {
        $guardname = $this->getContainer()->getProperty("wurblet", "guardname");
        if(!isset($guardname)) {
            throw new Exception("can't determine guardname");
        }
        return($guardname);
    }
}
?>
