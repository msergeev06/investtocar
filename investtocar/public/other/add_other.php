<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("ADD_OTHER_COST")?></h1>
<?
	$car = $_GET["car"];
	$pService = CInvestToCarMain::GetInfoByCode ("pointtype","service");
	$pStore = CInvestToCarMain::GetInfoByCode ("pointtype","shop");
	$pCarwash = CInvestToCarMain::GetInfoByCode ("pointtype","wash");


if(isset($_POST["action"])) {
	if ($res = CInvestToCarMain::AddOtherCosts($_POST)) {
		?><span style="color: green;"><?=GetMessage("ADD_OTHER_SUCCESS")?></span><?
	}
	else {
		?><span style="color: red;"><?=GetMessage("ADD_OTHER_FAILED")?></span><?
	}
	//echo "<pre>"; print_r($_POST); echo "</pre>";
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
				<td class="title"><?=GetMessage("TYPE")?></td>
				<td><? echo CInvestToCarShowSelect::TypeOtherCosts("type"); ?></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NAME")?></td>
				<td><input class="name" type="text" name="name" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("NUMBER")?></td>
				<td><input type="text" name="number" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("ODOMETER_VALUE")?></td>
				<td><input type="text" name="odo" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("CATALOG_NUMBER")?></td>
				<td><input type="text" name="catalog_number" value=""></td>
			</tr>
			<tr>
				<td class="title"><?=GetMessage("WAYPOINT")?></td>
				<td><? echo CInvestToCarShowSelect::Points("waypoint",0,array($pService,$pStore,$pCarwash)); ?></td>
			</tr>
			<? echo CInvestToCarMain::ShowFormNewPointAdd (); ?>
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
			$(".name").prop("disabled", true);
			$(".type").on("change",function(){
				etext = $(".type :selected").text();
				eval = $(".type :selected").val();

				if (eval==0) {
					$(".name").prop("disabled", true);
					$(".name").val("");
				}
				else if (eval==<?=intval(CInvestToCarMain::GetInfoByCode ("flowtype","other"))?>) {
					$(".name").prop("disabled", false);
					$(".name").val("");
				}
				else {
					$(".name").prop("disabled", false);
					$(".name").val(etext);
				}

			});
		});
	</script>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>