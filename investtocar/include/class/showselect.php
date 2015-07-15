<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarShowSelect
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
		public function Auto ($select_name = "" ,$full=false, $selected=0) {
			global $DB;
			$query = "SELECT `id`, `name`,";
			if ($full) {
				$query .= "`trademark`, `model`, `year`, `carnumber`, ";
			}
			$query .= "`default` FROM `".CInvestToCarMain::GetTableByCode("mycar")."` ORDER BY `name` ASC";
			$arResult = $DB->Select ($query);

			$echo = "<select name=\"".$select_name."\">\n";
			//echo "<pre>"; print_r($arResult); echo "</pre>";
			foreach ($arResult as $arAuto)
			{
				if ($full) {
					$arAuto["trademark"] = CInvestToCarCars::GetCarTrademarkNameByID($arAuto["trademark"]);
					$arAuto["model"] = CInvestToCarCars::GetCarModelNameByID($arAuto["model"]);
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
		 * @param int $selected
		 * @param int|array $type
		 * @return string
		 */
		public function Points ($select_name = "", $selected=0, $type=0) {
			global $DB,$OPTIONS;
			if (is_array($type)) {
				$query = "SELECT `id` , `name` FROM `".CInvestToCarMain::GetTableByCode("points")."` WHERE `type` ";
				$query .= "IN (";
				$first = true;
				foreach ($type as $in) {
					if ($first) {
						$first = false;
					}
					else {
						$query .= ", ";
					}
					$query .= $in;
				}
				$query .= ")";
				$query .= " ORDER BY `period` DESC";
			}
			else {
				if ($type==0) $type = intval(CInvestToCarMain::GetInfoByCode ("pointtype",$OPTIONS->GetOptionString("point_default")));
				$query = "SELECT `id` , `name` FROM `".CInvestToCarMain::GetTableByCode("points")."` WHERE `type` =".$type." ORDER BY `period` DESC";
			}
			$arResult = $DB->Select ($query);

			$echo = "<select name=\"".$select_name."\">\n";
			$echo .= "<option value=\"0\"";
			if ($selected==0) $echo .= " selected=\"selected\"";
			$echo .= ">".GetMessage("SELECT_DEFAULT_SELECTED")."</option>\n";
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
		public function CarBrands ($selected = 0) {
			global $DB;

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("brand")."` ORDER BY `name` ASC";
			$res = $DB->Select ($query);

			$select = '<select name="car_brand" id="car_brand">'."\n";
			$select .= "\t<option value=\"0\"";
			if ($selected == 0)
			{
				$select .= " selected";
			}
			$select .= ">".GetMessage("SELECT_DEFAULT_SELECTED")."</option>\n";
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
		public function CarModel ($brand, $selected = 0)
		{
			global $DB;

			if (intval ($brand) == 0)
			{
				return false;
			}

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("model")."` WHERE `brand` =".intval ($brand)." ORDER BY `name` ASC";
			$res = $DB->Select ($query);

			if (!isset($res[0]["name"]))
			{
				return false;
			}

			$select = "<select name=\"car_model\" id=\"car_model\">";
			if ($selected == 0) {
				$select .= "<option value=\"0\" selected>".GetMessage("SELECT_DEFAULT_SELECTED")."</option>";
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
		public function CarCreateYear ($selected = 0, $start = 1970, $end = 0)
		{
			if ($end == 0)
			{
				$end = date ("Y");
			}

			$select = "<select name=\"car_year\" id=\"car_year\">";
			if ($selected == 0) {
				$select .= "<option value=\"0\" selected>".GetMessage("SELECT_DEFAULT_SELECTED")."</option>";
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
		public function CarBody ($selected=0, $sortCol = "sort", $sort = "ASC")
		{
			global $DB;

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("body")."` ORDER BY `".$sortCol."` ".$sort;
			$res = $DB->Select ($query);

			$select = "<select name=\"car_body\" id=\"car_body\">";
			if ($selected==0) {
				$select .= "<option value=\"0\" selected>".GetMessage("SELECT_DEFAULT_SELECTED")."</option>";
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
		public function CarGearbox ($selected=0, $sortCol = "sort", $sort = "ASC")
		{
			global $DB;

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("gearbox")."` ORDER BY `".$sortCol."` ".$sort;
			$res = $DB->Select ($query);

			$select = "<select name=\"car_gearbox\" id=\"car_gearbox\">";
			if ($selected==0) {
				$select .= "<option value=\"0\" selected>".GetMessage("SELECT_DEFAULT_SELECTED")."</option>";
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
		public function Ts ($name="ts_num", $selected=-1) {
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
		public function Repair ($name="", $selected=0) {
			global $DB;
			if ($name=="") $name = "repair";
			$echo = "";

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("repairtype")."` ORDER BY `sort` ASC";
			if ($res = $DB->Select($query)) {
				$echo .= "<select name=\"".$name."\" class=\"".$name."\">";
				$bFirst = true;
				foreach ($res as $arRes) {
					$echo .= "<option value=\"".$arRes["id"]."\"";
					if ($bFirst) {
						if ($selected==0 || $selected==$arRes["id"]) {
							$echo .= " selected=\"selected\"";
						}
						$bFirst = false;
					}
					else {
						if ($selected>0 && $selected==$arRes["id"]) {
							$echo .= " selected=\"selected\"";
						}
					}
					$echo .= ">".$arRes["name"]."</option>";
				}
				$echo .= "</select>";
				return $echo;
			}
			else {
				return "";
			}

		}

		/**
		 * Функция возвращает <select> состоящий из марок топлива
		 *
		 * @param string $name
		 * @param int $car
		 * @param int $selected
		 * @return string
		 */
		public function FuelMark ($name="", $car=0, $selected=0) {
			global $DB;
			if ($name=="") $name = "fuel_mark";
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();
			$echo = "";

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("fuelmark")."` ORDER BY `sort` ASC";
			$res = $DB->Select($query);
			$echo .= "<select name=\"".$name."\">";
			if ($selected==0) {
				$echo .= "<option value=\"0\" selected=\"selected\">".GetMessage("SELECT_DEFAULT_SELECTED")."</option>";
			}
			foreach ($res as $arRes) {
				$echo .= "<option value=\"".$arRes["id"]."\"";
				if ($selected>0 && $selected==$arRes["id"]) {
					$echo .= " selected=\"selected\"";
				}
				$echo .= ">".$arRes["name"]."</option>";
			}
			$echo .= "</select>";

			return $echo;
		}

		/**
		 * Функция возвращает <select> состоящий из списка пройденных ТО
		 *
		 * @param string $name
		 * @param int $car
		 * @param int $selected
		 * @param string $additional_data
		 * @return string
		 */
		public function ReasonTs ($name="", $car=0, $selected=0, $additional_data="") {
			global $DB;

			if ($name=="") $name="reason_ts";
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();
			$echo = "<select name=\"".$name."\" class=\"".$name."\"".$additional_data.">";

			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("ts")."` WHERE `auto` =".$car;
			if ($res = $DB->Select($query)) {
				if ($selected==0) {
					$echo .= "<option value=\"0\" selected>".GetMessage("NOT_SELECTED")."</option>";
				}
				foreach ($res as $arRes) {
					$echo .= "<option value=\"".$arRes["id"]."\"";
					if ($selected>0 && $selected==$arRes["id"]) {
						$echo .= " selected";
					}
					$echo .= ">".date("d.m.Y",$arRes["date"])." ".GetMessage("TS")."-".$arRes["ts_num"]."</option>";
				}
			}
			else {
				$echo .= "<option value=\"0\" selected>".GetMessage("NO_TS")."</option>";
			}

			$echo .= "</select>";

			return $echo;
		}

		/**
		 * Функция возвращает <select> состоящий из списка проведенных ремонтов
		 *
		 * @param string $name
		 * @param int $car
		 * @param int $selected
		 * @param string $additional_data
		 * @return string
		 */
		public function ReasonRepair($name="", $car=0, $selected=0, $additional_data="") {
			global $DB;

			if ($name=="") $name="reason_repair";
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();
			$echo = "<select name=\"".$name."\" class=\"".$name."\"".$additional_data.">";

			/*
			$query = "SELECT * FROM `ms_icar_ts` WHERE `auto` =".$car;
			if ($res = $DB->Select($query)) {
				if ($selected==0) {
					$echo .= "<option value=\"0\" selected>".GetMessage("NOT_SELECTED")."</option>";
				}

				foreach ($res as $arRes) {
					$echo .= "<option value=\"".$arRes["id"]."\"";
					if ($selected>0 && $selected==$arRes["id"]) {
						$echo .= " selected";
					}
					$echo .= ">".date("d.m.Y",$arRes["date"])." ".GetMessage("TS")."-".$arRes["ts_num"]."</option>";
				}
			}
			else {
				$echo .= "<option value=\"0\" selected>".GetMessage("NO_REPAIR")."</option>";
			}
			*/
			$echo .= "<option value=\"0\" selected>".GetMessage("NO_REPAIR")."</option>";

			$echo .= "</select>";

			return $echo;
		}

		/**
		 * Функция возвращает <select> состоящий из списка зарегистрированных ДТП
		 *
		 * @param string $name
		 * @param int $car
		 * @param int $selected
		 * @param string $additional_data
		 * @return string
		 */
		public function ReasonDtp ($name="", $car=0, $selected=0, $additional_data="") {
			global $DB;
			if ($name=="") $name="reason_dtp";
			if ($car==0) $car = CInvestToCarCars::GetDefaultCar();

			$echo = "<select name=\"".$name."\" class=\"".$name."\"".$additional_data.">";

			/*
			$query = "SELECT * FROM `ms_icar_ts` WHERE `auto` =".$car;
			if ($res = $DB->Select($query)) {
				if ($selected==0) {
					$echo .= "<option value=\"0\" selected>".GetMessage("NOT_SELECTED")."</option>";
				}

				foreach ($res as $arRes) {
					$echo .= "<option value=\"".$arRes["id"]."\"";
					if ($selected>0 && $selected==$arRes["id"]) {
						$echo .= " selected";
					}
					$echo .= ">".date("d.m.Y",$arRes["date"])." ".GetMessage("TS")."-".$arRes["ts_num"]."</option>";
				}
			}
			else {
				$echo .= "<option value=\"0\" selected>".GetMessage("NO_REPAIR")."</option>";
			}
			*/
			$echo .= "<option value=\"0\" selected>".GetMessage("NO_DTP")."</option>";

			$echo .= "</select>";

			return $echo;
		}

		/**
		 * Функция возвращает <select> состоящий из мест хранения
		 *
		 * @param string $name
		 * @param int $selected
		 * @return string
		 */
		public function Storage ($name="",$selected=0) {
			global $DB;

			if ($name=="") $name = "storage";

			$echo = "<select name=\"".$name."\">";
			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("storage")."` ORDER BY `sort` ASC";
			if ($res = $DB->Select($query)) {
				$first = true;
				foreach ($res as $arRes) {
					$echo .= "<option value=\"".$arRes["id"]."\"";
					if ($first) {
						if ($selected==0 || $selected==$arRes["id"]) {
							$echo .= " selected";
						}
						$first = false;
					}
					else {
						if ($selected>0 && $selected == $arRes["id"]) {
							$echo .= " selected";
						}
					}
					$echo .= ">".$arRes["name"]."</option>";
				}
			}
			else {
				return "";
			}
			$echo .= "</select>";

			return $echo;
		}

		/**
		 * Функция возвращает <select> состоящий из причин замены запчасти
		 *
		 * @param string $name
		 * @param int $selected
		 * @return string
		 */
		public function ReasonReplacement ($name="",$selected=0) {
			global $DB;
			if ($name=="") $name = "reason";

			$echo = "<select name=\"".$name."\" class=\"".$name."\">";
			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("reason")."` ORDER BY `sort` ASC";
			if ($res = $DB->Select($query)) {
				$first = true;
				foreach ($res as $arRes) {
					$echo .= "<option value=\"".$arRes["id"]."\"";
					if ($first) {
						if ($selected==0 || $selected==$arRes["id"]) {
							$echo .= " selected";
						}
						$first = false;
					}
					else {
						if ($selected>0 && $selected==$arRes["id"]) {
							$echo .= " selected";
						}
					}
					$echo .= ">".$arRes["name"]."</option>";
				}
				$echo .= "</select>";

				return $echo;
			}
			else {
				return "";
			}

		}

		/**
		 * Функция возвращает <select> состоящий из списка плательщиков
		 *
		 * @param string $name
		 * @param int $selected
		 * @return string
		 */
		public function WhoPaid ($name="",$selected=0) {
			global $DB;
			if ($name=="") $name = "who_paid";

			$echo = "<select name=\"".$name."\" class=\"".$name."\">";
			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("whopaid")."` ORDER BY `sort` ASC";
			if ($res = $DB->Select($query)) {
				$first = true;
				foreach ($res as $arRes) {
					$echo .= "<option value=\"".$arRes["id"]."\"";
					if ($first) {
						if ($selected==0 || $selected==$arRes["id"]) {
							$echo .= " selected";
						}
						$first = false;
					}
					else {
						if ($selected>0 && $selected==$arRes["id"]) {
							$echo .= " selected";
						}
					}
					$echo .= ">".$arRes["name"]."</option>";
				}
				$echo .= "</select>";

				return $echo;
			}
			else {
				return "";
			}
		}

		/**
		 * Функция возвращает <select> состоящий из типов прочих расходов
		 *
		 * @param string $name
		 * @param int $selected
		 * @return string
		 */
		public function TypeOtherCosts ($name="", $selected=0) {
			global $DB;
			if ($name=="") $name="other_type";

			$echo = "<select name=\"".$name."\" class=\"".$name."\">";
			$echo .= "<option value=\"0\"";
			if ($selected==0) $echo .= " selected";
			$echo .= ">".GetMessage("SELECT_DEFAULT_SELECTED")."</option>";
			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("flowtype")."` ORDER BY `sort` ASC";
			if ($res = $DB->Select($query)) {
				foreach ($res as $arRes) {
					$echo .= "<option value=\"".$arRes["id"]."\"";
					if ($selected>0 && $selected==$arRes["id"]) {
						$echo .= " selected";
					}
					$echo .= ">".$arRes["name"]."</option>";
				}
				$echo .= "</select>";

				return $echo;
			}
			else {
				return "";
			}
		}

		/**
		 * Функция возвращает <select> состоящий из типов Путевых точкек
		 *
		 * @param string $name
		 * @param int $selected
		 * @param array $arTypes
		 * @return string
		 */
		public function PointTypes ($name="", $selected=0, $arTypes=array()) {
			global $DB;
			if ($name=="") $name = "newpoint_type";

			$echo = "<select name=\"".$name."\" class=\"".$name."\">";
			$echo .= "<option value=\"0\"";
			if ($selected==0) $echo .= " selected";
			$echo .= ">".GetMessage("SELECT_DEFAULT_DEFAULT")."</option>";
			$query = "SELECT * FROM `".CInvestToCarMain::GetTableByCode("pointtype")."` ";
			if (!empty($arTypes)) {
				$query .= "WHERE `id` IN (";
				$first = true;
				foreach ($arTypes as $type) {
					if ($first) {
						$first = false;
						$query .= $type;
					}
					else {
						$query .= ", ".$type;
					}
				}
				$query .= ") ";
			}
			$query .= "ORDER BY `sort` ASC";
			if ($res = $DB->Select($query)) {
				foreach ($res as $arRes) {
					$echo .= "<option value=\"".$arRes["id"]."\"";
					if ($selected>0 && $selected==$arRes["id"]) $echo .= " selected";
					$echo .= ">".$arRes["name"]."</option>";
				}
				$echo .= "</select>";
				return $echo;
			}
			else {
				return "";
			}
		}
	}