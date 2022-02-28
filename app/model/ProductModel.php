<?php

class ProductModel
{
    private $table = 'tb_product';
    private $db;
    private $invoe;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllData()
    {
        $sql = "SELECT * FROM " . $this->table;
        return $this->db->getAll($sql);
    }

    public function addDataProduct($data)
    {
        $extension = array('png', 'jpg');
        $productname = $data['productname'];
        $image = $_FILES['productimage']['name'];
        $desc = $data['description'];
        $qty = $data['quantity'];
        $price = $data['price'];
        $date = date('Y-m-d');

        $temp = $_FILES['productimage']['tmp_name'];
        $target_dir = 'upload/' . $image;
        $ext = pathinfo($image, PATHINFO_EXTENSION);

        if (empty($image)) {
            echo "Image must be filled";
        } else if (!in_array($ext, $extension)) {
            echo "File must be of type png or jpg";
        } else {
            if (file_exists($target_dir)) {
                echo "Sorry, image file already exist";
            } else {
                $sql = "INSERT INTO tb_product VALUES('','$productname','$image','$desc',$qty,$price,'$date')";
                if ($sql) {
                    move_uploaded_file($temp, "uploads/" . $image);
                    return $this->db->runSQL($sql);
                }
            }
        }
    }

    public function getItemById($id)
    {
        $sql = "SELECT p.idproduct, p.name, p.price, p.quantity FROM tb_product AS p WHERE p.idproduct = '$id'";
        return $this->db->getItemByID($sql);
    }

    public function search($search)
    {
        $sql = "SELECT * FROM tb_product AS p WHERE p.name LIKE '%" . $search . "%'";
        return $this->db->getAll($sql);
    }

    public function updateQty($id, $qty)
    {
        try {
            //code...
            $sql = "UPDATE tb_product SET quantity = $qty WHERE idproduct= '$id'";
            return $this->db->runSQL($sql);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function transaction($iduser, $payment, $total)
    {
        // $row = $this->getLastInvoice();
        // $number = substr($row['invoice_number'], 7);
        $date = date('Y-m-d');
        $invoice = $this->generateInvoice();
        // if ($this->getLastInvoice() == null) {
        //     $this->invoe = 'T' . date('y') . date('m') . str_pad(1, 3, '0', STR_PAD_LEFT);
        // } else {
        //     $this->invoe = 'T' . date('y') . date('m') . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        // }
        $sql = "INSERT INTO tb_transaction VALUES ('$invoice', $iduser, $payment, $total, '$date')";
        return $this->db->runSQL($sql);
    }

    private function getLastInvoice()
    {
        $query = "SELECT * FROM tb_transaction ORDER BY invoice_number DESC LIMIT 1";
        return $this->db->getItem($query);
    }

    public function addTransactionProduct($idproduct, $qty)
    {
        $invoice = $this->generateInvoice();
        $date = date('Y-m-d');
        $sql = "INSERT INTO tb_product_transaction VALUES ('','$idproduct','$invoice', $qty, '$date')";
        // Service::show($sql);
        // exit;
        return $this->db->runSQL($sql);
    }

    private function generateInvoice()
    {
        $row = $this->getLastInvoice();
        $number = substr($row['invoice_number'], 7);
        if ($this->getLastInvoice() == null) {
            $invoe = 'T' . date('y') . date('m') . str_pad(1, 3, '0', STR_PAD_LEFT);
        } else {
            $invoe = 'T' . date('y') . date('m') . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        }

        return $invoe;
    }
}
