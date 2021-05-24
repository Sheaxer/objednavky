<?php

require_once ("helpers.php");
require_once (__DIR__ . "/../config/serviceConfig.php");

$data = load_data_from_json("../data.json");
$products = transform_to_products($data['products']);
$order = get_order_data();
// celkova cena objednavky
$total_price = 0.0;
// iteracia cez vsetky objednane produkty
foreach ($order['products'] as $ordered_product)
{
    $p  = $products[$ordered_product['id']];
    if(
        // produkt s id neexistuje
        ($p === null) ||
        // nie je dostupny pocet na sklade
        ($p['maxQty'] < intval($ordered_product['qty'])) ||
        // cena za kus na serveri nie je rovnaka ako cena za kus u zakaznika  - zle data
        (floatval($ordered_product['single_price']) !== floatval($p['price'])) ||
        // celkova cena za jeden produkt sa nerovna skutocnej cene - zle data
        (floatval($ordered_product['total_price']) !== (intval($ordered_product['qty']) * floatval($ordered_product['single_price']))) ||
        // nazov produktu na serveri a od zakaznika sa nezhoduje - zle data
        (strcmp($p['name'], $ordered_product['product_name']) !== 0)
    )
    {
        header('X-PHP-Response-Code: 500',true,500);
        $error['error'] = "cannot order the product";
        echo json_encode($error);
        die();
    }
    // celkova cena obednavky - pripocitame cenu za dany tovar
    $total_price+=floatval($ordered_product['total_price']);
    // znizime dostupne pocty na sklade o objednany tovar?
    // az ked cela objednavka prejde zapiseme zmeny do API / suboru
    $p['maxQty']  =  $p['maxQty'] - intval($ordered_product['qty']);
    $products[$ordered_product['id']] = $p;

}
//var_dump($products);
$order['total_price'] = $total_price;
//var_dump($total_price);
//$order['total_price'] = $total_price;
$order_service = getOrderServiceImpl();
// vlozenie objednavky do databazy
$order_service->createOrder($order);
$result['response'] = "order accepted";
header('X-PHP-Response-Code: 200',true,200);
echo json_encode($result);

$producs_after_order = transform_from_products($products);
// zapis zmenenych poctov spat do suboru
$data['products'] = $producs_after_order;
write_after_order("../data.json",$data);



