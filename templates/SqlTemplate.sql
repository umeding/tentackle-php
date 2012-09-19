--
-- Copyright (c) 2009 Meding Software Technik - All Rights Reserved
--
-- SQL template file
--

--
-- @{
-- typespath = $sysmodelpath/skynet
-- types     = $typespath/DatatypeSQL.map
-- modelpath = $sysmodelpath/timeseries
-- decls     = $modelpath/Measurement.map
-- mapping   = build/models/MEASUREMENT_CPP_MAPPING
-- @}
--

-- @wurblet $mapping IncludeMappings $types $decls -line-comment=--


-- @wurblet uwe_measurements SQLCreateTable $mapping



