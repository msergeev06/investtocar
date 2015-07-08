<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("REPAIR_PARTS")?></h1>
<?
	$defaultCar = CInvestToCarMain::GetDefaultCar();
?>
	<p><?=GetMessage("INFORMATION_FOR")?>: <? echo CInvestToCarMain::ShowSelectAuto("my_car",true); ?><?=GetMessage("TOTAL_REPAIR_PARTS_COSTS")?>: <?=CInvestToCarMain::GetTotalMaintenanceCosts()?> <?=GetMessage("RUB")?><br><br></p>
	<p><a href="<?=$path?>repair_parts/add_repair_parts.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_NOTE")?></a><br><br></p>



<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>