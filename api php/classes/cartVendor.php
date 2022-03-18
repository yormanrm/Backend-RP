<?php

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');


    require_once "../config/Connection.php";


    class CartVendor{

        public static function insertCartItemVendor($vendor_id, $product_id, $seller_id, $quantity){
            $db = new Connection();

            $existorder = mysqli_query($db, "SELECT order_id FROM ordersvendors WHERE vendor_id=$vendor_id AND status='pending'");
            $row = $existorder -> fetch_assoc();

            if (isset($row['order_id'])){
                $id_order = $row['order_id'];
                $q=("SELECT * FROM cartitemsvendors WHERE product_id=$product_id AND vendor_id=$vendor_id AND order_id = $id_order");
                $result = $db->query($q);
                if ($result->num_rows){
                    $updateQuantity = "UPDATE cartitemsvendors SET quantity=quantity+'".$quantity."' WHERE product_id=$product_id AND vendor_id=$vendor_id AND order_id = $id_order";
                    $db->query($updateQuantity);
                    if ($db->affected_rows)
                    {
                        return TRUE;
                    }
                    return FALSE;
                }
                $query = "INSERT INTO `cartitemsvendors`(`vendor_id`, `product_id`, `seller_id`, `quantity`, `ordercart`, `order_id`) VALUES ('".$vendor_id."', '".$product_id."', '".$seller_id."', '".$quantity."', 'in cart', '".$id_order."')";
                $db->query($query);
                if ($db->affected_rows) {
                    return TRUE;
                }
                return FALSE;

            }

            $neworder = "INSERT INTO `ordersvendors`(`vendor_id`, `status`, `registration_date`) VALUES ('".$vendor_id."', 'pending', CURDATE())";
            $db->query($neworder);
            if ($db->affected_rows) {
                $result2 = mysqli_query($db, "SELECT MAX(order_id) FROM ordersvendors WHERE vendor_id='".$vendor_id."' AND status='pending'");
                $row = mysqli_fetch_array($result2);
                $id_order2 = $row[0];
                $query2 = "INSERT INTO `cartitemsvendors`(`vendor_id`, `product_id`, `seller_id`, `quantity`, `ordercart`, `order_id`) VALUES ('".$vendor_id."', '".$product_id."', '".$seller_id."', '".$quantity."', 'in cart', '".$id_order2."')";
                $db->query($query2);
            }
            return TRUE;

        }

        public static function getCartItemsVendor($vendor_id){
            $db = new Connection();
            //$query = "SELECT cartitemsvendors.cartitem_id, cartitemsvendors.vendor_id, cartitemsvendors.order_id, cartitemsvendors.quantity, cartitemsvendors.ordercart, cartitemsvendors.product_id, cartitemsvendors.seller_id, products.product_name, products.quantity AS stock, products.price FROM `cartitemsvendors` inner join products on cartitemsvendors.product_id = products.product_id WHERE cartitemsvendors.vendor_id = $vendor_id AND cartitemsvendors.ordercart='in cart'";
            $query = "SELECT cartitemsvendors.cartitem_id, cartitemsvendors.vendor_id, cartitemsvendors.order_id, cartitemsvendors.quantity, cartitemsvendors.ordercart, cartitemsvendors.product_id, cartitemsvendors.seller_id, products.product_name, products.quantity AS stock, products.price,filesproducts.path 
            FROM `cartitemsvendors` inner join products on cartitemsvendors.product_id = products.product_id inner join filesproducts on filesproducts.product_id = products.product_id 
            WHERE cartitemsvendors.vendor_id = $vendor_id AND cartitemsvendors.ordercart='in cart' group by cartitem_id;";
            $result = $db->query($query);
            $data = [];
            if ($result->num_rows) {
                while ( ($row = $result->fetch_assoc()) ) {
                    $data[]=[
                        'cartitem_id' => $row['cartitem_id'],
                        'product_id' => $row['product_id'],
                        'vendor_id' => $row['vendor_id'],
                        'order_id' => $row['order_id'],
                        'seller_id' => $row['seller_id'],
                        'product_name' => $row['product_name'],
                        'price' => $row['price'],
                        'stock' => $row['stock'],
                        'quantity' => $row['quantity'],
                        'total' => $row['price']*$row['quantity'],
                        'path' => $row['path']
                    ];
                }
                return $data;
            }
            return $data;
        }

        public static function deleteCartItemVendor($cartitem_id){
            $db = new Connection();
            $query = "DELETE FROM `cartitemsvendors` WHERE `cartitemsvendors`.`cartitem_id`= $cartitem_id ";
            $db->query($query);
            if ($db->affected_rows >= 0) {
                return TRUE;
            }
            return FALSE;
        }

        public static function updateCartItemVendor($cartitem_id, $vendor_id, $quantity){
            $db = new Connection();
            $query = "UPDATE `cartitemsvendors` SET quantity='".$quantity."'WHERE cartitem_id=$cartitem_id AND vendor_id=$vendor_id";
            $db->query($query);
            if ($db->affected_rows)
            {

                return TRUE;
            }
            return FALSE;
        }

        public static function updateOrderVendor($vendor_id, $order_id, $status, $payorder, $total){
            $db = new Connection();
            $query = "UPDATE `ordersvendors` SET status='".$status."', payorder='".$payorder."', total='".$total."' WHERE order_id=$order_id AND vendor_id=$vendor_id";
            $db->query($query);
            if ($db->affected_rows)
            {
                $query2 = "UPDATE `cartitemsvendors` SET ordercart='".$status."' WHERE order_id=$order_id AND vendor_id=$vendor_id";
                $db->query($query2);
                return TRUE;
            }
            return FALSE;
        }

        public static function getPurchasesVendor($vendor_id){
            $db = new Connection();
            $query = "SELECT * FROM ordersvendors WHERE vendor_id = $vendor_id AND status='COMPLETED'";
            $result = $db->query($query);
            $data = [];
            if ($result->num_rows) {
                while ( ($row = $result->fetch_assoc()) ) {
                    $data[]=[
                        'order_id' => $row['order_id'],
                        'payorder' => $row['payorder'],
                        'name' => $row['name'],
                        'address' => $row['address'],                     
                        'email' => $row['email'],                     
                        'phone1' => $row['phone1'],                     
                        'phone2' => $row['phone2'],                     
                        'total' => $row['total'],                     
                        'status' => $row['status'],                     
                        'registration_date' => $row['registration_date'],                     
                    ];
                }
                return $data;
            }
            return $data;
        }

        public static function getSalesToVendors($vendor_id){
            $db = new Connection();
            $query = "SELECT cartitemsvendors.cartitem_id, cartitemsvendors.vendor_id, cartitemsvendors.order_id, cartitemsvendors.quantity, cartitemsvendors.ordercart, cartitemsvendors.product_id, cartitemsvendors.seller_id, products.product_name, products.quantity AS stock, products.price FROM `cartitemsvendors` inner join products on cartitemsvendors.product_id = products.product_id WHERE cartitemsvendors.seller_id = $vendor_id AND cartitemsvendors.ordercart='COMPLETED'";
            $result = $db->query($query);
            $data = [];
            if ($result->num_rows) {
                while ( ($row = $result->fetch_assoc()) ) {
                    $data[]=[
                        'cartitem_id' => $row['cartitem_id'],
                        'product_id' => $row['product_id'],
                        'vendor_id' => $row['vendor_id'],
                        'order_id' => $row['order_id'],
                        'seller_id' => $row['seller_id'],
                        'product_name' => $row['product_name'],
                        'price' => $row['price'],
                        'stock' => $row['stock'],
                        'quantity' => $row['quantity'],
                        'total' => $row['price']*$row['quantity']
                    ];
                }
                return $data;
            }
            return $data;
        }

        public static function getSalesToCustomers($vendor_id){
            $db = new Connection();
            $query = "SELECT cartitemscustomers.cartitem_id, cartitemscustomers.cust_id, cartitemscustomers.order_id, cartitemscustomers.quantity, cartitemscustomers.ordercart, cartitemscustomers.product_id, cartitemscustomers.seller_id, products.product_name, products.quantity AS stock, products.price FROM `cartitemscustomers` inner join products on cartitemscustomers.product_id = products.product_id WHERE cartitemscustomers.seller_id = $vendor_id AND cartitemscustomers.ordercart='COMPLETED'";
            $result = $db->query($query);
            $data = [];
            if ($result->num_rows) {
                while ( ($row = $result->fetch_assoc()) ) {
                    $data[]=[
                        'cartitem_id' => $row['cartitem_id'],
                        'product_id' => $row['product_id'],
                        'cust_id' => $row['cust_id'],
                        'order_id' => $row['order_id'],
                        'seller_id' => $row['seller_id'],
                        'product_name' => $row['product_name'],
                        'price' => $row['price'],
                        'stock' => $row['stock'],
                        'quantity' => $row['quantity'],
                        'total' => $row['price']*$row['quantity']
                    ];
                }
                return $data;
            }
            return $data;
        }




    }


    /*
    
            public static function insertCartItemVendor($vendor_id, $product_id, $quantity){
            
            $db = new Connection();
            $query = "INSERT INTO `cartitemsvendors`(`vendor_id`, `product_id`, `quantity`) VALUES ('".$vendor_id."', '".$product_id."', '".$quantity."')";
            $db->query($query);
            if ($db->affected_rows) {
                return TRUE;
            }
            return FALSE;

        }
    
    */