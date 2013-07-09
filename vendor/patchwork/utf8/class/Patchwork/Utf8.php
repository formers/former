<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2013 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

namespace Patchwork;

use Normalizer as n;

/**
 * UTF-8 Grapheme Cluster aware string manipulations implementing the quasi complete
 * set of native PHP string functions that need UTF-8 awareness and more.
 * Missing are printf-family functions.
 */
class Utf8
{
    protected static

    $commonCaseFold = array(
        array('µ','ſ',"\xCD\x85",'ς',"\xCF\x90","\xCF\x91","\xCF\x95","\xCF\x96","\xCF\xB0","\xCF\xB1","\xCF\xB5","\xE1\xBA\x9B","\xE1\xBE\xBE"),
        array('μ','s','ι',       'σ','β',       'θ',       'φ',       'π',       'κ',       'ρ',       'ε',       "\xE1\xB9\xA1",'ι'           )
    ),
    $cp1252 = array('','','','','','','','','','','','','','','','','','','','','','','','','','',''),
    $utf8   = array('€','‚','ƒ','„','…','†','‡','ˆ','‰','Š','‹','Œ','Ž','‘','’','“','”','•','–','—','˜','™','š','›','œ','ž','Ÿ');


    static function isUtf8($s)
    {
        return (bool) preg_match('//u', $s); // Since PHP 5.2.5, this also excludes invalid five and six bytes sequences
    }

    // Generic UTF-8 to ASCII transliteration

    static function toAscii($s)
    {
        if (preg_match("/[\x80-\xFF]/", $s))
        {
            static $translitExtra = false;
            $translitExtra or $translitExtra = self::getData('translit_extra');

            $s = n::normalize($s, n::NFKD);
            $s = preg_replace('/\p{Mn}+/u', '', $s);
            $s = str_replace($translitExtra[0], $translitExtra[1], $s);
            $s = iconv('UTF-8', 'ASCII' . ('glibc' !== ICONV_IMPL ? '//IGNORE' : '') . '//TRANSLIT', $s);
        }

        return $s;
    }

    // Unicode transformation for caseless matching
    // see http://unicode.org/reports/tr21/tr21-5.html

    static function strtocasefold($s, $full = true, $turkish = false)
    {
        $s = str_replace(self::$commonCaseFold[0], self::$commonCaseFold[1], $s);

        if ($turkish)
        {
            false !== strpos($s, 'I') && $s = str_replace('I', 'ı', $s);
            $full && false !== strpos($s, 'İ') && $s = str_replace('İ', 'i', $s);
        }

        if ($full)
        {
            static $fullCaseFold = false;
            $fullCaseFold || $fullCaseFold = self::getData('caseFolding_full');

            $s = str_replace($fullCaseFold[0], $fullCaseFold[1], $s);
        }

        return self::strtolower($s);
    }

    // Generic case sensitive collation support for self::strnatcmp()

    static function strtonatfold($s)
    {
        $s = n::normalize($s, n::NFD);
        return preg_replace('/\p{Mn}+/u', '', $s);
    }

    // PHP string functions that need UTF-8 awareness

    static function substr($s, $start, $len = 2147483647)
    {
/**/    if (extension_loaded('intl') && 'à' === grapheme_substr('éà', 1, -2))
/**/    {
            return PHP\Shim\Intl::grapheme_substr_workaround62759($s, $start, $len);
/**/    }
/**/    else
/**/    {
            return grapheme_substr($s, $start, $len);
/**/    }
    }

    static function strlen($s) {return grapheme_strlen($s);}
    static function strpos ($s, $needle, $offset = 0) {return grapheme_strpos ($s, $needle, $offset);}
    static function strrpos($s, $needle, $offset = 0) {return grapheme_strrpos($s, $needle, $offset);}

    static function stripos($s, $needle, $offset = 0)
    {
        // Don't use grapheme_stripos because of https://bugs.php.net/61860
        if ($offset < 0) $offset = 0;
        if (!$needle = mb_stripos($s, $needle, $offset, 'UTF-8')) return $needle;
        return grapheme_strlen(iconv_substr($s, 0, $needle, 'UTF-8'));
    }

    static function strripos($s, $needle, $offset = 0)
    {
        // Don't use grapheme_strripos because of https://bugs.php.net/61860
        if ($offset < 0) $offset = 0;
        if (!$needle = mb_strripos($s, $needle, $offset, 'UTF-8')) return $needle;
        return grapheme_strlen(iconv_substr($s, 0, $needle, 'UTF-8'));
    }

