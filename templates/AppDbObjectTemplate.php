<?php

/*
 * @{
 * tablename = SQL_TABLENAME
 *
 * typespath = $sysmodelpath/skynet
 * types     = $typespath/DatatypePHP.map
 * modelpath = $sysmodelpath/timeseries
 * decls     = $modelpath/Measurement.map
 * mapping   = build/models/MEASUREMENT_CPP_MAPPING
 * @}
 */

// @wurblet $mapping IncludeMappings $types $decls


set_include_path("..");
require_once("tentackle/tentackle.php");

class PHP_CLASSNAME extends DbObject {   // <<< FILL_THIS

    public function __construct(Db $db = NULL) {
        parent::__construct($db);
    }

    public function __toString() {
        return "Some String"; // <<< FILL_THIS
    }

// ==================================================================
//   ADD "select list", "select unique" or "delete by" calls here
//   For example:
// ==================================================================

    // \@wurblet selectSomethingUnique PHPDbSelectUnique $mapping field...  

    // \@wurblet selectSomeList PHPDbSelectList $mapping field...  
    // possible options: -limit -offset -orderByClause=...

    // \@wurblet deleteBySomthing PHPDbDeleteBy $mapping field...  

    
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
