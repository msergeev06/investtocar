<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("EDIT_REPAIR")?></h1>
<? $repairID = $_GET["id"];
	$pService = CInvestToCarMain::GetInfoByCode ("pointtype","service");
	$pStore = CInvestToCarMain::GetInfoByCode ("pointtype","shop");
	$pWaypoint = CInvestToCarMain::GetInfoByCode ("pointtype","waypoint");

	if (isset($_POST["action"])) {
		if ($res = CInvestToCarRepair::UpdateRepair($_POST)) {
			?><span style="color: green;"><?=GetMessage("EDIT_REPAIR_SUCCESS")?></span><?
		}
		else {
			?><span style="color: red;"><?=GetMessage("EDIT_REPAIR_FAILED")?></span><?
		}
	}
	else {
		$arRepair = CInvestToCarRepair::GetRepairInfo($repairID);
		?>
	<form action="" method="post">
		<input type="hidden" name="id" value="<?=$repairID?>">
		<table class="add_ts">
			<tr>
				<td class="title"><?=GetMessage("CAR")?></td>
				<td><? echo CInvestToCarShowSelect::Auto("auto",false,$arRepair["auto"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("DATE_DDMMYYYY")?></td>
				<td><input type="text" name="date" value="<?=date("d.m.Y",$arRepair["date"])?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("AMOUNT")?></td>
				<td><input type="text" name="cost" value="<?=$arRepair["cost"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ARTIST_WORKS")?></td>
				<td><? echo CInvestToCarShowSelect::Repair("repair",$arRepair["repair"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NAME")?></td>
				<td><input type="text" name="name" value="<?=$arRepair["name"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
				<td><input type="text" name="odo" value="<?=$arRepair["odo"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("REASON_REPLACEMENT")?></td>
				<td><? echo CInvestToCarShowSelect::ReasonReplacement("reason",$arRepair["reason"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WHO_PAID")?></td>
				<td><? echo CInvestToCarShowSelect::WhoPaid("who_paid",$arRepair["who_paid"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("REASON_DETAILS")?></td>
				<td class="reason_add">
					<?
						if ($arRepair["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","ts"))) {
							echo CInvestToCarShowSelect::ReasonTs("reason_ts",$arRepair["auto"],$arRepair["reason_detail"]);
						}
						else {
							echo CInvestToCarShowSelect::ReasonTs("reason_ts",$arRepair["auto"],0,' style="display: none;"');
						}
					?>
					<?if($arRepair["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","breakdown"))):?>
						<span class="reason_breakdown">-</span>
					<?else:?>
						<span class="reason_breakdown" style="display: none;">-</span>
					<?endif;?>
					<?
						if ($arRepair["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","dtp"))) {
							echo CInvestToCarShowSelect::ReasonTs("reason_dtp",$arRepair["auto"],$arRepair["reason_detail"]);
						}
						else {
							echo CInvestToCarShowSelect::ReasonDtp("reason_dtp",$arRepair["auto"],0,' style="display: none;"');
						}
					?>
					<?if($arRepair["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","tuning"))):?>
						<span class="reason_tuning">-</span>
					<?else:?>
						<span class="reason_tuning" style="display: none;">-</span>
					<?endif;?>
					<?if($arRepair["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","upgrade"))):?>
						<span class="reason_upgrade">-</span>
					<?else:?>
						<span class="reason_upgrade" style="display: none;">-</span>
					<?endif;?>
					<?if($arRepair["reason"]==intval(CInvestToCarMain::GetInfoByCode ("reason","tire"))):?>
						<span class="reason_tire">-</span>
					<?else:?>
						<span class="reason_tire" style="display: none;">-</span>
					<?endif;?>
				</td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WAYPOINT")?></td>
				<td><? echo CInvestToCarShowSelect::Points("waypoint",$arRepair["waypoint"],array($pWaypoint,$pService,$pStore)); ?></td>
			</tr>
			<? echo CInvestToCarPoints::ShowFormNewPointAdd (true,$pService,array($pWaypoint,$pService,$pStore)); ?>
			<tr>
				<td class="title"><?=GetMessage("COMMENT")?></td>
				<td><input type="text" name="comment" value="<?=$arRepair["comment"]?>"></td>
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
				if (sel==<?=intval(CInvestToCarMain::GetInfoByCode ("reason","ts"))?>) {
					$(".reason_ts").show();
					$(".reason_breakdown").hide();
					$(".reason_dtp").hide();
					$(".reason_tuning").hide();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==<?=intval(CInvestToCarMain::GetInfoByCode ("reason","breakdown"))?>) {
					$(".reason_ts").hide();
					$(".reason_breakdown").show();
					$(".reason_dtp").hide();
					$(".reason_tuning").hide();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==<?=intval(CInvestToCarMain::GetInfoByCode ("reason","dtp"))?>) {
					$(".reason_ts").hide();
					$(".reason_breakdown").hide();
					$(".reason_dtp").show();
					$(".reason_tuning").hide();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==<?=intval(CInvestToCarMain::GetInfoByCode ("reason","tuning"))?>) {
					$(".reason_ts").hide();
					$(".reason_breakdown").hide();
					$(".reason_dtp").hide();
					$(".reason_tuning").show();
					$(".reason_upgrade").hide();
					$(".reason_tire").hide();
				}
				if (sel==<?=intval(CInvestToCarMain::GetInfoByCode ("reason","upgrade"))?>) {
					$(".reason_ts").hide();
					$(".reason_breakdown").hide();
					$(".reason_dtp").hide();
					$(".reason_tuning").hide();
					$(".reason_upgrade").show();
					$(".reason_tire").hide();
				}
				if (sel==<?=intval(CInvestToCarMain::GetInfoByCode ("reason","tire"))?>) {
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