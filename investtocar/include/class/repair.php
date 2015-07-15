<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarRepair
	{
		public static $arMessage = array ();

		public function CreateTables () {
			$arTables = array();
			$arTables[] = self::QueryTableRepair();

			return $arTables;
		}

		public function QueryTableRepair () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("repair")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`auto` INT (11) NOT NULL, ";
			$query .= "`date` INT (11) NOT NULL, ";
			$query .= "`cost` FLOAT NOT NULL, ";
			$query .= "`repair` INT (11) NOT NULL, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "`odo` FLOAT NOT NULL, ";
			$query .= "`reason` INT (11) NOT NULL, ";
			$query .= "`reason_detail` INT (11) NOT NULL, ";
			$query .= "`waypoint` INT (11) NOT NULL, ";
			$query .= "`comment` VARCHAR(255) NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		/**
		 * Функция возвращает Название ремонтирующего по его ID
		 *
		 * @param $id
		 * @return string
		 */
		public function GetRepairNameByID ($id=0) {
			global $DB;
			if ($id==0) return GetMessage("NO_DATA");

			$query = "SELECT `name` FROM `".CInvestToCarMain::GetTableByCode("repairtype")."` WHERE `id` =".$id;
			if ($res = $DB->Select($query)) {
				return $res[0]["name"];
			}
			else {
				return GetMessage("NO_DATA");
			}
		}

		public function GetTotalRepairCosts ($car=0) {
			global $DB;
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();
			$sumCost = 0;

			$query = "SELECT `id`, `cost` FROM `".CInvestToCarMain::GetTableByCode("repair")."` WHERE `auto` =".$car;
			if ($res = $DB->Select($query)) {
				foreach ($res as $arRes) {
					if (floatval($arRes["cost"])==0) {
						$sumCost += CInvestToCarRepairParts::CalculateCostRepairParts(
							array(
								intval(CInvestToCarMain::GetInfoByCode("reason","ts")),
								intval(CInvestToCarMain::GetInfoByCode("reason","dtp"))
							),
							$arRes["id"],
							true
						);
					}
					else {
						$sumCost += $arRes["cost"];
					}
				}

				return round($sumCost, 2);
			}
			else {
				return 0;
			}
		}

		/**
		 * Функция подготавливает данные о расходе на ремонт к записи
		 *
		 * @param array $post
		 * @return bool
		 */
		public function AddRepair ($post=array()) {
			if (empty($post)) return false;
			$arData = array();

			$arData["auto"] = intval($post["auto"]);
			$arData["date"] = CInvestToCarMain::ConvertDateToTimestamp($post["date"]);
			$arData["cost"] = floatval(str_replace(",",".",$post["cost"]));
			$arData["repair"] = intval($post["repair"]);
			$arData["name"] = htmlspecialchars($post["name"]);
			$arData["odo"] = floatval(str_replace(",",".",$post["odo"]));
			$arData["reason"] = intval($post["reason"]);
			switch ($arData["reason"]) {
				case intval(CInvestToCarMain::GetInfoByCode ("reason","ts")):
					$arData["reason_detail"] = intval($post["reason_ts"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","dtp")):
					$arData["reason_detail"] = intval($post["reason_dtp"]);
					break;
				default:
					$arData["reason_detail"] = 0;
					break;
			}
			$arData["who_paid"] = intval($post["who_paid"]);
			$arData["waypoint"] = intval($post["waypoint"]);
			if ($arData["waypoint"]==0) {
				$arData["waypoint"] = CInvestToCarPoints::CreateNewPoint(
					$post["newpoint_name"],
					$post["newpoint_address"],
					$post["newpoint_lon"],
					$post["newpoint_lat"],
					$post["newpoint_type"]
				);
			}
			$arData["comment"] = htmlspecialchars($post["comment"]);
			if ($res = self::AddRepairDB($arData)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция добавляет расходы на ремонт в DB
		 *
		 * @param array $arData
		 * @return bool
		 */
		private function AddRepairDB ($arData=array()) {
			global $DB;
			if (empty($arData)) return false;

			$query = "INSERT INTO `".CInvestToCarMain::GetTableByCode("repair")."` (";
			$query .= "`auto` , `date` , `cost` , `repair` , ";
			$query .= "`name` , `odo` , `reason` , `who_paid` , ";
			$query .= "`reason_detail` , `waypoint` , `comment`)VALUES (";
			$query .= "'".$arData["auto"]."', '".$arData["date"]."', '".$arData["cost"]."', '".$arData["repair"]."', ";
			$query .= "'".$arData["name"]."', '".$arData["odo"]."', '".$arData["reason"]."', '".$arData["who_paid"]."', ";
			$query .= "'".$arData["reason_detail"]."', '".$arData["waypoint"]."', '".$arData["comment"]."');";
			if ($res = $DB->Insert($query)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция подготавливает данные о ремонте к удалению
		 *
		 * @param array $post
		 * @return bool
		 */
		public function DeleteRepair ($post=array()) {
			if (empty($post)) return false;

			$arData["id"] = intval($post["id"]);
			if ($res = self::DeleteRepairDB($arData)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция удаляет информацию о ремонте из DB
		 *
		 * @param $arData
		 * @return bool
		 */
		private function DeleteRepairDB ($arData) {
			global $DB;

			$query = "DELETE FROM `"
			         .CInvestToCarMain::GetTableByCode("repair")."` WHERE `"
			         .CInvestToCarMain::GetTableByCode("repair")."`.`id` = ".$arData["id"];
			if ($res = $DB->Delete($query)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает массив списка расходов на ремонт
		 *
		 * @param int $car
		 * @return bool|array
		 */
		public function GetListRepair ($car=0) {
			global $DB;
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("repair")."` WHERE `auto` =".$car." ORDER BY `date` ASC";
			if ($res = $DB->Select($query)) {
				$arData = array();
				$i=0;

				foreach ($res as $arRes) {
					$arData[$i]["id"] = intval($arRes["id"]);
					$arData[$i]["date"] = date("d.m.Y",$arRes["date"]);
					$arData[$i]["cost"] = number_format($arRes["cost"],2);
					$arData[$i]["repair"] = self::GetRepairNameByID($arRes["repair"]);
					$arData[$i]["name"] = $arRes["name"];
					$arData[$i]["odo"] = number_format($arRes["odo"],1);
					$arData[$i]["reason"] = CInvestToCarMain::GetNameByIDFromDB($arRes["reason"],CInvestToCarMain::GetTableByCode("reason"));
					$arData[$i]["reason_detail"] = (intval($arRes["reason_detail"])==0)?"-":$arRes["reason_detail"];
					$arData[$i]["who_paid"] = CInvestToCarMain::GetNameByIDFromDB($arRes["who_paid"],CInvestToCarMain::GetTableByCode("whopaid"));
					$arData[$i]["waypoint"] = CInvestToCarMain::GetNameByIDFromDB($arRes["waypoint"],CInvestToCarMain::GetTableByCode("points"));
					$arData[$i]["comment"] = $arRes["comment"];

					$i++;
				}

				return $arData;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает массив значений записи о ремонте по его ID
		 *
		 * @param int $repairID
		 * @return bool
		 */
		public function GetRepairInfo ($repairID=0) {
			global $DB;
			if ($repairID==0) return false;

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("repair")."` WHERE `id` =".$repairID;
			if ($res = $DB->Select($query)) {
				$res = $res[0];
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция подготавливает данные о ремонте для обновления
		 *
		 * @param array $post
		 * @return bool
		 */
		public function UpdateRepair ($post=array()) {
			if (empty($post)) return false;

			$arData = array();

			$arData["id"] = intval($post["id"]);
			$arData["auto"] = intval($post["auto"]);
			$arData["date"] = CInvestToCarMain::ConvertDateToTimestamp($post["date"]);
			$arData["cost"] = floatval(str_replace(",",".",$post["cost"]));
			$arData["repair"] = intval($post["repair"]);
			$arData["name"] = htmlspecialchars($post["name"]);
			$arData["odo"] = floatval(str_replace(",",".",$post["odo"]));
			$arData["reason"] = intval($post["reason"]);
			switch ($arData["reason"]) {
				case intval(CInvestToCarMain::GetInfoByCode ("reason","ts")):
					$arData["reason_detail"] = intval($post["reason_ts"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","dtp")):
					$arData["reason_detail"] = intval($post["reason_dtp"]);
					break;
				default:
					$arData["reason_detail"] = 0;
					break;
			}
			$arData["who_paid"] = intval($post["who_paid"]);
			$arData["waypoint"] = intval($post["waypoint"]);
			if ($arData["waypoint"]==0) {
				$arData["waypoint"] = CInvestToCarPoints::CreateNewPoint(
					$post["newpoint_name"],
					$post["newpoint_address"],
					$post["newpoint_lon"],
					$post["newpoint_lat"],
					$post["newpoint_type"]
				);
			}
			$arData["comment"] = htmlspecialchars($post["comment"]);
			if ($res = self::UpdateRepairDB($arData)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция обновляет данные записи о ремонте в DB
		 *
		 * @param array $arData
		 * @return bool
		 */
		private function UpdateRepairDB ($arData=array()) {
			global $DB;
			if (empty($arData)) return false;

			$query = "UPDATE `".CInvestToCarMain::GetTableByCode("repair")."` SET ";
			$query .= "`auto` = '".$arData["auto"]."', ";
			$query .= "`date` = '".$arData["date"]."', ";
			$query .= "`cost` = '".$arData["cost"]."', ";
			$query .= "`repair` = '".$arData["repair"]."', ";
			$query .= "`name` = '".$arData["name"]."', ";
			$query .= "`odo` = '".$arData["odo"]."', ";
			$query .= "`reason` = '".$arData["reason"]."', ";
			$query .= "`who_paid` = '".$arData["who_paid"]."', ";
			$query .= "`reason_detail` = '".$arData["reason_detail"]."', ";
			$query .= "`waypoint` = '".$arData["waypoint"]."', ";
			$query .= "`comment` = '".$arData["comment"]."' ";
			$query .= "WHERE `".CInvestToCarMain::GetTableByCode("repair")."`.`id` =".$arData["id"].";";
			if ($res = $DB->Update($query)) {
				return $res;
			}
			else {
				return false;
			}
		}

		public function CalculateCostRepair ($reason=0, $reasonDetail=0) {
			global $DB;
			if ($reason==0 || $reasonDetail==0) return 0;
			$sumCost = 0;

			$query = "SELECT `id`, `cost`, `reason` FROM `".CInvestToCarMain::GetTableByCode("repair")
			         ."` WHERE `reason` =".$reason
			         ." AND `reason_detail` =".$reasonDetail." ORDER BY `id` ASC";
			if ($res = $DB->Select($query)) {
				foreach ($res as $arRes) {
					if (floatval($arRes["cost"])==0) {
						$sumCost += CInvestToCarRepairParts::CalculateCostRepairParts(
							array(
								intval(CInvestToCarMain::GetInfoByCode("reason","ts")),
								intval(CInvestToCarMain::GetInfoByCode("reason","dtp"))
							),
							$arRes["id"],
							true
						);
					}
					else {
						$sumCost += $arRes["cost"];
					}
				}
				return $sumCost;
			}
			else {
				return 0;
			}

		}
	}