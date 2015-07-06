<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("FUEL")?></h1>
<?
	$defaultCar = CInvestToCarMain::GetDefaultCar();
?>
	<p><?=GetMessage("STATISTICS_FOR")?>: <? echo CInvestToCarMain::ShowSelectAuto("my_car",true); ?><br><br></p>
	<p><a href="<?=$path?>fuel/add_fuel.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_NOTE")?></a><br><br></p>
	<table class="ts_list">
		<thead>
		<tr>
			<td>Дата</td>
			<td>Марка топлива</td>
			<td><?=GetMessage("AMOUNT")?></td>
			<td>Литраж</td>
			<td>Цена за литр</td>
			<td>Расход</td>
			<td>Пробег</td>
			<td>Точка</td>
			<td>Полный<br>бак</td>
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
				<td><?=CInvestToCarMain::GetFuelMarkByID($arFuel["fuel_mark"])?></td>
				<td><?=$arFuel["summ"]?></td>
				<td><?=$arFuel["liter"]?></td>
				<td><?=$arFuel["liter_cost"]?></td>
				<td>Расход</td>
				<td><?=$arFuel["odo"]?></td>
				<td>Точка</td>
				<td><?=(intval($arFuel["full"])>0)?"+":"-"?></td>
				<td>Инфо</td>
				<td><a href="<?=$path?>fuel/fuel_edit.php?id=<?=$arFuel["id"]?>"><img src="/msergeev/images/edit.png"></a></td>
				<td><a href="<?=$path?>fuel/fuel_delete.php?id=<?=$arFuel["id"]?>"><img src="/msergeev/images/del.png"></a></td>
			</tr>
		<?endforeach;?>
		</tbody>
	</table>



<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>