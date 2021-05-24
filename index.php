<!DOCTYPE html>
<html lang="sk">
<head>
    <title>Objednávka produktov</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="functions.js"></script>
    <link rel="stylesheet" href="style1.css">
</head>
<body>

<?php
require_once(__DIR__ . "/scripts/helpers.php");

    $data = load_data_from_json("./data.json");



?>

<form method="post" action="" id="order_form">
    <h1>Objednávka</h1>
    <label id="title"> <?php echo $data['title'] ?></label> <br>
    <label id="user_name"><?php echo $data['userName'] ?> </label> <br>
    <input type="hidden" id="user_id" value="<?php echo $data['userId'] ?>">
    <label> Produkty:</label> <br>
    <table id="products_table">
        <thead>
            <tr>
                <th>Názov produktu</th>
                <th>Cena za kus</th>
                <th>Počet dostupných kusov</th>
                <th>Počet kusov na objednávku</th>
                <th> Celková cena</th>
            </tr>
        </thead>
        <tbody>
        <?php
            fill_table_row($data['products']);
        ?>
        </tbody>
    </table>
    <input type="submit" value="Potvrdiť objednávku">
</form>
<div id="mod"></div>

</body>
</html>

