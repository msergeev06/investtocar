<? require_once ($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/include/header.php"); ?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<style>
			body {
				margin: 0;
				padding: 0;
			}
		</style>
	</head>
	<body>
<?
	$arSettings = array(
		"chartWidth" => $_GET["chartWidth"],
		"chartHeight" => $_GET["chartHeight"],
		"xTitle" => $_GET["xTitle"],
		"yTitle" => $_GET["yTitle"],
		"type" => $_GET["type"]
	);
	if ($echo = CInvestToCarOdo::ShowChartsOdo($arSettings)) echo $echo;

?>
	</body>
</html>

<? require_once ($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/include/footer.php"); ?>