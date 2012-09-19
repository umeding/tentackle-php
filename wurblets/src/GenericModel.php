<?php
/*
 * Reads a generic model file
 */

//include("AbstractModel.php");
define('GENERIC_MODEL_LOADED', true);

// Appendix value
class Appendix {
    var $name;
    var $av;

    public function __construct($name) {
        $this->name = $name;
        $this->av = array();
    }

    public function add($arg) {
        $this->av[] = $arg;
    }

    public function getName() {
        return($this->name);
    }

    public function getArg($pos) {
        if(isset($this->av[$pos])) {
            return($this->av[$pos]);
        } else {
            return("??empty??");
        }
    }

    public function __toString() {
        $sb = '@'.$this->name.'(';
        $delim = '';
        foreach($this->av as $a) {
            $sb .= $delim.$a;
            $delim = ', ';
        }
        $sb .= ')';
        return($sb);
    }
}


// appendix analyzer
define('FREF', "/(\\S+)\\s*\\(\\s*(\\S+)\\s*\\)\\s+(.*)/");
define('GREF1', "/(\\S+)\\s*\\(\\s*\\)\\s+(.*)/");
define('GREF2', "/(\\S+)\\s+(.*)/");
// expression analyzer
define('EREF', "/(\\S+)\\s*\\(([^)]*)\\)/");

class AppendixManager {
    private $name;   // appendix name
    public $globals;
    public $fields;

    public function __construct($name) {
        $this->name = $name;
        $this->globals = array();
        $this->fields = array();
    }

    public function __toString() {
        return("AppendixManager: {$this->name}\n");
    }

    public function parse($string) {
        $isGlobal = false;
        $fuckedUp = false;
        if(preg_match(FREF, $string, $matches) > 0) {
            $appendixName = $matches[1];
            $fieldName = $matches[2];
            $expr = trim("{$matches[3]}");
            if(strlen($expr) == 0)
                $fuckedUp = true;
        } else {
            if(preg_match(GREF1, $string, $matches) > 0) {
                $appendixName = $matches[1];
                $expr = $matches[2];
                if(strlen("$expr") == 0)
                    $fuckedUp = true;
                $isGlobal = true;
            } else {
                if(preg_match(GREF2, $string, $matches) > 0) {
                    $appendixName = $matches[1];
                    $expr = $matches[2];
                    if(strlen("$expr") == 0)
                        $fuckedUp = true;
                    $isGlobal = true;
                } else {
                    $fuckedUp = true;
                }
            }
        }

        if(!$fuckedUp) {
            if($isGlobal) {
                $parameters = &$this->globals;
            } else {
                $this->fields[$fieldName] = array();
                $parameters = &$this->fields[$fieldName];
            }

            // analyze the expression
            $snippets = preg_split("/[;]/", $expr);
            foreach($snippets as $snippet) {
                $snippet = trim($snippet);
                if(strlen("$snippet") == 0)
                    continue;
                if(preg_match(EREF, $snippet, $matches) > 0) {
                    $appendixValue = new Appendix($matches[1]);
                    $args = preg_split("/[,]/", $matches[2]);
                    foreach($args as $arg) {
                        $arg = trim($arg);
                        $appendixValue->add($arg);
                    }
                } else {
                    $appendixValue = new Appendix($snippet);
                }
                $parameters[] = $appendixValue;
            }
        } else {
            // ignore fucked appendixes
        }
    }
}

function methodNameCompare($s1, $s2) {
    $s1 = "".strtolower($s1);
    $s2 = "".strtolower($s2);
    if(0 == strcmp($s1, $s2))
        return(0);
    if(0 == strncmp($s1, "get", 3) || 0 == strncmp($s1, "set", 3))
        $xs1 = " ".substr($s1, 3);
    else
        $xs1 = $s1;
    if(0 == strncmp($s2, "get", 3) || 0 == strncmp($s2, "set", 3))
        $xs2 = " ".substr($s2, 3);
    else
        $xs2 = $s2;
    if(0 == strcmp($xs1, $xs2)) {
        return(strcmp($s1, $s2));
    }
    return(strcmp($xs1, $xs2));
}

define('APPENDIX_SNIPPET', '/[ (]/'); // ) stooopid get this wrong

class GenericModel extends AbstractWurblet {

    private $modelName;
    private $attrs;
    private $comments;
    public $appendixes;
    private $ncols;

    public function __construct() {
        parent::__construct();
        $this->attrs = array();
        $this->comments = array();
        $this->appendixes = array();

        $this->ncols = 4; // default to 4 cols
    }

