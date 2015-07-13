<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("OTHER")?></h1>
	<?=GetMessage("OTHER_INFO")?>
<?
	$defaultCar = CInvestToCarCars::GetDefaultCar();
?>
	<p><?=GetMessage("STATISTICS_FOR")?>: <? echo CInvestToCarShowSelect::Auto("my_car",true); ?><?=GetMessage("TOTAL_COSTS")?>: <?=CInvestToCarMain::GetTotalMaintenanceCosts()?> <?=GetMessage("RUB")?><br><br></p>
	<p><a href="<?=$path?>other/add_other.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_NOTE")?></a><br><br></p>


<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>