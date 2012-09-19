<?php

/*
 * Reads a model file and creates an object model
 */

require_once('AbstractWurblet.php');


/*
 * Implement this to define constraint definitons
 */
interface Constraint {
    public function getName();
    public function getExpr($name, $parameter);
}
// {{{ abstract constraint
abstract class ConstraintImpl implements Constraint {
    private	$constName;
    public function __construct($name) {
	$this->constName = $name;
    }

    public function getName() {
	return($this->constName);
    }

    public function __toString() {
	return("<<constraint: {$this->constName}>>");
    }
}
/// }}}

// {{{one of constraint
class Constraint_oneof extends ConstraintImpl {

    public function __construct() {
	parent::__construct('one of');
    }

    public function getExpr($name, $parameter) {
	return("(in_array($name, ".var_export($parameter, true)."))");
    }

}
//}}}
// {{{length constraint
class Constraint_length extends ConstraintImpl {
    public function __construct() {
	parent::__construct('length');
    }

    public function getExpr($name, $parameter) {
	return("(strlen($name)<{$parameter[0]})");
    }

}
//}}}
// {{{between constraint
class Constraint_between extends ConstraintImpl {
    public function __construct() {
	parent::__construct('between');
    }

    public function getExpr($name, $parameter) {
	if(count($parameter) != 2) {
	    throw new Exception("Exactly 2 parameter must be passed to the 'between; constraint");
	}
	return("(($name>={$parameter[0]})&&($name<={$parameter[1]}))");
    }

}
//}}}
// {{{lt constraint
class Constraint_lt extends ConstraintImpl {
    public function __construct() {
	parent::__construct('lt');
    }

    public function getExpr($name, $parameter) {
	if(count($parameter) != 1) {
	    throw new Exception("Exactly 1 parameter must be passed to the 'lt' constraint");
	}
	return("($name<{$parameter[0]})");
    }

}
//}}}
// {{{le constraint
class Constraint_le extends ConstraintImpl {
    public function __construct() {
	parent::__construct('le');
    }

    public function getExpr($name, $parameter) {
	if(count($parameter) != 1) {
	    throw new Exception("Exactly 1 parameter must be passed to the 'le' constraint");
	}
	return("($name<={$parameter[0]})");
    }

}
//}}}
// {{{eq constraint
class Constraint_eq extends ConstraintImpl {
    public function __construct() {
	parent::__construct('eq');
    }

    public function getExpr($name, $parameter) {
	if(count($parameter) != 1) {
	    throw new Exception("Exactly 1 parameter must be passed to the 'eq' constraint");
	}
	return("($name=={$parameter[0]})");
    }

}
//}}}
// {{{gt constraint
class Constraint_gt extends ConstraintImpl {
    public function __construct() {
	parent::__construct('gt');
    }

    public function getExpr($name, $parameter) {
	if(count($parameter) != 1) {
	    throw new Exception("Exactly 1 parameter must be passed to the 'gt' constraint");
	}
	return("($name>{$parameter[0]})");
    }

}
//}}}
// {{{ge constraint
class Constraint_ge extends ConstraintImpl {
    public function __construct() {
	parent::__construct('ge');
    }

    public function getExpr($name, $parameter) {
	if(count($parameter) != 1) {
	    throw new Exception("Exactly 1 parameter must be passed to the 'ge' constraint");
	}
	return("($name>={$parameter[0]})");
    }

}
//}}}
// {{{readonly constraint
class Constraint_readonly extends ConstraintImpl {
    public function __construct() {
	parent::__construct('readonly');
    }

    public function getExpr($name, $parameter) {
	throw new Exception("'readonly' constraint cannot be evaluated");
    }

}
//}}}
// {{{uppercase constraint
class Constraint_uppercase extends ConstraintImpl {
    public function __construct() {
	parent::__construct('uppercase');
    }

    public function getExpr($name, $parameter) {
	throw new Exception("'uppercase' constraint cannot be evaluated");
    }

}
//}}}
// {{{lowercase constraint
class Constraint_lowercase extends ConstraintImpl {
    public function __construct() {
	parent::__construct('lowercase');
    }

    public function getExpr($name, $parameter) {
	throw new Exception("'lowercase' constraint cannot be evaluated");
    }

}
//}}}
// {{{propercase constraint
class Constraint_propercase extends ConstraintImpl {
    public function __construct() {
	parent::__construct('propercase');
    }

    public function getExpr($name, $parameter) {
	throw new Exception("'propercase' constraint cannot be evaluated");
    }

}
//}}}
// {{{required constraint
class Constraint_required extends ConstraintImpl {
    public function __construct() {
	parent::__construct('required');
    }

