<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("EDIT_OTHER_COST")?></h1>
<?
	$otherID = $_GET["id"];
	$pService = CInvestToCarMain::GetInfoByCode ("pointtype","service");
	$pStore = CInvestToCarMain::GetInfoByCode ("pointtype","shop");
	$pCarwash = CInvestToCarMain::GetInfoByCode ("pointtype","wash");
	$pParking = CInvestToCarMain::GetInfoByCode ("pointtype","parking");


	if(isset($_POST["action"])) {
		if ($res = CInvestToCarOther::UpdateInfo($_POST)) {
			?><span style="color: green;"><?=GetMessage("EDIT_OTHER_SUCCESS")?></span><?
		}
		else {
			?><span style="color: red;"><?=GetMessage("EDIT_OTHER_FAILED")?></span><?
		}
		//echo "<pre>"; print_r($_POST); echo "</pre>";
	}
	else {
		$arOther = CInvestToCarOther::GetInfo(0,$otherID);
		?>
	<form action="" method="post">
		<input type="hidden" name="id" value="<?=$otherID?>">
		<table class="add_ts">
			<tr>
				<td class="title"><?=GetMessage("CAR")?></td>
				<td><? echo CInvestToCarShowSelect::Auto("auto",false,$arOther["car"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("DATE_DDMMYYYY")?></td>
				<td><input type="text" name="date" value="<?=date("d.m.Y",$arOther["date"])?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("AMOUNT")?></td>
				<td><input type="text" name="cost" value="<?=$arOther["cost"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("TYPE")?></td>
				<td><? echo CInvestToCarShowSelect::TypeOtherCosts("type",$arOther["type"]); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NAME")?></td>
				<td><input class="name" type="text" name="name" value="<?=$arOther["name"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NUMBER")?></td>
				<td><input type="text" name="number" value="<?=$arOther["number"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
				<td><input type="text" name="odo" value="<?=$arOther["odo"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("CATALOG_NUMBER")?></td>
				<td><input type="text" name="catalog_number" value="<?=$arOther["catalog_number"]?>"></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WAYPOINT")?></td>
				<td><? echo CInvestToCarShowSelect::Points("waypoint",$arOther["waypoint"],array($pService,$pStore,$pCarwash,$pParking)); ?></td>
			</tr>
			<? echo CInvestToCarPoints::ShowFormNewPointAdd (true,$pService,array($pService,$pStore,$pCarwash,$pParking)); ?>
			<tr>
				<td class="title"><?=GetMessage("COMMENT")?></td>
				<td><input type="text" name="comment" value=""></td>
			</tr>
			<tr>
				<td class="center" colspan="2"><input type="hidden" name="action" value="1"><input type="submit" value="<?=GetMessage("SUBMIT_SAVE")?>"></td>
			</tr>
		</table>
	</form>
	<script type="text/javascript">
		$(document).on("ready",function(){
			$(".type").on("change",function(){
				etext = $(".type :selected").text();
				eval = $(".type :selected").val();

				if (eval==0) {
					$(".name").val("");
				}
				else if (eval==<?=intval(CInvestToCarMain::GetInfoByCode ("flowtype","other"))?>) {
					$(".name").val("");
				}
				else {
					$(".name").val(etext);
				}

			});
		});
	</script>
		<?
	}
	?>
<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>