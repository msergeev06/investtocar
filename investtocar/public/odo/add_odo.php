<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
<h1><?=GetMessage("ADDING_ROUTE")?></h1>
<? $car = intval($_GET["car"]); ?>
<?if(!isset($_POST["action"])):?>
	<form action="" method="post">
		<input type="hidden" name="car" value="<?=$car?>">
		<table class="add_ts">
			<tr>
				<td class="title"><?=GetMessage("CAR")?></td>
				<td><? echo CInvestToCarMain::ShowSelectAuto("odo_auto",false,$car); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("DATE_DDMMYYYY")?></td>
				<td><input type="text" name="date" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
				<td><input type="text" name="odo" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("HOME_WAYPOINT")?></td>
				<td><? echo CInvestToCarMain::ShowSelectPoints("start_point"); ?></td>
			</tr>
			<tr>
				<td class="center" colspan="2"><?=GetMessage("OR")?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NAME_NEW_WAYPOINT")?></td>
				<td><input type="text" name="start_point_name" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ADDRESS_NEW_WAYPOINT")?></td>
				<td><input type="text" name="start_point_address" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("LONGITUDE_NEW_WAYPOINT")?></td>
				<td><input type="text" name="start_point_lon" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("LATITUDE_NEW_WAYPOINT")?></td>
				<td><input type="text" name="start_point_lat" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("IN_THE_CITY_Q")?></td>
				<td><input type="checkbox" name="city" value="1"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("FINAL_WAYPOINT")?></td>
				<td><? echo CInvestToCarMain::ShowSelectPoints("end_point"); ?></td>
			</tr>
			<tr>
				<td class="center" colspan="2"><?=GetMessage("OR")?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NAME_NEW_WAYPOINT")?></td>
				<td><input type="text" name="end_point_name" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ADDRESS_NEW_WAYPOINT")?></td>
				<td><input type="text" name="end_point_address" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("LONGITUDE_NEW_WAYPOINT")?></td>
				<td><input type="text" name="end_point_lon" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("LATITUDE_NEW_WAYPOINT")?></td>
				<td><input type="text" name="end_point_lat" value=""></td>
			</tr>
			<tr>
				<td class="center" colspan="2">
					<input type="hidden" name="action" value="1">
					<input type="submit" value="<?=GetMessage("SUBMIT_ADD")?>">
				</td>
			</tr>
		</table>
	</form>
<?else:?>
<?
	if ($res = CInvestToCarMain::AddNewOdo($_POST)) {
		?><span style="color: green;"><?=GetMessage("NEW_ODO_ADD_SUCCESS")?></span><p><a href="<?=$path?>odo/add_odo.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_MORE")?></a></p><?
	}
	else {
		?><span style="color: red;"><?=GetMessage("NEW_ODO_ADD_FAILED")?></span><?
	}
?>
<?endif;?>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>