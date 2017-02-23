<?php

class EShipper_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('eshipper.tracking');

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        $columns = fgetcsv($fp);
        /*
            Ship Date,
            Transaction #,
            Reference,
            Carrier,
            Service,
            Package Name,
            # of Packages,
            ShipFrom Company,
            ShipFrom Address,
            ShipFrom City,
            ShipFrom Province,
            ShipFrom PostalCode/Zip,
            ShipFrom Country,
            ShipTo Company,
            ShipTo Address,
            ShipTo City,
            ShipTo Province,
            ShipTo PostalCode/Zip,
            ShipTo Country,
            Actual Weight(lbs),
            Dim Weight(lbs),
            Master Tracking #,
            Tracking #s,
            Is Residential,
            COD value,
            Payment type,
            Insurance Amount,
            POD: Date of Delivery,
            POD: Signed By,
            Status,
            Base Charge,
            Fuel Surcharge,
            Surcharge1 Name,
            Surcharge1 Charge,
            Surcharge2 Name,
            Surcharge2 Charge,
            Surcharge3 Name,
            Surcharge3 Charge,
            Surcharge4 Name,
            Surcharge4 Charge,
            Surcharge5 Name,
            Surcharge5 Charge,
            Surcharge6 Name,
            Surcharge6 Charge,
            Surcharge7 Name,
            Surcharge7 Charge,
            Surcharge8 Name,
            Surcharge8 Charge,
            Surcharge9 Name,
            Surcharge9 Charge,
            Surcharge10 Name,
            Surcharge10 Charge,
            Total Surcharges,
            Total Charge,
            Currency
        */

        while (($values = fgetcsv($fp))!== FALSE) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__ . print_r($values, true));
                continue;
            }

            $fields = array_combine($columns, $values);

            $orderId        = $fields['Reference'];
            $trackingNumber = $fields['Tracking #s'];
            $shipDate       = $fields['Ship Date'];
            $carrier        = $fields['Carrier'];
            $shipMethod     = $fields['Service'];

            $this->saveToDb([
                'orderId'        => $orderId,
                'shipDate'       => $shipDate,
                'carrier'        => $carrier,
                'shipMethod'     => $shipMethod,
                'trackingNumber' => $trackingNumber,
                'sender'         => 'eShipper',
            ]);
        }

        fclose ($fp);
    }
}
