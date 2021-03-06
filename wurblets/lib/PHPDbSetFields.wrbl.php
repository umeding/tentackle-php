<?php
// wurblet generated by Wurbiler, see http://www.wurblizer.org for more details.

require_once('AbstractWurblet.php');
require_once('AbstractFlex.php');

class PHPDbSetFields extends AbstractFlex {
  public function run() {
    parent::run();

/* vim: set filetype=php :*//*<?php*/
    $ac = $this->getArgCount();
    if($ac != 1)
        throw new Exception("usage: wurblet <guard> PHPDbSetFields <modelname>");

    $modelFile = WurbUtil::translateVars($this->getArg(0), $this->getContainer()->getProperties(PROPSPACE_WURBLET));

    // fetch the model
    $model = $this->newGeneric4ColModelInstance($modelFile);
    
    fwrite($this->out,$this->source[0]); //  $ndx = 0; 
    foreach($model->getAttributeList() as $attr) {
        if(!isset($attr['nomethod'])) {
            $fieldName = "self::\$FIELD_".strtoupper($attr['name']);
            $colName = "self::\$COLUMN_".strtoupper($attr['name']);

            switch($attr['type']) {
                case 'Timestamp':
                    $getvar = '$this->'.$attr['name'].'?$this->'.$attr['name'].'->getSQLDate() : \'0000-00-00 00:00:00\'';
                    $setfunc = 'setValue';
                    break;

                case 'String':
                    $getvar = '$this->'.$attr['name'];
                    $setfunc = 'setStringValue';
                    break;

                default:
                    $getvar = '$this->'.$attr['name'];
                    $setfunc = 'setValue';
                    break;
            }
            fwrite($this->out,$this->source[1]); //  $st->
            fwrite($this->out,$setfunc);
            fwrite($this->out,$this->source[2]); // (++$ndx, 
            fwrite($this->out,$getvar);
            fwrite($this->out,$this->source[3]); // ); 
        }
    }
    fwrite($this->out,$this->source[4]); //  $st->setValue(++$nd...
/*?>*/
  }
}
?>
