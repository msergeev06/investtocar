<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("DELETE_NOTE_REPAIR_PARTS")?></h1>
<?if(!isset($_POST["repair_parts_id"])):?>
	<p><?=GetMessage("SURE_TO_DELETE_Q")?></p>
	<p><form action="" method="post">
		<input type="hidden" name="repair_parts_id" value="<?=$_GET["id"]?>">
		<input type="submit" value="<?=GetMessage("SUBMIT_DELETE")?>">&nbsp;&nbsp;&nbsp;<a href="<?$path?>../repair_parts.php"><?=GetMessage("SUBMIT_CANCEL")?></a>
	</form></p>
<?else:?>
	<?
	if ($res = CInvestToCarMain::DeleteRepairPartsInfoDB($_POST["repair_parts_id"])) {
		?><span style="color: green;"><?=GetMessage("DATA_DELETE_SUCCESS")?></span><?
	}
	else {
		?><span style="color: red;"><?=GetMessage("DATA_DELETE_ERROR")?></span><?
	}
	?>
<?endif;?>
<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>