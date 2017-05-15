<?php

namespace Ajax\Controllers;

use Phalcon\Mvc\Controller;

class QueryController extends ControllerBase
{
    /**
     * /ajax/query/upc/xxxx
     */
    public function upcAction($upc)
    {
        $skuList = $this->skuService->getSkuListByUPC($upc);
        $this->response->setJsonContent(['status' => 'OK', 'data' => $skuList ]);

        return $this->response;
    }

    /**
     * /ajax/query/mpn/xxxx
     */
    public function mpnAction($mpn)
    {
        $skuList = $this->skuService->getSkuListByMPN($mpn);
        $this->response->setJsonContent(['status' => 'OK', 'data' => $skuList ]);

        return $this->response;
    }

    /**
     * /ajax/query/priceavail
     */
    public function priceAvailAction()
    {
        if ($this->request->isPost()) {
            $sku = $this->request->getPost('sku');

            try {
                // Make a xmlapi call to get price and availability
                $data = $this->priceAvailService->getPriceAvailability($sku);

                // $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Unknown supplier']);
                $this->response->setJsonContent(['status' => 'OK', 'data' => $data]);
            } catch (\Exception $e) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => $e->getMessage()]);
            }

            return $this->response;
        }
    }

    /**
     * /ajax/query/order?id=ORDER_NUMBER
     */
    public function orderAction()
    {
        $orderId = $this->request->getQuery('id');

        $order = $this->orderService->getOrder($orderId);
        $items = $this->orderService->getOrderItems($orderId);
        $shippingAddress = $this->orderService->getShippingAddress($orderId);

        if ($order) {
            $order['items'] = $items;
            $order['shippingAddress'] = $shippingAddress;

            $this->response->setJsonContent(['status' => 'OK', 'data' => $order]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Order not found']);
        }

        return $this->response;
    }

    /**
     * /ajax/query/mastersku?id=SKU
     */
    public function masterSkuAction()
    {
        $sku = $this->request->getQuery('id');

        $info = $this->skuService->getMasterSku($sku);

        if ($info) {
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'SKU not found']);
        }

        return $this->response;
    }

    /**
     * /ajax/query/tracking?id=ORDER_NUMBER|TRACKING_NUMBER
     */
    public function trackingAction()
    {
        // id can be OrderNumber OR TrackingNumber
        $id = $this->request->getQuery('id');

        $info = $this->shipmentService->getMasterTracking($id);

        if ($info) {
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Tracking not found']);
        }

        return $this->response;
    }

    /**
     * /ajax/query/shippingeasy?id=ORDER_NUMBER|TRACKING_NUMBER
     */
    public function shippingEasyAction()
    {
        // id can be OrderNumber OR TrackingNumber
        $id = $this->request->getQuery('id');

        $info = $this->shipmentService->getShippingEasy($id);

        if ($info) {
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Tracking not found']);
        }

        return $this->response;
    }
}
