<?php

namespace Marketplace\Amazon\Feeds;

class PriceQtyUpdateFile extends FlatFile
{
    protected $columns  = ['sku', 'price', 'quantity'];

    protected $columns2 = [
        'sku',
        'price',
        'minimum-seller-allowed-price',
        'maximum-seller-allowed-price',
        'quantity',
        'leadtime-to-ship'
    ];
}
