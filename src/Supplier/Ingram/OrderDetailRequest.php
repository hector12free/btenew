<?php

namespace Supplier\Ingram;

use Toolkit\Utils;
use Supplier\Model\OrderStatusRequest as BaseRequest;

class OrderDetailRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function toXml()
    {
        $lines = array();
        // ...
        return Utils::formatXml(implode("\n", $lines));
    }
}
