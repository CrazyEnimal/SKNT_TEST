<?php
/**
 * тут собрал маленькие функции? чтоб вынести их в отдельный файл, 
 * чисто теоретически могут быть задействованы по мере необходимости.
 * Можно было и в коде оставить, но зачем плодит сущьности.
 */
    function GetTariffs($url) {
        $jsonTariff = file_get_contents($url);
        return json_decode($jsonTariff);
    }

    function compareArrayObjectsByID($itemA, $itemB) {
        return $itemA->ID > $itemB->ID;
    }

    function compareArrayObjectsByPricePerMonth($itemA, $itemB) {
        return (float)$itemA->price / (int)$itemA->pay_period < (float)$itemB->price / (int)$itemB->pay_period;
    }

    function selectTariffById($array, $selector) {
        foreach($array as $object) {
            if($object->ID == $selector) return $object;
        }
        return NULL;
    }

    function getSpeedColor($speed){
         switch($speed){
            case "50" :
                $result = "tariff__speed-count--color-brown";
                break;
            case "100" :
                $result = "tariff__speed-count--color-blue";
                break;
            case "200" :
                $result = "tariff__speed-count--color-red";
                break;
            default:
                $result = "tariff__speed-count--color-brown";
        }
        return $result;

    }

    function selectTariffObjectFromAllTariffsById($array, $selector) {
        $result = new StdClass();

        foreach($array->tarifs as $objectTarifs) {
            $titleTarif = $objectTarifs->title;
            $tariffObject = selectTariffById($objectTarifs->tarifs, $selector);
            if($tariffObject->ID){
                $result = $tariffObject;
                $result->link = $objectTarifs->link;
                $result->free_options = $objectTarifs->free_options;
                return $result;
            }
        }
        return NULL;       
    }

    function getMontPrefix($months) {
        $ts = array(1);
        $tsa = array(2,3,4);
        $tsev = array(5,6,7,8,9,10,11,12);
        if(in_array($months, $ts)){
            return $months." месяц";
        } elseif(in_array($months, $tsa)) {
            return $months." месяца";
        } elseif(in_array($months, $tsev)){
            return $months." месяцев";
        }
    }