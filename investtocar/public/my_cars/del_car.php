<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("DELETING_CAR")?></h1>
<?if(!isset($_POST["action"])):?>
	<?$car = intval($_GET["car"]); ?>
	<?if (!$canDelete = CInvestToCarCars::CheckCanDeleteCar($car)):?>
		<p><?=GetMessage("YOU_CAN_NOT_DELETE_BY")?>:</p>
		<ul>
			<?if(isset(CInvestToCarCars::$arMessage["ERROR"])):?>
			<li><?=CInvestToCarCars::$arMessage["ERROR"]?></li>
			<?endif;?>
			<?if(isset(CInvestToCarCars::$arMessage["CAN_DELETE"])):?>
				<?foreach(CInvestToCarCars::$arMessage["CAN_DELETE"] as $message):?>
					<li><?=$message?></li>
				<?endforeach;?>
			<?endif;?>
		</ul>
	<?else:?>
		<p><?=GetMessage("ARE_YOU_SHURE_DELETE")?></p>
		<form action="" method="POST">
			<input type="checkbox" name="confirm_delete" value="1">&nbsp;<?=GetMessage("YES_IAM_DELETE_THIS_CAR")?><br><br>
			<input type="hidden" name="action" value="1"><input type="hidden" name="car" value="<?=$car?>">
			<input type="submit" value="<?=GetMessage("SUBMIT_DELETE")?>">
		</form>
	<?endif;?>
<?else:?>
	<?
		if (isset($_POST["confirm_delete"])) {
			$deleteCar = intval($_POST["car"]);
			if ($res = CInvestToCarCars::DeleteCarInGarage($deleteCar)) {
				?><span style="color: green;"><?=GetMessage("DELETING_CAR_SUCCESS")?></span><?
			}
			else {
				?><span style="color: red;"><?=GetMessage("DELETING_CAR_FAILED")?></span><?
			}
		}
	?>
<?endif;?>


<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>