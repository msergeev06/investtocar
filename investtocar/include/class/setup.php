<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarSetup
	{
		public static $arMessage = array ();

		public function CreateTableDB (/*$tableName, $arFields, $primaryKey*/)
		{
			global $DB;

			$arClasses = array (
				"CInvestToCarCars",
				"CInvestToCarFuel",
				"CInvestToCarTs",
				"CInvestToCarMain",
				"CInvestToCarOdo",
				"CInvestToCarOther",
				"CInvestToCarPoints",
				"CInvestToCarRepairParts",
				"CInvestToCarSetup"
			);

			$arTables = array();
			foreach ($arClasses as $class) {
				if (method_exists($class,"CreateTables")) {
					$arTables = array_merge($arTables,call_user_func(array($class, 'CreateTables')));
				}
			}

			/*
			$queryTableTables = self::QueryTableTables();
			$res = $DB->Query($queryTableTables);

			foreach ($arTables as $table) {
				$res = $DB->Query($table);
			}
			*/
			//echo "<pre>"; print_r($arTables); echo "</pre>";
		}

		public function RestoreDataFromFile ($date="") {
			global $DB,$OPTIONS;
			if ($date=="") return false;

			$backupPath = INVESTTOCAR_INCLUDE_PATH."backup/";
			$backupFile = strval($date).".php";
			require_once ($backupPath.$backupFile);

		}

		public function ExportDataTablesDB () {
			$arClasses = array (
				"CInvestToCarCars",
				"CInvestToCarFuel",
				"CInvestToCarTs",
				"CInvestToCarMain",
				"CInvestToCarOdo",
				"CInvestToCarOther",
				"CInvestToCarPoints",
				"CInvestToCarRepairParts",
				"CInvestToCarSetup"
			);

			$arData = array();
			foreach ($arClasses as $class) {
				if (method_exists($class, "DataTables")) {
					$arData = array_merge($arData,call_user_func(array($class, 'DataTables')));
				}
			}

			$arTableTables = self::DataTableTables();

			$backupPath = INVESTTOCAR_INCLUDE_PATH."backup/";
			$backupFile = time().".php";
			$hFile = fopen ($backupPath.$backupFile,"w");
			fwrite($hFile,"<?php\n");
			fwrite($hFile,"\t\$DB_prefix = \$OPTIONS->GetOptionString(\"DB_table_prefix\");\n");
			fwrite($hFile,"\n");
			fwrite($hFile,"\t//TABLE: setup_tables\n");
			fwrite($hFile,self::WriteDataIntoFile("setup_tables",$arTableTables));
			fwrite($hFile,"\n");
			foreach ($arData as $table=>$arDat) {
				fwrite($hFile,"\t//TABLE: ".CInvestToCarMain::GetTableByCode($table,false)."\n");
				fwrite($hFile,self::WriteDataIntoFile(CInvestToCarMain::GetTableByCode($table,false),$arDat));
				fwrite($hFile,"\n");
			}
			fclose($hFile);
		}

		public function ClearTableDB ($table) {
			global $DB;

			$query = "SELECT * FROM `".$table."`";
			if ($res = $DB->Select($query)) {
				foreach ($res as $arRes) {
					$query2 = "DELETE FROM `".$table."` WHERE `".$table."`.`id` = ".$arRes["id"];
					$res2 = $DB->Delete($query2);
				}
			}
		}

		public function WriteDataIntoFile ($table, $arData) {
			$sFile = "";
			if (isset($arData["FIELDS"])) {
				$sFile .= "\tCInvestToCarSetup::ClearTableDB(\$DB_prefix.\"".$table."\");\n";
				$bFirst = true;
				$sFields = "";
				foreach ($arData["FIELDS"] as $sField) {
					if ($bFirst) {
						$sFields .= "`".$sField."` ";
						$bFirst = false;
					}
					else {
						$sFields .= ", `".$sField."` ";
					}
				}
				foreach ($arData["DATA"] as $arDat)
				{
					$bFirst = true;
					$sFile .= "\t\$query = \"INSERT INTO `\".\$DB_prefix.\"".$table."` (";
					$sFile .= $sFields.") VALUES (";
					for ($i = 0; $i < count ($arDat); $i++)
					{
						if ($bFirst)
						{
							$sFile .= "'".addslashes($arDat[$i])."'";
							$bFirst = false;
						}
						else
						{
							$sFile .= ", '".addslashes($arDat[$i])."'";
						}
					}
					$sFile .= ");\";\n";
					$sFile .= "\t\$res = \$DB->Insert(\$query);\n";
				}
			}
			return $sFile;
		}

		public function AddDataToTable ($table="", $arData=array()) {
			global $DB;
			if (empty($arData) || $table=="") return false;

			if (isset($arData["DATA"])) {
				$bFirst = true;
				$sFields = "";
				foreach ($arData["FIELDS"] as $sField) {
					if ($bFirst) {
						$sFields .= "`".$sField."` ";
						$bFirst = false;
					}
					else {
						$sFields .= ", `".$sField."` ";
					}
				}
				$bFirst = true;
				foreach ($arData["DATA"] as $arDat) {
					$query = "INSERT INTO `".$table."` (";
					$query .= $sFields.") VALUES (";
					for ($i=0; $i<count($arDat); $i++) {
						if ($bFirst) {
							$query .= "'".$arDat[$i]."'";
							$bFirst = false;
						}
						else {
							$query .= ", '".$arDat[$i]."'";
						}
					}
					$query .= ");";
					$res = $DB->Insert($query);
				}
			}


		}

		public function CreateTables () {
			$arTables = array();
			$arTables[] = self::QueryTableCarBody();
			$arTables[] = self::QueryTableCarBrand();
			$arTables[] = self::QueryTableCarGearbox();
			$arTables[] = self::QueryTableCarModel();
			$arTables[] = self::QueryTableFuelMark();
			$arTables[] = self::QueryTableFlowType();
			$arTables[] = self::QueryTablePointsType();
			$arTables[] = self::QueryTableReasonReplacement();
			//$arTables[] = self::QueryTableTables();
			$arTables[] = self::QueryTableWhoPaid();
			$arTables[] = self::QueryTableParams();

			return $arTables;
		}

		public function QueryTableCarBody () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("body")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "`sort` INT (11) NOT NULL DEFAULT 500, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableCarBrand () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("brand")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableCarGearbox () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("gearbox")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "`sort` INT (11) NOT NULL DEFAULT 500, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableCarModel () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("model")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`brand` INT (11) NOT NULL, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableFuelMark () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("fuelmark")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`sort` INT (11) NOT NULL DEFAULT 500, ";
			$query .= "`short_name` VARCHAR(255) NOT NULL, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableFlowType () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("flowtype")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "`sort` INT (11) NOT NULL DEFAULT 500, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTablePointsType () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("pointtype")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`sort` INT (11) NOT NULL DEFAULT 500, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "`default` TINYINT (1) NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableReasonReplacement () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("reason")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "`sort` INT (11) NOT NULL DEFAULT 500, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableStorage () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("storage")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "`sort` INT (11) NOT NULL DEFAULT 500, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableTables () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("tables")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`table` VARCHAR(255) NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableWhoPaid () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("whopaid")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`code` VARCHAR(255) NULL, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "`sort` INT (11) NOT NULL DEFAULT 500, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableParams () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("params")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`option` VARCHAR(255) NOT NULL, ";
			$query .= "`value` VARCHAR(255) NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function DataTables () {
			$arData = array();
			$arData["body"] = self::DataTableCarBody();
			$arData["brand"] = self::DataTableCarBrand();
			$arData["gearbox"] = self::DataTableCarGearbox();
			$arData["model"] = self::DataTableCarModel();
			$arData["fuelmark"] = self::DataTableFuelMark();
			$arData["flowtype"] = self::DataTableFlowType();
			$arData["pointtype"] = self::DataTablePointsType();
			$arData["reason"] = self::DataTableReasonReplacement();
			$arData["storage"] = self::DataTableStorage();
			//$arData["tables"] = self::DataTableTables();
			$arData["whopaid"] = self::DataTableWhoPaid();
			$arData["params"] = self::DataTableParams();

			return $arData;
		}

		public function DataTableCarBody () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("body")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "code", "name", "sort"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["code"],$arRes["name"],$arRes["sort"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableCarBrand () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("brand")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "code", "name"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["code"],$arRes["name"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableCarGearbox () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("gearbox")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "code", "name", "sort"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["code"],$arRes["name"],$arRes["sort"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableCarModel () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("model")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "brand", "code", "name"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["brand"],$arRes["code"],$arRes["name"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableFuelMark () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("fuelmark")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "code", "sort", "short_name", "name"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["code"],$arRes["sort"],$arRes["short_name"],$arRes["name"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableFlowType () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("flowtype")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "code", "name", "sort"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["code"],$arRes["name"],$arRes["sort"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTablePointsType () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("pointtype")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "code", "name", "sort", "default"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["code"],$arRes["name"],$arRes["sort"],$arRes["default"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableReasonReplacement () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("reason")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "code", "name", "sort"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["code"],$arRes["name"],$arRes["sort"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableStorage () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("storage")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "code", "name", "sort"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["code"],$arRes["name"],$arRes["sort"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableTables () {
			global $DB,$OPTIONS;
			$arData = array();

			$query = "SELECT * FROM `".$OPTIONS->GetOptionString("DB_table_prefix")."setup_tables`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id","code", "table"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["code"],$arRes["table"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableWhoPaid () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("whopaid")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "code", "name", "sort"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["code"],$arRes["name"],$arRes["sort"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableParams () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("params")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "option", "value"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["option"],$arRes["value"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

	}