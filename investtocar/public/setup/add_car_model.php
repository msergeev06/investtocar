<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("ADD_AUTO_MODEL")?></h1>
<?
	if (isset($_POST["car_brand"])) {
		$selected = $_POST["car_brand"];
	}
	else {
		$selected = 0;
	}

	if (isset($_POST["action"]) && intval($_POST["action"])==1) {
		//Добавляем модель в базу
		if ($added = CInvestToCarMain::AddNewModel($_POST["car_brand"],$_POST["car_model"])) {
			echo "<span style=\"color: green;\">".GetMessage("ADDED").": (".$added.") ".$_POST["car_model"]."</span><br>";
		}
	}
?>
<form action="" method="POST">
	<? echo CInvestToCarMain::ShowSelectCarBrands($selected); ?><br>
	<input type="text" value="" name="car_model"><input type="hidden" name="action" value="1">
	<input type="submit" value="<?=GetMessage("SUBMIT_ADD")?>">
</form>


<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>