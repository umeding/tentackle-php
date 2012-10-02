<?php
// wurblet generated by Wurbiler, see http://www.wurblizer.org for more details.

require_once('AbstractWurblet.php');
require_once('AbstractFlex.php');

class PHPDbGetFields extends AbstractFlex {
  public function run() {
    parent::run();

/* vim: set filetype=php :*//*<?php*/
    $ac = $this->getArgCount();
    if($ac != 1)
        throw new Exception("usage: wurblet <guard> PHPDbGetFields <modelname>");

    $modelFile = WurbUtil::translateVars($this->getArg(0), $this->getContainer()->getProperties(PROPSPACE_WURBLET));

    // fetch the model
    $model = $this->newGeneric4ColModelInstance($modelFile);
    
    foreach($model->getAttributeList() as $attr) {
        if(!isset($attr['nomethod'])) {
            $fieldName = "self::\$FIELD_".strtoupper($attr['name']);
            $colName = "self::\$COLUMN_".strtoupper($attr['name']);

            switch($attr['type']) {
            case 'Timestamp':
                $assignment = 'Timestamp::valueOf($rs->getValueByName('.$fieldName.'))';
                break;

            default:
                $assignment = '$rs->getValueByName('.$fieldName.')';
                break;
            }
            fwrite($this->out,$this->source[0]); //  $this->
            fwrite($this->out,$attr['name']);
            fwrite($this->out,$this->source[1]); //  = 
            fwrite($this->out,$assignment);
            fwrite($this->out,$this->source[2]); // ; 
        }
    }
    fwrite($this->out,$this->source[3]); //  $this->setId($rs->g...
/*?>*/
  }
}
?>
