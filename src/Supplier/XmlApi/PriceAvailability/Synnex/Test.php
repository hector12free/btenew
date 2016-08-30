<?php

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { print_r($d); echo EOL; }

$client = new \Supplier\XmlApi\PriceAvailability\Synnex\Client($config['synnex']);

$request = $client->createRequest();
$request->addPartnum('SYN-5471137');
#$request->addPartnum('SYN-5497540');
pr($request->toXml());

$response = $client->sendRequest($request);
pr($response->getXmlDoc());

$items = $response->getItems();
#pr($items);


// Response-1
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Synnex/fixtures/price-response-1.xml');
#$response = new \Supplier\XmlApi\PriceAvailability\Synnex\Response($xml);
##$x = $response->parseXml();
#$x = $response->getItems();
##pr($x);
#

// Response-2
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Synnex/fixtures/price-response-2.xml');
#$response = new \Supplier\XmlApi\PriceAvailability\Synnex\Response($xml);
#$x = $response->getItems();
#pr($x);
