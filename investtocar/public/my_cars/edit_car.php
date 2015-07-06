<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1>Редактирование автомобиля</h1>
<?
	if (isset($_POST["action"])) {
		$arData["name"]             = trim($_POST["name"]);
		$arData["trademark"]        = intval($_POST["car_brand"]);
		if (isset($_POST["car_model"])) {
			$arData["model"] = intval($_POST["car_model"]);
		}
		else {
			$arData["model"] = 0;
		}
		$arData["year"]             = intval($_POST["car_year"]);
		$arData["vin"]              = trim($_POST["vin"]);
		$arData["carnumber"]        = $_POST["gosnum"];
		$arData["enginecapacity"]   = $_POST["engine"];
		$arData["enginecapacity"]   = floatval(str_replace(",",".",$arData["enginecapacity"]));
		$arData["gearshift"]        = intval($_POST["car_gearbox"]);
		$arData["body"]             = intval($_POST["car_body"]);
		$arData["interval_ts"]      = intval($_POST["interval_ts"]);
		$arData["cost"]             = str_replace(",",".", $_POST["cost"]);
		$arData["cost"]             = floatval(str_replace(" ", "", $arData["cost"]));
		$arData["mileage"]          = floatval(str_replace(",",".",$_POST["odo"]));
		if (isset($_POST["credit"]) && $_POST["credit"]==1) {
			$arData["credit"]       = 1;
		}
		else {
			$arData["credit"]       = 0;
		}
		$arData["creditcost"]       = floatval(str_replace(" ", "", $_POST["creditcost"]));
		$osago_end        = str_replace("-",".",$_POST["end_osago"]);
		list (
			$osago_end_day,
			$osago_end_month,
			$osago_end_year
			) = explode (".",$osago_end);
		$arData["osago_end"] = mktime(0,0,0,intval($osago_end_month),intval($osago_end_day),intval($osago_end_year))+3600;
		$gto_end          = str_replace("-",".",$_POST["end_gto"]);
		list (
			$gto_end_day,
			$gto_end_month,
			$gto_end_year
			) = explode (".",$gto_end);
		$arData["gto_end"] = mktime(0,0,0,intval($gto_end_month),intval($gto_end_day),intval($gto_end_year))+3600;
		if (isset($_POST["default"]) && $_POST["default"]==1) {
			$arData["default"]      = 1;
		}
		else {
			$arData["default"]      = 0;
		}

		if ($newCar = CInvestToCarMain::UpdateCarInGarage($_POST["car"], $arData)) {
			echo "<span style=\"color: green;\">Автомобиль (".$newCar.") ".$arData["name"]." - успешно обновлен!</span><br>";
		}

	}
	else {
		if (intval($_GET["car"])<=0) {
			die();
		}
		$arCar = CInvestToCarMain::GetMyCars(intval($_GET["car"]));
		//echo "<pre>"; print_r($arCar); echo "</pre>";
		?>
		<form action="" method="POST">
			<table class="add_new_car">
				<tr>
					<td>Имя авто:</td>
					<td><input type="text" name="name" value="<?=$arCar["name"]?>"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Марка авто:</td>
					<td><? echo CInvestToCarMain::ShowSelectCarBrands($arCar["trademark"]); ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Модель:</td>
					<td><? echo CInvestToCarMain::ShowSelectCarModel($arCar["trademark"],$arCar["model"]); ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Год выпуска: </td>
					<td><? echo CInvestToCarMain::ShowSelectCarCreateYear($arCar["year"]); ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>VIN:</td>
					<td><input type="text" name="vin" value="<?=$arCar["vin"]?>"></td>
					<td>Цифры и латинские буквы</td>
				</tr>
				<tr>
					<td>Гос номер:</td>
					<td><input type="text" name="gosnum" value="<?=$arCar["carnumber"]?>"></td>
					<td>Цифры и латинские буквы</td>
				</tr>
				<tr>
					<td>Объём двигателя:</td>
					<td><input type="text" name="engine" value="<?=$arCar["enginecapacity"]?>"></td>
					<td>литра</td>
				</tr>
				<tr>
					<td>КПП:</td>
					<td><? echo CInvestToCarMain::ShowSelectCarGearbox($arCar["gearshift"]); ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Тип кузова:</td>
					<td><? echo CInvestToCarMain::ShowSelectCarBody($arCar["body"]); ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Интервал прохождения ТО:</td>
					<td><input type="text" name="interval_ts" value="<?=$arCar["interval_ts"]?>"></td>
					<td>км</td>
				</tr>
				<tr>
					<td>Стоимость при покупке:</td>
					<td><input type="text" name="cost" value="<?=$arCar["cost"]?>"></td>
					<td>руб.</td>
				</tr>
				<tr>
					<td>Пробег при покупке:</td>
					<td><input type="text" name="odo" value="<?=$arCar["mileage"]?>"></td>
					<td>км</td>
				</tr>
				<tr>
					<td>Автомобиль в кредит:</td>
					<td><input type="checkbox" name="credit" value="1"<? if (intval($arCar["credit"])==1) echo " checked"; ?>></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Сумма кредита:</td>
					<td><input type="text" name="creditcost" value="<? if (intval($arCar["credit"])==1) echo $arCar["creditcost"]; ?>"></td>
					<td>руб.</td>
				</tr>
				<tr>
					<td>Дата окончания ОСАГО:</td>
					<td><input type="text" name="end_osago" value="<?=date("d.m.Y",$arCar["osago_end"])?>"></td>
					<td><a href="#">Настроить напоминание Алисы</a></td>
				</tr>
				<tr>
					<td>Дата окончания ГТО:</td>
					<td><input type="text" name="end_gto" value="<?=date("d.m.Y",$arCar["gto_end"])?>"></td>
					<td><a href="#">Настроить напоминание Алисы</a></td>
				</tr>
				<tr>
					<td>Использовать данный автомобиль по-умолчанию:</td>
					<td><input type="checkbox" name="default" value="1"<? if (intval($arCar["default"])==1) echo " checked"; ?>></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2"><input type="hidden" name="car" value="<?=$_GET["car"]?>"><input type="hidden" name="action" value="1"><input type="submit" value="Сохранить"></td>
				</tr>

			</table>
		</form>
	<? }
	require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>