    static function stristr($s, $needle, $before_needle = false)
    {
        if ('' === (string) $needle) return false;
        return mb_stristr($s, $needle, $before_needle, 'UTF-8');
    }

    static function strstr  ($s, $needle, $before_needle = false) {return grapheme_strstr($s, $needle, $before_needle);}
    static function strrchr ($s, $needle, $before_needle = false) {return mb_strrchr ($s, $needle, $before_needle, 'UTF-8');}
    static function strrichr($s, $needle, $before_needle = false) {return mb_strrichr($s, $needle, $before_needle, 'UTF-8');}

    static function strtolower($s, $form = n::NFC) {if (n::isNormalized($s = mb_strtolower($s, 'UTF-8'), $form)) return $s; return n::normalize($s, $form);}
    static function strtoupper($s, $form = n::NFC) {if (n::isNormalized($s = mb_strtoupper($s, 'UTF-8'), $form)) return $s; return n::normalize($s, $form);}

    static function wordwrap($s, $width = 75, $break = "\n", $cut = false)
    {
        // This implementation could be extended to handle unicode word boundaries,
        // but that's enough work for today (see http://www.unicode.org/reports/tr29/)

        $width = (int) $width;
        $s = explode($break, $s);

        $iLen = count($s);
        $result = array();
        $line = '';
        $lineLen = 0;

        for ($i = 0; $i < $iLen; ++$i)
        {
            $words = explode(' ', $s[$i]);
            $line && $result[] = $line;
            $lineLen = grapheme_strlen($line);
            $jLen = count($words);

            for ($j = 0; $j < $jLen; ++$j)
            {
                $w = $words[$j];
                $wLen = grapheme_strlen($w);

                if ($lineLen + $wLen < $width)
                {
                    if ($j) $line .= ' ';
                    $line .= $w;
                    $lineLen += $wLen + 1;
                }
                else
                {
                    if ($j || $i) $result[] = $line;
                    $line = '';
                    $lineLen = 0;

                    if ($cut && $wLen > $width)
                    {
                        $w = self::str_split($w);

                        do
                        {
                            $result[] = implode('', array_slice($w, 0, $width));
                            $line = implode('', $w = array_slice($w, $width));
                            $lineLen = $wLen -= $width;
                        }
                        while ($wLen > $width);

                        $w = implode('', $w);
                    }

                    $line = $w;
                    $lineLen = $wLen;
                }
            }
        }

        $line && $result[] = $line;

        return implode($break, $result);
    }

    static function chr($c)
    {
        if (0x80 > $c %= 0x200000) return chr($c);
        if (0x800 > $c) return chr(0xC0 | $c>>6) . chr(0x80 | $c & 0x3F);
        if (0x10000 > $c) return chr(0xE0 | $c>>12) . chr(0x80 | $c>>6 & 0x3F) . chr(0x80 | $c & 0x3F);
        return chr(0xF0 | $c>>18) . chr(0x80 | $c>>12 & 0x3F) . chr(0x80 | $c>>6 & 0x3F) . chr(0x80 | $c & 0x3F);
    }

    static function count_chars($s, $mode = 0)
    {
        if (1 != $mode) user_error(__METHOD__ . '(): the only allowed $mode is 1', E_USER_WARNING);
        $s = self::str_split($s);
        return array_count_values($s);
    }

    static function ltrim($s, $charlist = INF)
    {
        $charlist = INF === $charlist ? '\s' : self::rxClass($charlist);
        return preg_replace("/^{$charlist}+/u", '', $s);
    }

    static function ord($s)
    {
        $a = ($s = unpack('C*', substr($s, 0, 4))) ? $s[1] : 0;
        if (0xF0 <= $a) return (($a - 0xF0)<<18) + (($s[2] - 0x80)<<12) + (($s[3] - 0x80)<<6) + $s[4] - 0x80;
        if (0xE0 <= $a) return (($a - 0xE0)<<12) + (($s[2] - 0x80)<<6) + $s[3] - 0x80;
        if (0xC0 <= $a) return (($a - 0xC0)<<6) + $s[2] - 0x80;
        return $a;
    }

