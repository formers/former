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
 * Partial intl implementation in pure PHP.
 *
 * Implemented:
 * - grapheme_extract  - Extract a sequence of grapheme clusters from a text buffer, which must be encoded in UTF-8
 * - grapheme_stripos  - Find position (in grapheme units) of first occurrence of a case-insensitive string
 * - grapheme_stristr  - Returns part of haystack string from the first occurrence of case-insensitive needle to the end of haystack
 * - grapheme_strlen   - Get string length in grapheme units
 * - grapheme_strpos   - Find position (in grapheme units) of first occurrence of a string
 * - grapheme_strripos - Find position (in grapheme units) of last occurrence of a case-insensitive string
 * - grapheme_strrpos  - Find position (in grapheme units) of last occurrence of a string
 * - grapheme_strstr   - Returns part of haystack string from the first occurrence of needle to the end of haystack
 * - grapheme_substr   - Return part of a string
 */
class Intl
{
    static function grapheme_extract($s, $size, $type = GRAPHEME_EXTR_COUNT, $start = 0, &$next = 0)
    {
        if (is_array($s)) return !user_error(__METHOD__ . '() expects parameter 1 to be string, array given', E_USER_WARNING);

        $s     = (string) $s;
        $size  = (int) $size;
        $type  = (int) $type;
        $start = (int) $start;

        if ('' === $s || 0 > $size || 0 > $start || 0 > $type || 2 < $type) return false;
        if (0 === $size) return '';

        $next = $start;
        $s = substr($s, $start); //TODO: seek to the first character boundary when needed

        if (GRAPHEME_EXTR_COUNT === $type)
        {
            if ($size > 65635)
            {
                // Workaround PCRE limiting quantifiers to 65635.
                $rx = floor(sqrt($size));
                $size -= $rx * $rx; // This can't be greather than 65635: the native intl is limited to 2Gio strings
                $rx = '(?:' . GRAPHEME_CLUSTER_RX . "{{$rx}}){{$rx}}" . GRAPHEME_CLUSTER_RX . "{1,{$size}}";
            }
            else $rx = GRAPHEME_CLUSTER_RX . "{1,{$size}}";

            $s = preg_split("/({$rx})/u", $s, 2, PREG_SPLIT_DELIM_CAPTURE);
            $next += strlen($s[0]);
            $s = isset($s[1]) ? $s[1] : '';
        }
        else
        {
            //TODO
            return !user_error(__METHOD__ . '() with GRAPHEME_EXTR_MAXBYTES or GRAPHEME_EXTR_MAXCHARS is not implemented', E_USER_WARNING);
        }

        $next += strlen($s);

        return $s;
    }

    static function grapheme_strlen($s)
    {
        preg_replace('/' . GRAPHEME_CLUSTER_RX . '/u', '', $s, -1, $s);
        return $s;
    }

    static function grapheme_substr($s, $start, $len = 2147483647)
    {
        preg_match_all('/' . GRAPHEME_CLUSTER_RX . '/u', $s, $s);

        $slen = count($s[0]);
        $start = (int) $start;

        if (0 > $start) $start += $slen;
        if (0 > $start) return false;
        if ($start >= $slen) return false;

        $rem = $slen - $start;

        if (0 > $len) $len += $rem;
        if (0 === $len) return '';
        if (0 > $len) return false;
        if ($len > $rem) $len = $rem;

        return implode('', array_slice($s[0], $start, $len));
    }

    static function grapheme_substr_workaround62759($s, $start, $len)
    {
        // Intl based http://bugs.php.net/62759 and 55562 workaround

        if (2147483647 == $len) return grapheme_substr($s, $start);

        $slen = grapheme_strlen($s);
        $start = (int) $start;

        if (0 > $start) $start += $slen;
        if (0 > $start) return false;
        if ($start >= $slen) return false;

        $rem = $slen - $start;

        if (0 > $len) $len += $rem;
        if (0 === $len) return '';
        if (0 > $len) return false;
        if ($len > $rem) $len = $rem;

        return grapheme_substr($s, $start, $len);
    }

    static function grapheme_strpos  ($s, $needle, $offset = 0) {return self::grapheme_position($s, $needle, $offset, 0);}
    static function grapheme_stripos ($s, $needle, $offset = 0) {return self::grapheme_position($s, $needle, $offset, 1);}
    static function grapheme_strrpos ($s, $needle, $offset = 0) {return self::grapheme_position($s, $needle, $offset, 2);}
    static function grapheme_strripos($s, $needle, $offset = 0) {return self::grapheme_position($s, $needle, $offset, 3);}
    static function grapheme_stristr ($s, $needle, $before_needle = false) {return mb_stristr($s, $needle, $before_needle, 'UTF-8');}
    static function grapheme_strstr  ($s, $needle, $before_needle = false) {return mb_strstr ($s, $needle, $before_needle, 'UTF-8');}


    protected static function grapheme_position($s, $needle, $offset, $mode)
    {
        if ($offset > 0) $s = (string) self::grapheme_substr($s, $offset);
        else if ($offset < 0) $offset = 0;
        if ('' === (string) $needle) return false;
        if ('' === (string) $s) return false;

        switch ($mode)
        {
        case 0: $needle = iconv_strpos ($s, $needle, 0, 'UTF-8'); break;
        case 1: $needle = mb_stripos   ($s, $needle, 0, 'UTF-8'); break;
        case 2: $needle = iconv_strrpos($s, $needle,    'UTF-8'); break;
        default: $needle = mb_strripos ($s, $needle, 0, 'UTF-8'); break;
        }

        return $needle ? self::grapheme_strlen(iconv_substr($s, 0, $needle, 'UTF-8')) + $offset : $needle;
    }
}
