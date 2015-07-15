<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarTs
	{
		public static $arMessage = array ();

		/**
		 * Функция возвращает массив записей о прохождении ТО для автомобиля, либо false
		 *
		 * @param int $car
		 * @return bool
		 */
		public function GetListCarTs ($car=0) {
			global $DB;

			if ($car==0) {
				return false;
			}

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("ts")."` WHERE `auto` =".$car." ORDER BY `date` ASC";
			$res = $DB->Select($query);

			if (isset($res[0]["id"])) {
				foreach ($res as &$arTs) {
					if (intval($arTs["point"])>0) {
						$arTs["point"] = CInvestToCarPoints::GetPointInfoByID($arTs["point"]);
					}
					$arTs["repair"] = CInvestToCarRepair::GetRepairNameByID($arTs["repair"]);
				}
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция добавления расходов на ТО
		 *
		 * @param string $post
		 * @return bool
		 */
		public function AddNewTs ($post="") {
			if (!is_array($post)) {
				return false;
			}

			$arTs["auto"] = $post["ts_auto"];
			$arTs["ts_num"] = $post["ts_num"];
			$arTs["date"] = CInvestToCarMain::ConvertDateToTimestamp($post["date"]);
			$arTs["repair"] = $post["ts_repair"];
			$arTs["cost"] = $post["cost"];
			$arTs["odo"] = $post["odo"];
			$arTs["point"] = $post["ts_point"];
			if ($arTs["point"]==0) {
				$arTs["point"] = CInvestToCarPoints::CreateNewPoint (
					$post["newpoint_name"],
					$post["newpoint_address"],
					$post["newpoint_lon"],
					$post["newpoint_lat"]
				);
			}
			$arTs["description"] = $post["comment"];

			if ($res = self::AddNewTsDB($arTs)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Добавляет данные о расходах на ТО в базу
		 *
		 * @param $arData
		 * @return bool
		 */
		public function AddNewTsDB ($arData) {
			global $DB;

			$query = "INSERT INTO `".CInvestToCarMain::GetTableByCode("ts")."`";
			$query .= " (`ts_num` ,`auto` ,`date` ,`repair` ,`cost` ,`odo` ,`point` ,`description`)";
			$query .= "VALUES ('".$arData["ts_num"]."', '".$arData["auto"]."', '".$arData["date"]."', '".$arData["repair"]."',";
			$query .= "'".$arData["cost"]."', '".$arData["odo"]."', '".$arData["point"]."', '".$arData["description"]."');";
			if ($res = $DB->Insert($query)) {
				return $res;
			}
			else {
				return false;
			}

		}

		/**
		 * Функция возвращает массив данных по указанному ID записи о ТО
		 *
		 * @param int $tsID
		 * @return bool
		 */
		public function GetTsInfo ($tsID=0) {
			global $DB;
			if ($tsID==0) {
				return false;
			}

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("ts")."` WHERE `id` =".$tsID;
			if ($res = $DB->Select($query)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция обновляет информации о расходе на ТО
		 *
		 * @param int $tsID
		 * @param array $arPost
		 * @return bool
		 */
		public function UpdateTsInfo ($tsID=0, $arPost=array()) {
			if ($tsID==0 || empty($arPost)) return false;

			$arData = array();
			$arData["id"] = intval($arPost["tsID"]);
			$arData["ts_num"] = intval($arPost["ts_num"]);
			$arData["ts_auto"] = intval($arPost["ts_auto"]);
			$arData["date"] = CInvestToCarMain::ConvertDateToTimestamp($arPost["date"]);
			$arData["ts_repair"] = intval($arPost["ts_repair"]);
			$arData["cost"] = floatval(str_replace(",",".",$arPost["cost"]));
			$arData["odo"] = floatval(str_replace(",",".",$arPost["odo"]));
			if ($arPost["ts_point"]==0) {
				$arData["point"] = CInvestToCarPoints::CreateNewPoint (
					$arPost["newpoint_name"],
					$arPost["newpoint_address"],
					$arPost["newpoint_lon"],
					$arPost["newpoint_lat"]
				);
			}
			$arData["comment"] = $arPost["comment"];
			if ($res = self::UpdateTsInfoDB($arData)) {
				return true;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция обновляет информацию о расходен на ТО в DB
		 *
		 * @param $arData
		 * @return bool
		 */
		public function UpdateTsInfoDB ($arData) {
			global $DB;

			$query = "UPDATE `".CInvestToCarMain::GetTableByCode("ts")."` SET ";
			$query .= "`ts_num` = '".$arData["ts_num"]."', ";
			$query .= "`auto` = '".$arData["ts_auto"]."', ";
			$query .= "`date` = '".$arData["date"]."', ";
			$query .= "`repair` = '".$arData["ts_repair"]."', ";
			$query .= "`cost` = '".$arData["cost"]."', ";
			$query .= "`odo` = '".$arData["odo"]."', ";
			$query .= "`point` = '".$arData["point"]."', ";
			$query .= "`description` = '".$arData["comment"]."' ";
			$query .= "WHERE `ms_icar_ts`.`id` =".$arData["id"].";";
			if ($res = $DB->Update($query)) {
				return true;
			}
			else {
				return false;
			}

		}

		/**
		 * Функция удаляет информацию о расходе на ТО из DB;
		 *
		 * @param int $tsID
		 * @return bool
		 */
		public function DeleteTsInfoDB ($tsID=0) {
			global $DB;

			if ($tsID==0) return false;

			$query = "DELETE FROM `".CInvestToCarMain::GetTableByCode("ts")."` WHERE `id` = ".$tsID;
			return $res = $DB->Delete($query);
		}

		/**
		 * Функция возвращает общую сумму расходов на ТО
		 *
		 * @param int $car
		 * @return float|int
		 */
		public function GetTotalMaintenanceCosts ($car=0) {
			global $DB;
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();

			$query = "SELECT SUM(`cost`) FROM `".CInvestToCarMain::GetTableByCode("ts")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);
			$res = $res[0]["SUM(`cost`)"];
			if (floatval($res)>0) {
				return round($res, 2);
			}
			else {
				return 0;
			}
		}

		public function CreateTables () {
			$arTables = array();
			$arTables[] = self::QueryTableTs();

			return $arTables;
		}

		public function QueryTableTs () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("ts")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`ts_num` INT (11) NOT NULL, ";
			$query .= "`auto` INT (11) NOT NULL, ";
			$query .= "`date` INT (11) NOT NULL, ";
			$query .= "`repair` INT (11) NOT NULL, ";
			$query .= "`cost` FLOAT NOT NULL, ";
			$query .= "`odo` FLOAT NOT NULL, ";
			$query .= "`point` INT (11) NOT NULL, ";
			$query .= "`description` VARCHAR(255) NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function DataTables () {
			$arData = array();
			$arData["ts"] = self::DataTableTs();

			return $arData;
		}

		public function DataTableTs () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("ts")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "ts_num", "auto", "date",
					"repair","cost","odo","point",
					"description"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["ts_num"],$arRes["auto"],$arRes["date"],
						$arRes["repair"],$arRes["cost"],$arRes["odo"],$arRes["point"],
						$arRes["description"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

	}