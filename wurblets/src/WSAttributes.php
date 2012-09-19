<?php

require_once('AbstractModel.php');

/**
 * WS attributes
 */
class WSAttributes extends AbstractModel {

    private $javaClass;
    private $contextDbFunc;

    public function __construct() {
        parent::__construct();

        // if the max number of rows is this number we
        // consider the search limited
        $this->max = 1000;
    }


    /**
     *  Load the model.
     */
    public function run() {

        $av = $this->getContainer()->getArgs();
        $ac = count($av);
        for($i = 0; $i < $ac; $i++) {
            if(0 == strncmp($av[$i], '--java-class=', 13)) {
                $this->javaClass = substr($av[$i], 13);
            } else if(0 == strncmp($av[$i], '--context-db-func=', 18)) {
                $this->contextDbFunc = substr($av[$i], 18);
            } else {
                $this->setModelName($av[$i]);
            }
        }

        if(!isset($this->javaClass)) {
            throw new Exception("No class name defined, use '--java-class <name>'");
        }
        if(!isset($this->contextDbFunc)) {
            throw new Exception("No context db func name defined, use '--context-db-func <name>'");
        }

        // read the model now
        parent::run();
    }

    public function getJavaClassName() {
        return($this->javaClass);
    }

    public function getContextDBFunc() {
        return($this->contextDbFunc);
    }

    public function getGuardName() {
        $guardname = $this->getContainer()->getProperty("wurblet", "guardname");
        if(!isset($guardname)) {
            throw new Exception("can't determine guardname");
        }
        return($guardname);
    }

    public function getClassName() {
        return($this->getGuardName());
    }

}
?>
