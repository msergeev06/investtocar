<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarMain
	{
		public static $arMessage = array();
		/**
		 * Функция возвращает html-код графика Километража
		 *
		 * @param string $arSettings
		 * @return bool|string
		 */
		public function ShowChartsOdo ($arSettings = "")
		{
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
				$arSettings["fromTitle"] = self::GetNameMonth (date ("n"))." ".date ("Y")." г.";
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
				$arSettings["fromTitle"] = self::GetNameMonth ($prevMonth)." ".$year." г.";
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

			if ($echo = self::HtmlCharts ($arSettings))
			{
				return $echo;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Функция возвращает html-код графика
		 *
		 * @param string $arSettings
		 * * chartWidth Ширина графика
		 * * chartHeight Высота графика
		 * * xTitle Заголовок оси Х
		 * * yTitle Заголовок оси У
		 * * data = array (dataX => dataY) Данные
		 * * fullTitle Заголовок графика
		 * @return bool|string
		 */
		public function HtmlCharts ($arSettings = "")
		{
			if (!is_array ($arSettings) || !isset($arSettings["data"]))
			{
				return false;
			}

			$echo = "<div id=\"curve_chart\" style=\"width: ".$arSettings["chartWidth"]."px; height: "
			        .$arSettings["chartHeight"]."px\"></div>\n";
			$echo .= "\t<script type=\"text/javascript\" src=\"https://www.google.com/jsapi?autoload={\n";
			$echo .= "\t\t'modules':[{\n\t\t'name':'visualization',\n\t\t'version':'1',\n\t\t'packages':['corechart']\n\t\t}]\n\t}\"></script>\n";
			$echo .= "\t<script type=\"text/javascript\">\n\tgoogle.setOnLoadCallback(drawChart);\n\n";
			$echo .= "\t\tfunction drawChart() {\n";
			$echo .= "\t\t\tvar data = google.visualization.arrayToDataTable([\n";
			$echo .= "\t\t\t\t['".$arSettings["xTitle"]."', '".$arSettings["yTitle"]."']";

			foreach ($arSettings["data"] as $x => $y)
			{
				$echo .= ",\n\t\t\t\t['".$x."',  ".$y."]";
			}

			$echo .= "\n\t\t\t]);\n";
			$echo .= "\t\t\tvar options = {\n";

			$echo .= "\t\t\t\ttitle: '".$arSettings["fullTitle"]."',\n";
			$echo .= "\t\t\t\tcurveType: 'function',\n";
			$echo .= "\t\t\t\tlegend: { position: 'bottom' }\n";
			$echo .= "\t\t\t};\n\n";

			$echo .= "\t\t\tvar chart = new google.visualization.LineChart(document.getElementById('curve_chart'));\n";
			$echo .= "\t\t\tchart.draw(data, options);\n";
			$echo .= "\t\t}\n";
			$echo .= "\t</script>\n";

			return $echo;

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
			$arResult["date"] = self::ConvertDateToTimestamp($post["date"]);
			$arResult["start_point"] = intval ($post["start_point"]);
			if ($arResult["start_point"] == 0)
			{
				$arResult["start_point"] = self::CreateNewPoint (
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
					$arResult["end_point"] = self::CreateNewPoint (
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
					self::IncreasePointPeriod ($arResult["end_point"]);
				}

				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Функция увеличивает частоту выбора маршрутной точки на 1
		 *
		 * @param int $pointID
		 * @return bool
		 */
		public function IncreasePointPeriod ($pointID = 0)
		{
			global $DB;

			if ($pointID == 0)
			{
				return false;
			}

			$query =
				"UPDATE `ms_icar_points` SET `period` = `period` + 1 WHERE `ms_icar_points`.`id` =".$pointID.";";

			$res = $DB->Update ($query);

			return $res;

		}

		/**
		 * Функция получает координаты объекта по его адресу через сервис Яндекса
		 *
		 * @param string $address Адрес
		 * @return array|bool Массив координат, либо false
		 */
		public function GetCoordsByAddressYandex ($address = "")
		{
			if ($address != "")
			{
				$xmlStr = file_get_contents ("http://geocode-maps.yandex.ru/1.x/?geocode=".urlencode ($address));
				$xml = simplexml_load_string ($xmlStr);
				$arCoords["all"] = $xml->GeoObjectCollection->featureMember->GeoObject->Point->pos;
				list($arCoords["lon"], $arCoords["lat"]) = explode (" ", $arCoords["all"]);

				return $arCoords;
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
				"INSERT INTO `ms_icar_routs` (`auto` , `date` , `start_point` , `end_start` , `end_point` ,`odo` ) VALUES ('"
				.$data["auto"]."', '".$data["date"]."', '".$data["start_point"]."', '".$data["end_start"]."', '"
				.$data["end_point"]."', '".$data["odo"]."');";

			$res = $DB->Insert ($query);

			return $res;
		}

		/**
		 * Функция добавляет новую точку в список
		 *
		 * @param array $data
		 * @return mixed
		 */
		public function AddNewPointDB ($data)
		{
			global $DB;

			if (!isset($data["type"]))
			{
				$data["type"] = 1;
			}

			$query =
				"INSERT INTO `ms_icar_points` (`name`, `type`, `address`, `longitude`, `latitude`) VALUES ('"
				.$data["name"]."', '".$data["type"]."', '".$data["address"]."', '"
				.$data["longitude"]."', '".$data["latitude"]."')";

			$res = $DB->Insert ($query);

			return $res;
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
				$query = "SELECT `id` , `auto` , `date` , `odo` FROM `ms_icar_routs` ORDER BY `date` ASC";
				$arSelect = $DB->Select ($query);
			}
			else
			{
				// Если дата задана, обновляем данные только данного дня
				$query = "SELECT `id` , `auto` , `date` , `odo` FROM `ms_icar_routs` WHERE `date` = ".$date." ORDER BY `date` ASC";
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
					$startTimestamp = self::ConvertDateToTimestamp(date("d.m.Y", $select["date"]));
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
								"SELECT `id` FROM `ms_icar_odometer` WHERE `auto` =".$car." AND `date` =".$dateTimes;
							//echo $query."<br>";
							if (!$res = $DB->Select ($query))
							{
								// Insert
								$query = "INSERT INTO `ms_icar_odometer` (`auto` ,`date` ,`odo`) VALUES ('".$car."', '"
								         .$dateTimes."', '".floatval ($arDay["mil"])."');";
								$res2 = $DB->Insert ($query);
								//echo $query."<br>";
							}
							else
							{
								// Update
								$query =
									"UPDATE `ms_icar_odometer` SET `odo` = '".floatval ($arDay["mil"])."' WHERE `id` ="
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

			$query = "SELECT `date` , `odo` FROM `ms_icar_odometer` WHERE date BETWEEN ".$from." AND ".$to
			         ." ORDER BY `date` ASC";
			$res = $DB->Select ($query);

			$arResult = array ();
			$odoMonth = 0;
			$lastMonth = 0;
			foreach ($res as $arRes)
			{
				if ($year == 0)
				{
					$x = date ("j", $arRes["date"])." (".self::GetNameDayOfWeek (date ("w", $arRes["date"])).")";
					$arResult[$x] = $arRes["odo"];
				}
				else
				{
					//Если необходимо средние значения по месяцам за год
					$x = self::GetNameMonth (date ("n", $arRes["date"]));
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
		 * Возвращает сокращенное наименование дня недели
		 * (Пн., Вт., Ср., Чт., Пт., Сб., Вс.)
		 *
		 * @param $day Число дня недели date("w");
		 * @return bool|string Если ошибка false
		 */
		public function GetNameDayOfWeek ($day)
		{
			switch ($day)
			{
				case 0:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_SUN");
				case 1:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_MON");
				case 2:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_TUE");
				case 3:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_WED");
				case 4:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_THU");
				case 5:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_FRI");
				case 6:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_SAT");
				default:
					return false;
			}
		}

		/**
		 * Возвращает название месяца по его номеру
		 * (Январь, Фебраль, Март, Апрель, Май, Июнь,
		 * Июль, Август, Сентябрь, Октябрь, Ноябрь, Декабрь)
		 *
		 * @param $month Число месяца
		 * @return bool|string Название, либо false
		 */
		public function GetNameMonth ($month)
		{
			$month = intval ($month);
			switch ($month)
			{
				case 1:
					return GetMessage("JANUARY");
				case 2:
					return GetMessage("FEBRUARY");
				case 3:
					return GetMessage("MARCH");
				case 4:
					return GetMessage("APRIL");
				case 5:
					return GetMessage("MAY");
				case 6:
					return GetMessage("JUNE");
				case 7:
					return GetMessage("JULY");
				case 8:
					return GetMessage("AUGUST");
				case 9:
					return GetMessage("SEPTEMBER");
				case 10:
					return GetMessage("OCTOBER");
				case 11:
					return GetMessage("NOVEMBER");
				case 12:
					return GetMessage("DECEMBER");
				default:
					return false;
			}
		}

		/**
		 * Функция получает подробные данные о всех автомобилях в гараже, либо о заданном
		 *
		 * @return array
		 */
		public function GetMyCarsInfo ($car=0)
		{
			global $DB;

			if (intval($car) == 0) {
				$query = "SELECT * FROM `ms_icar_my_car` ORDER BY `name`";
			}
			else {
				$query = "SELECT * FROM `ms_icar_my_car` WHERE `id`=".$car;
			}
			$res = $DB->Select ($query);

			foreach ($res as &$arCar)
			{
				$query2 = "SELECT * FROM `ms_icar_setup_car_brand` WHERE `id` =".$arCar["trademark"];
				$res2 = $DB->Select ($query2);
				$arCar["trademark"] = $res2[0]["name"];

				$query2 = "SELECT * FROM `ms_icar_setup_car_model` WHERE `id` =".$arCar["model"];
				$res2 = $DB->Select ($query2);
				$arCar["model"] = $res2[0]["name"];

				$query2 = "SELECT * FROM `ms_icar_setup_car_gearbox` WHERE `id` =".$arCar["gearshift"];
				$res2 = $DB->Select ($query2);
				$arCar["gearshift"] = $res2[0]["name"];

				$query2 = "SELECT * FROM `ms_icar_setup_car_body` WHERE `id` =".$arCar["body"];
				$res2 = $DB->Select ($query2);
				$arCar["body"] = $res2[0]["name"];

				$arCar["total_costs"] = self::GetTotalCosts($arCar["id"]);
				$arCar["average_fuel_consum"] = self::GetAverageFuelConsumption($arCar["id"]);
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
				$query = "SELECT * FROM `ms_icar_my_car` ORDER BY `name`";
			}
			else {
				$query = "SELECT * FROM `ms_icar_my_car` WHERE `id`=".$car;
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

			$query = "INSERT INTO `ms_icar_setup_car_model` (`brand` , `name`) VALUES ('".$brand."', '".$model."');";
			if ($res = $DB->Insert ($query))
			{
				return $res;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Функция добавляет новый автомобиль в гараж
		 *
		 * @param $arData Данные
		 * @return bool|int id нового автомобиля, либо false
		 */
		public function AddNewCarGarage ($arData)
		{
			global $DB;

			if (intval($arData["default"])==1) {
				//Удаляем отметку "по-умолчанию" с другого автомобиля (автомобилей)
				self::DeleteMarkDefaultCars();
			}

			$query = "INSERT INTO `ms_icar_my_car` (";
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
		 * @param int $car
		 * @param $arData
		 * @return bool
		 */
		public function UpdateCarInGarage ($car=0, $arData) {
			global $DB;

			if ($car==0) return false;

			if (intval($arData["default"])==1) {
				self::DeleteMarkDefaultCars();
			}

			$query = "UPDATE `ms_icar_my_car` SET `name` = '".$arData["name"]."', ";
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

			$query = "SELECT `id`,`default` FROM `ms_icar_my_car` WHERE `default`=1";
			$res = $DB->Select($query);
			foreach ($res as $arCar) {
				$query2 = "UPDATE `ms_icar_my_car` SET `default` = '0' WHERE `id` =".$arCar["id"].";";
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

			$query = "SELECT `id` , `auto` FROM `ms_icar_routs` WHERE `auto` =".$car;
			$res = $DB->Select($query);

			if (isset($res[0]["id"])) {
				$canDelete = false;
				self::$arMessage["CAN_DELETE"][] = GetMessage("REFERENCED_DATA_TRIPS")."<br>";
			}

			$query = "SELECT `id` , `auto` FROM `ms_icar_fuel` WHERE `auto` =".$car;
			$res = $DB->Select($query);

			if (isset($res[0]["id"])) {
				$canDelete = false;
				self::$arMessage["CAN_DELETE"][] = GetMessage("PETROL_STATION_LINKED")."<br>";
			}

			$query = "SELECT `id` , `auto` FROM `ms_icar_odometer` WHERE `auto` =".$car;
			$res = $DB->Select($query);

			if (isset($res[0]["id"])) {
				$canDelete = false;
				self::$arMessage["CAN_DELETE"][] = GetMessage("BOUND_TO_RUN_RECORD")."<br>";
			}

			$query = "SELECT `id` , `auto` FROM `ms_icar_ts` WHERE `auto` =".$car;
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

			$query = "DELETE FROM `ms_icar_my_car` WHERE `id` = ".$car;
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

			$query = "SELECT `id` , `default` FROM `ms_icar_my_car` WHERE `default` =1";
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

			$query = "SELECT * FROM `ms_icar_setup_car_brand` WHERE `id` =".$id;
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

			$query = "SELECT * FROM `ms_icar_setup_car_model` WHERE `id` =".$id;
			$res = $DB->Select($query);

			if (isset($res[0]["name"])) {
				return $res[0]["name"];
			}
			else {
				return "";
			}
		}

		/**
		 * Функция возвращает массив с информацией о точке, либо false
		 *
		 * @param int $id
		 * @return bool
		 */
		public function GetPointInfoByID ($id=0) {
			global $DB;

			if ($id == 0) return false;

			$query = "SELECT * FROM `ms_icar_points` WHERE `id` =".$id;
			$res = $DB->Select($query);
			if (isset($res[0]["id"])) {
				return $res[0];
			}
			else {
				return false;
			}
		}

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

			$query = "SELECT * FROM `ms_icar_ts` WHERE `auto` =".$car." ORDER BY `date` ASC";
			$res = $DB->Select($query);

			if (isset($res[0]["id"])) {
				foreach ($res as &$arTs) {
					if (intval($arTs["point"])>0) {
						$arTs["point"] = self::GetPointInfoByID($arTs["point"]);
					}
					$arTs["repair"] = self::GetRepairNameByID($arTs["repair"]);
				}
				return $res;
			}
			else {
				return false;
			}
		}

		/**
		 * Функция возвращает Название ремонтирующего по его ID
		 *
		 * @param $id
		 * @return string
		 */
		public function GetRepairNameByID ($id) {
			switch ($id) {
				case 1:
					return GetMessage("NO_DEALER");
				case 2:
					return GetMessage("DEALER");
				case 3:
					return GetMessage("SERVICE_STATION");
				case 4:
					return GetMessage("DID_HE");
				case 5:
					return GetMessage("PRIVATE_SERVICE");
				default:
					return GetMessage("NO_DATA");
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
			$arTs["date"] = self::ConvertDateToTimestamp($post["date"]);
			$arTs["repair"] = $post["ts_repair"];
			$arTs["cost"] = $post["cost"];
			$arTs["odo"] = $post["odo"];
			$arTs["point"] = $post["ts_point"];
			if ($arTs["point"]==0) {
				$arTs["point"] = self::CreateNewPoint (
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

			$query = "INSERT INTO `ms_icar_ts`";
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

			$query = "SELECT * FROM `ms_icar_ts` WHERE `id` =".$tsID;
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
			$arData["date"] = self::ConvertDateToTimestamp($arPost["date"]);
			$arData["ts_repair"] = intval($arPost["ts_repair"]);
			$arData["cost"] = floatval(str_replace(",",".",$arPost["cost"]));
			$arData["odo"] = floatval(str_replace(",",".",$arPost["odo"]));
			if ($arPost["ts_point"]==0) {
				$arData["point"] = self::CreateNewPoint (
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

			$query = "UPDATE `ms_icar_ts` SET ";
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

			$query = "DELETE FROM `ms_icar_ts` WHERE `id` = ".$tsID;
			return $res = $DB->Delete($query);
		}

		/**
		 * Функция получает массив записей о расходе топлива
		 *
		 * @param int $carID
		 * @return bool
		 */
		public function GetFuelList ($carID=0) {
			global $DB;
			if ($carID==0) return false;

			$query = "SELECT * FROM `ms_icar_fuel` WHERE `auto` =".$carID." ORDER BY `date` ASC";
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

			$query = "SELECT * FROM `ms_icar_setup_fuel_mark` WHERE `id` =".$fuelMarkID;
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
			if ($car==0) $car = self::GetDefaultCar();

			$query = "SELECT SUM(`summ`) FROM `ms_icar_fuel` WHERE `auto` =".$car;
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
		 * Функция возвращает общую сумму расходов на ТО
		 *
		 * @param int $car
		 * @return float|int
		 */
		public function GetTotalMaintenanceCosts ($car=0) {
			global $DB;
			if ($car==0) $car = self::GetDefaultCar();

			$query = "SELECT SUM(`cost`) FROM `ms_icar_ts` WHERE `auto` =".$car;
			$res = $DB->Select($query);
			$res = $res[0]["SUM(`cost`)"];
			if (floatval($res)>0) {
				return round($res, 2);
			}
			else {
				return 0;
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
			$summ += self::GetTotalFuelCosts($car);
			//Расходы на ТО
			$summ += self::GetTotalMaintenanceCosts($car);

			if ($summ>0) {
				return round($summ,2);
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
			if ($car==0) $car = self::GetDefaultCar();

			$query = "SELECT `expense` FROM `ms_icar_fuel` WHERE `auto` =".$car." ORDER BY `date` ASC";
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
			$arData["date"] = self::ConvertDateToTimestamp($post["date"]);
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
				$arData["fuel_point"] = self::CreateNewPoint (
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
				$increase = self::IncreasePointPeriod($arData["point"]);

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
		 * Функция создает новую точку, определяя координаты по адресу, если необходимо
		 *
		 * @param $name
		 * @param $address
		 * @param $lon
		 * @param $lat
		 * @param int $type
		 * @return int|mixed
		 */
		public function CreateNewPoint ($name, $address,$lon,$lat,$type=0) {
			global $OPTIONS;
			if ($type==0) $type = $OPTIONS->GetOptionInt("point_default");
			if (strlen($lon)<2 || strlen($lat)<2)
			{
				if (strlen ($address) > 3)
				{
					if ($arCoords = self::GetCoordsByAddressYandex ($address))
					{
						$lon = $arCoords["lon"];
						$lat = $arCoords["lat"];
					}
				}
			}
			$new_point = self::AddNewPointDB (
				array (
					"name"      => $name,
					"address"   => $address,
					"longitude" => $lon,
					"latitude"  => $lat,
					"type"      => $type
				)
			);
			if (intval ($new_point) > 0)
			{
				return $new_point;
			}
			else {
				return 0;
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

			$query = "INSERT INTO `ms_icar_fuel` (";
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
			if ($date==0) $date = self::ConvertDateToTimestamp();
			if ($car==0) $car = self::GetDefaultCar();

			$query = "SELECT * FROM `ms_icar_fuel` WHERE `date` >".$date." LIMIT 0 , 5";
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

			$query = "SELECT `id` , `date` , `auto` , `odo` , `liter` , `liter_cost` , `full` FROM `ms_icar_fuel`";
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
			if ($date==0) $date = self::ConvertDateToTimestamp();
			if ($car==0) $car = self::GetDefaultCar();

			$query = "SELECT `odo` , `liter` , `full` FROM `ms_icar_fuel` WHERE `auto` =".$car." AND `date` <".$date." ORDER BY `date` DESC";
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

			$query = "UPDATE `ms_icar_fuel` SET `expense` = '".$expense."' WHERE `ms_icar_fuel`.`id` =".$id.";";
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

			$query = "SELECT * FROM `ms_icar_fuel` WHERE `id` =".$id;
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
			$arData["date"] = self::ConvertDateToTimestamp($post["date"]);
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
				$arData["fuel_point"] = self::CreateNewPoint (
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

			$query = "UPDATE `ms_icar_fuel` SET ";
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
			$query = "SELECT `auto` FROM `ms_icar_fuel` WHERE `id` =".$arData["id"];
			if ($res = $DB->Select($query)) {
				$arData["auto"] = $res[0]["auto"];
				$query2 = "DELETE FROM `ms_icar_fuel` WHERE `id` = ".$arData["id"];
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
		 * Функция преобразует дату формата dd.mm.YYYY в timestamp
		 *
		 * @param string $date
		 * @return int
		 */
		public function ConvertDateToTimestamp ($date="") {
			global $OPTIONS;
			if ($date=="") $date=date("d.m.Y");
			list($day,$month,$year) = explode(".",$date);
			$timestamp = mktime(0,0,0,$month,$day,$year)+$OPTIONS->GetOptionInt("mktime_add_time");
			return $timestamp;
		}

		/**
		 * Функция добавляет информацию о запчасти
		 *
		 * @param array $post
		 * @return bool
		 */
		public function AddRepairParts ($post=array()) {
			global $OPTIONS;
			if (empty($post)) return false;
			$arData = array();

			$arData["auto"] = intval($post["auto"]);
			$arData["date"] = self::ConvertDateToTimestamp($post["date"]);
			$arData["name"] = htmlspecialchars($post["name"]);
			$arData["storage"] = intval($post["storage"]);
			$arData["catalog_number"] = htmlspecialchars($post["catalog_number"]);
			$arData["number"] = floatval(str_replace(",",".",$post["number"]));
			$arData["cost"] = floatval(str_replace(",",".",$post["cost"]));
			$arData["reason"] = intval($post["reason"]);
			switch ($arData["reason"]) {
				case $OPTIONS->GetOptionInt("reason_replacement_ts"):
					$arData["reason_detail"] = intval($post["reason_ts"]);
					break;
				case $OPTIONS->GetOptionInt("reason_replacement_breakdown"):
					$arData["reason_detail"] = intval($post["reason_breakdown"]);
					break;
				case $OPTIONS->GetOptionInt("reason_replacement_dtp"):
					$arData["reason_detail"] = intval($post["reason_dtp"]);
					break;
				case $OPTIONS->GetOptionInt("reason_replacement_tuning"):
					$arData["reason_detail"] = intval($post["reason_tuning"]);
					break;
				case $OPTIONS->GetOptionInt("reason_replacement_upgrade"):
					$arData["reason_detail"] = intval($post["reason_upgrade"]);
					break;
				case $OPTIONS->GetOptionInt("reason_replacement_tire"):
					$arData["reason_detail"] = 0;
					break;
			}
			$arData["who_paid"] = intval($post["who_paid"]);
			$arData["odo"] = floatval(str_replace(",",".",$post["odo"]));
			$arData["waypoint"] = intval($post["waypoint"]);
			if ($arData["waypoint"]==0) {
				$arData["waypoint"] = self::CreateNewPoint(
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

			$query = "INSERT INTO `ms_icar_repair_parts` (";
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
		    if ($car==0) $car = self::GetDefaultCar();
		    $arData = array();

		    $query = "SELECT * FROM `ms_icar_repair_parts` WHERE `auto` =".$car." ORDER BY `date` ASC";
		    if ($res = $DB->Select($query)) {
			    $i=0;
			    foreach ($res as $arRes) {
				    $arData[$i]["id"] = $arRes["id"];
			        //$arData[$i]["auto"] = self::GetMyCarsInfo($car);
				    $arData[$i]["date"] = date("d.m.Y",$arRes["date"]);
				    $arData[$i]["name"] = $arRes["name"];
				    $arData[$i]["storage"] = self::GetNameByIDFromDB($arRes["storage"],"ms_icar_setup_storage");
				    $arData[$i]["catalog_number"] = $arRes["catalog_number"];
				    $arData[$i]["number"] = round($arRes["number"],2);
				    $arData[$i]["cost"] = number_format($arRes["cost"],2);
				    $arData[$i]["reason"] = self::GetNameByIDFromDB($arRes["reason"],"ms_icar_setup_reason_replacement");
				    if ($arRes["reason"]==$OPTIONS->GetOptionInt("reason_replacement_ts")) {
						if ($arRes["reason_detail"]>0) {
							$arTemp = self::GetTsInfo($arRes["reason_detail"]);
							$arData[$i]["reason_detail"] = date("d.m.Y",$arTemp[0]["date"])." ".GetMessage("TS")."-".$arTemp[0]["ts_num"];
						}
					    else {
						    $arData[$i]["reason_detail"] = GetMessage("NOT_SELECTED");
					    }
				    }
				    elseif ($arRes["reason"]==$OPTIONS->GetOptionInt("reason_replacement_breakdown")) {
					    if ($arRes["reason_detail"]>0) {
						    $arData[$i]["reason_detail"] = "Нет данных";
					    }
					    else {
						    $arData[$i]["reason_detail"] = GetMessage("NOT_SELECTED");
					    }
				    }
				    elseif ($arRes["reason"]==$OPTIONS->GetOptionInt("reason_replacement_dtp")) {
					    if ($arRes["reason_detail"]>0) {
						    $arData[$i]["reason_detail"] = "Нет данных";
					    }
					    else {
						    $arData[$i]["reason_detail"] = GetMessage("NOT_SELECTED");
					    }
				    }
				    elseif ($arRes["reason"]==$OPTIONS->GetOptionInt("reason_replacement_tuning")) {
					    if ($arRes["reason_detail"]>0) {
						    $arData[$i]["reason_detail"] = "Нет данных";
					    }
					    else {
						    $arData[$i]["reason_detail"] = GetMessage("NOT_SELECTED");
					    }
				    }
				    elseif ($arRes["reason"]==$OPTIONS->GetOptionInt("reason_replacement_upgrade")) {
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
				    $arData[$i]["who_paid"] = self::GetNameByIDFromDB($arRes["who_paid"],"ms_icar_setup_who_paid");
				    $arData[$i]["odo"] = round($arRes["odo"],2);
				    $arData[$i]["waypoint"] = self::GetNameByIDFromDB($arRes["waypoint"],"ms_icar_points");
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
		 * Функция возвращает значение указанной колонки строки по ее id из DB
		 *
		 * @param int $ID
		 * @param string $table
		 * @param string $name_col
		 * @return bool
		 */
		public function GetNameByIDFromDB ($ID=0, $table="", $name_col="name") {
			global $DB;
			if ($ID==0 || $table=="") return false;

			$query = "SELECT `".$name_col."` FROM `".$table."` WHERE `id` =".$ID;
			if ($res = $DB->Select($query)) {
				return $res[0][$name_col];
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

			$query = "DELETE FROM `ms_icar_repair_parts` WHERE `id` = ".$repairPartsID;
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

			$query = "SELECT * FROM `ms_icar_repair_parts` WHERE `id` =".$repairPartsID;
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
			$arData["date"] = self::ConvertDateToTimestamp($post["date"]);
			$arData["name"] = htmlspecialchars($post["name"]);
			$arData["storage"] = intval($post["storage"]);
			$arData["catalog_number"] = htmlspecialchars($post["catalog_number"]);
			$arData["number"] = floatval(str_replace(",",".",$post["number"]));
			$arData["cost"] = floatval(str_replace(",",".",$post["cost"]));
			$arData["reason"] = intval($post["reason"]);
			switch ($arData["reason"]) {
				case $OPTIONS->GetOptionInt("reason_replacement_ts"):
					$arData["reason_detail"] = intval($post["reason_ts"]);
					break;
				case $OPTIONS->GetOptionInt("reason_replacement_breakdown"):
					$arData["reason_detail"] = intval($post["reason_breakdown"]);
					break;
				case $OPTIONS->GetOptionInt("reason_replacement_dtp"):
					$arData["reason_detail"] = intval($post["reason_dtp"]);
					break;
				case $OPTIONS->GetOptionInt("reason_replacement_tuning"):
					$arData["reason_detail"] = intval($post["reason_tuning"]);
					break;
				case $OPTIONS->GetOptionInt("reason_replacement_upgrade"):
					$arData["reason_detail"] = intval($post["reason_upgrade"]);
					break;
				case $OPTIONS->GetOptionInt("reason_replacement_tire"):
					$arData["reason_detail"] = 0;
					break;
			}
			$arData["who_paid"] = intval($post["who_paid"]);
			$arData["odo"] = floatval(str_replace(",",".",$post["odo"]));
			$arData["waypoint"] = intval($post["waypoint"]);
			if ($arData["waypoint"]==0) {
				$arData["waypoint"] = self::CreateNewPoint(
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

			$query = "UPDATE `ms_icar_repair_parts` SET ";
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
			if ($car==0) $car = self::GetDefaultCar();

			$query = "SELECT SUM(`cost`) FROM `ms_icar_repair_parts` WHERE `auto` =".$car." AND `who_paid` =".$OPTIONS->GetOptionInt("who_paid_himself");
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