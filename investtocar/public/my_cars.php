<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1>Мои машины</h1>
	<p>В Вашем гараже стоят машины:</p>
	<div class="mycars">
		<?$arCars = CInvestToCarMain::GetMyCarsInfo(); ?>
		<?//echo "<pre>"; print_r($arCars); echo "</pre><br>";?>
		<?foreach ($arCars as $myCar):?>
			<div class="carinfo" id="car_<?=$myCar["id"]?>">
				<div class="blockcar">
					<b><?=$myCar["trademark"]?>&nbsp;<?=$myCar["model"]?>&nbsp;<?=$myCar["year"]?>&nbsp;год</b><br>
					<b>Гос. номер:</b> <?=$myCar["carnumber"]?><br>
					<b>VIN:</b> <?=$myCar["vin"]?>
				</div>
				<div class="detailinfo">
					<table class="cardetailinfo">
						<tr>
							<td><b>Тип кузова:</b></td>
							<td><?=$myCar["body"]?></td>
							<td><b>КПП:</b></td>
							<td><?=$myCar["gearshift"]?></td>
							<td><b>Объем двигателя:</b></td>
							<td><?=$myCar["enginecapacity"]?></td>
						</tr>
						<tr>
							<td><b>Стоимость авто:</b></td>
							<td><?=$myCar["cost"]?>&nbsp;р.</td>
							<td><b>Кредит:</b>&nbsp;<?=($myCar["credit"]==1) ? "Да" : "Нет"?></td>
							<td><?=$myCar["creditcost"]?>&nbsp;р.</td>
							<td><b>Интервал ТО:</b></td>
							<td><?=$myCar["interval_ts"]?>&nbsp;км</td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td><b>Дата окончания ОСАГО</b></td>
							<td><?=date("d-m-Y",$myCar["osago_end"])?></td>
							<td><b>Дата окончания ГТО</b></td>
							<td><?=date("d-m-Y",$myCar["gto_end"])?></td>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td><b>Доп.описание</b></td>
							<td colspan="5">Внедряется</td>
						</tr>
						<tr>
							<td><b>Фото машины:</b></td>
							<td colspan="5">Внедряется</td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td><a href="<?=$path?>my_cars/edit_car.php?car=<?=$myCar["id"]?>">Редактировать</a></td>
							<td colspan="5"><a href="<?=$path?>my_cars/del_car.php?car=<?=$myCar["id"]?>">Удалить</a></td>
						</tr>
					</table>

				</div>
			</div>
		<?endforeach?>
	</div>
	<p><a href="<?=$path?>my_cars/add_car.php">Добавить новый автомобиль</a></p>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>