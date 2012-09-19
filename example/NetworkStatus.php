<?php

/*
 * @{
 * tablename = network_statuses
 *
 * types     = $sysmodelpath/DatatypePHP.map
 * decls     = $sysmodelpath/NetworkStatus.map
 * mapping   = build/models/NETWORK_STATUS_DBO_MAPPING
 * @}
 */

// @wurblet $mapping IncludeMappings $types $decls




class NetworkStatus extends DbObject {  

    public function __construct(Db $db = NULL) {
        parent::__construct($db);
    }

    public function __toString() {
        return $this->id.":".$this->interfaceName."@".$this->when."=>".$this->rxRelativeBytes;
    }

// ==================================================================
//   ADD "select list", "select unique" or "delete by" calls here
//   For example:
// ==================================================================


    // @wurblet selectByInterface PHPDbSelectList $mapping interfaceName -limit -offset


    // @wurblet deleteByInterface PHPDbDeleteBy $mapping interfaceName


    
// ==================================================================
//   DON'T CHANGE ANYTHING BELOW UNLESS YOU KNOW WHAT YOU ARE DOING
// ==================================================================

// @wurblet get_table_name PHPGetTablename $tablename


// @wurblet declare PHPDeclare $mapping


// @wurblet methods PHPMethods $mapping


// @wurblet fieldnames PHPDbFieldNames $mapping


    /**
     * Get the fields from the database
     * @param $rs is the result set
     */
    public function getFields(ResultSet $rs) {
        // @wurblet getfields PHPDbGetFields $mapping


    }

     
    /**
     * Set the fields into the prepared statement
     * @param $st is the prepared statement
     */
    public function setFields(PreparedStatement $st) {
        // @wurblet setfields PHPDbSetFields $mapping


    }

    /**
     * Prepare a database insert statement for this class
     * @return the statement identifier
     */
    public function prepareInsertStatement() {
        // @wurblet insert PHPDbInsert $mapping


        return $stmtId;
    }

    /**
     * Prepare a database update statement for this class
     * @return the statement identifer
     */
    public function prepareUpdateStatement() {
        // @wurblet update PHPDbUpdate $mapping


        return $stmtId;
    }

// ===============================================================
// THE JSON CONVERSIONS FOR THIS CLASS

// @wurblet jsonSerializer PHPJsonSerializer $mapping


}
?>
