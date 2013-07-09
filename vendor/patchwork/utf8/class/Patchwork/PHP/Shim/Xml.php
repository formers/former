<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2013 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

namespace Patchwork\PHP\Shim;

/**
 * utf8_encode/decode
 */
class Xml
{
    static function utf8_encode($s)
    {
        $len = strlen($s);
        $e = $s . $s;

        for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) switch (true)
        {
        case $s[$i] < "\x80": $e[$j] = $s[$i]; break;
        case $s[$i] < "\xC0": $e[$j] = "\xC2"; $e[++$j] = $s[$i]; break;
        default:              $e[$j] = "\xC3"; $e[++$j] = chr(ord($s[$i]) - 64); break;
        }

        return substr($e, 0, $j);
    }

    static function utf8_decode($s)
    {
        $len = strlen($s);

        for ($i = 0, $j = 0; $i < $len; ++$i, ++$j)
        {
            switch ($s[$i] & "\xF0")
            {
            case "\xC0":
            case "\xD0":
                $c = (ord($s[$i] & "\x1F") << 6) | ord($s[++$i] & "\x3F");
                $s[$j] = $c < 256 ? chr($c) : '?';
                break;

            case "\xF0": ++$i;
            case "\xE0":
                $s[$j] = '?';
                $i += 2;
                break;

            default:
                $s[$j] = $s[$i];
            }
        }

        return substr($s, 0, $j);
    }
}
