<?php // vi: set fenc=utf-8 ts=4 sw=4 et:
/*
 * Copyright (C) 2013 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

namespace Patchwork\Utf8;

use Normalizer as n;
use Patchwork\Utf8 as u;


class Bootup
{
    static function initAll()
    {
        self::initUtf8Encode();
        self::initMbstring();
        self::initIconv();
        self::initExif();
        self::initIntl();
        self::initLocale();
    }

    static function initUtf8Encode()
    {
        function_exists('utf8_encode') or require __DIR__ . '/Bootup/utf8_encode.php';
    }

    static function initMbstring()
    {
        if (extension_loaded('mbstring'))
        {
            if ( ((int) ini_get('mbstring.encoding_translation') || in_array(strtolower(ini_get('mbstring.encoding_translation')), array('on', 'yes', 'true')))
                && !in_array(strtolower(ini_get('mbstring.http_input')), array('pass', '8bit', 'utf-8')) )
            {
                user_error('php.ini settings: Please disable mbstring.encoding_translation or set mbstring.http_input to "pass"',  E_USER_WARNING);
            }

            if (MB_OVERLOAD_STRING & (int) ini_get('mbstring.func_overload'))
            {
                user_error('php.ini settings: Please disable mbstring.func_overload', E_USER_WARNING);
            }

            mb_regex_encoding('UTF-8');
            ini_set('mbstring.script_encoding', 'pass');

            if ('utf-8' !== strtolower(mb_internal_encoding()))
            {
                mb_internal_encoding('UTF-8');
                ini_set('mbstring.internal_encoding', 'UTF-8');
            }

            if ('none' !== strtolower(mb_substitute_character()))
            {
                mb_substitute_character('none');
                ini_set('mbstring.substitute_character', 'none');
            }

            if (!in_array(strtolower(mb_http_output()), array('pass', '8bit')))
            {
                mb_http_output('pass');
                ini_set('mbstring.http_output', 'pass');
            }

            if (!in_array(strtolower(mb_language()), array('uni', 'neutral')))
            {
                mb_language('uni');
                ini_set('mbstring.language', 'uni');
            }
        }
        else if (!defined('MB_OVERLOAD_MAIL'))
        {
            require __DIR__ . '/Bootup/mbstring.php';
        }
    }

    static function initIconv()
    {
        if (extension_loaded('iconv'))
        {
            if ('UTF-8' !== iconv_get_encoding('input_encoding'))
            {
                iconv_set_encoding('input_encoding', 'UTF-8');
                ini_set('iconv.input_encoding', 'UTF-8');
            }

            if ('UTF-8' !== iconv_get_encoding('internal_encoding'))
            {
                iconv_set_encoding('internal_encoding', 'UTF-8');
                ini_set('iconv.internal_encoding', 'UTF-8');
            }

            if ('UTF-8' !== iconv_get_encoding('output_encoding'))
            {
                iconv_set_encoding('output_encoding' , 'UTF-8');
                ini_set('iconv.output_encoding', 'UTF-8');
            }
        }
        else if (!defined('ICONV_IMPL'))
        {
            require __DIR__ . '/Bootup/iconv.php';
        }
    }

    static function initExif()
    {
        if (extension_loaded('exif'))
        {
            if (ini_get('exif.encode_unicode') && 'UTF-8' !== strtoupper(ini_get('exif.encode_unicode')))
            {
                ini_set('exif.encode_unicode', 'UTF-8');
            }

            if (ini_get('exif.encode_jis') && 'UTF-8' !== strtoupper(ini_get('exif.encode_jis')))
            {
                ini_set('exif.encode_jis', 'UTF-8');
            }
        }
    }

    static function initIntl()
    {
        if (defined('GRAPHEME_CLUSTER_RX')) return;

        preg_match('/^.$/u', '§') or user_error('PCRE is compiled without UTF-8 support', E_USER_WARNING);

        extension_loaded('intl') or require __DIR__ . '/Bootup/intl.php';

        if (PCRE_VERSION < '8.32')
        {
            // (CRLF|([ZWNJ-ZWJ]|T+|L*(LV?V+|LV|LVT)T*|L+|[^Control])[Extend]*|[Control])
            // This regular expression is not up to date with the latest unicode grapheme cluster definition.
            // However, until http://bugs.exim.org/show_bug.cgi?id=1279 is fixed, it's still better than \X

            define('GRAPHEME_CLUSTER_RX', '(?:\r\n|(?:[ -~\x{200C}\x{200D}]|[ᆨ-ᇹ]+|[ᄀ-ᅟ]*(?:[가개갸걔거게겨계고과괘괴교구궈궤귀규그긔기까깨꺄꺠꺼께껴꼐꼬꽈꽤꾀꾜꾸꿔꿰뀌뀨끄끠끼나내냐냬너네녀녜노놔놰뇌뇨누눠눼뉘뉴느늬니다대댜댸더데뎌뎨도돠돼되됴두둬뒈뒤듀드듸디따때땨떄떠떼뗘뗴또똬뙈뙤뚀뚜뚸뛔뛰뜌뜨띄띠라래랴럐러레려례로롸뢔뢰료루뤄뤠뤼류르릐리마매먀먜머메며몌모뫄뫠뫼묘무뭐뭬뮈뮤므믜미바배뱌뱨버베벼볘보봐봬뵈뵤부붜붸뷔뷰브븨비빠빼뺘뺴뻐뻬뼈뼤뽀뽜뽸뾔뾰뿌뿨쀄쀠쀼쁘쁴삐사새샤섀서세셔셰소솨쇄쇠쇼수숴쉐쉬슈스싀시싸쌔쌰썌써쎄쎠쎼쏘쏴쐐쐬쑈쑤쒀쒜쒸쓔쓰씌씨아애야얘어에여예오와왜외요우워웨위유으의이자재쟈쟤저제져졔조좌좨죄죠주줘줴쥐쥬즈즤지짜째쨔쨰쩌쩨쪄쪠쪼쫘쫴쬐쬬쭈쭤쮀쮜쮸쯔쯰찌차채챠챼처체쳐쳬초촤쵀최쵸추춰췌취츄츠츼치카캐캬컈커케켜켸코콰쾌쾨쿄쿠쿼퀘퀴큐크킈키타태탸턔터테텨톄토톼퇘퇴툐투퉈퉤튀튜트틔티파패퍄퍠퍼페펴폐포퐈퐤푀표푸풔풰퓌퓨프픠피하해햐햬허헤혀혜호화홰회효후훠훼휘휴흐희히]?[ᅠ-ᆢ]+|[가-힣])[ᆨ-ᇹ]*|[ᄀ-ᅟ]+|[^\p{Cc}\p{Cf}\p{Zl}\p{Zp}])[\p{Mn}\p{Me}\x{09BE}\x{09D7}\x{0B3E}\x{0B57}\x{0BBE}\x{0BD7}\x{0CC2}\x{0CD5}\x{0CD6}\x{0D3E}\x{0D57}\x{0DCF}\x{0DDF}\x{200C}\x{200D}\x{1D165}\x{1D16E}-\x{1D172}]*|[\p{Cc}\p{Cf}\p{Zl}\p{Zp}])');
        }
        else
        {
            define('GRAPHEME_CLUSTER_RX', '\X');
        }
    }

    static function initLocale()
    {
        // With non-UTF-8 locale, basename() bugs.
        // Be aware that setlocale() can be slow.
        // You'd better properly configure your LANG environment variable to an UTF-8 locale.

        if ('' === basename('§'))
        {
            setlocale(LC_ALL, 'C.UTF-8', 'C');
            setlocale(LC_CTYPE, 'en_US.UTF-8', 'fr_FR.UTF-8', 'es_ES.UTF-8', 'de_DE.UTF-8', 'ru_RU.UTF-8', 'pt_BR.UTF-8', 'it_IT.UTF-8', 'ja_JP.UTF-8', 'zh_CN.UTF-8', 0);
        }
    }

    static function filterRequestUri()
    {
        // Ensures the URL is well formed UTF-8
        // When not, assumes Windows-1252 and redirects to the corresponding UTF-8 encoded URL

        if (isset($_SERVER['REQUEST_URI']) && !preg_match('//u', urldecode($a = $_SERVER['REQUEST_URI'])))
        {
            if ($a === u::utf8_decode($a))
            {
                $a = preg_replace_callback(
                    '/(?:%[89A-F][0-9A-F])+/i',
                    function($m) {return urlencode(u::utf8_encode(urldecode($m[0])));},
                    $a
                );
            }
            else $a = '/';

            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $a);

            exit;
        }
    }

    static function filterRequestInputs($normalization_form = /* n::NFC = */ 4, $pre_lead_comb = '◌')
    {
        // Ensures inputs are well formed UTF-8
        // When not, assumes Windows-1252 and converts to UTF-8
        // Tests only values, not keys

        $a = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST, &$_ENV);
        foreach ($_FILES as &$v) $a[] = array(&$v['name'], &$v['type']);

        $len = count($a);
        for ($i = 0; $i < $len; ++$i)
        {
            foreach ($a[$i] as &$v)
            {
                if (is_array($v)) $a[$len++] =& $v;
                else if (preg_match('/[\x80-\xFF]/', $v))
                {
                    if (n::isNormalized($v, $normalization_form)) $w = '';
                    else
                    {
                        $w = n::normalize($v, $normalization_form);
                        if (false === $w) $v = u::utf8_encode($v);
                        else $v = $w;
                    }

                    if ($v[0] >= "\x80" && false !== $w && isset($pre_lead_comb[0]) && preg_match('/^\p{Mn}/u', $v))
                    {
                        // Prevent leading combining chars
                        // for NFC-safe concatenations.
                        $v = $pre_lead_comb . $v;
                    }
                }
            }

            reset($a[$i]);
            unset($a[$i]);
        }
    }
}
