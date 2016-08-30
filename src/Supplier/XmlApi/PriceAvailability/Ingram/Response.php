<?php

namespace Supplier\XmlApi\PriceAvailability\Ingram;

class Response
{
    /**
     * @var string
     */
    protected $xmldoc;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var array
     */
    protected $items;

    public function __construct($xmldoc)
    {
        $this->xmldoc = $xmldoc;
        $this->parseXml();
    }

    public function getXmlDoc()
    {
        return $this->xmldoc;
    }

    /**
     * @return array
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $this->items = array();

        $this->status = strval($xml->TransactionHeader->ErrorStatus['ErrorNumber']);
        if (!empty($this->status)) {
            $this->error = strval($xml->TransactionHeader->ErrorStatus);
            return $this->items;
        }

        /**
         * $item = [
         *     'sku'   => '...',
         *     'price' => '...',
         *     'avail' => [
         *         [ 'branch' => 'BRANCH-1', 'qty' => 1 ],
         *         [ 'branch' => 'BRANCH-1', 'qty' => 2 ],
         *         [ 'branch' => 'BRANCH-1', 'qty' => 3 ],
         *     ]
         * ];
         */
        foreach ($xml->PriceAndAvailability as $x) {
            $item = [];
            $item['sku'] = 'ING-'.strval($x['SKU']);
            $item['price'] = strval($x->Price);
            $item['avail'] = [];

            foreach ($x->Branch as $branch) {
                #$branchID   = strval($branch['ID']);
                #$branchName = strval($branch['Name']);
                if ($branch->Availability > 0) {
                    $item['avail'][] = [
                        'branch' => strval($branch['Name']),
                        'qty'    => strval($branch->Availability),
                    ];
                }
            }

            $this->items[] = $item;
        }

        $this->status = 'OK';

        return $this->items;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items[0];
    }
}
