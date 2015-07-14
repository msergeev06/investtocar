<?php
	global $DB, $MESS, $OPTIONS;


	class CInvestToCarMain
	{
		public static $arMessage = array();
		private static $arTable = array();

		/**
		 * Функция получает координаты объекта по его адресу через сервис Яндекса
		 *
		 * @param string $address Адрес
		 * @return array|bool Массив координат, либо false
		 */
		public function GetCoordsByAddressYandex ($address = "")
		{
			if ($address != "")
			{
				$xmlStr = file_get_contents ("http://geocode-maps.yandex.ru/1.x/?geocode=".urlencode ($address));
				$xml = simplexml_load_string ($xmlStr);
				$arCoords["all"] = $xml->GeoObjectCollection->featureMember->GeoObject->Point->pos;
				list($arCoords["lon"], $arCoords["lat"]) = explode (" ", $arCoords["all"]);

				return $arCoords;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Возвращает сокращенное наименование дня недели
		 * (Пн., Вт., Ср., Чт., Пт., Сб., Вс.)
		 *
		 * @param $day Число дня недели date("w");
		 * @return bool|string Если ошибка false
		 */
		public function GetNameDayOfWeek ($day)
		{
			switch ($day)
			{
				case 0:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_SUN");
				case 1:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_MON");
				case 2:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_TUE");
				case 3:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_WED");
				case 4:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_THU");
				case 5:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_FRI");
				case 6:
					return GetMessage("ABBREV_NAME_DAY_OF_WEEK_SAT");
				default:
					return false;
			}
		}

		/**
		 * Возвращает название месяца по его номеру
		 * (Январь, Фебраль, Март, Апрель, Май, Июнь,
		 * Июль, Август, Сентябрь, Октябрь, Ноябрь, Декабрь)
		 *
		 * @param $month Число месяца
		 * @return bool|string Название, либо false
		 */
		public function GetNameMonth ($month)
		{
			$month = intval ($month);
			switch ($month)
			{
				case 1:
					return GetMessage("JANUARY");
				case 2:
					return GetMessage("FEBRUARY");
				case 3:
					return GetMessage("MARCH");
				case 4:
					return GetMessage("APRIL");
				case 5:
					return GetMessage("MAY");
				case 6:
					return GetMessage("JUNE");
				case 7:
					return GetMessage("JULY");
				case 8:
					return GetMessage("AUGUST");
				case 9:
					return GetMessage("SEPTEMBER");
				case 10:
					return GetMessage("OCTOBER");
				case 11:
					return GetMessage("NOVEMBER");
				case 12:
					return GetMessage("DECEMBER");
				default:
					return false;
			}
		}

		/**
		 * Функция возвращает Название ремонтирующего по его ID
		 *
		 * @param $id
		 * @return string
		 */
		public function GetRepairNameByID ($id) {
			switch ($id) {
				case 1:
					return GetMessage("NO_DEALER");
				case 2:
					return GetMessage("DEALER");
				case 3:
					return GetMessage("SERVICE_STATION");
				case 4:
					return GetMessage("DID_HE");
				case 5:
					return GetMessage("PRIVATE_SERVICE");
				default:
					return GetMessage("NO_DATA");
			}
		}

		/**
		 * Функция преобразует дату формата dd.mm.YYYY в timestamp
		 *
		 * @param string $date
		 * @return int
		 */
		public function ConvertDateToTimestamp ($date="") {
			global $OPTIONS;
			if ($date=="") $date=date("d.m.Y");
			list($day,$month,$year) = explode(".",$date);
			$timestamp = mktime(0,0,0,intval($month),intval($day),intval($year))+$OPTIONS->GetOptionInt("mktime_add_time");
			return $timestamp;
		}

		/**
		 * Функция возвращает значение указанной колонки строки по ее id из DB
		 *
		 * @param int $ID
		 * @param string $table
		 * @param string $name_col
		 * @return bool
		 */
		public function GetNameByIDFromDB ($ID=0, $table="", $name_col="name") {
			global $DB;
			if ($ID==0 || $table=="") return false;

			$query = "SELECT `".$name_col."` FROM `".$table."` WHERE `id` =".$ID;
			if ($res = $DB->Select($query)) {
				return $res[0][$name_col];
			}
			else {
				return false;
			}

		}

		/**
		 * Функция возвращает название таблицы по ее коду
		 *
		 * @param string $code
		 * @param bool $prefix
		 * @return mixed
		 */
		public function GetTableByCode ($code="",$prefix=true) {
			global $DB,$OPTIONS;
			if ($code=="") return false;

			if (!isset(self::$arTable[$code])) {
				$query = "SELECT `table` FROM `"
				         .$OPTIONS->GetOptionString("DB_table_prefix")
				         ."setup_tables` WHERE `code` LIKE '".$code."'";
				$res = $DB->Select($query);
				self::$arTable[$code] = $res[0]["table"];
			}
			if ($prefix) {
				return $OPTIONS->GetOptionString("DB_table_prefix").self::$arTable[$code];
			}
			else {
				return self::$arTable[$code];
			}
		}

		/**
		 * Функция возвращает info строки по code для заданной table
		 *
		 * @param string $table
		 * @param string $code
		 * @param string $info
		 * @return mixed
		 */
		public function GetInfoByCode ($table="", $code="", $info="id") {
			global $DB;
			if ($table=="" || $code=="") return false;
			$table = self::GetTableByCode($table);

			$query = "SELECT `".$info."` FROM `".$table."` WHERE `code` LIKE '".$code."'";
			$res = $DB->Select($query);
			return $res[0][$info];
		}



/*


		/**
		 * removed Функция возвращает html-код графика Километража
		 *
		 * @param string $arSettings
		 * @return bool|string
		 *
		public function ShowChartsOdo ($arSettings = "")
		{
			return CInvestToCarOdo::ShowChartsOdo($arSettings);
		}

		/**
		 * removed Функция возвращает html-код графика
		 *
		 * @param string $arSettings
		 * * chartWidth Ширина графика
		 * * chartHeight Высота графика
		 * * xTitle Заголовок оси Х
		 * * yTitle Заголовок оси У
		 * * data = array (dataX => dataY) Данные
		 * * fullTitle Заголовок графика
		 * @return bool|string
		 *
		public function HtmlCharts ($arSettings = "")
		{
			return CInvestToCarCharts::HtmlCharts($arSettings);
		}

		/**
		 * removed Функция добавляет информацию о новом маршруте, точках и пробеге
		 *
		 * @param string $post
		 * @return bool
		 *
		public function AddNewRoute ($post = "")
		{
			return CInvestToCarOdo::AddNewRoute($post);
		}

		/**
		 * removed Функция увеличивает частоту выбора маршрутной точки на 1
		 *
		 * @param int $pointID
		 * @return bool
		 *
		public function IncreasePointPeriod ($pointID = 0)
		{
			return CInvestToCarPoints::IncreasePointPeriod($pointID);
		}

		/**
		 * removed Функция добавляет информацию о новом маршруте в DB
		 *
		 * @param array $data Массив данных
		 * @return mixed Результат
		 *
		public function AddNewRouteDB ($data)
		{
			return CInvestToCarOdo::AddNewRouteDB($data);
		}

		/**
		 * removed Функция добавляет новую точку в список
		 *
		 * @param array $data
		 * @return mixed
		 *
		public function AddNewPointDB ($data)
		{
			return CInvestToCarPoints::AddNewPointDB($data);
		}

		/**
		 * removed Функция обновляет данные о пробеге для заданной даты либо для всей базы
		 *
		 * @param int $date
		 *
		public function UpdateDayOdometer ($date = 0)
		{
			CInvestToCarOdo::UpdateDayOdometer($date);
		}

		/**
		 * removed Функция получает данные из DB о километраже за заданный период
		 *
		 * @param $from Начало периода (включительно)
		 * @param $to Окончание периоды (включительно)
		 * @param int $year Средние данные по месяцам (за год)
		 * @param int $car ID автомобля
		 * @return array|bool Данные либо false
		 *
		public function GetListOdoFromTo ($from, $to, $year = 0, $car = 1)
		{
			return CInvestToCarOdo::GetListOdoFromTo($from,$to,$year,$car);
		}

		/**
		 * removed Функция получает подробные данные о всех автомобилях в гараже, либо о заданном
		 *
		 * @return array
		 *
		public function GetMyCarsInfo ($car=0)
		{
			return CInvestToCarCars::GetMyCarsInfo($car);
		}

		/**
		 * removed Функция получает данные указанного автомобиля, либо всех автомобилей
		 *
		 * @param int $car
		 * @return mixed
		 *
		public function GetMyCars ($car=0) {
			return CInvestToCarCars::GetMyCars($car);
		}

		/**
		 * removed Функция добавляет новую марку автомобиля, связывая ее с брендом
		 *
		 * @param $brand id бренда
		 * @param $model Имя модели
		 * @return int|bool id добавленной марки, либо false
		 *
		public function AddNewModel ($brand, $model)
		{
			return CInvestToCarCars::AddNewModel($brand, $model);
		}

		/**
		 * removed Функция добавляет новый автомобиль в гараж
		 *
		 * @param $arData Данные
		 * @return bool|int id нового автомобиля, либо false
		 *
		public function AddNewCarGarage ($arData)
		{
			return CInvestToCarCars::AddNewCarGarage($arData);
		}

		/**
		 * removed Функция обновляет информацию об автомобиле в гараже
		 *
		 * @param int $car
		 * @param $arData
		 * @return bool
		 *
		public function UpdateCarInGarage ($car=0, $arData) {
			return CInvestToCarCars::UpdateCarInGarage($car);
		}

		/**
		 * removed Функция удаляет отметку default у всех автомобилей
		 *
		public function DeleteMarkDefaultCars () {
			CInvestToCarCars::DeleteMarkDefaultCars();
		}

		/**
		 * removed Функция проверяет, можно ли удалить указанный автомобиль, т.е. нет ли записей, связанных с ним
		 *
		 * @param int $car
		 * @return bool
		 *
		public function CheckCanDeleteCar ($car=0) {
			return CInvestToCarCars::CheckCanDeleteCar($car);
		}

		/**
		 * removed Функция удаляет указанный автомобиль, дополнительно проверив на возможность удаления
		 *
		 * @param int $car ID автомобиля
		 * @param bool $checkDelete проверять ли на возможность удаления
		 * @return bool Удалось/Не удалось удалить
		 *
		public function DeleteCarInGarage ($car=0,$checkDelete=true) {
			return CInvestToCarCars::DeleteCarInGarage($car,$checkDelete);
		}

		/**
		 * removed Функция возвращает ID автомобиля по-умолчанию
		 *
		 * @return int|bool ID автомобиля по-умолчанию, либо false
		 *
		public function GetDefaultCar () {
			return CInvestToCarCars::GetDefaultCar();
		}

		/**
		 * removed Функция возвращает название бренда по его ID
		 *
		 * @param $id
		 * @return string
		 *
		public function GetCarTrademarkNameByID ($id) {
			return CInvestToCarCars::GetCarTrademarkNameByID($id);
		}

		/**
		 * removed Функция возвращает модель авто по его ID
		 *
		 * @param $id
		 * @return string
		 *
		public function GetCarModelNameByID ($id) {
			return CInvestToCarCars::GetCarModelNameByID($id);
		}

		/**
		 * removed Функция возвращает массив с информацией о точке, либо false
		 *
		 * @param int $id
		 * @return bool
		 *
		public function GetPointInfoByID ($id=0) {
			return CInvestToCarPoints::GetPointInfoByID($id);
		}

		/**
		 * removed Функция возвращает массив записей о прохождении ТО для автомобиля, либо false
		 *
		 * @param int $car
		 * @return bool
		 *
		public function GetListCarTs ($car=0) {
			return CInvestToCarTs::GetListCarTs($car);
		}

		/**
		 * removed Функция добавления расходов на ТО
		 *
		 * @param string $post
		 * @return bool
		 *
		public function AddNewTs ($post="") {
			return CInvestToCarTs::AddNewTs($post);
		}

		/**
		 * removed Добавляет данные о расходах на ТО в базу
		 *
		 * @param $arData
		 * @return bool
		 *
		public function AddNewTsDB ($arData) {
			return CInvestToCarTs::AddNewTsDB($arData);

		}

		/**
		 * removed Функция возвращает массив данных по указанному ID записи о ТО
		 *
		 * @param int $tsID
		 * @return bool
		 *
		public function GetTsInfo ($tsID=0) {
			return CInvestToCarTs::GetTsInfo($tsID);
		}

		/**
		 * removed Функция обновляет информации о расходе на ТО
		 *
		 * @param int $tsID
		 * @param array $arPost
		 * @return bool
		 *
		public function UpdateTsInfo ($tsID=0, $arPost=array()) {
			return CInvestToCarTs::UpdateTsInfo($tsID, $arPost);
		}

		/**
		 * removed Функция обновляет информацию о расходен на ТО в DB
		 *
		 * @param $arData
		 * @return bool
		 *
		public function UpdateTsInfoDB ($arData) {
			return CInvestToCarTs::UpdateTsInfoDB($arData);
		}

		/**
		 * removed Функция удаляет информацию о расходе на ТО из DB;
		 *
		 * @param int $tsID
		 * @return bool
		 *
		public function DeleteTsInfoDB ($tsID=0) {
			return CInvestToCarTs::DeleteTsInfoDB($tsID);
		}

		/**
		 * removed Функция получает массив записей о расходе топлива
		 *
		 * @param int $carID
		 * @return bool
		 *
		public function GetFuelList ($carID=0) {
			return CInvestToCarFuel::GetFuelList($carID);
		}

		/**
		 * removed Функция получает наименование топлива по ID, либо краткое (по-умолчанию), либо полное
		 *
		 * @param int $fuelMarkID
		 * @param bool $full
		 * @return string
		 *
		public function GetFuelMarkByID ($fuelMarkID=0,$full=false) {
			return CInvestToCarFuel::GetFuelMarkByID($fuelMarkID,$full);
		}

		/**
		 * removed Функция возвращает общую сумму расходов на топливо
		 *
		 * @param int $car
		 * @return float|int
		 *
		public function GetTotalFuelCosts ($car=0) {
			return CInvestToCarFuel::GetTotalFuelCosts($car);
		}

		/**
		 * removed Функция возвращает общую сумму расходов на ТО
		 *
		 * @param int $car
		 * @return float|int
		 *
		public function GetTotalMaintenanceCosts ($car=0) {
			return CInvestToCarTs::GetTotalMaintenanceCosts($car);
		}

		/**
		 * removed Функция возвращает общую сумму расходов на автомобиль
		 *
		 * @param int $car
		 * @return float|int
		 *
		public function GetTotalCosts ($car=0) {
			return CInvestToCarCars::GetTotalCosts($car);
		}

		/**
		 * removed Функция возвращает средний расход топлива на 100км
		 *
		 * @param int $car
		 * @return float|int
		 *
		public function GetAverageFuelConsumption ($car=0) {
			return CInvestToCarFuel::GetAverageFuelConsumption($car);
		}

		/**
		 * removed Функция добавляет информацию о заправке а также проверяет, необходимо ли пересчитать средний расход топлива
		 *
		 * @param array $post
		 * @return bool
		 *
		public function AddFuelCosts ($post=array()) {
			return CInvestToCarFuel::AddFuelCosts($post);
		}

		/**
		 * removed Функция создает новую точку, определяя координаты по адресу, если необходимо
		 *
		 * @param $name
		 * @param $address
		 * @param $lon
		 * @param $lat
		 * @param int $type
		 * @return int|mixed
		 *
		public function CreateNewPoint ($name, $address,$lon,$lat,$type=0) {
			return CInvestToCarPoints::CreateNewPoint($name, $address, $lon, $lat,$type);
		}

		/**
		 * removed Функция добавляет информацию о заправке в DB
		 *
		 * @param array $arData
		 * @return bool
		 *
		public function AddFuelCostsDB ($arData=array()) {
			return CInvestToCarFuel::AddFuelCostsDB($arData);

		}

		/**
		 * removed Функция проверяет нет ли более поздних данных о заправках, после добавленной
		 *
		 * @param int $date
		 * @param int $car
		 * @return bool
		 *
		public function CheckLastFuelCosts ($date=0, $car=0) {
			return CInvestToCarFuel::CheckLastFuelCosts($date,$car);

		}

		/**
		 * removed Функция выполняет пересчет расхода для всех записей о заправках
		 *
		 * @param int $car
		 *
		public function RecalculationExpense ($car=0) {
			CInvestToCarFuel::RecalculationExpense($car);
		}

		/**
		 * removed Функция высчитывает расход топлива на 100км
		 *
		 * @param int $odo
		 * @param int $liters
		 * @param int $car
		 * @param int $date
		 * @return float|int
		 *
		public function CalculationExpense ($odo=0,$liters=0,$car=0,$date=0) {
			return CInvestToCarFuel::CalculationExpense($odo,$liters,$car,$date);
		}

		/**
		 * removed Функция обновляет значение расхода для указанной записи
		 *
		 * @param $id
		 * @param $expense
		 * @return bool
		 *
		public function UpdateExpense ($id, $expense) {
			return CInvestToCarFuel::UpdateExpense($id,$expense);
		}

		/**
		 * removed Функция возвращает массив данных записи о заправке, по ее ID
		 *
		 * @param int $id
		 * @return bool
		 *
		public function GetFuelCostsByID ($id=0) {
			return CInvestToCarFuel::GetFuelCostsByID($id);
		}

		/**
		 * removed Функция обновляет данные о заправке и пересчитывает все показания расхода топлива
		 *
		 * @param array $post
		 * @return bool
		 *
		public function UpdateFuelCosts ($post=array()) {
			return CInvestToCarFuel::UpdateFuelCosts($post);
		}

		/**
		 * removed Функция обновляет данные о заправке в DB
		 *
		 * @param array $arData
		 * @return bool
		 *
		public function UpdateFuelCostsDB ($arData=array()) {
			return CInvestToCarFuel::UpdateFuelCostsDB($arData);
		}

		/**
		 * removed Функция удаляет из DB информацию о заправке и пересчитывает значения расхода топлива
		 *
		 * @param array $post
		 * @return bool
		 *
		public function DeleteFuelCostsDB ($post=array()) {
			return CInvestToCarFuel::DeleteFuelCostsDB($post);
		}

		/**
		 * removed Функция добавляет информацию о запчасти
		 *
		 * @param array $post
		 * @return bool
		 *
		public function AddRepairParts ($post=array()) {
			return CInvestToCarRepairParts::AddRepairParts($post);
		}

		/**
		 * removed Функция добавляет в DB информацию о запасной части
		 *
		 * @param array $arData
		 * @return bool
		 *
		public function AddRepairPartsDB ($arData=array()) {
			return CInvestToCarRepairParts::AddRepairPartsDB($arData);
		}

		/**
		 * removed Функция возвращает массив со списком запчастей
		 *
		 * @param int $car
		 * @return bool
		 *
	    public function GetListRepairParts ($car=0) {
		    return CInvestToCarRepairParts::GetListRepairParts($car);
	    }

		/**
		 * removed Функция удаляет информацию о запчасти из DB по ее id
		 *
		 * @param int $repairPartsID
		 * @return bool
		 *
		public function DeleteRepairPartsInfoDB ($repairPartsID=0) {
			return CInvestToCarRepairParts::DeleteRepairPartsInfoDB($repairPartsID);
		}

		/**
		 * removed Функция возвращает массив значений для id запчасти
		 *
		 * @param int $repairPartsID
		 * @return bool
		 *
		public function GetRepairPartsInfo ($repairPartsID=0) {
			return CInvestToCarRepairParts::GetRepairPartsInfo($repairPartsID);
		}

		/**
		 * removed Функция подготавливает запись о запчасти к записи
		 *
		 * @param $post
		 * @return bool
		 *
		public function UpdateRepairParts ($post) {
			return CInvestToCarRepairParts::UpdateRepairParts($post);
		}

		/**
		 * removed Функция обновляет информацию о запчасти в DB
		 *
		 * @param array $arData
		 * @return bool
		 *
		public function UpdateRepairPartsDB ($arData=array()) {
			return CInvestToCarRepairParts::UpdateRepairPartsDB($arData);
		}

		/**
		 * removed Функция возвращает общую сумму расходов на запчасти
		 *
		 * @param int $car
		 * @return float|int
		 *
		public function GetTotalRepairPartsCosts ($car=0) {
			return CInvestToCarRepairParts::GetTotalRepairPartsCosts($car);
		}

		/**
		 * removed Функция возвращает число израсходованных литров бензина, основываясь на записях о заправках
		 *
		 * @param int $car
		 * @return float
		 *
		public function GetTotalSpentFuel($car=0) {
			return CInvestToCarFuel::GetTotalSpentFuel($car);
		}

		/**
		 * removed Функция возвращает текущий пробег, находя максимальную запись в разных таблицах
		 *
		 * @param int $car
		 * @return float
		 *
		public function GetCurrentMileage ($car=0) {
			return CInvestToCarOdo::GetCurrentMileage($car);
		}

		/**
		 * removed Функция возвращает html-код добавляющий форму добавления новой точки
		 *
		 * @param bool $showType
		 * @param int $typeSelect
		 * @param array $arTypes
		 * @return string
		 *
		public function ShowFormNewPointAdd ($showType=false, $typeSelect=0, $arTypes=array()) {
			return CInvestToCarPoints::ShowFormNewPointAdd($showType,$typeSelect,$arTypes);
		}
*/

	}