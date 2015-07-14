<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarFuel
	{
		public static $arMessage = array ();

		/**
		 * Функция получает массив записей о расходе топлива
		 *
		 * @param int $carID
		 * @return bool
		 */
		public function GetFuelList ($carID=0) {
			global $DB;
			if ($carID==0) return false;

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `auto` =".$carID." ORDER BY `date` ASC";
			return $res = $DB->Select($query);
		}

		/**
		 * Функция получает наименование топлива по ID, либо краткое (по-умолчанию), либо полное
		 *
		 * @param int $fuelMarkID
		 * @param bool $full
		 * @return string
		 */
		public  function GetFuelMarkByID ($fuelMarkID=0,$full=false) {
			global $DB;
			if ($fuelMarkID==0) return "-";

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("fuelmark")."` WHERE `id` =".$fuelMarkID;
			if ($res = $DB->Select($query)) {
				if ($full) {
					return $res[0]["name"];
				}
				else {
					return $res[0]["shot_name"];
				}
			}
			else {
				return "-";
			}
		}

		/**
		 * Функция возвращает общую сумму расходов на топливо
		 *
		 * @param int $car
		 * @return float|int
		 */
		public function GetTotalFuelCosts ($car=0) {
			global $DB;
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();

			$query = "SELECT SUM(`summ`) FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);
			$res = $res[0]["SUM(`summ`)"];
			if (floatval($res)>0) {
				return round($res, 2);
			}
			else {
				return 0;
			}
		}

		/**
		 * Функция возвращает средний расход топлива на 100км
		 *
		 * @param int $car
		 * @return float|int
		 */
		public function GetAverageFuelConsumption ($car=0) {
			global $DB;
			$amount = 0;
			$quantity = 0;
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();

			$query = "SELECT `expense` FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `auto` =".$car." ORDER BY `date` ASC";
			if ($res =  $DB->Select($query)) {
				foreach ($res as $arRes) {
					if (floatval($arRes["expense"])>0) {
						$amount += $arRes["expense"];
						$quantity++;
					}
				}
				return round(($amount/$quantity),2);
			}
			else {
				return 0;
			}
		}

		/**
		 * Функция добавляет информацию о заправке а также проверяет, необходимо ли пересчитать средний расход топлива
		 *
		 * @param array $post
		 * @return bool
		 */
		public function AddFuelCosts ($post=array()) {
			if (empty($post)) return false;
			$arData = array();

			$arData["auto"] = intval($post["fuel_auto"]);
			$arData["date"] = CInvestToCarMain::ConvertDateToTimestamp($post["date"]);
			$arData["odo"] = floatval(str_replace(",",".",$post["odo"]));
			$arData["fuel_mark"] = intval($post["fuel_mark"]);
			$arData["liters"] = floatval(str_replace(",",".",$post["liters"]));
			$arData["cost_liter"] = floatval(str_replace(",",".",$post["cost_liter"]));
			$arData["summ"] = $arData["liters"] * $arData["cost_liter"];
			if (isset($post["full_tank"])) {
				$arData["full_tank"] = 1;
			}
			else {
				$arData["full_tank"] =0;
			}
			$arData["fuel_point"] = intval($post["fuel_point"]);
			if ($arData["fuel_point"]==0) {
				$arData["fuel_point"] = CInvestToCarPoints::CreateNewPoint (
					$post["newpoint_name"],
					$post["newpoint_address"],
					$post["newpoint_lon"],
					$post["newpoint_lat"],
					2
				);
			}
			$arData["comment"] = htmlspecialchars($post["comment"]);
			if ($arData["full_tank"] > 0) {
				$arData["expense"] = self::CalculationExpense($arData["odo"],$arData["liters"],$arData["auto"],$arData["date"]);
				//$arData["expense"] = self::CalculationExpense(2549,41.99,1,1428699600);
			}
			else {
				$arData["expense"] = 0;
			}
			//echo "EXPENSE=(".$arData["expense"].")";
			if ($res = self::AddFuelCostsDB($arData)) {
				$increase = CInvestToCarPoints::IncreasePointPeriod($arData["point"]);

				if (!self::CheckLastFuelCosts($arData["date"], $arData["auto"])) {
					//Необходимо пересчитать все средние значения расхода на 100 км
					self::RecalculationExpense($arData["auto"]);
				}
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция добавляет информацию о заправке в DB
		 *
		 * @param array $arData
		 * @return bool
		 */
		public function AddFuelCostsDB ($arData=array()) {
			global $DB;
			if (empty($arData)) return false;

			$query = "INSERT INTO `".CInvestToCarMain::GetTableByCode("fuel")."` (";
			$query .= "`auto` , `date` , `odo` , `fuel_mark` , ";
			$query .= "`summ` , `liter` , `liter_cost` , `full` , ";
			$query .= "`expense` , `point` , `description`) VALUES (";
			$query .= "'".$arData["auto"]."', '".$arData["date"]."', '".$arData["odo"]."', '".$arData["fuel_mark"]."', ";
			$query .= "'".$arData["summ"]."', '".$arData["liters"]."', '".$arData["cost_liter"]."', '".$arData["full_tank"]."', ";
			$query .= "'".$arData["expense"]."', '".$arData["fuel_point"]."', '".$arData["comment"]."');";
			if ($res = $DB->Insert($query)) {
				return $res;
			}
			else {
				return false;
			}

		}

		/**
		 * Функция проверяет нет ли более поздних данных о заправках, после добавленной
		 *
		 * @param int $date
		 * @param int $car
		 * @return bool
		 */
		public function CheckLastFuelCosts ($date=0, $car=0) {
			global $DB;
			if ($date==0) $date = CInvestToCarMain::ConvertDateToTimestamp();
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `date` >".$date." LIMIT 0 , 5";
			if ($res = $DB->Select($query)) {
				return false;
			}
			else {
				return true;
			}

		}

		/**
		 * Функция выполняет пересчет расхода для всех записей о заправках
		 *
		 * @param int $car
		 */
		public function RecalculationExpense ($car=0) {
			global $DB;

			$query = "SELECT `id` , `date` , `auto` , `odo` , `liter` , `liter_cost` , `full` FROM `".CInvestToCarMain::GetTableByCode("fuel")."`";
			if ($car>0) {
				$query .= " WHERE `auto` =".$car;
			}
			$query .= " ORDER BY `date` ASC";
			$first = true;
			$res = $DB->Select($query);
			foreach ($res as $arRes) {
				$expense = 0;
				if ($first) {
					$first = false;
					self::UpdateExpense($arRes["id"],0);
				}
				else {
					if ($arRes["full"]>0) {
						$expense = self::CalculationExpense($arRes["odo"],$arRes["liter"],$arRes["auto"],$arRes["date"]);
						self::UpdateExpense($arRes["id"],$expense);
					}
					else {
						self::UpdateExpense($arRes["id"],0);
					}
				}
			}

		}

		/**
		 * Функция высчитывает расход топлива на 100км
		 *
		 * @param int $odo
		 * @param int $liters
		 * @param int $car
		 * @param int $date
		 * @return float|int
		 */
		public function CalculationExpense ($odo=0,$liters=0,$car=0,$date=0) {
			global $DB;
			if ($odo==0 || $liters==0) return 0;
			if ($date==0) $date = CInvestToCarMain::ConvertDateToTimestamp();
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();

			$query = "SELECT `odo` , `liter` , `full` FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `auto` =".$car." AND `date` <".$date." ORDER BY `date` DESC";
			//$query = "SELECT `odo` , `liter` , `full` FROM `ms_icar_fuel` WHERE `auto` =".$car." AND `date` <1428699600 ORDER BY `date` DESC";
			if ($res = $DB->Select($query)) {
				$mileage = 0;
				$liter_sum = $liters;
				$expense = 0;
				$null = 0;
				foreach ($res as $arRes) {
					if (intval($arRes["full"])>0) {
						if ($arRes["odo"]>0) {
							if ($null == 1) {
								//echo "liter_sum = ".$liter_sum." + ".$arRes["liter"]." = ";
								$liter_sum += $arRes["liter"];
								//echo $liter_sum."<br>";
							}
							//echo "mileage = ".$odo." - ".$arRes["odo"]." = ";
							$mileage = $odo - $arRes["odo"];
							//echo $mileage."<br>";
							break;
						}
						else {
							$null = 1;
							//echo "liter_sum = ".$liter_sum." + ".$arRes["liter"]." = ";
							$liter_sum += $arRes["liter"];
							//echo $liter_sum."<br>";
						}
					}
					else {
						//echo "liter_sum = ".$liter_sum." + ".$arRes["liter"]." = ";
						$liter_sum += $arRes["liter"];
						//echo $liter_sum."<br>";

					}
				}
				if ($mileage>0) {
					//echo "expense = (".$liter_sum." * 100) / ".$mileage." = ";
					$expense = ($liter_sum*100)/$mileage;
					$expense = round($expense,2);
					//echo $expense."<br>";
					return $expense;
				}
				else {
					return 0;
				}
			}
			else {
				return 0;
			}
		}

		/**
		 * Функция обновляет значение расхода для указанной записи
		 *
		 * @param $id
		 * @param $expense
		 * @return bool
		 */
		public function UpdateExpense ($id, $expense) {
			global $DB;

			$query = "UPDATE `".CInvestToCarMain::GetTableByCode("fuel")
			         ."` SET `expense` = '".$expense."' WHERE `"
			         .CInvestToCarMain::GetTableByCode("fuel")."`.`id` =".$id.";";
			if ($res = $DB->Update($query)) {
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает массив данных записи о заправке, по ее ID
		 *
		 * @param int $id
		 * @return bool
		 */
		public function GetFuelCostsByID ($id=0) {
			global $DB;
			if ($id==0) return false;

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `id` =".$id;
			if ($res = $DB->Select($query)) {
				return $res[0];
			}
			else {
				return false;
			}
		}

		/**
		 * Функция обновляет данные о заправке и пересчитывает все показания расхода топлива
		 *
		 * @param array $post
		 * @return bool
		 */
		public function UpdateFuelCosts ($post=array()) {
			if (empty($post)) return false;

			$arData = array();

			$arData["id"] = intval($post["id"]);
			$arData["auto"] = intval($post["fuel_auto"]);
			$arData["date"] = CInvestToCarMain::ConvertDateToTimestamp($post["date"]);
			$arData["odo"] = floatval(str_replace(",",".",$post["odo"]));
			$arData["fuel_mark"] = intval($post["fuel_mark"]);
			$arData["liters"] = floatval(str_replace(",",".",$post["liters"]));
			$arData["cost_liter"] = floatval(str_replace(",",".",$post["cost_liter"]));
			$arData["summ"] = $arData["liters"] * $arData["cost_liter"];
			if (isset($post["full_tank"])) {
				$arData["full_tank"] = 1;
			}
			else {
				$arData["full_tank"] =0;
			}
			$arData["fuel_point"] = intval($post["fuel_point"]);
			if ($arData["fuel_point"]==0) {
				$arData["fuel_point"] = CInvestToCarPoints::CreateNewPoint (
					$post["newpoint_name"],
					$post["newpoint_address"],
					$post["newpoint_lon"],
					$post["newpoint_lat"],
					2
				);
			}
			$arData["comment"] = htmlspecialchars($post["comment"]);
			if ($arData["full_tank"] > 0) {
				$arData["expense"] = self::CalculationExpense($arData["odo"],$arData["liters"],$arData["auto"],$arData["date"]);
			}
			else {
				$arData["expense"] = 0;
			}
			if ($res = self::UpdateFuelCostsDB($arData)) {
				self::RecalculationExpense($arData["auto"]);
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция обновляет данные о заправке в DB
		 *
		 * @param array $arData
		 * @return bool
		 */
		public function UpdateFuelCostsDB ($arData=array()) {
			global $DB;
			if (empty($arData)) return false;

			$query = "UPDATE `".CInvestToCarMain::GetTableByCode("fuel")."` SET ";
			$query .= "`auto` = '".$arData["auto"]."', ";
			$query .= "`date` = '".$arData["date"]."', ";
			$query .= "`odo` = '".$arData["odo"]."', ";
			$query .= "`fuel_mark` = '".$arData["fuel_mark"]."', ";
			$query .= "`summ` = '".$arData["summ"]."', ";
			$query .= "`liter` = '".$arData["liters"]."', ";
			$query .= "`liter_cost` = '".$arData["cost_liter"]."', ";
			$query .= "`full` = '".$arData["full_tank"]."', ";
			$query .= "`expense` = '".$arData["expense"]."', ";
			$query .= "`point` = '".$arData["fuel_point"]."', ";
			$query .= "`description` = '".$arData["comment"]."' ";
			$query .= "WHERE `id` =".$arData["id"].";";


			if ($res = $DB->Update($query)) {
				return $res;
			}
			else {
				return false;
			}

		}

		/**
		 * Функция удаляет из DB информацию о заправке и пересчитывает значения расхода топлива
		 *
		 * @param array $post
		 * @return bool
		 */
		public function DeleteFuelCostsDB ($post=array()) {
			global $DB;
			if (empty($post)) return false;

			$arData["id"] = intval($post["id"]);
			$query = "SELECT `auto` FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `id` =".$arData["id"];
			if ($res = $DB->Select($query)) {
				$arData["auto"] = $res[0]["auto"];
				$query2 = "DELETE FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `id` = ".$arData["id"];
				if ($res2 = $DB->Delete($query2)) {
					self::RecalculationExpense($arData["auto"]);
					return $res2;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает число израсходованных литров бензина, основываясь на записях о заправках
		 *
		 * @param int $car
		 * @return float
		 */
		public function GetTotalSpentFuel($car=0) {
			global $DB;
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();

			$query = "SELECT SUM(`liter`) FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);
			$res = $res[0]["SUM(`liter`)"];
			return round($res,2);
		}

		public function CreateTables () {
			$arTables = array();
			$arTables[] = self::QueryTableFuel();

			return $arTables;
		}

		public function QueryTableFuel () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("fuel")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`auto` INT (11) NOT NULL, ";
			$query .= "`date` INT (11) NOT NULL, ";
			$query .= "`odo` FLOAT NOT NULL, ";
			$query .= "`fuel_mark` INT (11) NOT NULL, ";
			$query .= "`summ` FLOAT NOT NULL, ";
			$query .= "`liter` FLOAT NOT NULL, ";
			$query .= "`liter_cost` FLOAT NOT NULL, ";
			$query .= "`full` TINYINT(1) NOT NULL, ";
			$query .= "`expense` FLOAT NOT NULL, ";
			$query .= "`point` INT (11) NOT NULL, ";
			$query .= "`description` VARCHAR(255) NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

	}