--
-- Copyright (c) 2009 Meding Software Technik - All Rights Reserved
--
-- SQL template file
--

--
-- @{
-- tablename = TABLENAME
-- types     = $sysmodelpath/util/DatatypeSQL.map
-- decls     = $sysmodelpath/sm/Measurement.map
-- mapping   = build/models/MEASUREMENT_CPP_MAPPING
-- @}
--

-- @wurblet $mapping IncludeMappings $types $decls -line-comment=--


-- @wurblet $tablename SQLCreateTable $mapping



