<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("MILEAGE")?></h1>
<?
	$defaultCar = CInvestToCarCars::GetDefaultCar();
?>
	<p><?=GetMessage("STATISTICS_FOR")?>: <? echo CInvestToCarShowSelect::Auto("my_car",true); ?><br><br></p>
	<p><a href="<?=$path?>odo/add_route.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_MILEAGE_INFORMATION")?></a></p>

	<select name="period" id="period_select">
		<option value="1" selected><?=GetMessage("NOW_MONTH")?></option>
		<option value="2"><?=GetMessage("LAST_MONTH")?></option>
		<option value="3"><?=GetMessage("FROM_YEAR")?></option>
	</select>&nbsp;&nbsp;<a class="update" href="#"><?=GetMessage("UPDATE_DAY_ODO")?></a><br><br>

	<div class="charts">
	</div>
	<script type="text/javascript">
		$(document).on("ready",function(){
			var sel;
			var chartWidth = 1000;
			var chartHeight = 500;
			var xTitle = "<?=urlencode("Дата")?>";
			var yTitle = "<?=urlencode("Километраж")?>";

			sel = $("#period_select").val();
			$(".charts").html('<iframe src="/msergeev/investtocar/include/tools/getchartsodo.php?chartWidth='+chartWidth+'&chartHeight='+chartHeight+'&type='+sel+'&xTitle='+xTitle+'&yTitle='+yTitle+'" scrolling="no" frameborder="no" width="'+chartWidth+'" height="'+chartHeight+'" align="left"></iframe>');

			$("#period_select").on("change",function(){
				sel = $(this).val();

				$(".charts").html('<iframe src="/msergeev/investtocar/include/tools/getchartsodo.php?chartWidth='+chartWidth+'&chartHeight='+chartHeight+'&type='+sel+'&xTitle='+xTitle+'&yTitle='+yTitle+'" scrolling="no" frameborder="no" width="'+chartWidth+'" height="'+chartHeight+'" align="left"></iframe>');

			});

			$(".update").on("click", function(){
				$.post("/msergeev/investtocar/include/tools/update_day_odo.php",function(){});
			});
		});
	</script>


<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>