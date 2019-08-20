<?php

    $result = new StdClass();
    include("functions.php");
    // Это на всякий случай сделал страницу? чтоб было кда переходить.
    if(isset($_POST["tariffID"])){
        $objectsTariff = GetTariffs("http://sknt.ru/job/frontend/data.json");
        $itemTariff = selectTariffObjectFromAllTariffsById($objectsTariff, $_POST["tariffID"]);
        $monthsString = getMontPrefix($itemTariff->pay_period);
        $result->html_data = "
        <a class=\"tariffs__main-link_back\" data-iteration=\"0\" onclick=\"BackLink($(this))\">
            <div class=\"tariffs__main-block\">
                <div class=\"tariffs__main-button-wrapper\">
                    <button class=\"tariffs__main-button\">
                        <img class=\"tariffs__main-image-arrow\" src=\"img/back.svg\">
                    </button>
                </div>
                <div class=\"tariffs__main-title-wrapper\">
                    <h1 class=\"tariffs__main-title\">Поздравляем!</h1>
                </div>			
            </div>
        </a>
        <div class=\"tariff__item-last-Wrapper\">
            <div class=\"tariff__item-last\">
                <h2 class=\"tariff__title\">Вы выбрали тариф {$itemTariff->title}</h2>
                    <div class=\"tariff__block\">
                        <div class=\"tariff__block-data\">
                            <div class=\"tariff__price-last\">Период оплаты &#8212; {$monthsString}</div>
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
                </div>
            </div>				
        ";
        
        $result->success = true;
            
        
    } else {
        $result->error = true;
        $result->message = "Не выбран тариф. Попробуйте снова!";
    }

    echo json_encode($result);