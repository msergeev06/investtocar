<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("EDIT_REPAIR_PARTS")?></h1>
<? $repairPartsID = $_GET["id"];
	$pService = $OPTIONS->GetOptionInt("point_service");
	$pStore = $OPTIONS->GetOptionInt("point_auto_parts_store");
	$pCarwash = $OPTIONS->GetOptionInt("point_carwash");

	if (isset($_POST["action"])) {
		if ($res = CInvestToCarRepairParts::UpdateRepairParts($_POST)) {
			?><span style="color: green;"><?=GetMessage("EDIT_REPAIR_PARTS_SUCCESS")?></span><?
		}
		else {
			?><span style="color: red;"><?=GetMessage("EDIT_REPAIR_PARTS_FAILED")?></span><?
		}
	}
	else {
		$arRepairParts = CInvestToCarRepairParts::GetRepairPartsInfo($repairPartsID);
		?>
	<form action="" method="post">
		<input type="hidden" name="id" value="<?=$repairPartsID?>">
		<table class="add_ts">
			<tr>
				<td class="title"><?=GetMessage("CAR")?></td>
				<td><? echo CInvestToCarShowSelect::Auto("auto",false,$arRepairParts["auto"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("DATE_DDMMYYYY")?></td>
				<td><input type="text" name="date" value="<?=date("d.m.Y",$arRepairParts["date"])?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NAME")?></td>
				<td><input type="text" name="name" value="<?=$arRepairParts["name"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("STORAGE")?></td>
				<td><? echo CInvestToCarShowSelect::Storage("storage",$arRepairParts["storage"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("CATALOG_NUMBER")?></td>
				<td><input type="text" name="catalog_number" value="<?=$arRepairParts["catalog_number"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NUMBER")?></td>
				<td><input type="text" name="number" value="<?=$arRepairParts["number"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("AMOUNT")?></td>
				<td><input type="text" name="cost" value="<?=$arRepairParts["cost"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("REASON_REPLACEMENT")?></td>
				<td><? echo CInvestToCarShowSelect::ReasonReplacement("reason",$arRepairParts["reason"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("REASON_DETAILS")?></td>
				<td class="reason_add">
					<?
						if ($arRepairParts["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","ts"))) {
							echo CInvestToCarShowSelect::ReasonTs("reason_ts",$arRepairParts["auto"],$arRepairParts["reason_detail"]);
						}
						else {
							echo CInvestToCarShowSelect::ReasonTs("reason_ts",$arRepairParts["auto"],0,' style="display: none;"');
						}
					?>
					<?
						if ($arRepairParts["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","breakdown"))) {
							echo CInvestToCarShowSelect::ReasonRepair("reason_breakdown",$arRepairParts["auto"],$arRepairParts["reason_detail"]);
						}
						else {
							echo CInvestToCarShowSelect::ReasonRepair("reason_breakdown",$arRepairParts["auto"],0,' style="display: none;"');
						}
					?>
					<?
						if ($arRepairParts["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","dtp"))) {
							echo CInvestToCarShowSelect::ReasonDtp("reason_dtp",$arRepairParts["auto"],$arRepairParts["reason_detail"]);
						}
						else {
							echo CInvestToCarShowSelect::ReasonDtp("reason_dtp",$arRepairParts["auto"],0,' style="display: none;"');
						}
					?>
					<?
						if ($arRepairParts["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","tuning"))) {
							echo CInvestToCarShowSelect::ReasonRepair("reason_tuning",$arRepairParts["auto"],$arRepairParts["reason_detail"]);
						}
						else {
							echo CInvestToCarShowSelect::ReasonRepair("reason_tuning",$arRepairParts["auto"],0,' style="display: none;"');
						}
					?>
					<?
						if ($arRepairParts["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","upgrade"))) {
							echo CInvestToCarShowSelect::ReasonRepair("reason_upgrade",$arRepairParts["auto"],$arRepairParts["reason_detail"]);
						}
						else {
							echo CInvestToCarShowSelect::ReasonRepair("reason_upgrade",$arRepairParts["auto"],0,' style="display: none;"');
						}
					?>
					<?
						if ($arRepairParts["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","tire"))) {
							?><span class="reason_tire"">-</span><?
						}
						else {
							?><span class="reason_tire" style="display: none;">-</span><?
						}
					?>

				</td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WHO_PAID")?></td>
				<td><? echo CInvestToCarShowSelect::WhoPaid("who_paid",$arRepairParts["who_paid"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
				<td><input type="text" name="odo" value="<?=$arRepairParts["odo"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WAYPOINT")?></td>
				<td><? echo CInvestToCarShowSelect::Points("waypoint",$arRepairParts["waypoint"],array($pService,$pStore,$pCarwash)); ?></td>
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
				<td><input type="text" name="comment" value="<?=$arRepairParts["comment"]?>"></td>
			</tr>
			<tr>
				<td class="center" colspan="2"><input type="hidden" name="action" value="1"><input type="submit" value="<?=GetMessage("SUBMIT_SAVE")?>"></td>
			</tr>
		</table>
	</form>
	<script type="text/javascript">

		$(document).on("ready",function(){
			$(".reason").on("change",function(){
				sel = $(this).val();
				if (sel==<?=$OPTIONS->GetOptionInt("reason_replacement_ts")?>) {
					$(".reason_ts").show();
					$(".reason_breakdown").hide();
					$(".reason_dtp").hide();
					$(".reason_tuning").hide();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==<?=$OPTIONS->GetOptionInt("reason_replacement_breakdown")?>) {
					$(".reason_ts").hide();
					$(".reason_breakdown").show();
					$(".reason_dtp").hide();
					$(".reason_tuning").hide();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==<?=$OPTIONS->GetOptionInt("reason_replacement_dtp")?>) {
					$(".reason_ts").hide();
					$(".reason_breakdown").hide();
					$(".reason_dtp").show();
					$(".reason_tuning").hide();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==<?=$OPTIONS->GetOptionInt("reason_replacement_tuning")?>) {
					$(".reason_ts").hide();
					$(".reason_breakdown").hide();
					$(".reason_dtp").hide();
					$(".reason_tuning").show();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==<?=$OPTIONS->GetOptionInt("reason_replacement_upgrade")?>) {
					$(".reason_ts").hide();
					$(".reason_breakdown").hide();
					$(".reason_dtp").hide();
					$(".reason_tuning").hide();
					$(".reason_upgrade").show();
					$(".reason_tire").hide();
				}
				if (sel==<?=$OPTIONS->GetOptionInt("reason_replacement_tire")?>) {
					$(".reason_ts").hide();
					$(".reason_breakdown").hide();
					$(".reason_dtp").hide();
					$(".reason_tuning").hide();
					$(".reason_upgrade").hide();
					$(".reason_tire").show();
				}
			});
		});

	</script>
	<?
	}
?>
<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>