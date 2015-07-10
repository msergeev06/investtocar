<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("MAINTENANCE")?></h1>
<?
	$defaultCar = CInvestToCarMain::GetDefaultCar();
?>
	<?=GetMessage("TS_INFO")?>
	<p><?=GetMessage("STATISTICS_FOR")?>: <? echo CInvestToCarShowSelect::Auto("my_car",true); ?><?=GetMessage("TOTAL_MAINTENANCE_COSTS")?>: <?=CInvestToCarMain::GetTotalMaintenanceCosts()?> <?=GetMessage("RUB")?><br><br></p>
	<p><a href="<?=$path?>ts/add_ts.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_NOTE")?></a><br><br></p>
	<table class="ts_list">
		<thead>
			<tr>
				<td><?=GetMessage("TITLE")?></td>
				<td><?=GetMessage("DATE")?></td>
				<td><?=GetMessage("AMOUNT")?>,<br><?=GetMessage("RUB")?></td>
				<td><?=GetMessage("MILEAGE")?></td>
				<td><?=GetMessage("ARTIST_WORKS")?></td>
				<td><?=GetMessage("POINT")?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</thead>
		<tbody class="list_ts">
		<? $arTsList = CInvestToCarMain::GetListCarTs ($defaultCar); ?>
		<?foreach($arTsList as $arTs):?>
			<tr>
				<td>ТО-<?=$arTs["ts_num"]?></td>
				<td><?=date("d.m.Y",$arTs["date"])?></td>
				<td><?=$arTs["cost"]?></td>
				<td><?=$arTs["odo"]?></td>
				<td><?=$arTs["repair"]?></td>
				<td><?=($arTs["point"]!==false) ? $arTs["point"]["name"] : GetMessage("NO_DATA")?></td>
				<td><a href="<?=$path?>ts/ts_edit.php?id=<?=$arTs["id"]?>"><img src="/msergeev/images/edit.png"></a></td>
				<td><a href="<?=$path?>ts/ts_delete.php?id=<?=$arTs["id"]?>"><img src="/msergeev/images/del.png"></a></td>
			</tr>
		<?endforeach;?>
		</tbody>
	</table>
	<script type="text/javascript">
		$(document).on("ready", function(){
			var select;
			$("#my_car").on("change",function(){
				select = $("#my_car").val();
				$.post(
					"/msergeev/investtocar/tools/getlistcarts.php",
					{
						car: select
					},
					function (data) {
						$(".list_ts").html(data.html);
					},
					"json"
				);
			});

		});
	</script>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>