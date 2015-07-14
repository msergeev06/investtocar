<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarOdo
	{
		public static $arMessage = array();

		/**
		 * Функция возвращает html-код графика Километража
		 *
		 * @param string $arSettings
		 * @return bool|string
		 */
		public function ShowChartsOdo ($arSettings = "") {
			global $OPTIONS;
			if (!is_array ($arSettings))
			{
				return false;
			}

			if (intval ($arSettings["type"]) == 1)
			{
				//за Текущий месяц
				$firstMonthDay = mktime (0, 0, 0, date ("n"), 1, date ("Y")) + $OPTIONS->GetOptionInt("mktime_add_time");
				$daysInMonth = date ("t");
				$lastMonthDay = mktime (0, 0, 0, date ("n"), $daysInMonth, date ("Y")) + $OPTIONS->GetOptionInt("mktime_add_time");
				if ($data = self::GetListOdoFromTo ($firstMonthDay, $lastMonthDay))
				{
					$arSettings["data"] = $data;
				}
				$arSettings["fromTitle"] = CInvestToCarMain::GetNameMonth (date ("n"))." ".date ("Y")." г.";
			}
			elseif (intval ($arSettings["type"]) == 2)
			{
				//за Предыдущий месяц
				$prevMonth = date ("n") - 1;
				$year = date ("Y");
				if ($prevMonth == 0)
				{
					$prevMonth = 12;
					$year = $year - 1;
				}
				$firstMonthDay = mktime (0, 0, 0, $prevMonth, 1, $year) + $OPTIONS->GetOptionInt("mktime_add_time");
				$daysInMonth = date ("t", mktime (0, 0, 0, $prevMonth, 1, $year) + $OPTIONS->GetOptionInt("mktime_add_time"));
				$lastMonthDay = mktime (0, 0, 0, $prevMonth, $daysInMonth, $year) + $OPTIONS->GetOptionInt("mktime_add_time");
				if ($data = self::GetListOdoFromTo ($firstMonthDay, $lastMonthDay))
				{
					$arSettings["data"] = $data;
				}
				$arSettings["fromTitle"] = CInvestToCarMain::GetNameMonth ($prevMonth)." ".$year." г.";
			}
			elseif (intval ($arSettings["type"]) == 3)
			{
				//за Текущий год
				$firstMonthDay = mktime (0, 0, 0, 1, 1, date ("Y")) + $OPTIONS->GetOptionInt("mktime_add_time");
				$lastMonthDay = mktime (0, 0, 0, 12, 31, date ("Y")) + $OPTIONS->GetOptionInt("mktime_add_time");
				if ($data = self::GetListOdoFromTo ($firstMonthDay, $lastMonthDay, 1))
				{
					$arSettings["data"] = $data;
				}
				$arSettings["fromTitle"] = date ("Y")." г.";
			}

			$arSettings["fullTitle"] = GetMessage("WE_DROVE_KM_FOR",array("PERIOD"=>$arSettings["fromTitle"]));

			if ($echo = CInvestToCarCharts::HtmlCharts ($arSettings))
			{
				return $echo;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Функция получает данные из DB о километраже за заданный период
		 *
		 * @param $from Начало периода (включительно)
		 * @param $to Окончание периоды (включительно)
		 * @param int $year Средние данные по месяцам (за год)
		 * @param int $car ID автомобля
		 * @return array|bool Данные либо false
		 */
		public function GetListOdoFromTo ($from, $to, $year = 0, $car = 1)
		{
			global $DB;

			if (intval ($from) < 10000
			    || intval ($to) < 10000
			    || intval ($car) == 0
			)
			{
				return false;
			}

			$query = "SELECT `date` , `odo` FROM `".CInvestToCarMain::GetTableByCode("odo")."` WHERE date BETWEEN ".$from." AND ".$to
			         ." ORDER BY `date` ASC";
			$res = $DB->Select ($query);

			$arResult = array ();
			$odoMonth = 0;
			$lastMonth = 0;
			foreach ($res as $arRes)
			{
				if ($year == 0)
				{
					$x = date ("j", $arRes["date"])." (".CInvestToCarMain::GetNameDayOfWeek (date ("w", $arRes["date"])).")";
					$arResult[$x] = $arRes["odo"];
				}
				else
				{
					//Если необходимо средние значения по месяцам за год
					$x = CInvestToCarMain::GetNameMonth (date ("n", $arRes["date"]));
					if (date ("n", $arRes["date"]) > $lastMonth)
					{
						$lastMonth = date ("n", $arRes["date"]);
						$odoMonth = $arRes["odo"];
						$arResult[$x] = $odoMonth;
					}
					else
					{
						$odoMonth += $arRes["odo"];
						$arResult[$x] = $odoMonth;
					}
				}
			}
			//echo $query."<br>";
			//echo "<pre>"; print_r($arResult); echo "</pre>";
			return $arResult;
		}

		/**
		 * Функция обновляет данные о пробеге для заданной даты либо для всей базы
		 *
		 * @param int $date
		 */
		public function UpdateDayOdometer ($date = 0)
		{
			global $DB,$OPTIONS;
			//$startTimestamp = 1426194000;
			$startTimestamp = 0;
			$nowTimestamp = time ();

			if (intval ($date) == 0)
			{
				// Если дата не задана, обновляем информацию по всем дням.
				$query = "SELECT `id` , `auto` , `date` , `odo` FROM `".CInvestToCarMain::GetTableByCode("routs")."` ORDER BY `date` ASC";
				$arSelect = $DB->Select ($query);
			}
			else
			{
				// Если дата задана, обновляем данные только данного дня
				$query = "SELECT `id` , `auto` , `date` , `odo` FROM `".CInvestToCarMain::GetTableByCode("routs")."` WHERE `date` = ".$date." ORDER BY `date` ASC";
				$arSelect = $DB->Select ($query);
			}

			$arResult["odo"] = array ();

			foreach ($arSelect as $select)
			{
				$day = date ("d", $select["date"]);
				$month = date ("m", $select["date"]);
				$year = date ("Y", $select["date"]);
				$endDay = date ("t", $select["date"]);
				$odo = (floatval ($select["odo"]) > 0) ? floatval ($select["odo"]) : 0;
				$arResult["odo"][$select["auto"]][$year][$month]["endDay"] = $endDay;
				if (!isset($arResult["odo"][$select["auto"]][$year][$month]["days"][$day]["odo"])
				    || $odo > $arResult[$select["auto"]][$year][$month]["days"][$day]["odo"]
				)
				{
					$arResult["odo"][$select["auto"]][$year][$month]["days"][$day]["odo"] = $odo;
				}
				if ($startTimestamp == 0)
				{
					$startTimestamp = CInvestToCarMain::ConvertDateToTimestamp(date("d.m.Y", $select["date"]));
				}
			}

			$lastOdo = 0;

			foreach ($arResult["odo"] as $car => &$arCar)
			{
				foreach ($arCar as $year => &$arYear)
				{
					foreach ($arYear as $month => &$arMonth)
					{
						for ($i = 1; $i <= $arMonth["endDay"]; $i++)
						{
							if ($i > 0 && $i < 10)
							{
								$day = '0'.$i;
							}
							else
							{
								$day = strval ($i);
							}

							if ((mktime (0, 0, 0, $month, $i, $year) + $OPTIONS->GetOptionInt("mktime_add_time")) >= $startTimestamp
							    && (mktime (0, 0, 0, $month, $i, $year) + $OPTIONS->GetOptionInt("mktime_add_time")) <= $nowTimestamp
							)
							{
								if (!isset($arMonth["days"][$day]))
								{
									$arMonth["days"][$day]["odo"] = 0;
									$arMonth["days"][$day]["mil"] = 0;
								}
								elseif ($arMonth["days"][$day]["odo"] > 0)
								{
									$arMonth["days"][$day]["mil"] = round ($arMonth["days"][$day]["odo"] - $lastOdo, 1);
									$lastOdo = $arMonth["days"][$day]["odo"];
								}
							}
						}
						ksort ($arMonth["days"]);
					}
				}
			}

			foreach ($arResult["odo"] as $car => &$arCar)
			{
				foreach ($arCar as $year => &$arYear)
				{
					foreach ($arYear as $month => &$arMonth)
					{
						foreach ($arMonth["days"] as $day => $arDay)
						{
							$dateTimes = mktime (0, 0, 0, $month, $day, $year) + $OPTIONS->GetOptionInt("mktime_add_time");
							$query =
								"SELECT `id` FROM `".CInvestToCarMain::GetTableByCode("odo")."` WHERE `auto` =".$car." AND `date` =".$dateTimes;
							//echo $query."<br>";
							if (!$res = $DB->Select ($query))
							{
								// Insert
								$query = "INSERT INTO `".CInvestToCarMain::GetTableByCode("odo")."` (`auto` ,`date` ,`odo`) VALUES ('".$car."', '"
								         .$dateTimes."', '".floatval ($arDay["mil"])."');";
								$res2 = $DB->Insert ($query);
								//echo $query."<br>";
							}
							else
							{
								// Update
								$query =
									"UPDATE `".CInvestToCarMain::GetTableByCode("odo")."` SET `odo` = '".floatval ($arDay["mil"])."' WHERE `id` ="
									.$res[0]["id"].";";
								$res2 = $DB->Update ($query);
								//echo $query."<br>";
							}

						}
					}
				}
			}


			//echo "<pre>"; print_r($res); echo "</pre><br>";
			//echo "<pre>"; print_r($arResult); echo "</pre>";
		}


		/**
		 * Функция возвращает текущий пробег, находя максимальную запись в разных таблицах
		 *
		 * @param int $car
		 * @return float
		 */
		public function GetCurrentMileage ($car=0) {
			global $DB;
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();
			$mileage = 0;

			//Максимальный пробег в записях о заправках
			$query = "SELECT MAX(`odo`) AS maxODO FROM `".CInvestToCarMain::GetTableByCode("fuel")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);
			$res = $res[0]["maxODO"];
			if ($res>$mileage) $mileage = $res;

			//Максимальный пробег в записях о маршрутах
			$query = "SELECT MAX(`odo`) AS maxODO FROM `".CInvestToCarMain::GetTableByCode("routs")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);
			$res = $res[0]["maxODO"];
			if ($res>$mileage) $mileage = $res;

			//Максимальный пробег в записях о запчастях
			$query = "SELECT MAX(`odo`) AS maxODO FROM `".CInvestToCarMain::GetTableByCode("repairparts")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);
			$res = $res[0]["maxODO"];
			if ($res>$mileage) $mileage = $res;

			//Максимальный пробег в записях о прохождении ТО
			$query = "SELECT MAX(`odo`) AS maxODO FROM `".CInvestToCarMain::GetTableByCode("ts")."` WHERE `auto` =".$car;
			$res = $DB->Select($query);
			$res = $res[0]["maxODO"];
			if ($res>$mileage) $mileage = $res;

			return round($mileage,2);
		}

		/**
		 * Функция добавляет информацию о новом маршруте, точках и пробеге
		 *
		 * @param string $post
		 * @return bool
		 */
		public function AddNewRoute ($post = "")
		{
			$arResult["auto"] = intval ($post["auto"]);
			$arResult["date"] = CInvestToCarMain::ConvertDateToTimestamp($post["date"]);
			$arResult["start_point"] = intval ($post["start_point"]);
			if ($arResult["start_point"] == 0)
			{
				$arResult["start_point"] = CInvestToCarPoints::CreateNewPoint (
					$post["start_name"],
					$post["start_address"],
					$post["start_lon"],
					$post["start_lat"]
				);
			}
			if (isset($post["end_start"]))
			{
				$arResult["end_start"] = 1;
				$arResult["end_point"] = 0;
			}
			else
			{
				$arResult["end_start"] = 0;
				$arResult["end_point"] = intval ($post["end_point"]);
				if ($arResult["end_point"] == 0)
				{
					$arResult["end_point"] = CInvestToCarPoints::CreateNewPoint (
						$post["end_name"],
						$post["end_address"],
						$post["end_lon"],
						$post["end_lat"]
					);
				}
			}
			$arResult["odo"] = $post["odo"];
			$arResult["odo"] = str_replace (",", ".", $arResult["odo"]);
			$arResult["odo"] = floatval ($arResult["odo"]);

			$res = self::AddNewRouteDB ($arResult);

			if (intval ($res) > 0)
			{
				/*
				if (intval($arResult["start_point"])>0) {
					self::IncreasePointPeriod($arResult["start_point"]);
				}
				*/

				if (intval ($arResult["end_point"]) > 0)
				{
					CInvestToCarPoints::IncreasePointPeriod ($arResult["end_point"]);
				}

				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Функция добавляет информацию о новом маршруте в DB
		 *
		 * @param array $data Массив данных
		 * @return mixed Результат
		 */
		public function AddNewRouteDB ($data)
		{
			global $DB;

			$query =
				"INSERT INTO `".CInvestToCarMain::GetTableByCode("routs")."` (`auto` , `date` , `start_point` , `end_start` , `end_point` ,`odo` ) VALUES ('"
				.$data["auto"]."', '".$data["date"]."', '".$data["start_point"]."', '".$data["end_start"]."', '"
				.$data["end_point"]."', '".$data["odo"]."');";

			$res = $DB->Insert ($query);

			return $res;
		}

		public function CreateTables () {
			$arTables = array();
			$arTables[] = self::QueryTableOdo();
			$arTables[] = self::QueryTableRouts();

			return $arTables;
		}

		public function QueryTableOdo () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("odo")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`auto` INT (11) NOT NULL, ";
			$query .= "`date` INT (11) NOT NULL, ";
			$query .= "`odo` FLOAT NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function QueryTableRouts () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("routs")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`auto` INT (11) NOT NULL, ";
			$query .= "`date` INT (11) NOT NULL, ";
			$query .= "`start_point` INT (11) NOT NULL, ";
			$query .= "`end_start` TINYINT(1) NOT NULL, ";
			$query .= "`end_point` INT (11) NOT NULL, ";
			$query .= "`odo` FLOAT NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function DataTables () {
			$arData = array();
			$arData["odo"] = self::DataTableOdo();
			$arData["routs"] = self::DataTableRouts();

			return $arData;
		}

		public function DataTableOdo () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("odo")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array("id", "auto", "date", "odo");
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["auto"],$arRes["date"],$arRes["odo"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

		public function DataTableRouts () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("routs")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array("id", "auto", "date", "start_point", "end_start", "end_point", "odo");
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["auto"],$arRes["date"],$arRes["start_point"],
						$arRes["end_start"],$arRes["end_point"],$arRes["odo"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

	}