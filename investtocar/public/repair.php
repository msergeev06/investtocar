<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("REPAIR")?></h1>
	<?=GetMessage("REPAIR_INFO")?>
<?
	$defaultCar = CInvestToCarCars::GetDefaultCar();
?>
	<p><?=GetMessage("STATISTICS_FOR")?>: <? echo CInvestToCarShowSelect::Auto("my_car",true); ?><?=GetMessage("TOTAL_REPAIR_COSTS")?>: <?=number_format(CInvestToCarRepair::GetTotalRepairCosts(),2)?> <?=GetMessage("RUB")?><br><br></p>
	<p><a href="<?=$path?>repair/add_repair.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_NOTE")?></a><br><br></p>
	<table class="ts_list">
		<thead>
		<tr>
			<td><?=GetMessage("DATE")?></td>
			<td><?=GetMessage("AMOUNT")?>,<br><?=GetMessage("RUB")?></td>
			<td><?=GetMessage("ARTIST_WORKS")?></td>
			<td><?=GetMessage("NAME")?></td>
			<td><?=GetMessage("ODOMETER_VALUE")?></td>
			<td><?=GetMessage("REASON_REPLACEMENT")?></td>
			<td><?=GetMessage("WHO_PAID")?></td>
			<td><?=GetMessage("REASON_DETAILS")?></td>
			<td><?=GetMessage("WAYPOINT")?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		</thead>
		<tbody class="list_ts">
		<? $arRepairList = CInvestToCarRepair::GetListRepair ($defaultCar); ?>
		<? //echo "<pre>"; print_r($arRepairPartsList); echo "</pre>"; ?>
		<?foreach($arRepairList as $arRP):?>
			<tr>
				<td><?=$arRP["date"]?></td>
				<td><?=$arRP["cost"]?></td>
				<td><?=$arRP["repair"]?></td>
				<td><?=$arRP["name"]?></td>
				<td><?=$arRP["odo"]?></td>
				<td><?=$arRP["reason"]?></td>
				<td><?=$arRP["who_paid"]?></td>
				<td><?=$arRP["reason_detail"]?></td>
				<td><?=$arRP["waypoint"]?></td>
				<td><a href="#" title="<?=$arRP["comment"]?>">(i)</a></td>
				<td><a href="<?=$path?>repair/edit_repair.php?id=<?=$arRP["id"]?>"><img src="/msergeev/images/edit.png"></a></td>
				<td><a href="<?=$path?>repair/delete_repair.php?id=<?=$arRP["id"]?>"><img src="/msergeev/images/del.png"></a></td>
			</tr>
		<?endforeach;?>
		</tbody>
	</table>



<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>


<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>