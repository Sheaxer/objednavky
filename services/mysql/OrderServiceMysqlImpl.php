<?php
require_once (__DIR__ . "/../OrderService.php");

class OrderServiceMysqlImpl implements OrderService
{

    private $conn;

    function __construct(PDO $PDO)
    {
        $this->conn = $PDO;
    }

    public function createOrder(array $oder)
    {
        $query = "INSERT INTO orders (title, userId, userName, total_price) VALUES (:title, :userId, :userName, 
                                                                  :total_price)";
        $stm = $this->conn->prepare($query);
        $stm->bindParam(":title",$oder['title']);
        $stm->bindParam(":userId",$oder['userId']);
        $stm->bindParam(":userName",$oder['userName']);
        $stm->bindParam(":total_price", $oder['total_price']);

        $query2 = "INSERT INTO ordered_products (order_id, product_id, qty, price_per_one, total_price, product_name) 
        VALUES (:order_id, :product_id, :qty, :price_per_one, :total_price, :product_name)";

        $stm2 = $this->conn->prepare($query2);

        $this->conn->beginTransaction();

        $stm->execute();

        $added_id = intval($this->conn->lastInsertId());
        $stm2->bindParam(":order_id",$added_id);
        foreach ($oder['products'] as $product)
        {
            $stm2->bindParam(":product_id", $product['id']);
            $stm2->bindParam(":qty",  $product['qty']);
            $stm2->bindParam(":price_per_one", $product['single_price']);
            $stm2->bindParam(":total_price",$product['total_price']);
            $stm2->bindParam(":product_name", $product['product_name']);
            $stm2->execute();
        }
        $this->conn->commit();
    }
}