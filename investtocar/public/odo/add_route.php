<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("ADDING_ROUTE")?></h1>
<? $car = intval($_GET["car"]); ?>
<?
	$defaultCar = CInvestToCarCars::GetDefaultCar();
	$pWaypoint = CInvestToCarMain::GetInfoByCode ("pointtype","waypoint");
	$pService = CInvestToCarMain::GetInfoByCode ("pointtype","service");
	$pStore = CInvestToCarMain::GetInfoByCode ("pointtype","shop");
	$pCarwash = CInvestToCarMain::GetInfoByCode ("pointtype","wash");

?>

<?
	if (isset($_POST["action"])&&$_POST["action"]==1) {
		if (CInvestToCarOdo::AddNewRoute($_POST)) {
			echo "<span class=\"ok\">".GetMessage("DATA_ADD_SUCCESS")."</span>";
		}
		else {
			echo "<span class=\"err\">".GetMessage("DATA_ADD_ERROR")."</span>";
		}
		$arResult["date"] = CInvestToCarMain::ConvertDateToTimestamp($_POST["date"]);
		CInvestToCarOdo::UpdateDayOdometer($arResult["date"]);
	}



?>

<form name="add_route" method="POST">
	<table style="border: 0;">
		<tr>
			<td class="name"><?=GetMessage("CAR")?></td>
			<td class="value"><? echo CInvestToCarShowSelect::Auto("auto")?></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("DATE_DDMMYYYY")?></td>
			<td class="value"><input type="text" name="date" value="<?=date("d.m.Y")?>"></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("ODOMETER_VALUE")?></td>
			<td class="value"><input type="text" name="odo" value=""></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("HOME_WAYPOINT")?></td>
			<td class="value"><? echo CInvestToCarShowSelect::Points("start_point",0,array($pWaypoint,$pService,$pStore,$pCarwash))?></td>
		</tr>
		<tr>
			<td class="name">&nbsp;</td>
			<td class="value"><?=GetMessage("OR")?></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("NAME_NEW_WAYPOINT")?></td>
			<td class="value"><input type="text" name="start_name" value=""></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("ADDRESS_NEW_WAYPOINT")?></td>
			<td class="value"><input type="text" name="start_address" value=""></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("LONGITUDE_NEW_WAYPOINT")?></td>
			<td class="value"><input type="text" name="start_lon" value=""></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("LATITUDE_NEW_WAYPOINT")?></td>
			<td class="value"><input type="text" name="start_lat" value=""></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("IN_THE_CITY_Q")?></td>
			<td class="value"><input type="checkbox" name="end_start" value="1"></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("FINAL_WAYPOINT")?></td>
			<td class="value"><? echo CInvestToCarShowSelect::Points("end_point",0,array($pWaypoint,$pService,$pStore,$pCarwash))?></td>
		</tr>
		<tr>
			<td class="name">&nbsp;</td>
			<td class="value"><?=GetMessage("OR")?></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("NAME_NEW_WAYPOINT")?></td>
			<td class="value"><input type="text" name="end_name" value=""></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("ADDRESS_NEW_WAYPOINT")?></td>
			<td class="value"><input type="text" name="end_address" value=""></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("LONGITUDE_NEW_WAYPOINT")?></td>
			<td class="value"><input type="text" name="end_lon" value=""></td>
		</tr>
		<tr>
			<td class="name"><?=GetMessage("LATITUDE_NEW_WAYPOINT")?></td>
			<td class="value"><input type="text" name="end_lat" value=""></td>
		</tr>
		<tr>
			<td class="name"><input type="hidden" name="action" value="1"></td>
			<td class="value"><input type="submit" value="<?=GetMessage("SUBMIT_ADD")?>"></td>
		</tr>
	</table>
</form>
<style>
	.name {
		text-align: right;
	}
	.value {
		text-align: left;
	}
	.ok {
		color: green;
	}
	.err {
		color: red;
	}
</style>
<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>