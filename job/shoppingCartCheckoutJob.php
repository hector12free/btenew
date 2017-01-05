<?php

include 'classes/Job.php';

use Supplier\Supplier;
use Supplier\Model\Order;

class ShoppingCartCheckoutJob extends Job
{
    public function __construct()
    {
        parent::__construct();

        $this->shoppingCartService = $this->di->get('shoppingCartService');
    }

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        // The items will be shipped to us, get BTE shipping address
        $shippingAddress = $this->getShippingAddress();

        // Then get orders in shopping cart, the orders are grouped by suppliers
        $shoppingCartOrders = $this->getShoppingCartOrders();

        // Export report first, so we can display it right away
        $this->exportShoppingCartOrders($shoppingCartOrders);

        // Send the orders to suppliers
        foreach ($shoppingCartOrders as $supplier => $orders) {
            $info = [];
            $info['orderId'] = $supplier.'-'.date('Ymd-Hi');
            $info['branch'] = $this->getDefaultBranch($supplier);

            $this->log($info['orderId']);
           #$this->log(print_r(array_column($orders, 'sku'), true));
            $this->log(print_r($orders, true));

            $order = new Order($info);
            $order->setAddress($shippingAddress);
            $order->addItems($orders);

            $client = Supplier::createClient($supplier);
            if ($client) {
#               $result = $client->purchaseOrder($order);

               #don't do this, always keep orders in shopping cart
               #$this->removeOrdersInShoppingCart($orders);
            }
        }

        $this->markOrderAsCheckedout();
    }

    protected function getShoppingCartOrders()
    {
        $result = [];

        $orders = $this->shoppingCartService->getPendingOrders();

        foreach ($orders as $order) {
            $parts = explode('-', $order['sku']);
            $supplier = strtoupper($parts[0]);
            $result[$supplier][] = $order;
        }

        return $result;
    }

    protected function markOrderAsCheckedout()
    {
        $this->shoppingCartService->markOrderAsCheckedout(null);
    }

    /**
     * remove orders in shopping cart (DISABLED)
     */
    protected function removeOrdersInShoppingCart($orders)
    {
        #foreach ($orders as $order) {
        #    $orderId = $order['orderId'];
        #    $this->shoppingCartService->removeOrder($orderId);
        #}
    }

    /**
     * export shopping cart orders into csv file
     */
    protected function exportShoppingCartOrders($shoppingCartOrders)
    {
        $fp = fopen('W:/out/purchasing/shopping-cart.csv', 'w');

        fputcsv($fp, ['sku', 'order_id']);

        foreach ($shoppingCartOrders as $supplier => $orders) {
            foreach ($orders as $order) {
                fputcsv($fp, [ $order['sku'], $order['orderId'] ]);
            }
        }

        fclose($fp);
    }

    /**
     * get BTE shipping address from config file
     */
    protected function getShippingAddress()
    {
        $bte = $this->di->get('config')->bte;

        $address['buyer']      = $bte->contact;
        $address['buyer']      = $bte->name;
        $address['address']    = $bte->address;
        $address['city']       = $bte->city;
        $address['province']   = $bte->province;
        $address['postalcode'] = $bte->zipcode;
        $address['country']    = $bte->country;
        $address['phone']      = $bte->phone;
        $address['email']      = $bte->email;

        return $address;
    }

    /**
     * get default branch of each supplier
     */
    protected function getDefaultBranch($supplier)
    {
        $defaultBranches = [
            \Supplier\Prefix::DH       => 'Toronto',
            \Supplier\Prefix::INGRAM   => \Supplier\Ingram\Warehouse::TORONTO, // 40
            \Supplier\Prefix::SYNNEX   => \Supplier\Synnex\Warehouse::MARKHAM, // 57
            \Supplier\Prefix::TECHDATA => \Supplier\Techdata\Warehouse::MISSISSAUGA, // A1
        ];

        return isset($defaultBranches[$supplier]) ? $defaultBranches[$supplier] : '';
    }
}

include __DIR__ . '/../public/init.php';

$job = new ShoppingCartCheckoutJob();
$job->run($argv);
