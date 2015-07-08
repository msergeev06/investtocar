<?php

	global $MESS,$DB;

	class CInvestToCarOptions
	{
		private $arOptions=array();
		private $LastError;
		private $boolError = false;

		public function __construct ()
		{
			require_once (INVESTTOCAR_INCLUDE_PATH."default_options.php");
			if (!empty($arDefaultOptions)) {
				foreach ($arDefaultOptions as $option => $value) {
					$this->arOptions[$option]=$value;
				}
				self::NoErrors();
			}
			else {
				self::Error("Could not load options default");
			}
		}

		private function Error ($text, $file=__FILE__, $line=__LINE__) {
			$this->LastError = "ERROR: ".$text." (file:'".$file."', line:'".$line."')";
			$this->boolError = true;
		}

		private function NoErrors () {
			$this->boolError = false;
		}

		public function IsError () {
			return $this->boolError;
		}

		public function GetOptionString ($option="") {
			if ($option=="") {
				self::Error("Design parameters can not be empty");
				return "";
			}
			else {
				self::NoErrors();
				return self::GetOption($option);
			}
		}

		public function GetOptionInt ($option="") {
			if ($option=="") {
				self::Error("Design parameters can not be empty");
				return "";
			}
			else {
				self::NoErrors();
				return intval (self::GetOption ($option));
			}
		}

		public function GetOptionFloat ($option) {
			if ($option=="") {
				self::Error("Design parameters can not be empty");
				return "";
			}
			else {
				self::NoErrors();
				return floatval (self::GetOption ($option));
			}
		}

		private function GetOption ($option="") {
			global $DB;

			if ($option=="") {
				self::Error("Design parameters can not be empty");
				return "";
			}
			else {
				if (isset($this->arOptions[$option]))
				{
					return $this->arOptions[$option];
				}
				else
				{
					$query = "SELECT * FROM `ms_icar_params` WHERE `option` LIKE '".$option."'";
					if ($res = $DB->Select($query)) {
						self::NoErrors();
						$this->arOptions[$option] = $res[0]["value"];
						return $res[0]["value"];
					}
					else {
						self::Error("No Information for this parameter");
						return "";
					}
				}
			}
		}

		public function SetOption ($option, $value) {
			global $DB;

			$query = "SELECT * FROM `ms_icar_params` WHERE `option` LIKE '".$option."'";
			if ($res = $DB->Select($query)) {
				$query2 = "UPDATE `ms_icar_params` SET `value` = '".$value."' WHERE `id` =".$res[0]["id"].";";
				if ($res2 = $DB->Update($query2)) {
					self::NoErrors();
					return true;
				}
				else {
					self::Error("Unable to update parameter");
					return false;
				}
			}
			else {
				$query2 = "INSERT INTO `ms_icar_params` (`option` , `value`) VALUES ('".$option."', '".$value."');";
				if ($res2 = $DB->Insert($query2)) {
					self::NoErrors();
					return true;
				}
				else {
					self::Error("Unable to save parameter");
					return false;
				}
			}
		}
	}