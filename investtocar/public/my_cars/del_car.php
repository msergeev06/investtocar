<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1>Удаление автомобиля</h1>
<?if(!isset($_POST["action"])):?>
	<?$car = intval($_GET["car"]); ?>
	<?if (!$canDelete = CInvestToCarMain::CheckCanDeleteCar($car)):?>
		<p>Вы не можете удалить данный автомобиль по следующим причинам:</p>
		<ul>
			<?if(isset(CInvestToCarMain::$arMessage["ERROR"])):?>
			<li><?=CInvestToCarMain::$arMessage["ERROR"]?></li>
			<?endif;?>
			<?if(isset(CInvestToCarMain::$arMessage["CAN_DELETE"])):?>
				<?foreach(CInvestToCarMain::$arMessage["CAN_DELETE"] as $message):?>
					<li><?=$message?></li>
				<?endforeach;?>
			<?endif;?>
		</ul>
	<?else:?>
		<p>Вы действительно хотите удалить данный автомобиль? Восстановление будет невозможно!</p>
		<form action="" method="POST">
			<input type="checkbox" name="confirm_delete" value="1">&nbsp;Я подтверждаю, что хочу удалить данный автомобиль!<br><br>
			<input type="hidden" name="action" value="1"><input type="hidden" name="car" value="<?=$car?>">
			<input type="submit" value="Удалить">
		</form>
	<?endif;?>
<?else:?>
	<?
		if (isset($_POST["confirm_delete"])) {
			$deleteCar = intval($_POST["car"]);
			if ($res = CInvestToCarMain::DeleteCarInGarage($deleteCar)) {
				?><span style="color: green;">Автомобиль успешно удален</span><?
			}
			else {
				?><span style="color: red;">Не удалось удалить автомобиль!</span><?
			}
		}
	?>
<?endif;?>


<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>