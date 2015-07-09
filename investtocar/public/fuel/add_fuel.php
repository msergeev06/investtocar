<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
<h1><?=GetMessage("ADDING_FUEL_COSTS")?></h1>
<? $car = $_GET["car"];
 if(isset($_POST["action"])) {
	 if ($res = CInvestToCarMain::AddFuelCosts($_POST)) {
		 ?><span style="color: green;"><?=GetMessage("ADDING_FUEL_COSTS_SUCCESS")?></span><?
	 }
	 else {
		 ?><span style="color: red;"><?=GetMessage("ADDING_FUEL_COSTS_FAILED")?></span><?
	 }
 }
?>
	<form action="" method="post">
		<input type="hidden" name="car" value="<?=$car?>">
		<table class="add_ts">
			<tr>
				<td class="title"><?=GetMessage("CAR")?></td>
				<td><? echo CInvestToCarShowSelect::Auto("fuel_auto",false,$car); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("DATE_DDMMYYYY")?></td>
				<td><input type="text" name="date" value="<?=date("d.m.Y")?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
				<td><input type="text" name="odo" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("FUEL_MARK")?></td>
				<td><? echo CInvestToCarShowSelect::FuelMark("fuel_mark",$car); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("DISPLACEMENT")?></td>
				<td><input type="text" name="liters" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("COST_FOR_LITER")?></td>
				<td><input type="text" name="cost_liter" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("FULL_TANK")?></td>
				<td><input type="checkbox" name="full_tank" value="1"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WAYPOINT")?></td>
				<td><? echo CInvestToCarShowSelect::Points("fuel_point",0,2); ?></td>
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
				<td><input type="text" name="comment" value=""></td>
			</tr>
			<tr>
				<td class="center" colspan="2"><input type="hidden" name="action" value="1"><input type="submit" value="<?=GetMessage("SUBMIT_ADD")?>"></td>
			</tr>
		</table>
	</form>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>