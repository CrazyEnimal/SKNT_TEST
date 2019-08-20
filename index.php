<?php
	
	include("functions.php");
	$objectsTariff = GetTariffs("http://sknt.ru/job/frontend/data.json");
	$result = new StdClass();

	$result->html_data = "";

	// Если мы только пришли на страницу? выдаем список тарифов.
	if(!$_POST["iteration"] || $_POST["iteration"] === 0){
		$result->html_data .= "
		<h1 class=\"tariffs__main-title visually-hidden\">Тарифы</h1>
		<ul class=\"tariffs__list tariff\">";
		// Перебираем все тарифы
		foreach($objectsTariff->tarifs as $keyObjectTrafiff => $objectTariff){
			
			$result->html_data .= "
			<li class=\"tariff__item\">
				<h2 class=\"tariff__title\">Тариф \"{$objectTariff->title}\"</h2>
				<a class=\"tariff__link\" data-iteration=\"1\" data-tariff={$keyObjectTrafiff} onclick=\"ForwardLink($(this))\">
				";
			// Формируем строку для максимальной и минимальной цены если вариантов больше чем 1
			if(count($objectTariff->tarifs)>1){
				foreach($objectTariff->tarifs as $keyItemTariff => $itemTariff){
					if($keyItemTariff === 0) {
						$minPay = (float)$itemTariff->price / (int)$itemTariff->pay_period;
						$maxPay = (float)$itemTariff->price / (int)$itemTariff->pay_period;		
					} else {
						$minPay = (((float)$itemTariff->price / (int)$itemTariff->pay_period) < $minPay) ? (float)$itemTariff->price / (int)$itemTariff->pay_period : $minPay;
						$maxPay = (((float)$itemTariff->price / (int)$itemTariff->pay_period) > $maxPay) ? (float)$itemTariff->price / (int)$itemTariff->pay_period : $maxPay;	
					}
				}
				$paymentString = "{$minPay} &#8212; {$maxPay} &#8381;/мес";
			// Если у нас один вариант, то выдаем только одну цену на тариф.		
			} elseif(count($objectTariff->tarifs) == 1) {
				$price = (float)$objectTariff->tarifs[0]->price / (int)$itemTariff->pay_period;
				$paymentString = "{$price} &#8381;/мес";	
			} else {
				$paymentString = "";
			}
			$speedColorModificator = getSpeedColor($objectTariff->speed);
			$result->html_data .= "
					<div class=\"tariff__block\">
						<div class=\"tariff__speed\"> 
							<p class=\"tariff__information\">
								<span class=\"tariff__speed-count {$speedColorModificator}\">
									{$objectTariff->speed} Мбит/с
								</span>
							</p>
							<p class=\"tariff__price\">{$paymentString}</p>";
				// Если у тарифа есть опции то показываем их.
				if(is_array($objectTariff->free_options)){
					$result->html_data .= "
							<ul class=\"description__list\">";
					foreach($objectTariff->free_options as $tariffOption){
						$result->html_data .= "
								<li class=\"description__item\">
									<p class=\"description__text\">{$tariffOption}</p>
								</li>
							";
					}
					$result->html_data .= "
							</ul>
						";
				}		
			$result->html_data .= "
						</div>
						<div class=\"tariff__button-wrapper\">
							<button class=\"tariff__button\">
								<img class=\"tariff__image-arrow\" src=\"img/next.svg\">
							</button>
						</div>
					</div>
				</a>
				<a class=\"tariffs__link-more\" href=\"{$objectTariff->link}\">узнать подробней на сайте www.sknt.ru</a>
			</li>
			";
		}
		$result->html_data .= "
		</ul>
		";
		$result->success = true;
	// Если мы выбрали тариф, то отображаем список всех подтарифов.
	} elseif($_POST["iteration"] == 1) {
		if(isset($_POST["tariff"])){
			$tariffNumber = $_POST["tariff"];
			// Получаем массив вариантов тарифа
			$tariffArray = $objectsTariff->tarifs[$tariffNumber]->tarifs;
			// Сортируем их по стоимости за ежемесячную оплату.
			usort($tariffArray,"compareArrayObjectsByPricePerMonth");
	
			// Получаем максимальную плату за месяц
			$standartMontPrice = (float)$tariffArray[0]->price / (int)$tariffArray[0]->pay_period;
			// Сорртируем варианты по ID
			usort($tariffArray,"compareArrayObjectsByID");
			
			$result->html_data = "
				<a class=\"tariffs__main-link_back\" data-iteration=\"0\" onclick=\"BackLink($(this))\">
					<div class=\"tariffs__main-block\">
						<div class=\"tariffs__main-button-wrapper\">
							<button class=\"tariffs__main-button\">
								<img class=\"tariffs__main-image-arrow\" src=\"img/back.svg\">
							</button>
						</div>
						<div class=\"tariffs__main-title-wrapper\">
							<h1 class=\"tariffs__main-title\">Тариф \"{$objectsTariff->tarifs[$_POST["tariff"]]->title}\"</h1>
						</div>			
					</div>
				</a>
				<ul class=\"tariffs__list tariff\">
					";
			// Перебираем массив вариантов тарифа
			foreach($tariffArray as $itemTariff){
				// Делаем магию с подставнокой Месяца
				$monthsString = getMontPrefix($itemTariff->pay_period);
				// Вычисляем месячную оплату по данному варианту
				$pricePerMonth = (float)$itemTariff->price / (int)$itemTariff->pay_period;
				$result->html_data .= "
					<li class=\"tariff__item\">
						<h2 class=\"tariff__title\">{$monthsString}</h2>
						<a class=\"tariff__link\" data-iteration=\"2\" data-tariff=\"{$tariffNumber}\" data-tariff-id=\"{$itemTariff->ID}\" onclick=\"ForwardLink($(this))\">
							<div class=\"tariff__block\">
								<div class=\"tariff__speed\"> 
									<p class=\"tariff__price\">{$pricePerMonth} &#8381;/мес</p>
									";
				
				if((int)$pricePerMonth < (int)$standartMontPrice){
					$discount = ($standartMontPrice - $pricePerMonth) * (int)$itemTariff->pay_period;
					$result->html_data .= "
									<ul class=\"description__list\">
										<li class=\"description__item\">
											<p class=\"description__text\">разовй платеж &#8212; {$itemTariff->price}  &#8381;</p>
										</li>
										<li class=\"description__item\">
											<p class=\"description__text\">скидка &#8212; {$discount} &#8381;</p>
										</li>
									</ul>
									";
				} else {
					$result->html_data .= "
									<ul class=\"description__list\">
										<li class=\"description__item\">
											<p class=\"description__text\">разовй платеж &#8212; {$itemTariff->price}  &#8381;</p>
										</li>
									</ul>
									";					
				}
				
				$result->html_data .= "
								</div>
								<div class=\"tariff__button-wrapper\">
									<button class=\"tariff__button\">
										<img class=\"tariff__image-arrow\" src=\"img/next.svg\">
									</button>
								</div>
							</div>
						</a>
					</li>
				";
			}
			$result->html_data .= "
				</ul>";
			$result->success = true;
		} else {
			$result->success = false;
			$result->html_data = "Не выбран тариф!";
		}
		
		die(json_encode($result));
	// Сюда попадаем если мы выбрали вариант тарифа
	} elseif($_POST["iteration"] == 2) {
		if(isset($_POST["tariff"]) && isset($_POST["tariffID"])){
			$tariffNumber = (int)$_POST["tariff"];
			$tariffID = (int)$_POST["tariffID"];
			// Получаем информацию о конкретном тарифе
			$itemTariff = selectTariffObjectFromAllTariffsById($objectsTariff, $tariffID);
			// Опять делаем магию с месяцем
			$monthsString = getMontPrefix($itemTariff->pay_period);
			$pricePerMonth = (float)$itemTariff->price / (int)$itemTariff->pay_period;
			//Тут на всякий случай приводу строку к варианту когда можно без проблем работать с TimeStamp
			$timestampPayDay = (int)substr($itemTariff->new_payday,0,strpos("+",$itemTariff->new_payday));
			$offsetPayDay = (int)substr($itemTariff->new_payday,strpos("+",$itemTariff->new_payday));
			$newPayDay = date("d.m.Y",$timestampPayDay + $offsetPayDay);

			$result->html_data = "
			<a class=\"tariffs__main-link_back\" data-iteration=\"1\" data-tariff=\"{$tariffNumber}\" onclick=\"BackLink($(this))\">
				<div class=\"tariffs__main-block\">
					<div class=\"tariffs__main-button-wrapper\">
						<button class=\"tariffs__main-button\">
							<img class=\"tariffs__main-image-arrow\" src=\"img/back.svg\">
						</button>
					</div>
					<div class=\"tariffs__main-title-wrapper\">
						<h1 class=\"tariffs__main-title\">Выбор тарифа</h1>
					</div>			
				</div>
			</a>
			<div class=\"tariff__item-last-Wrapper\">
				<div class=\"tariff__item-last\">
					<h2 class=\"tariff__title\">Тариф \"{$itemTariff->title}\"</h2>
						<div class=\"tariff__block\">
							<div class=\"tariff__block-data\">
								<div class=\"tariff__price-last\">Период оплаты &#8212; {$monthsString}</div>
								<div class=\"tariff__price-last\">{$pricePerMonth} &#8381;/мес</div>
								<ul class=\"description__list-last\">
									<li class=\"description__item\">
										<p class=\"description__text\">разовй платеж &#8212; {$itemTariff->price}  &#8381;</p>
									</li>
									<li class=\"description__item\">
										<p class=\"description__text\">со счета спишется &#8212; {$itemTariff->price} &#8381;</p>
									</li>
								</ul>
								<ul class=\"description__list-last\">
									<li class=\"description__item\">
										<p class=\"description__text description__text--grey\">вступит в силу &#8212; сегодня</p>
									</li>
									<li class=\"description__item\">
										<p class=\"description__text description__text--grey\">активно до &#8212; {$newPayDay}</p>
									</li>
								</ul>
							</div>
						</div>
						<div class=\"tariff__button-last-wrapper\">
							<a class=\"tariff__button-last_link\" data-tariff-id=\"{$itemTariff->ID}\" onclick=\"SubmitTariff($(this))\"><button class=\"tariff__button-last\">Выбрать</button></a>
						</div>
					</div>
				</div>				
			";
			$result->success = true;
		} else {
			$result->success = false;
			$result->html_data = "Не выбран вариант тарифа!";
		}	
		die(json_encode($result));
	} else {
		$result->error=true;
		die(json_encode($result));
	}
	if($_POST["iteration"] === "0")die(json_encode($result));
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="css/app.css">
    	<link rel="stylesheet" href="css/destyle.css">
		<script src="js/jquery.min.js"></script>
		<title>Tarifs</title>
	</head>
	<body>
		<main class="content">
			<section class="tariffs">
				<div class="tariffs__container">
					<?php echo $result->html_data;?>
				</div>
			</section>
		</main>
	</body>
	<script src="js/main.js"></script>

</html>
	