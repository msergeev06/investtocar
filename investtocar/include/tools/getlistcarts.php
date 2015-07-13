<? require_once ($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/include/header.php");

	$car = $_POST["car"];
	$arReturn["html"] = "";
	$arTsList = CInvestToCarTs::GetListCarTs ($car);
	foreach($arTsList as $arTs) {
		$arReturn["html"] .= "<tr>";
		$arReturn["html"] .= "<td>ТО-".$arTs["ts_num"]."</td>";
		$arReturn["html"] .= "<td>".date("d.m.Y",$arTs["date"])."</td>";
		$arReturn["html"] .= "<td>".$arTs["cost"]."</td>";
		$arReturn["html"] .= "<td>".$arTs["odo"]."</td>";
		$arReturn["html"] .= "<td>".$arTs["repair"]."</td>";
		$arReturn["html"] .= "<td>";
		if ($arTs["point"]!==false)
		{
			$arReturn["html"] .= $arTs["point"]["name"];
		}
		else {
			$arReturn["html"] .= "Нет данных";
		}
		$arReturn["html"] .= "</td>";
		$arReturn["html"] .= "<td><a href=\"".INVESTTOCAR_PATH."public/ts/ts_edit.php?id=".$arTs["id"]."\"><img src=\"/msergeev/images/edit.png\"></a></td>";
		$arReturn["html"] .= "<td><a href=\"".INVESTTOCAR_PATH."public/ts/ts_delete.php?id=".$arTs["id"]."\"><img src=\"/msergeev/images/del.png\"></a></td>";
		$arReturn["html"] .= "</tr>";
	}

	echo json_encode($arReturn);

	require_once ($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/include/footer.php");

?>