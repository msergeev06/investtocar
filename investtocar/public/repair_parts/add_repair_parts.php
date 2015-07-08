<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("ADD_REPAIR_PARTS")?></h1>
<? $car = $_GET["car"]; ?>
	<form action="" method="post">
		<input type="hidden" name="car" value="<?=$car?>">
		<table class="add_ts">
			<tr>
				<td class="title"><?=GetMessage("CAR")?></td>
				<td><? echo CInvestToCarMain::ShowSelectAuto("auto",false,$car); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("DATE_DDMMYYYY")?></td>
				<td><input type="text" name="date" value="<?=date("d.m.Y")?>"></td>
			</tr>
			<tr>
				<td class="title">Название</td>
				<td><input type="text" name="name" value=""></td>
			</tr>
			<tr>
				<td class="title">Место хранения</td>
				<td><select name="stored">
						<option value="1" selected>Установлено</option>
						<option value="2">На складе</option>
					</select></td>
			</tr>
			<tr>
				<td class="title">Каталожный номер</td>
				<td><input type="text" name="catalog_number" value=""></td>
			</tr>
			<tr>
				<td class="title">Количество</td>
				<td><input type="text" name="amount" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("AMOUNT")?></td>
				<td><input type="text" name="cost" value=""></td>
			</tr>
			<tr>
				<td class="title">Причина замены</td>
				<td><select name="reason" class="reason">
						<option value="1" selected>Плановая или ТО</option>
						<option value="2">Поломка</option>
						<option value="3">ДТП</option>
						<option value="4">Тюнинг</option>
						<option value="5">Апгрейд</option>
						<option value="6">Шиномонтаж</option>
					</select></td>
			</tr>
			<tr>
				<td class="title">Причина (подробности)</td>
				<td class="reason_add">
					<? echo CInvestToCarMain::ShowSelectReasonTs("reason_ts",$car); ?>
					<? echo CInvestToCarMain::ShowSelectReasonRepair("reason_breakdown",$car,0,' style="display: none;"'); ?>
					<? echo CInvestToCarMain::ShowSelectReasonDtp("reason_dtp",$car,0,' style="display: none;"'); ?>
					<? echo CInvestToCarMain::ShowSelectReasonRepair("reason_tuning",$car,0,' style="display: none;"'); ?>
					<? echo CInvestToCarMain::ShowSelectReasonRepair("reason_upgrade",$car,0,' style="display: none;"'); ?>
					<span class="reason_tire" style="display: none;">-</span>
				</td>
			</tr>
			<tr>
				<td class="title">Кто оплачивал</td>
				<td><select name="who_paid">
						<option value="1" selected>Оплачивал сам</option>
						<option value="2">ОСАГО</option>
						<option value="3">КАСКО</option>
					</select></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
				<td><input type="text" name="odo" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WAYPOINT")?></td>
				<td><? echo CInvestToCarMain::ShowSelectPoints("ts_point"); ?></td>
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