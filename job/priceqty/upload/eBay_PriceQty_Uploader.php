<?php

use Toolkit\File;

class eBay_PriceQty_Uploader extends PriceQty_Uploader
{
    public function run($argv = [])
    {
        try {
            $this->upload();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function upload()
    {
        $filename = Filenames::get('ebay.odo.priceqty');
        if (file_exists($filename)) {
            $client = new Marketplace\eBay\Client('odo');
            $this->updatePriceQty($client, $filename);
            File::archive($filename);
        }

        $filename = Filenames::get('ebay.gfs.priceqty');
        if (file_exists($filename)) {
            $client = new Marketplace\eBay\Client('gfs');
            $this->updatePriceQty($client, $filename);
            File::archive($filename);
        }
    }

    protected function updatePriceQty($client, $filename)
    {
        $items = $this->csvToArray($filename);

        foreach ($items as $item) {
            $data = [
                'ItemID'   => $item['item_id'],
                'Quantity' => $item['quantity'],
                'Price'    => $item['price'],
            ];
            $res = $client->reviseInventoryStatus($data);
        }
    }
}
