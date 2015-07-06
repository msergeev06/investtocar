<? require_once ($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/include/header.php");

	$arReturn["brand"] = intval($_POST["brand"]);
	if ($arReturn["brand"]>0) {
		$arReturn["select"] = CInvestToCarMain::ShowSelectCarModel($arReturn["brand"]);
		if (!$arReturn["select"]) {
			$arReturn["select"] = "<input type=\"text\" name=\"car_model_add\" value=\"\">";
		}
	}

	echo json_encode($arReturn);

 require_once ($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/include/footer.php");

?>