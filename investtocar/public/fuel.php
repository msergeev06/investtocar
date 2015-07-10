<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("FUEL")?></h1>
<?
	$defaultCar = CInvestToCarMain::GetDefaultCar();
?>
	<?=GetMessage("FUEL_INFO")?>
	<p><?=GetMessage("STATISTICS_FOR")?>: <? echo CInvestToCarShowSelect::Auto("my_car",true); ?><br>
		<?=GetMessage("TOTAL_FUEL_COSTS")?>: <? echo CInvestToCarMain::GetTotalFuelCosts($defaultCar); ?> <?=GetMessage("RUB")?><br>
		<?=GetMessage("AVERAGE_FUEL_CONSUMPTION")?>: <?=number_format(CInvestToCarMain::GetAverageFuelConsumption($defaultCar),2)?> <?=GetMessage("LITERS_FOR_100KM")?><br>
		<?=GetMessage("TOTAL_SPENT_FUEL")?>: <?=number_format(CInvestToCarMain::GetTotalSpentFuel($defaultCar),2)?> <?=GetMessage("LITER")?><br><br></p>
	<p><a href="<?=$path?>fuel/add_fuel.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_NOTE")?></a><br><br></p>
	<table class="ts_list">
		<thead>
		<tr>
			<td><?=GetMessage("DATE")?></td>
			<td><?=GetMessage("FUEL_MARK")?></td>
			<td><?=GetMessage("AMOUNT")?>,<br><?=GetMessage("RUB")?></td>
			<td><?=GetMessage("DISPLACEMENT")?>,<br><?=GetMessage("LITER")?></td>
			<td><?=GetMessage("COST_FOR_LITER")?>,<br><?=GetMessage("RUB")?></td>
			<td><?=GetMessage("EXPENSE")?>,<br><?=GetMessage("LITERS_FOR_100KM")?></td>
			<td><?=GetMessage("MILEAGE")?>,<br><?=GetMessage("KM")?></td>
			<td><?=GetMessage("WAYPOINT")?></td>
			<td><?=GetMessage("FULL_TANK")?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		</thead>
		<tbody class="list_ts">
		<? $arFuelList = CInvestToCarMain::GetFuelList ($defaultCar); ?>
		<?foreach($arFuelList as $arFuel):?>
			<tr>
				<td><?=date("d.m.Y",$arFuel["date"])?></td>
				<td><?=CInvestToCarMain::GetFuelMarkByID($arFuel["fuel_mark"],true)?></td>
				<td><?=number_format($arFuel["summ"],2)?></td>
				<td><?=$arFuel["liter"]?></td>
				<td><?=number_format($arFuel["liter_cost"],2)?></td>
				<td><?=(floatval($arFuel["expense"])>0)?floatval($arFuel["expense"]):"-"?></td>
				<td><?=$arFuel["odo"]?></td>
				<? $arPoint = CInvestToCarMain::GetPointInfoByID($arFuel["point"]); ?>
				<td><?=$arPoint["name"]?></td>
				<td><?=(intval($arFuel["full"])>0)?"+":"-"?></td>
				<td>(i)</td>
				<td><a href="<?=$path?>fuel/fuel_edit.php?id=<?=$arFuel["id"]?>"><img src="/msergeev/images/edit.png"></a></td>
				<td><a href="<?=$path?>fuel/fuel_delete.php?id=<?=$arFuel["id"]?>"><img src="/msergeev/images/del.png"></a></td>
			</tr>
		<?endforeach;?>
		</tbody>
	</table>



<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>