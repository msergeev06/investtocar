<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("OTHER")?></h1>
	<?=GetMessage("OTHER_INFO")?>
<?
	$defaultCar = CInvestToCarCars::GetDefaultCar();
?>
	<p><?=GetMessage("STATISTICS_FOR")?>: <? echo CInvestToCarShowSelect::Auto("my_car",true); ?><?=GetMessage("TOTAL_COSTS")?>: <?=CInvestToCarOther::GetTotalOtherCosts()?> <?=GetMessage("RUB")?><br><br></p>
	<p><a href="<?=$path?>other/add_other.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_NOTE")?></a><br><br></p>
	<table class="ts_list">
		<thead>
		<tr>
			<td><?=GetMessage("DATE")?></td>
			<td><?=GetMessage("AMOUNT")?>,<br><?=GetMessage("RUB")?></td>
			<td><?=GetMessage("TYPE")?></td>
			<td><?=GetMessage("NAME")?></td>
			<td><?=GetMessage("NUMBER")?></td>
			<td><?=GetMessage("MILEAGE")?></td>
			<td><?=GetMessage("CATALOG_NUMBER")?></td>
			<td><?=GetMessage("POINT")?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		</thead>
		<tbody class="list_ts">
		<? $arOtherList = CInvestToCarOther::GetInfo ($defaultCar); ?>
		<?foreach($arOtherList as $arOther):?>
			<tr>
				<td><?=date("d.m.Y",$arOther["date"])?></td>
				<td><?=$arOther["cost"]?></td>
				<? $arTypeOther = CInvestToCarOther::GetTypeByID($arOther["type"]); ?>
				<td><?=$arTypeOther["name"]?></td>
				<td><?=$arOther["name"]?></td>
				<td><?=$arOther["number"]?></td>
				<td><?=$arOther["odo"]?></td>
				<td><?=$arOther["catalog_number"]?></td>
				<? $arPoint = CInvestToCarPoints::GetPointInfoByID($arOther["waypoint"]); ?>
				<td><?=$arPoint["name"]?></td>
				<td><a href="#" title="<?=$arOther["comment"]?>">(i)</a></td>
				<td><a href="<?=$path?>other/edit_other.php?id=<?=$arOther["id"]?>"><img src="/msergeev/images/edit.png"></a></td>
				<td><a href="<?=$path?>other/delete_other.php?id=<?=$arOther["id"]?>"><img src="/msergeev/images/del.png"></a></td>
			</tr>
		<?endforeach;?>
		</tbody>
	</table>


<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>