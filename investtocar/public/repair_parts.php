<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("REPAIR_PARTS")?></h1>
<?
	$defaultCar = CInvestToCarMain::GetDefaultCar();
?>
	<p><?=GetMessage("INFORMATION_FOR")?>: <? echo CInvestToCarShowSelect::Auto("my_car",true); ?><?=GetMessage("TOTAL_REPAIR_PARTS_COSTS")?>: <?=CInvestToCarMain::GetTotalMaintenanceCosts()?> <?=GetMessage("RUB")?><br><br></p>
	<p><a href="<?=$path?>repair_parts/add_repair_parts.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_NOTE")?></a><br><br></p>
	<table class="ts_list">
		<thead>
		<tr>
			<td><?=GetMessage("DATE")?></td>
			<td><?=GetMessage("TITLE")?></td>
			<td><?=GetMessage("STORAGE")?></td>
			<td><?=GetMessage("CATALOG_NUMBER")?></td>
			<td><?=GetMessage("NUMBER")?></td>
			<td><?=GetMessage("AMOUNT")?>,<br><?=GetMessage("RUB")?></td>
			<td><?=GetMessage("REASON_REPLACEMENT")?></td>
			<td><?=GetMessage("REASON_DETAILS")?></td>
			<td><?=GetMessage("WHO_PAID")?></td>
			<td><?=GetMessage("ODOMETER_VALUE")?></td>
			<td><?=GetMessage("WAYPOINT")?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		</thead>
		<tbody class="list_ts">
		<? $arRepairPartsList = CInvestToCarMain::GetListRepairParts ($defaultCar); ?>
		<?foreach($arRepairPartsList as $arRP):?>
			<tr>
				<td><?=$arRP["date"]?></td>
				<td><?=$arRP["name"]?></td>
				<td><?=$arRP["storage"]?></td>
				<td><?=$arRP["catalog_number"]?></td>
				<td><?=$arRP["number"]?></td>
				<td><?=$arRP["cost"]?></td>
				<td><?=$arRP["reason"]?></td>
				<td><?=$arRP["reason_detail"]?></td>
				<td><?=$arRP["who_paid"]?></td>
				<td><?=$arRP["odo"]?></td>
				<td><?=$arRP["waypoint"]?></td>
				<td><a href="#" title="<?=$arRP["comment"]?>">(i)</a></td>
				<td><a href="<?=$path?>repair_parts/edit_repair_parts.php?id=<?=$arRP["id"]?>"><img src="/msergeev/images/edit.png"></a></td>
				<td><a href="<?=$path?>repair_parts/delete_repair_parts.php?id=<?=$arRP["id"]?>"><img src="/msergeev/images/del.png"></a></td>
			</tr>
		<?endforeach;?>
		</tbody>
	</table>



<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>