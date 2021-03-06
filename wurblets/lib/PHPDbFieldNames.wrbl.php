<?php
// wurblet generated by Wurbiler, see http://www.wurblizer.org for more details.

require_once('AbstractWurblet.php');
require_once('AbstractFlex.php');

class PHPDbFieldNames extends AbstractFlex {
  public function run() {
    parent::run();

/* vim: set filetype=php :*//*<?php*/
    $ac = $this->getArgCount();
    if($ac != 1)
        throw new Exception("usage: wurblet <guard> PHPFieldNames <modelname>");

    $modelFile = WurbUtil::translateVars($this->getArg(0), $this->getContainer()->getProperties(PROPSPACE_WURBLET));

    // fetch the model
    $model = $this->newGeneric4ColModelInstance($modelFile);
    
    fwrite($this->out,$this->source[0]); //  private static $col...
    foreach($model->getAttributeList() as $attr) {
        if(!isset($attr['nomethod'])) {
            $type = $model->getMappedDataType($attr['type'], 'php');
            $line = sprintf("%-10s %-20s %s", $type, $attr['name'], $attr['comment']);
            fwrite($this->out,$this->source[1]); //  public static $FIEL...
            fwrite($this->out,strtoupper($attr['name']));
            fwrite($this->out,$this->source[2]); //  = '
            fwrite($this->out,$attr['dbname']);
            fwrite($this->out,$this->source[3]); // '; private static $C...
            fwrite($this->out,strtoupper($attr['name']));
            fwrite($this->out,$this->source[4]); // ; 
        }
    }
    fwrite($this->out,$this->source[5]); //  /** * Get a field v...
    foreach($model->getAttributeList() as $attr) {
        if(!isset($attr['nomethod'])) {
            $type = $model->getMappedDataType($attr['type'], 'php');
            $line = sprintf("%-10s %-20s %s", $type, $attr['name'], $attr['comment']);

            fwrite($this->out,$this->source[6]); //  case '
            fwrite($this->out,$attr['name']);
            fwrite($this->out,$this->source[7]); // ': return $this->
            fwrite($this->out,$attr['name']);
            fwrite($this->out,$this->source[8]); // ; 
        }
    }
    fwrite($this->out,$this->source[9]); //  default: throw new ...
/*?>*/
  }
}
?>
