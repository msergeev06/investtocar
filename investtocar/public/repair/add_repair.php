<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("ADD_REPAIR")?></h1>
<? $car = $_GET["car"];
	$pService = CInvestToCarMain::GetInfoByCode ("pointtype","service");
	$pStore = CInvestToCarMain::GetInfoByCode ("pointtype","shop");
	$pWaypoint = CInvestToCarMain::GetInfoByCode ("pointtype","waypoint");

	if (isset($_POST["action"])) {
		if ($res = CInvestToCarRepair::AddRepair($_POST)) {
			?><span style="color: green;"><?=GetMessage("ADD_REPAIR_SUCCESS")?></span><?
		}
		else {
			?><span style="color: red;"><?=GetMessage("ADD_REPAIR_FAILED")?></span><?
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
				<td class="title"><?=GetMessage("AMOUNT")?></td>
				<td><input type="text" name="cost" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ARTIST_WORKS")?></td>
				<td><? echo CInvestToCarShowSelect::Repair("repair"); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NAME")?></td>
				<td><input type="text" name="name" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
				<td><input type="text" name="odo" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("REASON_REPLACEMENT")?></td>
				<td><? echo CInvestToCarShowSelect::ReasonReplacement("reason"); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WHO_PAID")?></td>
				<td><? echo CInvestToCarShowSelect::WhoPaid("who_paid"); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("REASON_DETAILS")?></td>
				<td class="reason_add">
					<? echo CInvestToCarShowSelect::ReasonTs("reason_ts",$car); ?>
					<span class="reason_breakdown" style="display: none;">-</span>
					<? echo CInvestToCarShowSelect::ReasonDtp("reason_dtp",$car,0,' style="display: none;"'); ?>
					<span class="reason_tuning" style="display: none;">-</span>
					<span class="reason_upgrade" style="display: none;">-</span>
					<span class="reason_tire" style="display: none;">-</span>
				</td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WAYPOINT")?></td>
				<td><? echo CInvestToCarShowSelect::Points("waypoint",0,array($pWaypoint,$pService,$pStore)); ?></td>
			</tr>
			<? echo CInvestToCarPoints::ShowFormNewPointAdd (true,$pService,array($pWaypoint,$pService,$pStore)); ?>
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
<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>