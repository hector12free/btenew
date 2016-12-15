<?php

namespace Shipment;

class AmazonShipmentFile
{
    protected $handle;
    protected $filename;
    protected $csvtitle;
    protected $delimiter = "\t";

    public function __construct($country)
    {
        if ($country == 'CA') {
            $this->filename = 'w:/out/shipping/amazon_ca_shipment.txt';

            if (gethostname() != 'BTELENOVO') {
                $this->filename = 'E:/BTE/shipping/amazon_ca_shipment.txt';
            }

            $this->csvtitle = [
                'order-id',
                'order-item-id',
                'quantity',
                'ship-date',
                'carrier-code',
                'carrier-name',
                'tracking-number',
                'ship-method',
                'site'
            ];
        }

        if ($country == 'US') {
            $this->filename = 'w:/out/shipping/amazon_us_shipment.txt';

            if (gethostname() != 'BTELENOVO') {
                $this->filename = 'E:/BTE/shipping/amazon_us_shipment.txt';
            }

            $this->csvtitle = [
                'order-id',
                'order-item-id',
                'quantity',
                'ship-date',
                'carrier-code',
                'carrier-name',
                'tracking-number',
                'ship-method'
            ];
        }
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    public function write($data)
    {
        if (!$this->handle) {
            if (file_exists($this->filename)) {
                $this->handle = fopen($this->filename, 'a');
            } else {
                $this->handle = fopen($this->filename, 'w');
                fputcsv($this->handle, $this->csvtitle, $this->delimiter);
            }
        }

        if (count($data) != count($this->csvtitle)) {
            throw new \Exception('Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data, $this->delimiter);
    }
}