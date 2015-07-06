<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("ADDING_ROUTE")?></h1>
<? $car = intval($_GET["car"]); ?>
<?
	$defaultCar = CInvestToCarMain::GetDefaultCar();
?>

<?
	if (isset($_POST["action"])&&$_POST["action"]==1) {
		if (CInvestToCarMain::AddNewRoute($_POST)) {
			echo "<span class=\"ok\">Данные добавлены</span>";
		}
		else {
			echo "<span class=\"err\">Ошибка добавления данных</span>";
		}
		list($day, $month, $year) = explode (".", $post["date"]);
		$arResult["date"] = mktime (0, 0, 0, $month, $day, $year, 0) + 3600;
		CInvestToCarMain::UpdateDayOdometer($arResult["date"]);
	}



?>

<form name="add_route" method="POST">
	<table style="border: 0;">
		<tr>
			<td class="name">Автомобиль</td>
			<td class="value"><? echo CInvestToCarMain::ShowSelectAuto("auto")?></td>
		</tr>
		<tr>
			<td class="name">Дата</td>
			<td class="value"><input type="text" name="date" value="<?=date("d.m.Y")?>"></td>
		</tr>
		<tr>
			<td class="name">Одометр</td>
			<td class="value"><input type="text" name="odo" value=""></td>
		</tr>
		<tr>
			<td class="name">Стартовая точка</td>
			<td class="value"><? echo CInvestToCarMain::ShowSelectPoints("start_point")?></td>
		</tr>
		<tr>
			<td class="name">&nbsp;</td>
			<td class="value">или</td>
		</tr>
		<tr>
			<td class="name">Стартовая точка имя</td>
			<td class="value"><input type="text" name="start_name" value=""></td>
		</tr>
		<tr>
			<td class="name">Стартовая точка адрес</td>
			<td class="value"><input type="text" name="start_address" value=""></td>
		</tr>
		<tr>
			<td class="name">Стартовая точка долгота</td>
			<td class="value"><input type="text" name="start_lon" value=""></td>
		</tr>
		<tr>
			<td class="name">Стартовая точка широта</td>
			<td class="value"><input type="text" name="start_lat" value=""></td>
		</tr>
		<tr>
			<td class="name">По городу?</td>
			<td class="value"><input type="checkbox" name="end_start" value="1"></td>
		</tr>
		<tr>
			<td class="name">Конечная точка</td>
			<td class="value"><? echo CInvestToCarMain::ShowSelectPoints("end_point")?></td>
		</tr>
		<tr>
			<td class="name">&nbsp;</td>
			<td class="value">или</td>
		</tr>
		<tr>
			<td class="name">Конечная точка имя</td>
			<td class="value"><input type="text" name="end_name" value=""></td>
		</tr>
		<tr>
			<td class="name">Конечная точка адрес</td>
			<td class="value"><input type="text" name="end_address" value=""></td>
		</tr>
		<tr>
			<td class="name">Конечная точка долгота</td>
			<td class="value"><input type="text" name="end_lon" value=""></td>
		</tr>
		<tr>
			<td class="name">Конечная точка широта</td>
			<td class="value"><input type="text" name="end_lat" value=""></td>
		</tr>
		<tr>
			<td class="name"><input type="hidden" name="action" value="1"></td>
			<td class="value"><input type="submit" value="Добавить"></td>
		</tr>
	</table>
</form>
<style>
	.name {
		text-align: right;
	}
	.value {
		text-align: left;
	}
	.ok {
		color: green;
	}
	.err {
		color: red;
	}
</style>
<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>