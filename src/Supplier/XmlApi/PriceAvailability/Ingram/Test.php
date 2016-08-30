<?php

const EOL = PHP_EOL;

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { print_r($d); echo EOL; }

$client = new \Supplier\XmlApi\PriceAvailability\Ingram\Client($config['ingram']);

$request = $client->createRequest();
$request->addPartnum('ING-36438W');
$request->addPartnum('ING-26129Z');
$request->addPartnum('ING-75873Z');
#pr($request->toXml());
$response = $client->sendRequest($request);
#pr($response);
$items = $response->getItems();
pr($items);


// Response-1
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Ingram/fixtures/ing-pna-response-2.xml');
#$response = new \Supplier\XmlApi\PriceAvailability\Ingram\Response($xml);
#$x = $response->getItems();
#if ($response->getStatus()) { pr($response->getStatus()); pr($response->error); }
#pr($x);