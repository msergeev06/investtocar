<?php
	global $DB, $MESS;


	class CInvestToCarMain
	{
		public static $arMessage = array();

		/**
		 * Функция возвращает <select> состоящий из автомобилей
		 *
		 * @param string $select_name
		 * @param bool $full
		 * @param int $selected
		 *
		 * @return string
		 */
		public function ShowSelectAuto ($select_name = "" ,$full=false, $selected=0)
		{
			global $DB;
			$query = "SELECT `id`, `name`,";
			if ($full) {
				$query .= "`trademark`, `model`, `year`, `carnumber`, ";
			}
			$query .= "`default` FROM `ms_icar_my_car` ORDER BY `name` ASC";
			$arResult = $DB->Select ($query);

			$echo = "<select name=\"".$select_name."\">\n";
			//echo "<pre>"; print_r($arResult); echo "</pre>";
			foreach ($arResult as $arAuto)
			{
				if ($full) {
					$arAuto["trademark"] = self::GetCarTrademarkNameByID($arAuto["trademark"]);
					$arAuto["model"] = self::GetCarModelNameByID($arAuto["model"]);
				}
				$echo .= "<option value=\"".intval ($arAuto["id"])."\"";
				if ($selected==0) {
					if (intval ($arAuto["default"]) == 1)
					{
						$echo .= " selected";
					}
				}
				elseif ($selected>0 && $selected==$arAuto["id"]) {
					$echo .= " selected";
				}

				if ($full) {
					$echo .= ">".$arAuto["trademark"]." ".$arAuto["model"]." ".$arAuto["year"]." - ".$arAuto["carnumber"]."</option>\n";
				}
				else {
					$echo .= ">".$arAuto["name"]."</option>\n";
				}
			}
			$echo .= "</select>\n";

			return $echo;
		}

		/**
		 * Функция возвращает <select> состоящий из маршрутных точек
		 *
		 * @param string $select_name
		 * @param int selected
		 * @return string
		 */
		public function ShowSelectPoints ($select_name = "", $selected=0)
		{
			global $DB;
			$query = "SELECT `id` , `name` FROM `ms_icar_points` WHERE `type` =1 ORDER BY `period` DESC";
			$arResult = $DB->Select ($query);

			$echo = "<select name=\"".$select_name."\">\n";
			$echo .= "<option value=\"0\"";
			if ($selected==0) $echo .= " selected=\"selected\"";
			$echo .= ">--Выбрать--</option>\n";
			foreach ($arResult as $arPoint)
			{
				$echo .= "<option value=\"".$arPoint["id"]."\"";
				if ($selected>0 && $selected==$arPoint["id"]) $echo .= " selected=\"selected\"";
				$echo .= ">".$arPoint["name"]."</option>\n";
			}
			$echo .= "</select>\n";

			return $echo;
		}

		/**
		 * Функция возвращает <select> состоящий из автомобильных брендов
		 *
		 * @param int $selected Если указан, устанавливает выбранный бренд как selected
		 * @return string HTML тег <select></select>
		 */
		public function ShowSelectCarBrands ($selected = 0)
		{
			global $DB;

			$query = "SELECT * FROM `ms_icar_setup_car_brand` ORDER BY `name` ASC";
			$res = $DB->Select ($query);

			$select = '<select name="car_brand" id="car_brand">'."\n";
			$select .= "\t<option value=\"0\"";
			if ($selected == 0)
			{
				$select .= " selected";
			}
			$select .= ">--Выбрать--</option>\n";
			foreach ($res as $arBrand)
			{
				$select .= "\t<option value=\"".$arBrand["id"]."\"";
				if (intval($selected) == intval($arBrand["id"]))
				{
					$select .= " selected";
				}
				$select .= ">".$arBrand["name"]."</option>\n";
			}
			$select .= "</select>";

			return $select;
		}

		/**
		 * Функция возвращает <select> состоящий из марок автомобилей заданной марки
		 *
		 * @param $brand Марка автомобиля
		 * @return bool|string <select>, либо false
		 */
		public function ShowSelectCarModel ($brand, $selected = 0)
		{
			global $DB;

			if (intval ($brand) == 0)
			{
				return false;
			}

			$query = "SELECT * FROM `ms_icar_setup_car_model` WHERE `brand` =".intval ($brand)." ORDER BY `name` ASC";
			$res = $DB->Select ($query);

			if (!isset($res[0]["name"]))
			{
				return false;
			}

			$select = "<select name=\"car_model\" id=\"car_model\">";
			if ($selected == 0) {
				$select .= "<option value=\"0\" selected>--Выбрать--</option>";
			}
			foreach ($res as $arModel)
			{
				$select .= "<option value=\"".$arModel["id"]."\"";
				if ($arModel["id"]==$selected) $select .= " selected";
				$select .= ">".$arModel["name"]."</option>";
			}
			$select .= "</select>";

			return $select;
		}

		/**
		 * Функция возвращает <select> состоящий из годов с start до end
		 *
		 * @param int $start Начало списка
		 * @param int $end Конец списка
		 *
		 * @return string
		 */
		public function ShowSelectCarCreateYear ($selected = 0, $start = 1970, $end = 0)
		{
			if ($end == 0)
			{
				$end = date ("Y");
			}

			$select = "<select name=\"car_year\" id=\"car_year\">";
			if ($selected == 0) {
				$select .= "<option value=\"0\" selected>--Выбрать--</option>";
			}
			for ($i = $start; $i <= $end; $i++)
			{
				$select .= "<option value=\"".$i."\"";
				if ($i==$selected) $select .= " selected";
				$select .= ">".$i."</option>";
			}
			$select .= "</select>";

			return $select;
		}

		/**
		 * Функция возвращает <select> состоящий из типов Кузова отсортированных по sortCol в направлении sort
		 *
		 * @param string $sortCol Поле сортировки
		 * @param string $sort Направление сортировки
		 * @return string <select>
		 */
		public function ShowSelectCarBody ($selected=0, $sortCol = "sort", $sort = "ASC")
		{
			global $DB;

			$query = "SELECT * FROM `ms_icar_setup_car_body` ORDER BY `".$sortCol."` ".$sort;
			$res = $DB->Select ($query);

			$select = "<select name=\"car_body\" id=\"car_body\">";
			if ($selected==0) {
				$select .= "<option value=\"0\" selected>--Выбрать--</option>";
			}
			foreach ($res as $arBody)
			{
				$select .= "<option value=\"".$arBody["id"]."\"";
				if ($arBody["id"]==$selected) $select .= " selected";
				$select .= ">".$arBody["name"]."</option>";
			}
			$select .= "</select>";

			return $select;
		}

		/**
		 * Функция возвращает <select> состоящий из типов КПП отсортированных по sortCol в направлении sort
		 *
		 * @param string $sortCol Поле сортировки
		 * @param string $sort Направление сортировки
		 * @return string <select>
		 */
		public function ShowSelectCarGearbox ($selected=0, $sortCol = "sort", $sort = "ASC")
		{
			global $DB;

			$query = "SELECT * FROM `ms_icar_setup_car_gearbox` ORDER BY `".$sortCol."` ".$sort;
			$res = $DB->Select ($query);

			$select = "<select name=\"car_gearbox\" id=\"car_gearbox\">";
			if ($selected==0) {
				$select .= "<option value=\"0\" selected>--Выбрать--</option>";
			}
			foreach ($res as $arGear)
			{
				$select .= "<option value=\"".$arGear["id"]."\"";
				if ($arGear["id"]==$selected) $select .= " selected";
				$select .= ">".$arGear["name"]."</option>";
			}
			$select .= "</select>";

			return $select;
		}

		/**
		 * Функция возвращает <select> состоящий из номеров ТО (ТО-0, ТО-1 и т.д.)
		 *
		 * @param string $name
		 * @param int selected
		 * @return string
		 */
		public function ShowSelectTs ($name="ts_num", $selected=-1) {
			$echo = "<select name=\"".$name."\" id=\"".$name."\">";
			for ($i=0; $i<=25; $i++) {
				$echo .= "<option value=\"".$i."\"";
				if ($i==0) $echo .= " selected";
				if ($selected>=0 && $selected==$i) $echo .= " selected";
				$echo .= ">".GetMessage("TS")."-".$i."</option>";
			}
			$echo .= "</select>";

			return $echo;
		}

		/**
		 * Функция возвращает <select> состоящий из исполнителей ремонта
		 *
		 * @param string $name
		 * @param int $selected
		 * @return string
		 */
		public function ShowSelectRepair ($name="", $selected=0) {
			if ($name=="") {
				$name = "repair";
			}

			$echo = "<select name=\"".$name."\">";
			for ($i=1; $i<=5; $i++) {
				$echo .= "<option value=\"".$i."\"";
				if ($selected>0 && $selected==$i) {
					$echo .= " selected=\"selected\"";
				}
				else {
					if ($i==1) $echo .= " selected=\"selected\"";
				}
				$echo .= ">";
				$echo .= self::GetRepairNameByID($i);
				$echo .= "</option>";
			}
			$echo .= "</select>";

			return $echo;
		}

		/**
		 * Функция возвращает html-код графика Километража
		 *
		 * @param string $arSettings
		 * @return bool|string
		 */
		public function ShowChartsOdo ($arSettings = "")
		{
			if (!is_array ($arSettings))
			{
				return false;
			}

			if (intval ($arSettings["type"]) == 1)
			{
				//за Текущий месяц
				$firstMonthDay = mktime (0, 0, 0, date ("n"), 1, date ("Y")) + 3600;
				$daysInMonth = date ("t");
				$lastMonthDay = mktime (0, 0, 0, date ("n"), $daysInMonth, date ("Y")) + 3600;
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
				$firstMonthDay = mktime (0, 0, 0, $prevMonth, 1, $year) + 3600;
				$daysInMonth = date ("t", mktime (0, 0, 0, $prevMonth, 1, $year) + 3600);
				$lastMonthDay = mktime (0, 0, 0, $prevMonth, $daysInMonth, $year) + 3600;
				if ($data = self::GetListOdoFromTo ($firstMonthDay, $lastMonthDay))
				{
					$arSettings["data"] = $data;
				}
				$arSettings["fromTitle"] = self::GetNameMonth ($prevMonth)." ".$year." г.";
			}
			elseif (intval ($arSettings["type"]) == 3)
			{
				//за Текущий год
				$firstMonthDay = mktime (0, 0, 0, 1, 1, date ("Y")) + 3600;
				$lastMonthDay = mktime (0, 0, 0, 12, 31, date ("Y")) + 3600;
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
			list($day, $month, $year) = explode (".", $post["date"]);
			$arResult["date"] = mktime (0, 0, 0, $month, $day, $year, 0) + 3600;
			$arResult["start_point"] = intval ($post["start_point"]);
			if ($arResult["start_point"] == 0)
			{
				$arResult["start_new"]["start_name"] = $post["start_name"];
				$arResult["start_new"]["start_address"] = $post["start_address"];
				$arResult["start_new"]["start_lon"] = $post["start_lon"];
				$arResult["start_new"]["start_lat"] = $post["start_lat"];

				//Если координаты точки не заданы, запрашиваем их по указанному адресу через сервер Яндекс
				if (strlen ($arResult["start_new"]["start_lon"]) < 2
				    && strlen ($arResult["start_new"]["start_lat"]) < 2
				)
				{
					if (strlen ($arResult["start_new"]["start_address"]) > 3)
					{
						if ($arCoords = self::GetCoordsByAddressYandex ($arResult["start_new"]["start_address"]))
						{
							$arResult["start_new"]["start_lon"] = $arCoords["lon"];
							$arResult["start_new"]["start_lat"] = $arCoords["lat"];
						}
					}
				}

				$new_point = self::AddNewPointDB (
					array (
						"name"      => $arResult["start_new"]["start_name"],
						"address"   => $arResult["start_new"]["start_address"],
						"longitude" => $arResult["start_new"]["start_lon"],
						"latitude"  => $arResult["start_new"]["start_lat"]
					)
				);
				if (intval ($new_point) > 0)
				{
					$arResult["start_point"] = $new_point;
				}

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
					$arResult["end_new"]["end_name"] = $post["end_name"];
					$arResult["end_new"]["end_address"] = $post["end_address"];
					$arResult["end_new"]["end_lon"] = $post["end_lon"];
					$arResult["end_new"]["end_lat"] = $post["end_lat"];

					//Если координаты точки не заданы, запрашиваем их по указанному адресу через сервер Яндекс
					if (strlen ($arResult["end_new"]["end_lon"]) < 2 && strlen ($arResult["end_new"]["end_lat"]) < 2)
					{
						if (strlen ($arResult["end_new"]["end_address"]) > 3)
						{
							$arCoords = array ();
							if ($arCoords = self::GetCoordsByAddressYandex ($arResult["end_new"]["end_address"]))
							{
								$arResult["end_new"]["end_lon"] = $arCoords["lon"];
								$arResult["end_new"]["end_lat"] = $arCoords["lat"];
							}
						}
					}
					$new_point = self::AddNewPointDB (
						array (
							"name"      => $arResult["end_new"]["end_name"],
							"address"   => $arResult["end_new"]["end_address"],
							"longitude" => $arResult["end_new"]["end_lon"],
							"latitude"  => $arResult["end_new"]["end_lat"]
						)
					);
					if (intval ($new_point) > 0)
					{
						$arResult["end_point"] = $new_point;
					}

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
			global $DB;
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
					$startTimestamp = mktime (0, 0, 0, $month, $day, $year) + 3600;
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

							if ((mktime (0, 0, 0, $month, $i, $year) + 3600) >= $startTimestamp
							    && (mktime (0, 0, 0, $month, $i, $year) + 3600) <= $nowTimestamp
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
							$dateTimes = mktime (0, 0, 0, $month, $day, $year) + 3600;
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
			list($day,$month,$year) = explode (".",$post["date"]);
			$arTs["date"] = mktime(0,0,0,$month,$day,$year)+3600;
			$arTs["repair"] = $post["ts_repair"];
			$arTs["cost"] = $post["cost"];
			$arTs["odo"] = $post["odo"];
			$arTs["point"] = $post["ts_point"];
			if ($arTs["point"]==0) {
				if (strlen($post["newpoint_lon"])<2 || strlen($post["newpoint_lat"])<2) {
					if (strlen ($post["newpoint_address"]) > 3)
					{
						if ($arCoords = self::GetCoordsByAddressYandex ($post["newpoint_address"]))
						{
							$post["newpoint_lon"] = $arCoords["lon"];
							$post["newpoint_lat"] = $arCoords["lat"];
						}
					}

					$new_point = self::AddNewPointDB (
						array (
							"name"      => $post["newpoint_name"],
							"address"   => $post["newpoint_address"],
							"longitude" => $post["newpoint_lon"],
							"latitude"  => $post["newpoint_lat"]
						)
					);
					if (intval ($new_point) > 0)
					{
						$arTs["point"] = $new_point;
					}

				}
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
			list($day,$month,$year) = explode(".",$arPost["date"]);
			$arData["date"] = mktime(0,0,0,$month,$day,$year)+3600;
			$arData["ts_repair"] = intval($arPost["ts_repair"]);
			$arData["cost"] = floatval(str_replace(",",".",$arPost["cost"]));
			$arData["odo"] = floatval(str_replace(",",".",$arPost["odo"]));
			if ($arPost["ts_point"]==0) {
				if (strlen($post["newpoint_lon"])<2 || strlen($post["newpoint_lat"])<2) {
					if (strlen ($post["newpoint_address"]) > 3)
					{
						if ($arCoords = self::GetCoordsByAddressYandex ($post["newpoint_address"]))
						{
							$post["newpoint_lon"] = $arCoords["lon"];
							$post["newpoint_lat"] = $arCoords["lat"];
						}
					}

					$new_point = self::AddNewPointDB (
						array (
							"name"      => $post["newpoint_name"],
							"address"   => $post["newpoint_address"],
							"longitude" => $post["newpoint_lon"],
							"latitude"  => $post["newpoint_lat"]
						)
					);
					if (intval ($new_point) > 0)
					{
						$arPost["ts_point"] = $new_point;
					}
				}
			}
			$arData["point"] = $arPost["ts_point"];
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
	}