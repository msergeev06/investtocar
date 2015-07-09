<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("ADD_REPAIR_PARTS")?></h1>
<? $car = $_GET["car"];
	$pService = $OPTIONS->GetOptionInt("point_service");
	$pStore = $OPTIONS->GetOptionInt("point_auto_parts_store");
	$pCarwash = $OPTIONS->GetOptionInt("point_carwash");

	if (isset($_POST["action"])) {
		if ($res = CInvestToCarMain::AddRepairParts($_POST)) {
			?><span style="color: green;"><?=GetMessage("ADD_REPAIR_PARTS_SUCCESS")?></span><?
		}
		else {
			?><span style="color: red;"><?=GetMessage("ADD_REPAIR_PARTS_FAILED")?></span><?
		}
	}
?>
	<form action="" method="post">
		<input type="hidden" name="car" value="<?=$car?>">
		<table class="add_ts">
			<tr>
				<td class="title"><?=GetMessage("CAR")?></td>
				<td><? echo CInvestToCarShowSelect::Auto("auto",false,$car); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("DATE_DDMMYYYY")?></td>
				<td><input type="text" name="date" value="<?=date("d.m.Y")?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NAME")?></td>
				<td><input type="text" name="name" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("STORAGE")?></td>
				<td><select name="storage">
						<option value="1" selected><?=GetMessage("ESTABLISHED")?></option>
						<option value="2"><?=GetMessage("IN_STOCK")?></option>
					</select></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("CATALOG_NUMBER")?></td>
				<td><input type="text" name="catalog_number" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NUMBER")?></td>
				<td><input type="text" name="number" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("AMOUNT")?></td>
				<td><input type="text" name="cost" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("REASON_REPLACEMENT")?></td>
				<td><select name="reason" class="reason">
						<option value="1" selected><?=GetMessage("PLANNED_OR_TS")?></option>
						<option value="2"><?=GetMessage("BREAKDOWN")?></option>
						<option value="3"><?=GetMessage("ACCIDENT")?></option>
						<option value="4"><?=GetMessage("TUNING")?></option>
						<option value="5"><?=GetMessage("UPGRADE")?></option>
						<option value="6"><?=GetMessage("TIRE")?></option>
					</select></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("REASON_DETAILS")?></td>
				<td class="reason_add">
					<? echo CInvestToCarShowSelect::ReasonTs("reason_ts",$car); ?>
					<? echo CInvestToCarShowSelect::ReasonRepair("reason_breakdown",$car,0,' style="display: none;"'); ?>
					<? echo CInvestToCarShowSelect::ReasonDtp("reason_dtp",$car,0,' style="display: none;"'); ?>
					<? echo CInvestToCarShowSelect::ReasonRepair("reason_tuning",$car,0,' style="display: none;"'); ?>
					<? echo CInvestToCarShowSelect::ReasonRepair("reason_upgrade",$car,0,' style="display: none;"'); ?>
					<span class="reason_tire" style="display: none;">-</span>
				</td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WHO_PAID")?></td>
				<td><select name="who_paid">
						<option value="1" selected><?=GetMessage("PAID_HIMSELF")?></option>
						<option value="2"><?=GetMessage("OSAGO")?></option>
						<option value="3"><?=GetMessage("KASKO")?></option>
					</select></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
				<td><input type="text" name="odo" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WAYPOINT")?></td>
				<td><? echo CInvestToCarShowSelect::Points("waypoint",0,array($pService,$pStore,$pCarwash)); ?></td>
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
	<script type="text/javascript">

		$(document).on("ready",function(){
			$(".reason").on("change",function(){
				sel = $(this).val();
				if (sel==1) {
					$(".reason_ts").show();
					$(".reason_breakdown").hide();
					$(".reason_dtp").hide();
					$(".reason_tuning").hide();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==2) {
					$(".reason_ts").hide();
					$(".reason_breakdown").show();
					$(".reason_dtp").hide();
					$(".reason_tuning").hide();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==3) {
					$(".reason_ts").hide();
					$(".reason_breakdown").hide();
					$(".reason_dtp").show();
					$(".reason_tuning").hide();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==4) {
					$(".reason_ts").hide();
					$(".reason_breakdown").hide();
					$(".reason_dtp").hide();
					$(".reason_tuning").show();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==5) {
					$(".reason_ts").hide();
					$(".reason_breakdown").hide();
					$(".reason_dtp").hide();
					$(".reason_tuning").hide();
					$(".reason_upgrade").show();
					$(".reason_tire").hide();
				}
				if (sel==6) {
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
<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>