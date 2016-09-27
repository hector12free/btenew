<?php

namespace Utility;

class CanadaProvince
{
    static $names = array (
        'Alberta',
        'British Columbia',
        'Manitoba',
        'Saskatchewan',
        'Québec',
        'Quebec',
        'Nova Scotia',
        'Newfoundland',
        'New Brunsiwck',
        'Yukon',
        'Nunavut',
        'Prince Edward Island',
        'Ontario',
        'Northwest Territories'
    );

    static $codes = array ('AB','BC','MB','SK','QC','QC', 'NS','NL','NB','YT','NU','PEI','ON','NT');

    public static function codeToName($code)
    {
        $map = array_combine(self::$codes, self::$names);
        $code = strtoupper($code);
        return isset($map[$code]) ? $map[$code] : $code;
    }

    public static function nameToCode($name)
    {
        $map = array_combine(self::$names, self::$codes);
        $key = ucwords(mb_strtolower($name));
        return isset($map[$key]) ? $map[$key] : $name;
    }
}

//echo CanadaProvince::codeToName('qc');
//echo CanadaProvince::nameToCode('Québec');
//echo CanadaProvince::nameToCode('british columbia');
//echo CanadaProvince::nameToCode('nova scotia');
