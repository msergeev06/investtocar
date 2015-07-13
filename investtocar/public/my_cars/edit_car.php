<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("EDITING_CAR")?></h1>
<?
	if (isset($_POST["action"])) {
		if ($res = CInvestToCarCars::UpdateCarInGarage($_POST)) {
			?><span style="color: green;"><?=GetMessage("EDITING_CAR_SUCCESS")?></span><?
		}
		else {
			?><span style="color: red;"><?=GetMessage("EDITING_CAR_FAILED")?></span><?
		}
	}
	else {
		if (intval($_GET["car"])<=0) {
			die();
		}
		$arCar = CInvestToCarCars::GetMyCars(intval($_GET["car"]));
		//echo "<pre>"; print_r($arCar); echo "</pre>";
		?>
		<form action="" method="POST">
			<table class="add_new_car">
				<tr>
					<td><?=GetMessage("NAME")?>:</td>
					<td><input type="text" name="name" value="<?=$arCar["name"]?>"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><?=GetMessage("CAR_MARK")?>:</td>
					<td><? echo CInvestToCarShowSelect::CarBrands($arCar["trademark"]); ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><?=GetMessage("CAR_MODEL")?>:</td>
					<td><? echo CInvestToCarShowSelect::CarModel($arCar["trademark"],$arCar["model"]); ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><?=GetMessage("CAR_YEAR")?>: </td>
					<td><? echo CInvestToCarShowSelect::CarCreateYear($arCar["year"]); ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><?=GetMessage("VIN")?>:</td>
					<td><input type="text" name="vin" value="<?=$arCar["vin"]?>"></td>
					<td><?=GetMessage("NUMERIC_AND_LAT_SIGN")?></td>
				</tr>
				<tr>
					<td><?=GetMessage("CAR_NUMBER")?>:</td>
					<td><input type="text" name="gosnum" value="<?=$arCar["carnumber"]?>"></td>
					<td><?=GetMessage("NUMERIC_AND_LAT_SIGN")?></td>
				</tr>
				<tr>
					<td><?=GetMessage("ENGINE_CAPACITY")?>:</td>
					<td><input type="text" name="engine" value="<?=$arCar["enginecapacity"]?>"></td>
					<td><?=GetMessage("LITERS")?></td>
				</tr>
				<tr>
					<td><?=GetMessage("GEAR_BOX")?>:</td>
					<td><? echo CInvestToCarShowSelect::CarGearbox($arCar["gearshift"]); ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><?=GetMessage("BODY_TYPE")?>:</td>
					<td><? echo CInvestToCarShowSelect::CarBody($arCar["body"]); ?></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><?=GetMessage("MAINTENANCE_INTERVAL")?>:</td>
					<td><input type="text" name="interval_ts" value="<?=$arCar["interval_ts"]?>"></td>
					<td><?=GetMessage("KM")?></td>
				</tr>
				<tr>
					<td><?=GetMessage("CAR_COST")?>:</td>
					<td><input type="text" name="cost" value="<?=$arCar["cost"]?>"></td>
					<td><?=GetMessage("RUB")?></td>
				</tr>
				<tr>
					<td><?=GetMessage("START_ODO")?>:</td>
					<td><input type="text" name="odo" value="<?=$arCar["mileage"]?>"></td>
					<td><?=GetMessage("KM")?></td>
				</tr>
				<tr>
					<td><?=GetMessage("CREDIT")?>?</td>
					<td><input type="checkbox" name="credit" value="1"<? if (intval($arCar["credit"])==1) echo " checked"; ?>></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><?=GetMessage("CREDIT")?> (<?=GetMessage("AMOUNT")?>):</td>
					<td><input type="text" name="creditcost" value="<? if (intval($arCar["credit"])==1) echo $arCar["creditcost"]; ?>"></td>
					<td><?=GetMessage("RUB")?></td>
				</tr>
				<tr>
					<td><?=GetMessage("DATE_END_OSAGO")?>:</td>
					<td><input type="text" name="end_osago" value="<?=date("d.m.Y",$arCar["osago_end"])?>"></td>
					<td><a href="#"><?=GetMessage("SETUP_ALARM_SMART_HOME")?></a></td>
				</tr>
				<tr>
					<td><?=GetMessage("DATE_END_GTO")?>:</td>
					<td><input type="text" name="end_gto" value="<?=date("d.m.Y",$arCar["gto_end"])?>"></td>
					<td><a href="#"><?=GetMessage("SETUP_ALARM_SMART_HOME")?></a></td>
				</tr>
				<tr>
					<td><?=GetMessage("CAR_USE_AS_DEFAULT")?>:</td>
					<td><input type="checkbox" name="default" value="1"<? if (intval($arCar["default"])==1) echo " checked"; ?>></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2"><input type="hidden" name="car" value="<?=$_GET["car"]?>"><input type="hidden" name="action" value="1"><input type="submit" value="<?=GetMessage("SUBMIT_SAVE")?>"></td>
				</tr>

			</table>
		</form>
	<? }
	require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>