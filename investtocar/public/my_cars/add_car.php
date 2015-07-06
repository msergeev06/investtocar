<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1>Добавление нового автомобиля</h1>
<?
if (isset($_POST["action"])) {

	$arData["name"]             = trim($_POST["name"]);
	$arData["trademark"]        = intval($_POST["car_brand"]);
	if (isset($_POST["car_model"])) {
		$arData["model"] = intval($_POST["car_model"]);
	}
	elseif (isset($_POST["car_model_add"])) {
		//Необходимо добавить новую модель
		if ($arData["trademark"]>0) {
			if ($newModel = CInvestToCarMain::AddNewModel($arData["trademark"],$_POST["car_model_add"])) {
				$arData["model"] = $newModel;
			}
			else {
				$arData["model"] = 0;
			}
		}
		else {
			$arData["model"] = 0;
		}
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
	$arData["creditcost"]       = floatval(str_replace(" ", "", $arData["creditcost"]));
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

	if ($newCar = CInvestToCarMain::AddNewCarGarage($arData)) {
		echo "<span style=\"color: green;\">Автомобиль (".$newCar.") ".$arData["name"]." - успешно добавлен!</span><br>";
	}
}
else {
?>
<form action="" method="POST">
<table class="add_new_car">
	<tr>
		<td>Имя авто:</td>
		<td><input type="text" name="name" value=""></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Марка авто:</td>
		<td><? echo CInvestToCarMain::ShowSelectCarBrands(); ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Модель:</td>
		<td id="td_car_model"><input type="text" name="car_model_add" value=""></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Год выпуска: </td>
		<td><? echo CInvestToCarMain::ShowSelectCarCreateYear(); ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>VIN:</td>
		<td><input type="text" name="vin" value=""></td>
		<td>Цифры и латинские буквы</td>
	</tr>
	<tr>
		<td>Гос номер:</td>
		<td><input type="text" name="gosnum" value=""></td>
		<td>Цифры и латинские буквы</td>
	</tr>
	<tr>
		<td>Объём двигателя:</td>
		<td><input type="text" name="engine" value=""></td>
		<td>литра</td>
	</tr>
	<tr>
		<td>КПП:</td>
		<td><? echo CInvestToCarMain::ShowSelectCarGearbox(); ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Тип кузова:</td>
		<td><? echo CInvestToCarMain::ShowSelectCarBody(); ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Интервал прохождения ТО:</td>
		<td><input type="text" name="interval_ts" value=""></td>
		<td>км</td>
	</tr>
	<tr>
		<td>Стоимость при покупке:</td>
		<td><input type="text" name="cost" value=""></td>
		<td>руб.</td>
	</tr>
	<tr>
		<td>Пробег при покупке:</td>
		<td><input type="text" name="odo" value=""></td>
		<td>км</td>
	</tr>
	<tr>
		<td>Автомобиль в кредит:</td>
		<td><input type="checkbox" name="credit" value="1"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Сумма кредита:</td>
		<td><input type="text" name="creditcost" value=""></td>
		<td>руб.</td>
	</tr>
	<tr>
		<td>Дата окончания ОСАГО:</td>
		<td><input type="text" name="end_osago" value=""></td>
		<td><a href="#">Настроить напоминание Алисы</a></td>
	</tr>
	<tr>
		<td>Дата окончания ГТО:</td>
		<td><input type="text" name="end_gto" value=""></td>
		<td><a href="#">Настроить напоминание Алисы</a></td>
	</tr>
	<tr>
		<td>Использовать данный автомобиль по-умолчанию:</td>
		<td><input type="checkbox" name="default" value="1"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2"><input type="hidden" name="action" value="1"><input type="submit" value="Добавить"></td>
	</tr>

</table>
</form>
<script type="text/javascript">
	$(document).on("ready",function(){
		$("#car_brand").on("change",function(){
			var sel = $(this).val();

			$.post(
				"/msergeev/investtocar/include/tools/getselectmodel.php",
				{
					brand: sel
				},
				function (data) {
					//console.log(data);
					$("#td_car_model").html("");
					$("#td_car_model").append(data.select);
				},
				"json"
			);
		});
	});
</script>
<? }
 require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>