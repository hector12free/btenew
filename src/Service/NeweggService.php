<?php

namespace Service;

use Phalcon\Di\Injectable;

class NeweggService extends Injectable
{
    public function findSku($sku, $site)
    {
        $table = ($site == 'US') ? 'newegg_us_listings' : 'newegg_ca_listings';

        $sql = "SELECT * FROM $table WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);

        return $result;
    }
}
