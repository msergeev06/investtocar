<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarCharts
	{
		public static $arMessage = array ();

		/**
		 * Функция возвращает html-код графика
		 *
		 * @param string $arSettings
		 * * chartWidth Ширина графика
		 * * chartHeight Высота графика
		 * * xTitle Заголовок оси Х
		 * * yTitle Заголовок оси У
		 * * data = array (dataX => dataY) Данные
		 * * fullTitle Заголовок графика
		 * @return bool|string
		 */
		public function HtmlCharts ($arSettings = "")
		{
			if (!is_array ($arSettings) || !isset($arSettings["data"]))
			{
				return false;
			}

			$echo = "<div id=\"curve_chart\" style=\"width: ".$arSettings["chartWidth"]."px; height: "
			        .$arSettings["chartHeight"]."px\"></div>\n";
			$echo .= "\t<script type=\"text/javascript\" src=\"https://www.google.com/jsapi?autoload={\n";
			$echo .= "\t\t'modules':[{\n\t\t'name':'visualization',\n\t\t'version':'1',\n\t\t'packages':['corechart']\n\t\t}]\n\t}\"></script>\n";
			$echo .= "\t<script type=\"text/javascript\">\n\tgoogle.setOnLoadCallback(drawChart);\n\n";
			$echo .= "\t\tfunction drawChart() {\n";
			$echo .= "\t\t\tvar data = google.visualization.arrayToDataTable([\n";
			$echo .= "\t\t\t\t['".$arSettings["xTitle"]."', '".$arSettings["yTitle"]."']";

			foreach ($arSettings["data"] as $x => $y)
			{
				$echo .= ",\n\t\t\t\t['".$x."',  ".$y."]";
			}

			$echo .= "\n\t\t\t]);\n";
			$echo .= "\t\t\tvar options = {\n";

			$echo .= "\t\t\t\ttitle: '".$arSettings["fullTitle"]."',\n";
			$echo .= "\t\t\t\tcurveType: 'function',\n";
			$echo .= "\t\t\t\tlegend: { position: 'bottom' }\n";
			$echo .= "\t\t\t};\n\n";

			$echo .= "\t\t\tvar chart = new google.visualization.LineChart(document.getElementById('curve_chart'));\n";
			$echo .= "\t\t\tchart.draw(data, options);\n";
			$echo .= "\t\t}\n";
			$echo .= "\t</script>\n";

			return $echo;

		}

	}