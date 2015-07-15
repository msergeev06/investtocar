<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarRepair
	{
		public static $arMessage = array ();

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

	}