    static function rtrim($s, $charlist = INF)
    {
        $charlist = INF === $charlist ? '\s' : self::rxClass($charlist);
        return preg_replace("/{$charlist}+$/u", '', $s);
    }

    static function trim($s, $charlist = INF) {return self::rtrim(self::ltrim($s, $charlist), $charlist);}

    static function str_ireplace($search, $replace, $subject, &$count = null)
    {
        $search = (array) $search;
        foreach ($search as &$s) $s = '' !== (string) $s ? '/' . preg_quote($s, '/') . '/ui' : '/^(?<=.)$/';
        $subject = preg_replace($search, $replace, $subject, -1, $replace);
        $count = $replace;
        return $subject;
    }

    static function str_pad($s, $len, $pad = ' ', $type = STR_PAD_RIGHT)
    {
        $slen = grapheme_strlen($s);
        if ($len <= $slen) return $s;

        $padlen = grapheme_strlen($pad);
        $freelen = $len - $slen;
        $len = $freelen % $padlen;

        if (STR_PAD_RIGHT == $type) return $s . str_repeat($pad, $freelen / $padlen) . ($len ? grapheme_substr($pad, 0, $len) : '');
        if (STR_PAD_LEFT  == $type) return      str_repeat($pad, $freelen / $padlen) . ($len ? grapheme_substr($pad, 0, $len) : '') . $s;
        if (STR_PAD_BOTH  == $type)
        {
            $freelen /= 2;

            $type = ceil($freelen);
            $len = $type % $padlen;
            $s .= str_repeat($pad, $type / $padlen) . ($len ? grapheme_substr($pad, 0, $len) : '');

            $type = floor($freelen);
            $len = $type % $padlen;
            return str_repeat($pad, $type / $padlen) . ($len ? grapheme_substr($pad, 0, $len) : '') . $s;
        }

        user_error(__METHOD__ . '(): Padding type has to be STR_PAD_LEFT, STR_PAD_RIGHT, or STR_PAD_BOTH.');
    }

    static function str_shuffle($s)
    {
        $s = self::str_split($s);
        shuffle($s);
        return implode('', $s);
    }

    static function str_split($s, $len = 1)
    {
        if (1 > $len = (int) $len)
        {
            $len = func_get_arg(1);
            return str_split($s, $len);
        }

/**/    if (extension_loaded('intl'))
/**/    {
            $a = array();
            $p = 0;
            $l = strlen($s);

            while ($p < $l) $a[] = grapheme_extract($s, 1, GRAPHEME_EXTR_COUNT, $p, $p);
/**/    }
/**/    else
/**/    {
            preg_match_all('/' . GRAPHEME_CLUSTER_RX . '/u', $s, $a);
            $a = $a[0];
/**/    }

        if (1 == $len) return $a;

        $s = array();
        $p = -1;

        foreach ($a as $l => $a)
        {
            if ($l % $len) $s[$p] .= $a;
            else $s[++$p] = $a;
        }

        return $s;
    }

    static function str_word_count($s, $format = 0, $charlist = '')
    {
        $charlist = self::rxClass($charlist, '\pL');
        $s = preg_split("/({$charlist}+(?:[\p{Pd}’']{$charlist}+)*)/u", $s, -1, PREG_SPLIT_DELIM_CAPTURE);

        $charlist = array();
        $len = count($s);

        if (1 == $format) for ($i = 1; $i < $len; $i+=2) $charlist[] = $s[$i];
        else if (2 == $format)
        {
            $offset = grapheme_strlen($s[0]);
            for ($i = 1; $i < $len; $i+=2)
            {
                $charlist[$offset] = $s[$i];
                $offset += grapheme_strlen($s[$i]) + grapheme_strlen($s[$i+1]);
            }
        }
        else $charlist = ($len - 1) / 2;

        return $charlist;
    }

    static function strcmp       ($a, $b) {return (string) $a === (string) $b ? 0 : strcmp(n::normalize($a, n::NFD), n::normalize($b, n::NFD));}
    static function strnatcmp    ($a, $b) {return (string) $a === (string) $b ? 0 : strnatcmp(self::strtonatfold($a), self::strtonatfold($b));}
    static function strcasecmp   ($a, $b) {return self::strcmp   (self::strtocasefold($a), self::strtocasefold($b));}
    static function strnatcasecmp($a, $b) {return self::strnatcmp(self::strtocasefold($a), self::strtocasefold($b));}
    static function strncasecmp  ($a, $b, $len) {return self::strncmp(self::strtocasefold($a), self::strtocasefold($b), $len);}
    static function strncmp      ($a, $b, $len) {return self::strcmp(self::substr($a, 0, $len), self::substr($b, 0, $len));}

