<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("EDIT_NOTE_TS")?></h1>
<?
	if (isset($_GET["id"])) {
		$tsID = intval($_GET["id"]);
	}
	else {
		die("ERROR");
	}
	if (!isset($_POST["action"])) {
		if (!$arTs = CInvestToCarMain::GetTsInfo($tsID)) {
			die("ERROR");
		}
		//echo "<pre>"; print_r($arTs); echo "</pre>";

		?>
		<form action="" method="post">
			<input type="hidden" name="car" value="<?=$arTs[0]["auto"]?>">
			<table class="add_ts">
				<tr>
					<td class="title"><?=GetMessage("NUMBER_TS")?></td>
					<td><? echo CInvestToCarShowSelect::Ts("ts_num",$arTs[0]["ts_num"]); ?></td>
				</tr>
				<tr>
					<td class="title"><?=GetMessage("CAR")?></td>
					<td><? echo CInvestToCarShowSelect::Auto("ts_auto",false,$arTs[0]["auto"]); ?></td>
				</tr>
				<tr>
					<td class="title"><?=GetMessage("DATE_DDMMYYYY")?></td>
					<td><input type="text" name="date" value="<?=date("d.m.Y",$arTs[0]["date"])?>"></td>
				</tr>
				<tr>
					<td class="title"><?=GetMessage("ARTIST_WORKS")?></td>
					<td><? echo CInvestToCarShowSelect::Repair("ts_repair",$arTs[0]["repair"])?></td>
				</tr>
				<tr>
					<td class="title"><?=GetMessage("AMOUNT")?></td>
					<td><input type="text" name="cost" value="<?=$arTs[0]["cost"]?>"></td>
				</tr>
				<tr>
					<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
					<td><input type="text" name="odo" value="<?=$arTs[0]["odo"]?>"></td>
				</tr>
				<tr>
					<td class="title"><?=GetMessage("WAYPOINT")?></td>
					<td><? echo CInvestToCarShowSelect::Points("ts_point",$arTs[0]["point"]); ?></td>
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
					<td><input type="text" name="comment" value="<?=$arTs[0]["description"]?>"></td>
				</tr>
				<tr>
					<td class="center" colspan="2">
						<input type="hidden" name="action" value="1">
						<input type="hidden" name="tsID" value="<?=$tsID?>">
						<input type="submit" value="<?=GetMessage("SUBMIT_SAVE")?>">
					</td>
				</tr>
			</table>
		</form>

		<?
	}
	else {
		$arPost = $_POST;
		if ($res = CInvestToCarMain::UpdateTsInfo($tsID,$arPost)) {
			?><span style="color: green;"><?=GetMessage("DATA_UPDATE_SUCCESS")?></span><?
		}
		else {
			?><span style="color: red;"><?=GetMessage("DATA_UPDATE_ERROR")?></span><?
		}
		//echo "<pre>"; print_r($arPost); echo "</pre>";
	}
?>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>