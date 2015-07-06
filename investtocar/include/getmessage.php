<?php

	function GetMessage ($code, $arReplace=array()) {
		global $MESS;
		if (!isset($MESS[$code])) {
			return false;
		}
		else {
			$str = $MESS[$code];
			if (!empty($arReplace)) {
				foreach ($arReplace as $search=>$replace) {
					$str = str_replace("#".$search."#", $replace, $str);
				}
			}

			return $str;
		}
	}