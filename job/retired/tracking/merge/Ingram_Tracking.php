<?php

class Ingram_Tracking extends TrackingJob
{
    public function getStatus()
    {
        return 1; // 1-enabled, 0-disabled
    }

    public function merge()
    {
    }

    public function download()
    {
    }
}