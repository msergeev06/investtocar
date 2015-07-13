<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("ADDING_NEW_CAR")?></h1>
<?
if (isset($_POST["action"])) {
	if ($res = CInvestToCarCars::AddNewCarGarage($_POST)) {
		?><span style="color: green;"><?=GetMessage("ADDING_CAR_SUCCESS")?></span><?
	}
	else {
		?><span style="color: red;"><?=GetMessage("ADDING_CAR_FAILED")?></span><?
	}
}
else {
?>
<form action="" method="POST">
<table class="add_new_car">
	<tr>
		<td><?=GetMessage("NAME")?>:</td>
		<td><input type="text" name="name" value=""></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?=GetMessage("CAR_MARK")?>:</td>
		<td><? echo CInvestToCarShowSelect::CarBrands(); ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?=GetMessage("CAR_MODEL")?>:</td>
		<td id="td_car_model"><input type="text" name="car_model_add" value=""></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?=GetMessage("CAR_YEAR")?>: </td>
		<td><? echo CInvestToCarShowSelect::CarCreateYear(); ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?=GetMessage("VIN")?>:</td>
		<td><input type="text" name="vin" value=""></td>
		<td><?=GetMessage("NUMERIC_AND_LAT_SIGN")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("CAR_NUMBER")?>:</td>
		<td><input type="text" name="gosnum" value=""></td>
		<td><?=GetMessage("NUMERIC_AND_LAT_SIGN")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("ENGINE_CAPACITY")?>:</td>
		<td><input type="text" name="engine" value=""></td>
		<td><?=GetMessage("LITERS")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("GEAR_BOX")?>:</td>
		<td><? echo CInvestToCarShowSelect::CarGearbox(); ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?=GetMessage("BODY_TYPE")?>:</td>
		<td><? echo CInvestToCarShowSelect::CarBody(); ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?=GetMessage("MAINTENANCE_INTERVAL")?>:</td>
		<td><input type="text" name="interval_ts" value=""></td>
		<td><?=GetMessage("KM")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("CAR_COST")?>:</td>
		<td><input type="text" name="cost" value=""></td>
		<td><?=GetMessage("RUB")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("START_ODO")?>:</td>
		<td><input type="text" name="odo" value=""></td>
		<td><?=GetMessage("KM")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("CREDIT")?>?</td>
		<td><input type="checkbox" name="credit" value="1"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?=GetMessage("CREDIT")?> (<?=GetMessage("AMOUNT")?>):</td>
		<td><input type="text" name="creditcost" value=""></td>
		<td><?=GetMessage("RUB")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("DATE_END_OSAGO")?>:</td>
		<td><input type="text" name="end_osago" value=""></td>
		<td><a href="#"><?=GetMessage("SETUP_ALARM_SMART_HOME")?></a></td>
	</tr>
	<tr>
		<td><?=GetMessage("DATE_END_GTO")?>:</td>
		<td><input type="text" name="end_gto" value=""></td>
		<td><a href="#"><?=GetMessage("SETUP_ALARM_SMART_HOME")?></a></td>
	</tr>
	<tr>
		<td><?=GetMessage("CAR_USE_AS_DEFAULT")?>:</td>
		<td><input type="checkbox" name="default" value="1"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2"><input type="hidden" name="action" value="1"><input type="submit" value="<?=GetMessage("SUBMIT_ADD")?>"></td>
	</tr>

</table>
</form>
<script type="text/javascript">
	$(document).on("ready",function(){
		$("#car_brand").on("change",function(){
			var sel = $(this).val();

			$.post(
				"/msergeev/investtocar/include/tools/getselectmodel.php",
				{
					brand: sel
				},
				function (data) {
					//console.log(data);
					$("#td_car_model").html("");
					$("#td_car_model").append(data.select);
				},
				"json"
			);
		});
	});
</script>
<? }
 require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>