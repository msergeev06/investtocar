<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
<h1><?=GetMessage("DELETE_OTHER_COST")?></h1>
	<?if(!isset($_POST["action"])):?>
	<? $otherID = $_GET["id"]; ?>
	<?=GetMessage("DELETE_OTHER_COST_CONFIRM")?><br>
	<form action="" method="post">
		<input type="hidden" name="action" value="1">
		<input type="hidden" name="id" value="<?=$otherID?>">
		<input type="submit" value="<?=GetMessage("SUBMIT_DELETE")?>">&nbsp;<a href="../other.php"><?=GetMessage("SUBMIT_CANCEL")?></a>
	</form>
	<?else:?>
	<?
	if ($res = CInvestToCarOther::DeleteInfo($_POST)) {
		?><span style="color: green;"><?=GetMessage("DELETE_OTHER_SUCCESS")?></span><?
	}
	else {
		?><span style="color: red;"><?=GetMessage("DELETE_OTHER_FAILED")?></span><?
	}
	?>
	<?endif;?>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>