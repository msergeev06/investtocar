<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("EDITING_FUEL_COSTS")?></h1>
<?
	$fuelCostsID = $_GET["id"];

	if(isset($_POST["action"])) {
		if ($res = CInvestToCarMain::UpdateFuelCosts($_POST)) {
			?><span style="color: green;"><?=GetMessage("ADDING_FUEL_COSTS_SUCCESS")?></span><?
		}
		else {
			?><span style="color: red;"><?=GetMessage("ADDING_FUEL_COSTS_FAILED")?></span><?
		}
	}
	else {
		$arFuel = CInvestToCarMain::GetFuelCostsByID($fuelCostsID);
		//echo "<pre>"; print_r($arFuel); echo "</pre>";
	}
?>
	<form action="" method="post">
		<input type="hidden" name="id" value="<?=$fuelCostsID?>">
		<table class="add_ts">
			<tr>
				<td class="title"><?=GetMessage("CAR")?></td>
				<td><? echo CInvestToCarMain::ShowSelectAuto("fuel_auto",false,$arFuel["auto"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("DATE_DDMMYYYY")?></td>
				<td><input type="text" name="date" value="<?=date("d.m.Y",$arFuel["date"])?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
				<td><input type="text" name="odo" value="<?=$arFuel["odo"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("FUEL_MARK")?></td>
				<td><? echo CInvestToCarMain::ShowSelectFuelMark("fuel_mark",$arFuel["auto"],$arFuel["fuel_mark"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("DISPLACEMENT")?></td>
				<td><input type="text" name="liters" value="<?=$arFuel["liter"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("COST_FOR_LITER")?></td>
				<td><input type="text" name="cost_liter" value="<?=$arFuel["liter_cost"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("FULL_TANK")?></td>
				<td><input type="checkbox" name="full_tank" value="1"<?=($arFuel["full"]>0)?" checked":""?>></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WAYPOINT")?></td>
				<td><? echo CInvestToCarMain::ShowSelectPoints("fuel_point",$arFuel["point"],2); ?></td>
			</tr>
			<tr>
				<td class="center" colspan="2"><?=GetMessage("OR")?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NAME_NEW_WAYPOINT")?></td>
				<td><input type="text" name="newpoint_name" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ADDRESS_NEW_WAYPOINT")?></td>
				<td><input type="text" name="newpoint_address" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("LONGITUDE_NEW_WAYPOINT")?></td>
				<td><input type="text" name="newpoint_lon" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("LATITUDE_NEW_WAYPOINT")?></td>
				<td><input type="text" name="newpoint_lat" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("COMMENT")?></td>
				<td><input type="text" name="comment" value="<?=$arFuel["description"]?>"></td>
			</tr>
			<tr>
				<td class="center" colspan="2"><input type="hidden" name="action" value="1"><input type="submit" value="<?=GetMessage("SUBMIT_SAVE")?>"></td>
			</tr>
		</table>
	</form>


<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>