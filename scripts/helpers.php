<?php
// odstranenie trailing comma v jsone
function remove_trailing_commas($json_string): string
{
    $sanitizedString = preg_replace("/,\s*}/", "}", $json_string);
    $sanitizedString = preg_replace("/,\s*]/","]",$sanitizedString);
    return $sanitizedString;
}
// nahranie dat z jsonu do tabuliek
function load_data_from_json($file_name): array
{
    $data_file = file_get_contents($file_name);
    $data_file = remove_trailing_commas($data_file);
    return json_decode($data_file,true);
}
// vytvorenie tabulky objednavky
function fill_table_row($products)
{
    foreach ($products as $product)
    {
        echo "<tr><td><input type='hidden' value='". $product['id']. "'>".$product['name']."</td><td>".
            $product['price']. "</td><td>" . strval($product['maxQty']). "</td><td> <input type='number' step='1' value='0' min='0' max='".
            $product['maxQty'] ."'></td><td>" . number_format(0,2) .
            "</td></tr>";
    }
}
// ziskanie objednavky z json ajaxu
function get_order_data(): ?array
{
    $content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
    $object = null;
    if (stripos($content_type, 'application/json') !== false) {
        $object = json_decode(file_get_contents("php://input"), true);
    }
    return $object;
}
// transformacia na pole productov accessible pomocou id, umozni to lahsi search
function transform_to_products($input): ?array
{
    $products = [];
    foreach ($input as $item)
    {
        $p = [];
        $p['price'] = $item['price'];
        $p['maxQty'] = $item['maxQty'];
        $p['name'] = $item['name'];
        $products[$item['id']] = $p;
    }
    return $products;
}
// transformacia spat
function transform_from_products($products): ?array
{
    $newProducts = [];
    foreach($products as $id=> $product)
    {
        //var_dump($id);
        //var_dump($product);
        $p = [];
        $p['id'] = $id;
        $p['name'] = $product['name'];
        $p['price'] = $product['price'];
        $p['maxQty'] = $product['maxQty'];
        array_push($newProducts,$p);
    }
    return $newProducts;
}

function write_after_order($path, $val)
{
    $myfile = fopen($path, "w");
    fwrite($myfile, json_encode($val, JSON_UNESCAPED_UNICODE));
    fclose($myfile);
}