    public function run() {
        parent::run();
        $this->loadModel();
    }

    // Set the model name
    public function setModelName($modelName) {
        $this->modelName = $modelName;
    }

    // Set the number of columns to read
    public function setNCols($ncols) {
        $this->ncols = $ncols;
    }

    /**
     *  Load the model.
     */
    public function loadModel() {

        if($this->modelName == null) {
            throw new Exception("model file not specified");
        }

        $fp = WurbUtil::openReader($this->modelName);
        while(!feof($fp)) {
            $line = trim(fgets($fp, 1000));
            if(strlen($line) == 0)
                continue;

            if($line[0] == '{' && $line[strlen($line)-1] == '}') {
                $line = trim(substr($line, 1, -1));
                $this->parseAppendix($line);
            } elseif($line[0] == '.') {
                $line = substr($line, 1);
                $this->parseAppendix($line);
            } elseif($line[0] == '#') {
                $comment = trim(substr($line, 1));
                // ignore ##
                if($comment[0] != '#') {
                    $this->comments[] = $comment;
                    // parse the content of the comment lines for
                    // appendixes
                    if($comment[0]=="{" && $comment[strlen($comment)-1]=="}") {
                        $comment = trim(substr($comment, 1, -1));
                        $this->parseAppendix($comment);
                    }
                }
            } elseif($line[0] == '[' && $line[strlen($line)-1] == ']') {
                // @todo: not sure what to do with this stuff here
                //        we may not need it
            } else {
                // attribute definition
                $type = '';
                $name = '';
                $description = '';
                // depending on the number of columns we parse the
                // model a little different.
                switch($this->ncols) {
                case 2: // 2 column model
                    sscanf($line, "%s %s %[^$]", $type, $name, $description);
                    $length = 0;
                    $dbname = $name;
                    break;
                case 4: // 4 column model
                    sscanf($line, "%s %s %s %s %[^$]", $type, $length, $name, $dbname, $description);
                    break;
                }
                $description = trim($description);
                // enumerate all the stuff
                $attrs = array(
                        'name' => $name,
                        'label' => $name.'_label',
                        'length' => $length,
                        'type' => $type,
                        'methodSuffix' => ucfirst($name),
                        'dbname' => trim($dbname),
                        'comment' => trim($description)
                        );
                $attrs = array_merge($attrs, $this->extractOptions($description));
                $this->attrs[] = $attrs;
            }
        }
        fclose($fp);
    }

    private function parseAppendix($string) {
        $parts = preg_split(APPENDIX_SNIPPET, $string);
        if(strlen($string) > 0 && count($parts) > 0) {
            if(false === isset($this->appendixes[$parts[0]])) {
                $appendix = new AppendixManager($parts[0]);
                $this->appendixes[$parts[0]] = $appendix;
            } else {
                $appendix = $this->appendixes[$parts[0]];
            }
            $appendix->parse($string);
        }
    }

    // get the appendix for a field
    public function getAppendix($section, $fieldName) {
        if($this->haveAppendix($section)) {
            $appendix = $this->getAppendixManager($section);
            $values = isset($appendix->fields["$fieldName"]) ? $appendix->fields["$fieldName"] : "";
            if(strlen("$values") == 0)
                $values = array();
            return($values);
        } else {
            return(array());
        }
    }

    // Get a particular appendix
    public function getParticularAppendix($section, $fieldName, $name) {
        foreach($this->getAppendix($section, $fieldName) as $appendix) {
            if(0 == strcmp($appendix->getName(), $name))
                return($appendix);
        }
        return(null);
    }

    // get the global appendix
    public function getGlobalAppendix($section) {
        if($this->haveAppendix($section)) {
            $appendix = $this->getAppendixManager($section);
            return($appendix->globals);
        } else {
            return(array());
        }
    }

    // Get something from the global appendix, return the default if
    // we can't find what we are lookgin for.
    public function getFromGlobalAppendix($section, $name, $defValue) {
        $apps = $this->getGlobalAppendix($section);
        foreach($apps as $app) {
            if(0 == strcmp($app->name, $name))
                return($app->av[0]);
        }
        return($defValue);
    }

    public function getValuesFromGlobalAppendix($section, $name) {
        $apps = $this->getGlobalAppendix($section);
        foreach($apps as $app) {
            if(0 == strcmp($app->name, $name))
                return($app);
        }
        return array();
    }


    // do we have a particular appendix
    public function haveAppendix($name) {
        return(null != $this->appendixes[$name]);
    }

