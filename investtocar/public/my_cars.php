<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/header.php"); ?>
	<h1><?=GetMessage("MY_CARS")?></h1>
	<p><?=GetMessage("CARS_IN_YOUR_GARAGE")?>:</p>
	<div class="mycars">
		<?$arCars = CInvestToCarMain::GetMyCarsInfo(); ?>
		<?//echo "<pre>"; print_r($arCars); echo "</pre><br>";?>
		<?foreach ($arCars as $myCar):?>
			<div class="carinfo" id="car_<?=$myCar["id"]?>">
				<div class="blockcar">
					<b><?=$myCar["trademark"]?>&nbsp;<?=$myCar["model"]?>&nbsp;<?=$myCar["year"]?>&nbsp;<?=GetMessage("YEAR")?></b><br>
					<b><?=GetMessage("CAR_NUMBER")?>:</b> <?=$myCar["carnumber"]?><br>
					<b><?=GetMessage("VIN")?>:</b> <?=$myCar["vin"]?>
				</div>
				<div class="detailinfo">
					<table class="cardetailinfo">
						<tr>
							<td><b><?=GetMessage("BODY_TYPE")?>:</b></td>
							<td><?=$myCar["body"]?></td>
							<td><b><?=GetMessage("GEAR_BOX")?>:</b></td>
							<td><?=$myCar["gearshift"]?></td>
							<td><b><?=GetMessage("ENGINE_CAPACITY")?>:</b></td>
							<td><?=$myCar["enginecapacity"]?></td>
						</tr>
						<tr>
							<td><b><?=GetMessage("CAR_COST")?>:</b></td>
							<td><?=$myCar["cost"]?>&nbsp;<?=GetMessage("RUB")?></td>
							<td><b><?=GetMessage("CREDIT")?>:</b>&nbsp;<?=($myCar["credit"]==1) ? GetMessage("YES") : GetMessage("NO")?></td>
							<td><?=$myCar["creditcost"]?>&nbsp;<?=GetMessage("RUB")?></td>
							<td><b><?=GetMessage("MAINTENANCE_INTERVAL")?>:</b></td>
							<td><?=$myCar["interval_ts"]?>&nbsp;<?=GetMessage("KM")?></td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td><b><?=GetMessage("DATE_END_OSAGO")?></b></td>
							<td><?=date("d-m-Y",$myCar["osago_end"])?></td>
							<td><b><?=GetMessage("DATE_END_GTO")?></b></td>
							<td><?=date("d-m-Y",$myCar["gto_end"])?></td>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td><b><?=GetMessage("TOTAL_COSTS")?>:</b></td>
							<td colspan="5"><?=round($myCar["total_costs"],2)?> <?=GetMessage("RUB")?></td>
						</tr>
						<tr>
							<td><b><?=GetMessage("AVERAGE_FUEL_CONSUMPTION")?>:</b></td>
							<td colspan="5"><?=round($myCar["average_fuel_consum"],2)?> <?=GetMessage("LITERS_FOR_100KM")?></td>
						</tr>
						<tr>
							<td><b><?=GetMessage("TOTAL_SPENT_FUEL")?>:</b></td>
							<td colspan="5"><?=number_format(CInvestToCarMain::GetTotalSpentFuel($defaultCar),2)?> <?=GetMessage("LITER")?></td>
						</tr>
						<tr>
							<td><b><?=GetMessage("CURRENT_MILEAGE")?>:</b></td>
							<td colspan="5"><?=number_format(CInvestToCarMain::GetCurrentMileage($defaultCar),2)?> <?=GetMessage("KM")?></td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td><b><?=GetMessage("ADDIT_DESCRIPTION")?></b></td>
							<td colspan="5"><?=GetMessage("IMPLEMENTED")?></td>
						</tr>
						<tr>
							<td><b><?=GetMessage("CAR_PHOTOS")?>:</b></td>
							<td colspan="5"><?=GetMessage("IMPLEMENTED")?></td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td><a href="<?=$path?>my_cars/edit_car.php?car=<?=$myCar["id"]?>"><?=GetMessage("SUBMIT_EDIT")?></a></td>
							<td colspan="5"><a href="<?=$path?>my_cars/del_car.php?car=<?=$myCar["id"]?>"><?=GetMessage("SUBMIT_DELETE")?></a></td>
						</tr>
					</table>

				</div>
			</div>
		<?endforeach?>
	</div>
	<p><a href="<?=$path?>my_cars/add_car.php"><?=GetMessage("ADD_NEW_CAR")?></a></p>

<? require_once($_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/public/include/footer.php"); ?>