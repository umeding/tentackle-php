<?php

require_once('AbstractModel.php');

class AbstractAttributes extends AbstractModel {

    private $enableConstraintChecks;
    private $createAccess;
    private $createInterface;
    private $readOnly;
    private $protection;

    public function __construct() {
	parent::__construct();

	$this->enableConstraintChecks = false;
	$this->createAccess = true;
	$this->createInterface = false;
	$this->readOnly = false;
	$this->protection = 'private';
    }


    /**
     *  Load the model.
     */
    public function run() {

	$av = $this->getContainer()->getArgs();
	$ac = count($av);
	for($i = 0; $i < $ac; $i++) {
	    switch($av[$i]) {
		case '-i':
		case '--just-interface':
		    $this->createInterface = true;
		    break;
		case '-r':
		case '--readonly':
		    $this->readOnly = true;
		    break;
		case '-a':
		case '--declare-as':
		    $this->protection = $av[++$i];;
		    break;
		case '-d':
		case '--just-decls':
		    $this->createAccess = false;
		    break;
		case '-c':
		case '--enable-constraints':
		    $this->enableConstraintChecks = true;
		    break;
		default:
		    $this->setModelName($av[$i]);
		    break;
	    }
	}
	if($this->createInterface) {
	    $this->createAccess = false;
	}

	// read the model now
	parent::run();
    }

    public function needBeanAccess() {
	return($this->createAccess);
    }

    public function needConstraints() {
	return($this->enableConstraintChecks);
    }

    public function justInterface() {
	return($this->createInterface);
    }

    public function readOnly() {
	return($this->readOnly);
    }

    public function protection() {
	return($this->protection);
    }

}
?>
