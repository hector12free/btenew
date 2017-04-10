<?php

abstract class OrderTrigger extends Job
{
    protected $orders;
    protected $priority = 0; // Trigger with smaller priority runs first
                             // 0 to disable the trigger

    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    protected function getSku($sku)
    {
        $parts = explode('-', $sku);
        array_shift($parts);
        return implode('-', $parts);
    }

    protected function getSupplier($sku)
    {
        $names = [
            'AS'  => 'ASI',
            'SYN' => 'Synnex',
            'ING' => 'Ingram Micro',
            'EP'  => 'Eprom',
            'TD'  => 'Techdata',
            'TAK' => 'Taknology',
            'SP'  => 'Supercom',
        ];

        $parts = explode('-', $sku);
        $prefix = $parts[0];

        return isset($names[$prefix]) ? $names[$prefix] : $prefix;
    }
}
