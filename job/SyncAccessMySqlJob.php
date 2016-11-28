<?php

class SyncAccessMySqlJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
    {
        $this->accdb = $this->openAccessDB();

        $this->syncNeweggOrders();
        $this->syncAmazonCAOrders();
    }

    // Newegg orders

    protected function syncNeweggOrders()
    {
        $today = date('Y-m-d');

        $ordersInAccess = $this->getNeweggOrdersFromAccess();
        $ordersInMysql  = $this->getNeweggOrdersFromMysql();

        echo 'Newegg Orders: Access to Mysql', EOL;

        foreach ($ordersInAccess as $order) {
            try {
                $this->db->insertAsDict('purchase_order_log',
                    [
                        'channel'  => $order['channel'],
                        'sku'      => $order['sku'],
                        'orderid'  => $order['orderid'],
                        'ponumber' => $order['ponumber'],
                        'flag'     => $order['flag'],
                    ]
                );
                echo $order['orderid'], EOL;
            } catch (\Exception $e) {
                //echo $e->getMessage(), PHP_EOL;
            }
        }

        echo 'Newegg Orders: Mysql to Access', EOL;

        foreach ($ordersInMysql as $order) {
            $sku         = $order['sku'];
            $orderid     = $order['orderid'];
            $channel     = $order['channel'];
            $xpress      = 0;
            $stockStatus = $order['flag'];
            $qty         = 0;
            $supplier    = ' ';
            $ponum       = $order['ponumber'];
            $mfrpn       = ' ';

            $sql = "SELECT * FROM Newegg WHERE [PO #]='$orderid'";
            $result = $this->accdb->query($sql)->fetch();
            if ($result) {
                continue;
            }

            $sql = "INSERT INTO Newegg (
                        [Work Date],
                        [Channel],
                        [PO #],
                        [Xpress],
                        [Stock Status],
                        [Qty],
                        [Supplier],
                        [Supplier SKU],
                        [Mfr #],
                        [Supplier #],
                        [Remarks],
                        [RelatedSKU],
                        [Dimension]
                    )
                    VALUES (
                        '$today',
                        '$channel',
                        '$orderid',
                        $xpress,
                        '$stockStatus',
                        '$qty',
                        '$supplier',
                        '$sku',
                        '$mfrpn',
                        '$ponum',
                        '',
                        '',
                        ''
                    )";

            $ret = $this->accdb->exec($sql);

            if (!$ret) {
                print_r($this->accdb->errorInfo());
            }

            echo $orderid, EOL;
        }
    }

    protected function getNeweggOrdersFromAccess()
    {
        $orders = [];

        $today = date('Y-m-d');

        $sql = "SELECT * FROM Newegg WHERE [Work Date]='$today'";
        $sql = "SELECT * FROM Newegg";

        $result = $this->accdb->query($sql);
        if (!$result) {
            return $orders;
        }

        while ($row = $result->fetch()) {
            if (substr($row['Work Date'], 0, 10) != $today) {
                continue;
            }

            if (trim($row['Stock Status'])) {
                $orders[] = [
                    'channel'  => $row['Channel'],
                    'sku'      => $row['Supplier SKU'],
                    'orderid'  => $row['PO #'],
                    'ponumber' => $row['Supplier #'],
                    'flag'     => $row['Stock Status'],
                ];
            }
        }

        return $orders;
    }

    protected function getNeweggOrdersFromMysql()
    {
        $orders = [];

        $today = date('Y-m-d');

        $sql = "SELECT * FROM purchase_order_log WHERE channel='NeweggCA' AND date(time)='$today'";
        $result = $this->db->fetchAll($sql);

        foreach ($result as $order) {
            $orders[] = $order;
        }

        return $orders;
    }

    // Amazon CA orders

    protected function syncAmazonCAOrders()
    {
        $today = date('Y-m-d');

        $ordersInAccess = $this->getAmazonCAOrdersFromAccess();
        $ordersInMysql  = $this->getAmazonCAOrdersFromMysql();

        echo 'Amazon CA Orders: Access To Mysql', EOL;

        foreach ($ordersInAccess as $order) {
            try {
                $this->db->insertAsDict('purchase_order_log',
                    [
                        'sku'      => $order['sku'],
                        'orderid'  => $order['orderid'],
                        'ponumber' => $order['ponumber'],
                        'flag'     => $order['flag'],
                    ]
                );
                echo $order['orderid'], EOL;
            } catch (\Exception $e) {
                //echo $e->getMessage(), PHP_EOL;
            }
        }

        echo 'Amazon CA Orders: Mysql To Access', EOL;

        foreach ($ordersInMysql as $order) {
            $sku         = $order['sku'];
            $orderid     = $order['orderid'];
            $channel     = 'Amazon-ACA';
            $xpress      = 0;
            $stockStatus = $order['flag'];
            $qty         = 0;
            $supplier    = ' ';
            $ponum       = $order['ponumber'];
            $mfrpn       = ' ';

            $sql = "SELECT * FROM ca_order_notes WHERE [order_id]='$orderid'";
            $result = $this->accdb->query($sql)->fetch();
            if ($result) {
                continue;
            }

            $sql = "INSERT INTO ca_order_notes (
                        [date],
                        [order_id],
                        [Stock Status],
                        [Xpress],
                        [qty],
                        [supplier],
                        [supplier_sku],
                        [MPN],
                        [Supplier#],
                        [notes],
                        [related_sku],
                        [dimension]
                    )
                    VALUES (
                        '$today',
                        '$orderid',
                        '$stockStatus',
                        $xpress,
                        '$qty',
                        '$supplier',
                        '$sku',
                        '$mfrpn',
                        '$ponum',
                        '',
                        '',
                        ''
                    )";

            $ret = $this->accdb->exec($sql);

            if (!$ret) {
                print_r($this->accdb->errorInfo());
            }

            echo $orderid, EOL;
        }
    }

    protected function getAmazonCAOrdersFromAccess()
    {
        $orders = [];

        $today = date('Y-m-d');

        $sql = "SELECT * FROM ca_order_notes WHERE channel='Amazon-ACA' AND date='$today'";
        $sql = "SELECT * FROM ca_order_notes";

        $result = $this->accdb->query($sql);
        if (!$result) {
            return $orders;
        }

        while ($row = $result->fetch()) {
            if (substr($row['date'], 0, 10) != $today) {
                continue;
            }

            if (trim($row['Stock Status'])) {
                $orders[] = [
                    'channel'  => 'Amazon-ACA',
                    'sku'      => $row['supplier_sku'],
                    'orderid'  => $row['order_id'],
                    'ponumber' => $row['Supplier#'],
                    'flag'     => $row['Stock Status'],
                ];
            }
        }

        return $orders;
    }

    protected function getAmazonCAOrdersFromMysql()
    {
        $orders = [];

        $today = date('Y-m-d');

        $sql = "SELECT * FROM purchase_order_log WHERE channel='Amazon-ACA' AND date(time)='$today'";
        $result = $this->db->fetchAll($sql);

        foreach ($result as $order) {
            $orders[] = $order;
        }

        return $orders;
    }

    protected function openAccessDB()
    {
        $dbname = "Z:/Purchasing/General Purchase.accdb";

        if (gethostname() != 'BTELENOVO') {
            $dbname = "C:/Users/BTE/Desktop/General Purchase.accdb";
        }

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $db = new PDO($dsn);

        return $db;
    }
}

include __DIR__ . '/../public/init.php';

$job = new SyncAccessMySqlJob();
$job->run($argv);