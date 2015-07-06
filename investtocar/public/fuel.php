<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("FUEL")?></h1>
<?
	$defaultCar = CInvestToCarMain::GetDefaultCar();
?>
	<p><?=GetMessage("STATISTICS_FOR")?>: <? echo CInvestToCarMain::ShowSelectAuto("my_car",true); ?><br><br></p>
	<p><a href="<?=$path?>fuel/add_fuel.php?car=<?=$defaultCar?>"><?=GetMessage("ADD_NOTE")?></a><br><br></p>



<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>