    public function getExpr($name, $parameter) {
	throw new Exception("'required' constraint cannot be evaluated");
    }

}
//}}}
// {{{url constraint
class Constraint_url extends ConstraintImpl {
    public function __construct() {
	parent::__construct('url');
    }

    public function getExpr($name, $parameter) {
	throw new Exception("'url' constraint cannot be evaluated");
    }

}
//}}}
// {{{default constraint
class Constraint_default extends ConstraintImpl {
    public function __construct() {
	parent::__construct('default');
    }

    public function getExpr($name, $parameter) {
	throw new Exception("'default' constraint cannot be evaluated");
    }

}
//}}}
// {{{callback constraint
class Constraint_callback extends ConstraintImpl {
    public function __construct() {
	parent::__construct('callback');
    }

    public function getExpr($name, $parameter) {
	throw new Exception("'callback' constraint cannot be evaluated");
    }

}
//}}}
// {{{validator constraint
class Constraint_validator extends ConstraintImpl {
    public function __construct() {
	parent::__construct('validator');
    }

    public function getExpr($name, $parameter) {
	throw new Exception("'validator' constraint cannot be evaluated");
    }

}
//}}}


class AbstractModel extends AbstractWurblet {

    private $modelName;
    private $attrs;
    private $contraints;
    private $dbstuff;

    public function __construct() {
        parent::__construct();

        $this->attrs = array();
        $this->constraints = array();
        $this->dbstuff = array();
    }

    public function setModelName($modelName) {
        $this->modelName = $modelName;
    }

    public function getModelName() {
        return($this->modelName);
    }

    /**
     *  Load the model.
     */
    public function run() {
        parent::run();

        if($this->modelName == null) {
            throw new Exception("attribute file not specified");
        }

        $fp = WurbUtil::openReader($this->modelName);
        while(!feof($fp)) {
            $line = trim(fgets($fp, 200));

            if(strlen($line)>0 && $line[0] != '#') {
                if(0 == strncmp($line, '.constraint', 11)) {
                    // set up a constraint
                    $this->setupConstraint($this->constraints, $line);
                } else if(0 == strncmp($line, '.db', 3)) {
                    // set up a constraint
                    $this->setupDBStuff($line);
                } else if($line[0] == '.') {
                    // ignore any other line starting with a '.'
                } else {
                    // attribute definition
                    $type = '';
                    $name = '';
                    $description = '';
                    sscanf($line, "%s %s %s %[^$]", $type, $name, $label, $description);
                    $description = trim($description);
                    $this->attrs[] = array(
                            'name' => $name,
                            'type' => $type,
                            'methodSuffix' => ucfirst($name),
                            'getter' => 'get'.ucfirst($name),
                            'setter' => 'set'.ucfirst($name),
                            'label' => trim($label),
                            'comment' => trim($description)
                            );
                }
            }
        }
        fclose($fp);
        //var_dump($this->dbstuff);
    }

    private function setupDBStuff($line) {
        $db = '';
        $cmd = '';
        $args = '';
        sscanf($line, "%s %s %[^$]", $db, $cmd, $args);
        switch($cmd) {
            case 'mapall':
                foreach($this->getAttributeList() as $attrItem) {
                    $attr = &$this->findAttribute($attrItem['name']);
                    $attr['dbname'] = $attr['name'];
                    $this->dbstuff['map'][$name] = $name;
                }
                break;
            case 'map':
                $name = '';
                $dbname = '';
                sscanf($args, "%s %s", $name, $dbname);
                if($attr = &$this->findAttribute($name)) {
                    // do a specific mapping
                    $attr['dbname'] = $dbname;
                    $this->dbstuff['map'][$name] = $dbname;
                }
                break;
            case 'include':
                $include = '';
                sscanf($args, "%s", $include);
                $fp = WurbUtil::openReader($include);
                $contents = stream_get_contents($fp);
                fclose($fp);
                $this->dbstuff['include'][] = $contents;
                break;
            case 'primary':
                $primary = '';
                sscanf($args, "%[^$]", $primary);
                $this->dbstuff['primary'] = $primary;
                break;
            case 'foreign':
                $foreign = '';
                sscanf($args, "%[^$]", $foreign);
                $this->dbstuff['foreign'][] = $foreign;
                break;
            case 'autoincr':
                $autoincr = '';
                sscanf($args, "%s", $autoincr);
                $this->dbstuff['autoincr'] = $autoincr;
                break;
            case 'table':
                $tablename = '';
                sscanf($args, "%s", $tablename);
                $this->dbstuff['table'] = $tablename;
                break;
            case 'index':
                $indexname = '';
                $columns = '';
                sscanf($args, "%s %[^$]", $indexname, $columns);
                $this->dbstuff['index'][$indexname] = $columns;
                break;
        }
    }

