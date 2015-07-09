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
			$query .= "`default` FROM `ms_icar_my_car` ORDER BY `name` ASC";
			$arResult = $DB->Select ($query);

			$echo = "<select name=\"".$select_name."\">\n";
			//echo "<pre>"; print_r($arResult); echo "</pre>";
			foreach ($arResult as $arAuto)
			{
				if ($full) {
					$arAuto["trademark"] = CInvestToCarMain::GetCarTrademarkNameByID($arAuto["trademark"]);
					$arAuto["model"] = CInvestToCarMain::GetCarModelNameByID($arAuto["model"]);
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
				$query = "SELECT `id` , `name` FROM `ms_icar_points` WHERE `type` ";
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
				if ($type==0) $type = $OPTIONS->GetOptionInt("point_default");
				$query = "SELECT `id` , `name` FROM `ms_icar_points` WHERE `type` =".$type." ORDER BY `period` DESC";
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
		public function Brands ($selected = 0) {
			global $DB;

			$query = "SELECT * FROM `ms_icar_setup_car_brand` ORDER BY `name` ASC";
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

			$query = "SELECT * FROM `ms_icar_setup_car_model` WHERE `brand` =".intval ($brand)." ORDER BY `name` ASC";
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

			$query = "SELECT * FROM `ms_icar_setup_car_body` ORDER BY `".$sortCol."` ".$sort;
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

			$query = "SELECT * FROM `ms_icar_setup_car_gearbox` ORDER BY `".$sortCol."` ".$sort;
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
				$echo .= CInvestToCarMain::GetRepairNameByID($i);
				$echo .= "</option>";
			}
			$echo .= "</select>";

			return $echo;
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
			if ($car==0) $car = CInvestToCarMain::GetDefaultCar();
			$echo = "";

			$query = "SELECT * FROM `ms_icar_setup_fuel_mark` ORDER BY `sort` ASC";
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
			if ($car==0) $car = CInvestToCarMain::GetDefaultCar();
			$echo = "<select name=\"".$name."\" class=\"".$name."\"".$additional_data.">";

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
			if ($car==0) $car = CInvestToCarMain::GetDefaultCar();
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
			if ($car==0) $car = CInvestToCarMain::GetDefaultCar();

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

	}