    private function getAppendixManager($name) {
        if($this->haveAppendix($name)) {
            return($this->appendixes[$name]);
        } else {
            return(array());
        }
    }

    // Peel out the options from the field line
    private function extractOptions($text) {
        $attrs = array();
        if(preg_match('/\w*\[[^]]*\]/', $text, $matches, PREG_OFFSET_CAPTURE) > 0) {
            $string = substr($matches[0][0], 1, -1);
            // update the comment part
            $attrs['comment'] = trim(substr($text, 0, $matches[0][1]));
            $opts = preg_split('/[,;]/', $string);
            foreach($opts as $opt) {
                $key = strtolower(trim($opt));
                $attrs[$key] = true;
            }
        }
        return($attrs);
    }

    /**
     *  Get the list of attributes
     *  @return the attributes
     */
    public function getAttributeList() {
        return($this->attrs);
    }

    /**
     * Get the list of comments
     * @return the comments
     */
    public function getCommentList() {
        return($this->comments);
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

    // Add an extra attribute 
    public function addAttribute($type, $name, $length=0) {
        $attrs = array(
                'name' => $name,
                'label' => $name.'_label',
                'length' => $length,
                'type' => $type,
                'methodSuffix' => ucfirst($name),
                'getter' => 'get'.ucfirst($name),
                'setter' => 'set'.ucfirst($name),
                'dbname' => trim($name),
                'comment' => $name
                );
        $this->attrs[] = $attrs;
    }

// --------------------------------------------------------------------------
// Constraint access methods

    /**
     * Find a constraint of a particular type
     * @param name is the variable name
     * @param type is the constraint type
     */
    public function findConstraint($name, $type) {
        $appendixes = $this->getAppendix('constraint', $name);
        foreach($appendixes as $appendix) {
            if(0 == strcmp($appendix->name, $type)) {
                $parameter = array();
                foreach($appendix->av as $arg) {
                    $parameter[] = $arg;
                }
                $const['parameter'] = $parameter;
                return($const);
            }
        }
        return(null);
    }

// Method access methods

    public function getMethodDeclarations() {
        $names = array();
        foreach($this->appendixes as $k=>$v) {
            if(0 != strcmp("$k", "method"))
                continue;
            $am = $this->appendixes[$k];
            foreach($am->fields as $methodName=>$stuff) {
                $names[] = $methodName;
            }
        }
        usort($names, "methodNameCompare");
        return($names);
    }
    
    public function haveMethod($name) {
        $m = $this->getAppendix('method', $name);
        return(count($m) > 0);
    }

    public function getMethodReturnType($name) {
        $type = $this->getParticularAppendix('method', $name, 'return');
        if($type == null)
            $type = $this->getParticularAppendix('method', $name, 'returns');
        if($type == null)
            return("void");
        return($type->getArg(0));
    }

    public function haveMethodInvokationType($name, $hint) {
        $app = $this->getParticularAppendix('method', $name, 'type');
        if($app == null)
            return(false);
        foreach($app->av as $declared) {
            if(0 == strcmp("$declared", $hint))
                return(true);
        }
        return(false);
    }

    public function haveMethodThrows($name) {
        $throw = $this->getParticularAppendix('method', $name, 'throws');
        if($throw == null)
            $throw = $this->getParticularAppendix('method', $name, 'throw');
        return($throw != null);
    }

    public function getMethodThrows($name) {
        $throw = $this->getParticularAppendix('method', $name, 'throws');
        if($throw == null)
            $throw = $this->getParticularAppendix('method', $name, 'throw');
        if($throw == null)
            return(null);
        return($throw->getArg(0));
    }

    public function getMethodNumberOfArgs($name) {
        $in = $this->getParticularAppendix('method', $name, 'in');
        return(isset($in->av) ? count($in->av) : 0);
    }

    public function getMethodArgType($name, $pos) {
        $in = $this->getParticularAppendix('method', $name, 'in');
        $arg = $in->av[$pos];
        $arg = preg_split("/[\s]+/", $arg);
        return($arg[0]);
    }

    public function getMethodArgName($name, $pos) {
        $in = $this->getParticularAppendix('method', $name, 'in');
        $arg = $in->av[$pos];
        $arg = preg_split("/[\s]+/", $arg);
        return($arg[1]);
    }

    public function getMappedDataType($name, $mapping) {
        $app = $this->getParticularAppendix($mapping, $name, 'type');
        if($app == null)
            return($name);
        else
            return($app->av[0]);
    }

// --------------------------------------------------------------------------

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
