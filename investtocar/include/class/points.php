<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarPoints
	{
		public static $arMessage = array ();

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
				"UPDATE `".CInvestToCarMain::GetTableByCode("points")."` SET `period` = `period` + 1 WHERE `".CInvestToCarMain::GetTableByCode("points")."`.`id` =".$pointID.";";

			$res = $DB->Update ($query);

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
				"INSERT INTO `".CInvestToCarMain::GetTableByCode("points")."` (`name`, `type`, `address`, `longitude`, `latitude`) VALUES ('"
				.$data["name"]."', '".$data["type"]."', '".$data["address"]."', '"
				.$data["longitude"]."', '".$data["latitude"]."')";

			$res = $DB->Insert ($query);

			return $res;
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

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("points")."` WHERE `id` =".$id;
			$res = $DB->Select($query);
			if (isset($res[0]["id"])) {
				return $res[0];
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
			if ($type==0) $type = intval(CInvestToCarMain::GetInfoByCode ("pointtype",$OPTIONS->GetOptionString("point_default")));
			if (strlen($lon)<2 || strlen($lat)<2)
			{
				if (strlen ($address) > 3)
				{
					if ($arCoords = CInvestToCarMain::GetCoordsByAddressYandex ($address))
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
		 * Функция возвращает html-код добавляющий форму добавления новой точки
		 *
		 * @param bool $showType
		 * @param int $typeSelect
		 * @param array $arTypes
		 * @return string
		 */
		public function ShowFormNewPointAdd ($showType=false, $typeSelect=0, $arTypes=array()) {
			$echo = "<tr>";
			$echo .= "<td class=\"center\" colspan=\"2\">".GetMessage("OR")."</td>";
			$echo .= "</tr>";
			$echo .= "<tr>";
			$echo .= "<td class=\"title\">".GetMessage("NAME_NEW_WAYPOINT")."</td>";
			$echo .= "<td><input type=\"text\" name=\"newpoint_name\" value=\"\"></td>";
			$echo .= "</tr>";
			$echo .= "<tr>";
			$echo .= "<td class=\"title\">".GetMessage("ADDRESS_NEW_WAYPOINT")."</td>";
			$echo .= "<td><input type=\"text\" name=\"newpoint_address\" value=\"\"></td>";
			$echo .= "</tr>";
			$echo .= "<tr>";
			$echo .= "<td class=\"title\">".GetMessage("LONGITUDE_NEW_WAYPOINT")."</td>";
			$echo .= "<td><input type=\"text\" name=\"newpoint_lon\" value=\"\"></td>";
			$echo .= "</tr>";
			$echo .= "<tr>";
			$echo .= "<td class=\"title\">".GetMessage("LATITUDE_NEW_WAYPOINT")."</td>";
			$echo .= "<td><input type=\"text\" name=\"newpoint_lat\" value=\"\"></td>";
			$echo .= "</tr>";

			if ($showType) {
				$echo .= "<tr>";
				$echo .= "<td class=\"title\">".GetMessage("TYPE_NEW_WAYPOINT")."</td>";
				$echo .= "<td>";
				$echo .= CInvestToCarShowSelect::PointTypes("",$typeSelect,$arTypes);
				$echo .= "</td>";
				$echo .= "</tr>";
			}

			return $echo;
		}

		public function CreateTables () {
			$arTables = array();
			$arTables[] = self::QueryTablePoints();

			return $arTables;
		}

		public function QueryTablePoints () {
			$query = "CREATE TABLE `".CInvestToCarMain::GetTableByCode("points")."` ( ";
			$query .= "`id` INT (10) AUTO_INCREMENT, ";
			$query .= "`name` VARCHAR(255) NOT NULL, ";
			$query .= "`type` INT (11) NOT NULL, ";
			$query .= "`address` VARCHAR(255) NOT NULL, ";
			$query .= "`longitude` VARCHAR(255) NOT NULL, ";
			$query .= "`latitude` VARCHAR(255) NOT NULL, ";
			$query .= "`period` INT (11) NOT NULL, ";
			$query .= "PRIMARY KEY (`id`) );";

			return $query;
		}

		public function DataTables () {
			$arData = array();
			$arData["points"] = self::DataTablePoints();

			return $arData;
		}

		public function DataTablePoints () {
			global $DB;
			$arData = array();

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("points")."`";
			if ($res = $DB->Select($query)) {
				$arData["FIELDS"] = array(
					"id", "name", "type", "address",
					"longitude","latitude","period"
				);
				foreach ($res as $arRes) {
					$arData["DATA"][] = array(
						$arRes["id"],$arRes["name"],$arRes["type"],$arRes["address"],
						$arRes["longitude"],$arRes["latitude"],$arRes["period"]
					);
				}
				return $arData;
			}
			else {
				return array();
			}
		}

	}