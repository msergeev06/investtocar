<? require_once ($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/include/header.php"); ?>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

	</head>
	<body>
	<select name="period" id="period_select">
		<option value="1" selected>Текущий месяц</option>
		<option value="2">Предыдущий месяц</option>
		<option value="3">За год</option>
	</select><br><br>

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
		});
	</script>
	</body>
	</html>
<? require_once ($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/include/footer.php"); ?>