    private function setupConstraint(&$constraints, $line) {
        if(0 == strncmp($line, '.constraint', 11)) {
            $from = strpos($line, '(');
            $to = strpos($line, ')');
            $variable = trim(substr($line, $from+1, $to-$from-1));
            $expr = trim(substr($line, $to+1));

            $from = strpos($expr, '(');
            $to = strrpos($expr, ')');
            if($from == null) {
                $constraint = trim($expr);
                $array_values = trim(substr($expr, $from, $to-$from));
            } else {
                $constraint = trim(substr($expr, 0, $from));
                $array_values = trim(substr($expr, $from+1, $to-$from-1));
            }

            // clean up the cvalues we get from the user,
            // make the strings so we can parse them 
            $array_args = preg_split('/,/', $array_values);
            $array_values = "";
            $delim = "";
            foreach($array_args as $array_arg) {
                if(!($array_arg[0] == "'" || $array_arg[0] == '"'))
                    $array_arg = "'".$array_arg."'";

                $array_values .= $delim.$array_arg;
                $delim = ",";
            }

            $parameter = 'return array('.$array_values.');';
            $class = "Constraint_$constraint";

            $parameter = eval($parameter);

            $inst = new $class();

            if(!isset($constraints[$variable])) $constraints[$variable] = array();
            $cv = &$constraints[$variable];
            if(!isset($cv[$constraint])) $cv[$constraint] = array();
            $const = &$cv[$constraint];

            if(isset($const['parameter'])) {
                $const['parameter'] = array_merge($const['parameter'], $parameter);
            } else {
                $const['parameter'] = $parameter;
            }

            if(!isset($const['inst'])) {
                $const['inst'] = $inst;
            }
        } 
    }

    /**
     *  Get the list of attributes
     *  @return the attributes
     */
    public function getAttributeList() {
        return($this->attrs);
    }

    /**
     *  Find a particular attribute definition
     *  @param $name we are looking for
     *  @return the name (or null)
     */
    public function &findAttribute($name) {
        foreach($this->attrs as &$attr) {
            if($attr['name'] == $name) {
                return($attr);
            }
        }
        return(null);
    }

    /**
     *  Get the list of constraints
     *  @return the constraints
     */
    public function getConstraints() {
        return($this->constraints);
    }

    /**
     *  Find a constraint by name and type
     *  @param $name is the name of the variable
     *  @param $type is the type name of the constraint
     *  @return the constraint (or null)
     */
    public function findConstraint($name, $type) {
        $consts = $this->constraints[$name];
        if($consts == null) {
            return(null);
        }
        return($consts[$type]);
    }

    /**
     *  Get a constraints name
     *  @param $const is the constraint name
     *  @return the name
     */
    public function getConstraintName($const) {
        $name = $const['inst']->getName();
        return($name);
    }

    /**
     *  Evaluate a constraint
     *  @param $varname is the variable name
     *  @param $const is the constraint
     *  @return the code text
     */
    public function evalConstraint($varname, $const) {
        $val = $const['inst']->getExpr($varname, $const['parameter']);
        return($val);
    }

    /**
     *  Get the DB stuff
     *  @return the db stuff
     */
    public function getDBStuff() {
        return($this->dbstuff);
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


    // utility functions
    // isIdentifier: starts with char, then char, number, underscore
    public function isIdentifier($string) {
        $string = strtoupper($string);
        if($string[0] < 'A' || $string[0] > 'Z') {
            return(false);
        }
        $len = strlen($string);
        for($i=0; $i<$len; $i++) {
            switch($string[$i]) {
                case 'A': case 'B': case 'C': case 'D': case 'E':
                case 'F': case 'G': case 'H': case 'I': case 'J':
                case 'K': case 'L': case 'M': case 'N': case 'O':
                case 'P': case 'Q': case 'R': case 'S': case 'T':
                case 'U': case 'V': case 'W': case 'X': case 'Y':
                case 'Z': case '0': case '1': case '2': case '3':
                case '4': case '5': case '6': case '7': case '8':
                case '9': case '_':
                    break;
                default: 
                    return(false);
            }
        }
        return(true);
    }
}
?>
