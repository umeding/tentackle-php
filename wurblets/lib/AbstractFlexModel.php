<?php

if(!defined(GLOBAL_MODEL_LOADED))
    require_once('GenericModel.php');

class AbstractFlexModel extends GenericModel {

    private $args;
    private $attrs;

    public function __construct() {
        parent::__construct();
        $attrs = array();
        $args = array();
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
        // @todo: this is too magic
        if(count($this->args) > 0) {
            $this->setModelName($this->args[0]);
        }
        // read the model now
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


    private $flexAttrs = array();
    protected function putFlexVar($attr, $var, $value) {
        $this->flexAttr[$attr][$var] = $value;
    }
    protected function haveFlexVar($attr, $var) {
        return(strlen("{$this->flexAttr[$attr][$var]}")>0);
    }
    protected function getFlexVar($attr, $var) {
        return($this->flexAttr[$attr][$var]);
    }
}
?>
