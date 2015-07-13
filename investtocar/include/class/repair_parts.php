<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarRepairParts
	{
		public static $arMessage = array ();

		/**
		 * Функция добавляет информацию о запчасти
		 *
		 * @param array $post
		 * @return bool
		 */
		public function AddRepairParts ($post=array()) {
			//global $OPTIONS;
			if (empty($post)) return false;
			$arData = array();

			$arData["auto"] = intval($post["auto"]);
			$arData["date"] = CInvestToCarMain::ConvertDateToTimestamp($post["date"]);
			$arData["name"] = htmlspecialchars($post["name"]);
			$arData["storage"] = intval($post["storage"]);
			$arData["catalog_number"] = htmlspecialchars($post["catalog_number"]);
			$arData["number"] = floatval(str_replace(",",".",$post["number"]));
			$arData["cost"] = floatval(str_replace(",",".",$post["cost"]));
			$arData["reason"] = intval($post["reason"]);
			switch ($arData["reason"]) {
				case intval(CInvestToCarMain::GetInfoByCode ("reason","ts")):
					$arData["reason_detail"] = intval($post["reason_ts"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","breakdown")):
					$arData["reason_detail"] = intval($post["reason_breakdown"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","dtp")):
					$arData["reason_detail"] = intval($post["reason_dtp"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","tuning")):
					$arData["reason_detail"] = intval($post["reason_tuning"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","upgrade")):
					$arData["reason_detail"] = intval($post["reason_upgrade"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","tire")):
					$arData["reason_detail"] = 0;
					break;
			}
			$arData["who_paid"] = intval($post["who_paid"]);
			$arData["odo"] = floatval(str_replace(",",".",$post["odo"]));
			$arData["waypoint"] = intval($post["waypoint"]);
			if ($arData["waypoint"]==0) {
				$arData["waypoint"] = CInvestToCarPoints::CreateNewPoint(
					$post["newpoint_name"],
					$post["newpoint_address"],
					$post["newpoint_lon"],
					$post["newpoint_lat"],
					3
				);
			}
			$arData["comment"] = htmlspecialchars($post["comment"]);

			if ($res = self::AddRepairPartsDB($arData)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция добавляет в DB информацию о запасной части
		 *
		 * @param array $arData
		 * @return bool
		 */
		public function AddRepairPartsDB ($arData=array()) {
			global $DB;

			if (empty($arData)) return false;

			$query = "INSERT INTO `".CInvestToCarMain::GetTableByCode("repairparts")."` (";
			$query .= "`auto` , `date` , `name` , `storage` , ";
			$query .= "`catalog_number` , `number` , `cost` , `reason` , ";
			$query .= "`reason_detail` , `who_paid` , `odo` , `waypoint` , ";
			$query .= "`comment`) VALUES (";
			$query .= "'".$arData["auto"]."', '".$arData["date"]."', '".$arData["name"]."', '".$arData["storage"]."', ";
			$query .= "'".$arData["catalog_number"]."', '".$arData["number"]."', '".$arData["cost"]."', '".$arData["reason"]."', ";
			$query .= "'".$arData["reason_detail"]."', '".$arData["who_paid"]."', '".$arData["odo"]."', '".$arData["waypoint"]."', ";
			$query .= "'".$arData["comment"]."');";
			if ($res = $DB->Insert($query)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает массив со списком запчастей
		 *
		 * @param int $car
		 * @return bool
		 */
		public function GetListRepairParts ($car=0) {
			global $DB,$OPTIONS;
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("repairparts")."` WHERE `auto` =".$car." ORDER BY `date` ASC";
			if ($res = $DB->Select($query)) {
				$i=0;
				foreach ($res as $arRes) {
					$arData[$i]["id"] = $arRes["id"];
					//$arData[$i]["auto"] = self::GetMyCarsInfo($car);
					$arData[$i]["date"] = date("d.m.Y",$arRes["date"]);
					$arData[$i]["name"] = $arRes["name"];
					$arData[$i]["storage"] = CInvestToCarMain::GetNameByIDFromDB($arRes["storage"],"ms_icar_setup_storage");
					$arData[$i]["catalog_number"] = $arRes["catalog_number"];
					$arData[$i]["number"] = round($arRes["number"],2);
					$arData[$i]["cost"] = number_format($arRes["cost"],2);
					$arData[$i]["reason"] = CInvestToCarMain::GetNameByIDFromDB($arRes["reason"],"ms_icar_setup_reason_replacement");
					if ($arRes["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","ts"))) {
						if ($arRes["reason_detail"]>0) {
							$arTemp = CInvestToCarTs::GetTsInfo($arRes["reason_detail"]);
							$arData[$i]["reason_detail"] = date("d.m.Y",$arTemp[0]["date"])." ".GetMessage("TS")."-".$arTemp[0]["ts_num"];
						}
						else {
							$arData[$i]["reason_detail"] = GetMessage("NOT_SELECTED");
						}
					}
					elseif ($arRes["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","breakdown"))) {
						if ($arRes["reason_detail"]>0) {
							$arData[$i]["reason_detail"] = "Нет данных";
						}
						else {
							$arData[$i]["reason_detail"] = GetMessage("NOT_SELECTED");
						}
					}
					elseif ($arRes["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","dtp"))) {
						if ($arRes["reason_detail"]>0) {
							$arData[$i]["reason_detail"] = "Нет данных";
						}
						else {
							$arData[$i]["reason_detail"] = GetMessage("NOT_SELECTED");
						}
					}
					elseif ($arRes["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","tuning"))) {
						if ($arRes["reason_detail"]>0) {
							$arData[$i]["reason_detail"] = "Нет данных";
						}
						else {
							$arData[$i]["reason_detail"] = GetMessage("NOT_SELECTED");
						}
					}
					elseif ($arRes["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","upgrade"))) {
						if ($arRes["reason_detail"]>0) {
							$arData[$i]["reason_detail"] = "Нет данных";
						}
						else {
							$arData[$i]["reason_detail"] = GetMessage("NOT_SELECTED");
						}
					}
					else {
						$arData[$i]["reason_detail"] = "-";
					}
					//$arData[$i]["reason_detail"] = $arRes["reason_detail"];
					$arData[$i]["who_paid"] = CInvestToCarMain::GetNameByIDFromDB($arRes["who_paid"],"ms_icar_setup_who_paid");
					$arData[$i]["odo"] = round($arRes["odo"],2);
					$arData[$i]["waypoint"] = CInvestToCarMain::GetNameByIDFromDB($arRes["waypoint"],"ms_icar_points");
					$arData[$i]["comment"] = $arRes["comment"];
					$i++;
				}
				//echo "<pre>"; print_r($arData); echo "</pre>";
				return $arData;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция удаляет информацию о запчасти из DB по ее id
		 *
		 * @param int $repairPartsID
		 * @return bool
		 */
		public function DeleteRepairPartsInfoDB ($repairPartsID=0) {
			global $DB;
			if ($repairPartsID==0) return false;

			$query = "DELETE FROM `".CInvestToCarMain::GetTableByCode("repairparts")."` WHERE `id` = ".$repairPartsID;
			if ($res = $DB->Delete($query)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает массив значений для id запчасти
		 *
		 * @param int $repairPartsID
		 * @return bool
		 */
		public function GetRepairPartsInfo ($repairPartsID=0) {
			global $DB;
			if ($repairPartsID==0) return false;

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("repairparts")."` WHERE `id` =".$repairPartsID;
			if ($res = $DB->Select($query)) {
				$arRes = $res[0];
				return $arRes;
			}
			else {
				return false;
			}

		}

		/**
		 * Функция подготавливает запись о запчасти к записи
		 *
		 * @param $post
		 * @return bool
		 */
		public function UpdateRepairParts ($post) {
			global $OPTIONS;
			if (empty($post)) return false;
			$arData = array();

			$arData["id"] = intval($post["id"]);
			$arData["auto"] = intval($post["auto"]);
			$arData["date"] = CInvestToCarMain::ConvertDateToTimestamp($post["date"]);
			$arData["name"] = htmlspecialchars($post["name"]);
			$arData["storage"] = intval($post["storage"]);
			$arData["catalog_number"] = htmlspecialchars($post["catalog_number"]);
			$arData["number"] = floatval(str_replace(",",".",$post["number"]));
			$arData["cost"] = floatval(str_replace(",",".",$post["cost"]));
			$arData["reason"] = intval($post["reason"]);
			switch ($arData["reason"]) {
				case intval(CInvestToCarMain::GetInfoByCode ("reason","ts")):
					$arData["reason_detail"] = intval($post["reason_ts"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","breakdown")):
					$arData["reason_detail"] = intval($post["reason_breakdown"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","dtp")):
					$arData["reason_detail"] = intval($post["reason_dtp"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","tuning")):
					$arData["reason_detail"] = intval($post["reason_tuning"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","upgrade")):
					$arData["reason_detail"] = intval($post["reason_upgrade"]);
					break;
				case intval(CInvestToCarMain::GetInfoByCode ("reason","tire")):
					$arData["reason_detail"] = 0;
					break;
			}
			$arData["who_paid"] = intval($post["who_paid"]);
			$arData["odo"] = floatval(str_replace(",",".",$post["odo"]));
			$arData["waypoint"] = intval($post["waypoint"]);
			if ($arData["waypoint"]==0) {
				$arData["waypoint"] = CInvestToCarPoints::CreateNewPoint(
					$post["newpoint_name"],
					$post["newpoint_address"],
					$post["newpoint_lon"],
					$post["newpoint_lat"],
					3
				);
			}
			$arData["comment"] = htmlspecialchars($post["comment"]);

			if ($res = self::UpdateRepairPartsDB($arData)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция обновляет информацию о запчасти в DB
		 *
		 * @param array $arData
		 * @return bool
		 */
		public function UpdateRepairPartsDB ($arData=array()) {
			global $DB;

			if (empty($arData)) return false;

			$query = "UPDATE `".CInvestToCarMain::GetTableByCode("repairparts")."` SET ";
			$query .= "`auto` = '".$arData["auto"]."', ";
			$query .= "`date` = '".$arData["date"]."', ";
			$query .= "`name` = '".$arData["name"]."', ";
			$query .= "`storage` = '".$arData["storage"]."', ";
			$query .= "`catalog_number` = '".$arData["catalog_number"]."', ";
			$query .= "`number` = '".$arData["number"]."', ";
			$query .= "`cost` = '".$arData["cost"]."', ";
			$query .= "`reason` = '".$arData["reason"]."', ";
			$query .= "`reason_detail` = '".$arData["reason_detail"]."', ";
			$query .= "`who_paid` = '".$arData["who_paid"]."', ";
			$query .= "`odo` = '".$arData["odo"]."', ";
			$query .= "`waypoint` = '".$arData["waypoint"]."', ";
			$query .= "`comment` = '".$arData["comment"]."' ";
			$query .= "WHERE `id` =".$arData["id"].";";

			if ($res = $DB->Update($query)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает общую сумму расходов на запчасти
		 *
		 * @param int $car
		 * @return float|int
		 */
		public function GetTotalRepairPartsCosts ($car=0) {
			global $DB,$OPTIONS;
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();

			$query = "SELECT SUM(`cost`) FROM `".CInvestToCarMain::GetTableByCode("repairparts")."` WHERE `auto` =".$car." AND `who_paid` =".$OPTIONS->GetOptionInt("who_paid_himself");
			$res = $DB->Select($query);
			$res = $res[0]["SUM(`cost`)"];
			if (floatval($res)>0) {
				return round($res, 2);
			}
			else {
				return 0;
			}
		}

	}