    static function strcspn($s, $charlist, $start = 0, $len = 2147483647)
    {
        if ('' === (string) $charlist) return null;
        if ($start || 2147483647 != $len) $s = self::substr($s, $start, $len);

        return preg_match('/^(.*?)' . self::rxClass($charlist) . '/us', $s, $len) ? grapheme_strlen($len[1]) : grapheme_strlen($s);
    }

    static function strpbrk($s, $charlist)
    {
        if (preg_match('/' . self::rxClass($charlist) . '/us', $s, $m)) return substr($s, strpos($s, $m[0]));
        else return false;
    }

    static function strrev($s)
    {
        $s = self::str_split($s);
        return implode('', array_reverse($s));
    }

    static function strspn($s, $mask, $start = 0, $len = 2147483647)
    {
        if ($start || 2147483647 != $len) $s = self::substr($s, $start, $len);
        return preg_match('/^' . self::rxClass($mask) . '+/u', $s, $s) ? grapheme_strlen($s[0]) : 0;
    }

    static function strtr($s, $from, $to = INF)
    {
        if (INF !== $to)
        {
            $from = self::str_split($from);
            $to   = self::str_split($to);

            $a = count($from);
            $b = count($to);

                 if ($a > $b) $from = array_slice($from, 0, $b);
            else if ($a < $b) $to   = array_slice($to  , 0, $a);

            $from = array_combine($from, $to);
        }

        return strtr($s, $from);
    }

    static function substr_compare($a, $b, $offset, $len = 2147483647, $i = 0)
    {
        $a = self::substr($a, $offset, $len);
        return $i ? self::strcasecmp($a, $b) : self::strcmp($a, $b);
    }

    static function substr_count($s, $needle, $offset = 0, $len = 2147483647)
    {
        return substr_count(self::substr($s, $offset, $len), $needle);
    }

    static function substr_replace($s, $replace, $start, $len = 2147483647)
    {
        $s       = self::str_split($s);
        $replace = self::str_split($replace);
        array_splice($s, $start, $len, $replace);
        return implode('', $s);
    }

    static function ucfirst($s)
    {
        $c = iconv_substr($s, 0, 1, 'UTF-8');
        return self::ucwords($c) . substr($s, strlen($c));
    }

    static function lcfirst($s)
    {
        $c = iconv_substr($s, 0, 1, 'UTF-8');
        return mb_strtolower($c, 'UTF-8') . substr($s, strlen($c));
    }

    static function ucwords($s)
    {
        return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
    }

    static function number_format($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',')
    {
/**/    if (PHP_VERSION_ID < 50400)
/**/    {
            if (isset($thousands_sep[1]) || isset($dec_point[1]))
            {
                return str_replace(
                    array('.', ','),
                    array($dec_point, $thousands_sep),
                    number_format($number, $decimals, '.', ',')
                );
            }
/**/    }

        return number_format($number, $decimals, $dec_point, $thousands_sep);
    }

    static function utf8_encode($s)
    {
        $s = utf8_encode($s);
        if (false === strpos($s, "\xC2")) return $s;
        else return str_replace(self::$cp1252, self::$utf8, $s);
    }

    static function utf8_decode($s)
    {
        $s = str_replace(self::$utf8, self::$cp1252, $s);
        return utf8_decode($s);
    }


    protected static function rxClass($s, $class = '')
    {
        $class = array($class);

        foreach (self::str_split($s) as $s)
        {
            if ('-' === $s) $class[0] = '-' . $class[0];
            else if (!isset($s[2])) $class[0] .= preg_quote($s, '/');
            else if (1 === iconv_strlen($s, 'UTF-8')) $class[0] .= $s;
            else $class[] = $s;
        }

        $class[0] = '[' . $class[0] . ']';

        if (1 === count($class)) return $class[0];
        else return '(?:' . implode('|', $class) . ')';
    }

    protected static function getData($file)
    {
        $file = __DIR__ . '/Utf8/data/' . $file . '.ser';
        if (file_exists($file)) return unserialize(file_get_contents($file));
        else return false;
    }
}
