<?php

namespace Supplier\XmlApi\PurchaseOrder\Ingram;

use Supplier\XmlApi\PurchaseOrder\Client as PurchaseOrderClient;

class Client extends PurchaseOrderClient
{
    const PROD_URL = 'https://newport.ingrammicro.com/mustang';
    const TEST_URL = 'https://newport.ingrammicro.com/mustang';

    /**
     * @param array|null $order
     */
    public function createRequest($order = null)
    {
        /**
         * @var Supplier\XmlApi\PurchaseOrder\Ingram\Request
         */
        $request = new Request($this->config);

        if (!empty($order)) {
            $request->addOrder($order);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\PurchaseOrder\Ingram\Request $request
     */
    public function sendRequest($request)
    {
        $url = $this->getEndpoint();

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

        /**
         * @var Supplier\XmlApi\PurchaseOrder\Ingram\Response
         */
        $response = new Response($res);

        $this->saveLog($url, $request, $response);

        return $response;
    }

    public function getEndpoint()
    {
        return self::TEST_URL;
        return self::PROD_URL;
    }
}
