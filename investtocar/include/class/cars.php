<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarCars
	{
		public static $arMessage = array ();

		/**
		 * Функция получает подробные данные о всех автомобилях в гараже, либо о заданном
		 *
		 * @return array
		 */
		public function GetMyCarsInfo ($car=0)
		{
			global $DB;

			if (intval($car) == 0) {
				$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("mycar")."` ORDER BY `name`";
			}
			else {
				$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("mycar")."` WHERE `id`=".$car;
			}
			$res = $DB->Select ($query);

			foreach ($res as &$arCar)
			{
				$query2 = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("brand")."` WHERE `id` =".$arCar["trademark"];
				$res2 = $DB->Select ($query2);
				$arCar["trademark"] = $res2[0]["name"];

				$query2 = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("model")."` WHERE `id` =".$arCar["model"];
				$res2 = $DB->Select ($query2);
				$arCar["model"] = $res2[0]["name"];

				$query2 = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("gearbox")."` WHERE `id` =".$arCar["gearshift"];
				$res2 = $DB->Select ($query2);
				$arCar["gearshift"] = $res2[0]["name"];

				$query2 = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("body")."` WHERE `id` =".$arCar["body"];
				$res2 = $DB->Select ($query2);
				$arCar["body"] = $res2[0]["name"];

				$arCar["total_costs"] = self::GetTotalCosts($arCar["id"]);
				$arCar["average_fuel_consum"] = CInvestToCarFuel::GetAverageFuelConsumption($arCar["id"]);
			}

			if (intval($car) == 0) {
				return $res;
			}
			else {
				return $res[0];
			}
		}

		/**
		 * Функция получает данные указанного автомобиля, либо всех автомобилей
		 *
		 * @param int $car
		 * @return mixed
		 */
		public function GetMyCars ($car=0) {
			global $DB;

			if (intval($car) == 0) {
				$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("mycar")."` ORDER BY `name`";
			}
			else {
				$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("mycar")."` WHERE `id`=".$car;
			}
			$res = $DB->Select ($query);

			if (intval($car) == 0) {
				return $res;
			}
			else {
				return $res[0];
			}

		}

		/**
		 * Функция добавляет новую марку автомобиля, связывая ее с брендом
		 *
		 * @param $brand id бренда
		 * @param $model Имя модели
		 * @return int|bool id добавленной марки, либо false
		 */
		public function AddNewModel ($brand, $model)
		{
			global $DB;

			$query = "INSERT INTO `".CInvestToCarMain::GetTableByCode("model")."` (`brand` , `name`) VALUES ('".$brand."', '".$model."');";
			if ($res = $DB->Insert ($query))
			{
				return $res;
			}
			else
			{
				return false;
			}
		}

		public function AddNewCarGarage ($post=array()) {
			if (empty($post)) return false;

			$arData["name"]             = trim($post["name"]);
			$arData["trademark"]        = intval($post["car_brand"]);
			if (isset($post["car_model"])) {
				$arData["model"] = intval($post["car_model"]);
			}
			elseif (isset($post["car_model_add"])) {
				//Необходимо добавить новую модель
				if ($arData["trademark"]>0) {
					if ($newModel = CInvestToCarCars::AddNewModel($arData["trademark"],$post["car_model_add"])) {
						$arData["model"] = $newModel;
					}
					else {
						$arData["model"] = 0;
					}
				}
				else {
					$arData["model"] = 0;
				}
			}
			else {
				$arData["model"] = 0;
			}
			$arData["year"]             = intval($post["car_year"]);
			$arData["vin"]              = trim($post["vin"]);
			$arData["carnumber"]        = $post["gosnum"];
			$arData["enginecapacity"]   = $post["engine"];
			$arData["enginecapacity"]   = floatval(str_replace(",",".",$arData["enginecapacity"]));
			$arData["gearshift"]        = intval($post["car_gearbox"]);
			$arData["body"]             = intval($post["car_body"]);
			$arData["interval_ts"]      = intval($post["interval_ts"]);
			$arData["cost"]             = str_replace(",",".", $post["cost"]);
			$arData["cost"]             = floatval(str_replace(" ", "", $arData["cost"]));
			$arData["mileage"]          = floatval(str_replace(",",".",$post["odo"]));
			if (isset($_POST["credit"]) && $post["credit"]==1) {
				$arData["credit"]       = 1;
			}
			else {
				$arData["credit"]       = 0;
			}
			$arData["creditcost"]       = floatval(str_replace(" ", "", $arData["creditcost"]));
			$osago_end        = str_replace("-",".",$post["end_osago"]);
			$arData["osago_end"] = CInvestToCarMain::ConvertDateToTimestamp($osago_end);
			$gto_end          = str_replace("-",".",$post["end_gto"]);
			$arData["gto_end"] = CInvestToCarMain::ConvertDateToTimestamp($gto_end);
			if (isset($_POST["default"]) && $post["default"]==1) {
				$arData["default"]      = 1;
			}
			else {
				$arData["default"]      = 0;
			}

			if ($newCar = CInvestToCarMain::AddNewCarGarageDB($arData)) {
				return $newCar;
			}
			else {
				return false;
			}

		}

		/**
		 * Функция добавляет новый автомобиль в гараж
		 *
		 * @param $arData Данные
		 * @return bool|int id нового автомобиля, либо false
		 */
		public function AddNewCarGarageDB ($arData)
		{
			global $DB;

			if (intval($arData["default"])==1) {
				//Удаляем отметку "по-умолчанию" с другого автомобиля (автомобилей)
				self::DeleteMarkDefaultCars();
			}

			$query = "INSERT INTO `".CInvestToCarMain::GetTableByCode("mycar")."` (";
			$query .= "`name` ,`trademark` ,`model` ,`year` ,";
			$query .= "`vin` ,`carnumber` ,`enginecapacity` ,`gearshift` ,";
			$query .= "`body` ,`interval_ts` ,`cost` ,`mileage` ,";
			$query .= "`credit` ,`creditcost` ,`osago_end` ,`gto_end` ,";
			$query .= "`default`) VALUES (";
			$query .= "'".$arData["name"]."', '".$arData["trademark"]."', '".$arData["model"]."', '".$arData["year"]."',";
			$query .= "'".$arData["vin"]."', '".$arData["carnumber"]."', '".$arData["enginecapacity"]."', '".$arData["gearshift"]."',";
			$query .= "'".$arData["body"]."', '".$arData["interval_ts"]."', '".$arData["cost"]."', '".$arData["mileage"]."',";
			$query .= "'".$arData["credit"]."', '".$arData["creditcost"]."', '".$arData["osago_end"]."', '".$arData["gto_end"]."',";
			$query .= "'".$arData["default"]."');";

			if ($res = $DB->Insert($query)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция обновляет информацию об автомобиле в гараже
		 *
		 * @param array $post
		 * @return bool
		 */
		public function UpdateCarInGarage ($post=array()) {
			if (empty($post)) return false;

			$arData["name"]             = trim($post["name"]);
			$arData["trademark"]        = intval($post["car_brand"]);
			if (isset($_POST["car_model"])) {
				$arData["model"] = intval($post["car_model"]);
			}
			else {
				$arData["model"] = 0;
			}
			$arData["year"]             = intval($post["car_year"]);
			$arData["vin"]              = trim($post["vin"]);
			$arData["carnumber"]        = $post["gosnum"];
			$arData["enginecapacity"]   = $post["engine"];
			$arData["enginecapacity"]   = floatval(str_replace(",",".",$arData["enginecapacity"]));
			$arData["gearshift"]        = intval($post["car_gearbox"]);
			$arData["body"]             = intval($post["car_body"]);
			$arData["interval_ts"]      = intval($post["interval_ts"]);
			$arData["cost"]             = str_replace(",",".", $post["cost"]);
			$arData["cost"]             = floatval(str_replace(" ", "", $arData["cost"]));
			$arData["mileage"]          = floatval(str_replace(",",".",$post["odo"]));
			if (isset($_POST["credit"]) && $post["credit"]==1) {
				$arData["credit"]       = 1;
			}
			else {
				$arData["credit"]       = 0;
			}
			$arData["creditcost"]       = floatval(str_replace(" ", "", $post["creditcost"]));
			$osago_end        = str_replace("-",".",$post["end_osago"]);
			$arData["osago_end"] = CInvestToCarMain::ConvertDateToTimestamp($osago_end);
			$gto_end          = str_replace("-",".",$post["end_gto"]);
			$arData["gto_end"] = CInvestToCarMain::ConvertDateToTimestamp($gto_end);
			if (isset($_POST["default"]) && $post["default"]==1) {
				$arData["default"]      = 1;
			}
			else {
				$arData["default"]      = 0;
			}

			if ($newCar = self::UpdateCarInGarageDB($post["car"], $arData)) {
				return $newCar;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция обновляет информацию об автомобиле в DB
		 *
		 * @param int $car
		 * @param $arData
		 * @return bool
		 */
		public function UpdateCarInGarageDB ($car=0, $arData) {
			global $DB;

			if ($car==0) return false;

			if (intval($arData["default"])==1) {
				self::DeleteMarkDefaultCars();
			}

			$query = "UPDATE `".CInvestToCarMain::GetTableByCode("mycar")."` SET `name` = '".$arData["name"]."', ";
			$query .= "`trademark` = '".$arData["trademark"]."',";
			$query .= "`model` = '".$arData["model"]."',";
			$query .= "`year` = '".$arData["year"]."',";
			$query .= "`vin` = '".$arData["vin"]."',";
			$query .= "`carnumber` = '".$arData["carnumber"]."',";
			$query .= "`enginecapacity` = '".$arData["enginecapacity"]."',";
			$query .= "`gearshift` = '".$arData["gearshift"]."',";
			$query .= "`body` = '".$arData["body"]."', ";
			$query .= "`interval_ts` = '".$arData["interval_ts"]."', ";
			$query .= "`cost` = '".$arData["cost"]."', ";
			$query .= "`mileage` = '".$arData["mileage"]."', ";
			$query .= "`credit` = '".$arData["credit"]."', ";
			$query .= "`creditcost` = '".$arData["creditcost"]."', ";
			$query .= "`osago_end` = '".$arData["osago_end"]."', ";
			$query .= "`gto_end` = '".$arData["gto_end"]."', ";
			$query .= "`default` = '".$arData["default"]."' ";
			$query .= "WHERE `id` =".$car.";";

			$res = $DB->Update($query);

			return true;

		}

		/**
		 * Функция удаляет отметку default у всех автомобилей
		 */
		public function DeleteMarkDefaultCars () {
			global $DB;

			$query = "SELECT `id`,`default` FROM `".CInvestToCarMain::GetTableByCode("mycar")."` WHERE `default`=1";
			$res = $DB->Select($query);
			foreach ($res as $arCar) {
				$query2 = "UPDATE `".CInvestToCarMain::GetTableByCode("mycar")."` SET `default` = '0' WHERE `id` =".$arCar["id"].";";
				$res2 = $DB->Update($query2);
			}
		}

		/**
		 * Функция проверяет, можно ли удалить указанный автомобиль, т.е. нет ли записей, связанных с ним
		 *
		 * @param int $car
		 * @return bool
		 */
		public function CheckCanDeleteCar ($car=0) {
			global $DB;

			if ($car == 0) {
				self::$arMessage["ERROR"] = GetMessage("INVALID_CAR_ID");
				return false;
			}
			$canDelete = true;

			$query = "SELECT `id` , `auto` FROM `".CInvestToCarMain::GetTableByCode("routs")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);

			if (isset($res[0]["id"])) {
				$canDelete = false;
				self::$arMessage["CAN_DELETE"][] = GetMessage("REFERENCED_DATA_TRIPS")."<br>";
			}

			$query = "SELECT `id` , `auto` FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);

			if (isset($res[0]["id"])) {
				$canDelete = false;
				self::$arMessage["CAN_DELETE"][] = GetMessage("PETROL_STATION_LINKED")."<br>";
			}

			$query = "SELECT `id` , `auto` FROM `".CInvestToCarMain::GetTableByCode("odo")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);

			if (isset($res[0]["id"])) {
				$canDelete = false;
				self::$arMessage["CAN_DELETE"][] = GetMessage("BOUND_TO_RUN_RECORD")."<br>";
			}

			$query = "SELECT `id` , `auto` FROM `".CInvestToCarMain::GetTableByCode("ts")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);

			if (isset($res[0]["id"])) {
				$canDelete = false;
				self::$arMessage["CAN_DELETE"][] = GetMessage("BOUND_RECORD_MAINTENANCE")."<br>";
			}

			return $canDelete;
		}

		/**
		 * Функция удаляет указанный автомобиль, дополнительно проверив на возможность удаления
		 *
		 * @param int $car ID автомобиля
		 * @param bool $checkDelete проверять ли на возможность удаления
		 * @return bool Удалось/Не удалось удалить
		 */
		public function DeleteCarInGarage ($car=0,$checkDelete=true) {
			global $DB;

			if ($car==0) {
				return false;
			}

			if ($checkDelete){
				if (!self::CheckCanDeleteCar($car)) {
					return false;
				}
			}

			$query = "DELETE FROM `".CInvestToCarMain::GetTableByCode("mycar")."` WHERE `id` = ".$car;
			if ($res = $DB->Delete($query)) {
				return true;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает ID автомобиля по-умолчанию
		 *
		 * @return int|bool ID автомобиля по-умолчанию, либо false
		 */
		public function GetDefaultCar () {
			global $DB;

			$query = "SELECT `id` , `default` FROM `".CInvestToCarMain::GetTableByCode("mycar")."` WHERE `default` =1";
			$res = $DB->Select($query);

			if (isset($res[0]["id"])) {
				return $res[0]["id"];
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает название бренда по его ID
		 *
		 * @param $id
		 * @return string
		 */
		public function GetCarTrademarkNameByID ($id) {
			global $DB;

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("brand")."` WHERE `id` =".$id;
			$res = $DB->Select($query);

			if (isset($res[0]["name"])) {
				return $res[0]["name"];
			}
			else {
				return "";
			}
		}

		/**
		 * Функция возвращает модель авто по его ID
		 *
		 * @param $id
		 * @return string
		 */
		public function GetCarModelNameByID ($id) {
			global $DB;

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("model")."` WHERE `id` =".$id;
			$res = $DB->Select($query);

			if (isset($res[0]["name"])) {
				return $res[0]["name"];
			}
			else {
				return "";
			}
		}

		/**
		 * Функция возвращает общую сумму расходов на автомобиль
		 *
		 * @param int $car
		 * @return float|int
		 */
		public function GetTotalCosts ($car=0) {
			if ($car==0) $car = self::GetDefaultCar();
			$summ = 0;
			//Расходы на топливо
			$summ += CInvestToCarFuel::GetTotalFuelCosts($car);
			//Расходы на ТО
			$summ += CInvestToCarTs::GetTotalMaintenanceCosts($car);
			//Прочие расходы
			$summ += CInvestToCarOther::GetTotalOtherCosts($car);
			/*
			//Запчасти
			$summ += CInvestToCarRepairParts::GetTotalRepairPartsCosts($car);
			*/

			if ($summ>0) {
				return round($summ,2);
			}
			else {
				return 0;
			}
		}

	}