<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("MAINTENANCE_COST")?></h1>
<? $car = $_GET["car"]; ?>
<?if(!isset($_POST["action"])):?>
<form action="" method="post">
	<input type="hidden" name="car" value="<?=$car?>">
	<table class="add_ts">
		<tr>
			<td class="title"><?=GetMessage("NUMBER_TS")?></td>
			<td><? echo CInvestToCarShowSelect::Ts("ts_num"); ?></td>
		</tr>
		<tr>
			<td class="title"><?=GetMessage("CAR")?></td>
			<td><? echo CInvestToCarShowSelect::Auto("ts_auto",false,$car); ?></td>
		</tr>
		<tr>
			<td class="title"><?=GetMessage("DATE_DDMMYYYY")?></td>
			<td><input type="text" name="date" value=""></td>
		</tr>
		<tr>
			<td class="title"><?=GetMessage("ARTIST_WORKS")?></td>
			<td><select name="ts_repair">
					<option value="1" selected><?=GetMessage("NO_DEALER")?></option>
					<option value="2"><?=GetMessage("DEALER")?></option>
					<option value="3"><?=GetMessage("SERVICE_STATION")?></option>
					<option value="4"><?=GetMessage("DID_HE")?></option>
					<option value="5"><?=GetMessage("PRIVATE_SERVICE")?></option>
				</select></td>
		</tr>
		<tr>
			<td class="title"><?=GetMessage("AMOUNT")?></td>
			<td><input type="text" name="cost" value=""></td>
		</tr>
		<tr>
			<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
			<td><input type="text" name="odo" value=""></td>
		</tr>
		<tr>
			<td class="title"><?=GetMessage("WAYPOINT")?></td>
			<td><? echo CInvestToCarShowSelect::Points("ts_point"); ?></td>
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
<?else:?>
	<?
		if ($res = CInvestToCarMain::AddNewTs($_POST)) {
			?><span style="color: green;"><?=GetMessage("EXPENDED_TS_ADD_SUCCESS")?></span><?
		}
		else {
			?><span style="color: red;"><?=GetMessage("EXPENDED_TS_ADD_FAILED")?></span><?
		}
		//echo "<pre>"; print_r($_POST); echo "</pre>";
	?>
<?endif;?>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>