<?php
// wurblet generated by Wurbiler, see http://www.wurblizer.org for more details.

require_once('AbstractWurblet.php');
require_once('AbstractFlex.php');

class PHPDbInsert extends AbstractFlex {
  public function run() {
    parent::run();

/* vim: set filetype=php :*//*<?php*/
    $ac = $this->getArgCount();
    if($ac != 1)
        throw new Exception("usage: wurblet <guard> PHPDbInsert <modelname>");

    $modelFile = WurbUtil::translateVars($this->getArg(0), $this->getContainer()->getProperties(PROPSPACE_WURBLET));

    // fetch the model
    $model = $this->newGeneric4ColModelInstance($modelFile);
    
    fwrite($this->out,$this->source[0]); //  $stmtId = $this->ge...
    foreach($model->getAttributeList() as $attr) {
        if(!isset($attr['nomethod'])) {
            $fieldName = "self::\$FIELD_".strtoupper($attr['name']);
            fwrite($this->out,$this->source[1]); //  .
            fwrite($this->out,$fieldName);
            fwrite($this->out,$this->source[2]); // .',' 
        }
    }
    fwrite($this->out,$this->source[3]); //  .DbObject::$FIELD_I...
    foreach($model->getAttributeList() as $attr) {
        if(!isset($attr['nomethod'])) {
            $fieldName = "self::\$FIELD_".strtoupper($attr['name']);
            fwrite($this->out,$this->source[4]); //  .'?, ' 
        }
    }
    fwrite($this->out,$this->source[5]); //  .'?, ' .'?)'); $thi...
/*?>*/
  }
}
?>
