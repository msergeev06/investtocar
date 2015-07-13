<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("DELETING_FUEL_COSTS")?></h1>
<?
	$fuelCostsID = $_GET["id"];
	if(isset($_POST["action"]))
	{
		if ($res = CInvestToCarFuel::DeleteFuelCostsDB ($_POST))
		{
			?><span style="color: green;"><?=GetMessage ("DELETING_FUEL_COSTS_SUCCESS")?></span><?
		}
		else
		{
			?><span style="color: red;"><?=GetMessage ("DELETING_FUEL_COSTS_FAILED")?></span><?
		}
	}
	else {
		?>

		<form action="" method="post">
			<p><?=GetMessage("DELETING_FUEL_COSTS_QUESTION")?></p>
			<input type="hidden" name="action" value="1">
			<input type="hidden" name="id" value="<?=$fuelCostsID?>">
			<input type="submit" value="<?=GetMessage("SUBMIT_DELETE")?>">&nbsp;<a href="../fuel.php"><?=GetMessage("SUBMIT_CANCEL")?></a>
		</form>

		<?
	}
?>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>