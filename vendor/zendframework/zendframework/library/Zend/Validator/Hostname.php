<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace Zend\Validator;

use Zend\Stdlib\ErrorHandler;

/**
 * Please note there are two standalone test scripts for testing IDN characters due to problems
 * with file encoding.
 *
 * The first is tests/Zend/Validator/HostnameTestStandalone.php which is designed to be run on
 * the command line.
 *
 * The second is tests/Zend/Validator/HostnameTestForm.php which is designed to be run via HTML
 * to allow users to test entering UTF-8 characters in a form.
 *
 * @category   Zend
 * @package    Zend_Validator
 */
class Hostname extends AbstractValidator
{
    const CANNOT_DECODE_PUNYCODE  = 'hostnameCannotDecodePunycode';
    const INVALID                 = 'hostnameInvalid';
    const INVALID_DASH            = 'hostnameDashCharacter';
    const INVALID_HOSTNAME        = 'hostnameInvalidHostname';
    const INVALID_HOSTNAME_SCHEMA = 'hostnameInvalidHostnameSchema';
    const INVALID_LOCAL_NAME      = 'hostnameInvalidLocalName';
    const INVALID_URI             = 'hostnameInvalidUri';
    const IP_ADDRESS_NOT_ALLOWED  = 'hostnameIpAddressNotAllowed';
    const LOCAL_NAME_NOT_ALLOWED  = 'hostnameLocalNameNotAllowed';
    const UNDECIPHERABLE_TLD      = 'hostnameUndecipherableTld';
    const UNKNOWN_TLD             = 'hostnameUnknownTld';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::CANNOT_DECODE_PUNYCODE  => "The input appears to be a DNS hostname but the given punycode notation cannot be decoded",
        self::INVALID                 => "Invalid type given. String expected",
        self::INVALID_DASH            => "The input appears to be a DNS hostname but contains a dash in an invalid position",
        self::INVALID_HOSTNAME        => "The input does not match the expected structure for a DNS hostname",
        self::INVALID_HOSTNAME_SCHEMA => "The input appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'",
        self::INVALID_LOCAL_NAME      => "The input does not appear to be a valid local network name",
        self::INVALID_URI             => "The input does not appear to be a valid URI hostname",
        self::IP_ADDRESS_NOT_ALLOWED  => "The input appears to be an IP address, but IP addresses are not allowed",
        self::LOCAL_NAME_NOT_ALLOWED  => "The input appears to be a local network name but local network names are not allowed",
        self::UNDECIPHERABLE_TLD      => "The input appears to be a DNS hostname but cannot extract TLD part",
        self::UNKNOWN_TLD             => "The input appears to be a DNS hostname but cannot match TLD against known list",
    );

    /**
     * @var array
     */
    protected $messageVariables = array(
        'tld' => 'tld',
    );

    const ALLOW_DNS   = 1;  // Allows Internet domain names (e.g., example.com)
    const ALLOW_IP    = 2;  // Allows IP addresses
    const ALLOW_LOCAL = 4;  // Allows local network names (e.g., localhost, www.localdomain)
    const ALLOW_URI   = 8;  // Allows URI hostnames
    const ALLOW_ALL   = 15;  // Allows all types of hostnames

    /**
     * Array of valid top-level-domains
     *
     * @see ftp://data.iana.org/TLD/tlds-alpha-by-domain.txt  List of all TLDs by domain
     * @see http://www.iana.org/domains/root/db/ Official list of supported TLDs
     * @var array
     */
    protected $validTlds = array(
        'ac', 'ad', 'ae', 'aero', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'arpa',
        'as', 'asia', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi',
        'biz', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz', 'ca', 'cat', 'cc',
        'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'com', 'coop', 'cr', 'cu',
        'cv', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'edu', 'ee', 'eg', 'er',
        'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb', 'gd', 'ge', 'gf', 'gg',
        'gh', 'gi', 'gl', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk',
        'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'info', 'int', 'io', 'iq',
        'ir', 'is', 'it', 'je', 'jm', 'jo', 'jobs', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp',
        'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly',
        'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'mp',
        'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'name', 'nc',
        'ne', 'net', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'org', 'pa', 'pe',
        'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'pro', 'ps', 'pt', 'pw', 'py', 'qa', 're',
        'ro', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl',
        'sm', 'sn', 'so', 'sr', 'st', 'su', 'sv', 'sy', 'sz', 'tc', 'td', 'tel', 'tf', 'tg', 'th',
        'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'travel', 'tt', 'tv', 'tw', 'tz', 'ua',
        'ug', 'uk', 'um', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws',
        'xxx', 'ye', 'yt', 'yu', 'za', 'zm', 'zw'
    );

    /**
     * Array for valid Idns
     * @see http://www.iana.org/domains/idn-tables/ Official list of supported IDN Chars
     * (.AC) Ascension Island http://www.nic.ac/pdf/AC-IDN-Policy.pdf
     * (.AR) Argentina http://www.nic.ar/faqidn.html
     * (.AS) American Samoa http://www.nic.as/idn/chars.cfm
     * (.AT) Austria http://www.nic.at/en/service/technical_information/idn/charset_converter/
     * (.BIZ) International http://www.iana.org/domains/idn-tables/
     * (.BR) Brazil http://registro.br/faq/faq6.html
     * (.BV) Bouvett Island http://www.norid.no/domeneregistrering/idn/idn_nyetegn.en.html
     * (.CAT) Catalan http://www.iana.org/domains/idn-tables/tables/cat_ca_1.0.html
     * (.CH) Switzerland https://nic.switch.ch/reg/ocView.action?res=EF6GW2JBPVTG67DLNIQXU234MN6SC33JNQQGI7L6#anhang1
     * (.CL) Chile http://www.iana.org/domains/idn-tables/tables/cl_latn_1.0.html
     * (.COM) International http://www.verisign.com/information-services/naming-services/internationalized-domain-names/index.html
     * (.DE) Germany http://www.denic.de/en/domains/idns/liste.html
     * (.DK) Danmark http://www.dk-hostmaster.dk/index.php?id=151
     * (.ES) Spain https://www.nic.es/media/2008-05/1210147705287.pdf
     * (.FI) Finland http://www.ficora.fi/en/index/palvelut/fiverkkotunnukset/aakkostenkaytto.html
     * (.GR) Greece https://grweb.ics.forth.gr/CharacterTable1_en.jsp
     * (.HU) Hungary http://www.domain.hu/domain/English/szabalyzat/szabalyzat.html
     * (.INFO) International http://www.nic.info/info/idn
     * (.IO) British Indian Ocean Territory http://www.nic.io/IO-IDN-Policy.pdf
     * (.IR) Iran http://www.nic.ir/Allowable_Characters_dot-iran
     * (.IS) Iceland http://www.isnic.is/domain/rules.php
     * (.KR) Korea http://www.iana.org/domains/idn-tables/tables/kr_ko-kr_1.0.html
     * (.LI) Liechtenstein https://nic.switch.ch/reg/ocView.action?res=EF6GW2JBPVTG67DLNIQXU234MN6SC33JNQQGI7L6#anhang1
     * (.LT) Lithuania http://www.domreg.lt/static/doc/public/idn_symbols-en.pdf
     * (.MD) Moldova http://www.register.md/
     * (.MUSEUM) International http://www.iana.org/domains/idn-tables/tables/museum_latn_1.0.html
     * (.NET) International http://www.verisign.com/information-services/naming-services/internationalized-domain-names/index.html
     * (.NO) Norway http://www.norid.no/domeneregistrering/idn/idn_nyetegn.en.html
     * (.NU) Niue http://www.worldnames.net/
     * (.ORG) International http://www.pir.org/index.php?db=content/FAQs&tbl=FAQs_Registrant&id=2
     * (.PE) Peru https://www.nic.pe/nuevas_politicas_faq_2.php
     * (.PL) Poland http://www.dns.pl/IDN/allowed_character_sets.pdf
     * (.PR) Puerto Rico http://www.nic.pr/idn_rules.asp
     * (.PT) Portugal https://online.dns.pt/dns_2008/do?com=DS;8216320233;111;+PAGE(4000058)+K-CAT-CODIGO(C.125)+RCNT(100);
     * (.RU) Russia http://www.iana.org/domains/idn-tables/tables/ru_ru-ru_1.0.html
     * (.SA) Saudi Arabia http://www.iana.org/domains/idn-tables/tables/sa_ar_1.0.html
     * (.SE) Sweden http://www.iis.se/english/IDN_campaignsite.shtml?lang=en
     * (.SH) Saint Helena http://www.nic.sh/SH-IDN-Policy.pdf
     * (.SJ) Svalbard and Jan Mayen http://www.norid.no/domeneregistrering/idn/idn_nyetegn.en.html
     * (.TH) Thailand http://www.iana.org/domains/idn-tables/tables/th_th-th_1.0.html
     * (.TM) Turkmenistan http://www.nic.tm/TM-IDN-Policy.pdf
     * (.TR) Turkey https://www.nic.tr/index.php
     * (.VE) Venice http://www.iana.org/domains/idn-tables/tables/ve_es_1.0.html
     * (.VN) Vietnam http://www.vnnic.vn/english/5-6-300-2-2-04-20071115.htm#1.%20Introduction
     *
     * @var array
     */
    protected $validIdns = array(
        'AC'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿ÄýÄ’Ä&Ä!Ä0Ä9ÄýÄýÄÄÄÄ"Ä:ÄýÄ¡Ä£Ä¥Ä§Ä«Ä¯ÄµÄ·ÄºÄ¼Ä¾Å¬ÅÅÅ ÅÆÅ9ÅÅÅ"ÅÅ"Å:ÅýÅxÅ¡Å£Å¥Å§Å«Å­Å¯Å±Å³ÅµÅ·ÅºÅ¼Å¾]{1,63}$/iu'),
        'AR'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã£Ã§-ÃªÃ¬Ã­Ã±-ÃµÃ¼]{1,63}$/iu'),
        'AS'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿ÄýÄ’Ä&Ä!Ä0Ä9ÄýÄýÄÄÄ"ÄÄ"Ä:ÄýÄxÄ¡Ä£Ä¥Ä§Ä©Ä«Ä­Ä¯Ä±ÄµÄ·Ä¸ÄºÄ¼Ä¾ÅÅÅ ÅÆÅ9ÅýÅýÅÅÅ"ÅÅ"Å:ÅýÅxÅ¡Å£Å¥Å§Å©Å«Å­Å¯Å±Å³ÅµÅ·ÅºÅ¼]{1,63}$/iu'),
        'AT'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿ÅÅ¡Å¾]{1,63}$/iu'),
        'BIZ' => 'Hostname/Biz.php',
        'BR'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã£Ã§Ã©Ã­Ã³-ÃµÃºÃ¼]{1,63}$/iu'),
        'BV'  => array(1 => '/^[\x{002d}0-9a-zÃ Ã¡Ã¤-Ã©ÃªÃ±-Ã´Ã¶Ã¸Ã¼ÄýÄÅÅ9Å¡Å§Å¾]{1,63}$/iu'),
        'CAT' => array(1 => '/^[\x{002d}0-9a-zÂ·Ã Ã§-Ã©Ã­Ã¯Ã²Ã³ÃºÃ¼]{1,63}$/iu'),
        'CH'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿Å]{1,63}$/iu'),
        'CL'  => array(1 => '/^[\x{002d}0-9a-zÃ¡Ã©Ã­Ã±Ã³ÃºÃ¼]{1,63}$/iu'),
        'CN'  => 'Hostname/Cn.php',
        'COM' => 'Hostname/Com.php',
        'DE'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿Ä’Ä&ÄýÄ!Ä0ÄýÄ9ÄýÄÄ"Ä:ÄÄ"ÄÄxÄýÄ¡Ä£Ä¥Ä§Ä­Ä©Ä¯Ä«Ä±ÄµÄ·ÄºÄ¾Ä¼ÅÅÅÆÅ Å9ÅýÅÅýÅÄ¸Å"Å"ÅÅ:ÅýÅ¡ÅxÅ¥Å£Å§Å­Å¯Å±Å©Å³Å«ÅµÅ·ÅºÅ¾Å¼]{1,63}$/iu'),
        'DK'  => array(1 => '/^[\x{002d}0-9a-zÃ¤Ã©Ã¶Ã¼]{1,63}$/iu'),
        'ES'  => array(1 => '/^[\x{002d}0-9a-zÃ Ã¡Ã§Ã¨Ã©Ã­Ã¯Ã±Ã²Ã³ÃºÃ¼Â·]{1,63}$/iu'),
        'EU'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿]{1,63}$/iu',
            2 => '/^[\x{002d}0-9a-zÄýÄ’Ä&Ä!Ä0Ä9ÄýÄýÄÄÄ"ÄÄ"Ä:ÄýÄxÄ¡Ä£Ä¥Ä§Ä©Ä«Ä­Ä¯Ä±ÄµÄ·ÄºÄ¼Ä¾Å¬ÅÅÅ ÅÆÅ0Å9ÅýÅýÅÅÅ"ÅÅ"Å:ÅýÅ¡Å¥Å§Å©Å«Å­Å¯Å±Å³ÅµÅ·ÅºÅ¼Å¾]{1,63}$/iu',
            3 => '/^[\x{002d}0-9a-zÈ"È:]{1,63}$/iu',
            4 => '/^[\x{002d}0-9a-zÎýÎ¬Î­Î®Î¯Î°Î±Î²Î³Î´ÎµÎ¶Î·Î¸Î¹ÎºÎ»Î¼Î½Î¾Î¿Ï¬ÏýÏÏ’ÏÏ&Ï Ï!ÏÆÏ0Ï`Ï9ÏRÏýÏ}]{1,63}$/iu',
            5 => '/^[\x{002d}0-9a-zÐ°Ð±Ð²Ð³Ð´ÐµÐ¶Ð·Ð¸Ð¹ÐºÐ»Ð¼Ð½Ð¾Ð¿Ñ¬ÑýÑÑ’ÑÑ&Ñ Ñ!ÑÆÑ0Ñ`Ñ9ÑRÑýÑ}Ñý]{1,63}$/iu',
            6 => '/^[\x{002d}0-9a-zá¼¬-á¼!á¼ý-á¼"á¼ -á¼§á¼°-á¼·á½¬-á½&á½ý-á½á½ -á½§á½°-á½¼Ï}á¾¬-á¾!á¾ý-á¾á¾ -á¾§á¾°-á¾´á¾¶á¾·á¿á¿’á¿á¿ á¿!á¿ý-á¿Îýá¿á¿á¿ -á¿§á¿²á¿³á¿´á¿¶á¿·]{1,63}$/iu'),
        'FI'  => array(1 => '/^[\x{002d}0-9a-zÃ¤Ã¥Ã¶]{1,63}$/iu'),
        'GR'  => array(1 => '/^[\x{002d}0-9a-zÎ ÎÆÎ0Î`ÎRÎ}-Î¡Î£-Ï}á¼¬-á¼"á¼Ü-á¼ýá¼ -á½&á½Æ-á½ýá½ý-á½á½"á½:á½ýá½x-á½½á¾¬-á¾´á¾¶-á¾¼á¿á¿’á¿á¿ -á¿Rá¿ý-á¿á¿-á¿:á¿ -á¿¬á¿²á¿³á¿´á¿¶-á¿¼]{1,63}$/iu'),
        'HK'  => 'Hostname/Cn.php',
        'HU'  => array(1 => '/^[\x{002d}0-9a-zÃ¡Ã©Ã­Ã³Ã¶ÃºÃ¼ÅÅ±]{1,63}$/iu'),
        'INFO'=> array(1 => '/^[\x{002d}0-9a-zÃ¤Ã¥Ã¦Ã©Ã¶Ã¸Ã¼]{1,63}$/iu',
            2 => '/^[\x{002d}0-9a-zÃ¡Ã©Ã­Ã³Ã¶ÃºÃ¼ÅÅ±]{1,63}$/iu',
            3 => '/^[\x{002d}0-9a-zÃ¡Ã¦Ã©Ã­Ã°Ã³Ã¶ÃºÃ½Ã¾]{1,63}$/iu',
            4 => '/^[\x{AC00}-\x{D7A3}]{1,17}$/iu',
            5 => '/^[\x{002d}0-9a-zÄýÄýÄÄ£Ä«Ä·Ä¼Å ÅýÅÅ¡Å«Å¾]{1,63}$/iu',
            6 => '/^[\x{002d}0-9a-zÄ&ÄýÄÄ"Ä¯Å¡Å«Å³Å¾]{1,63}$/iu',
            7 => '/^[\x{002d}0-9a-zÃ³Ä&Ä!Ä"ÅÅÅ:ÅºÅ¼]{1,63}$/iu',
            8 => '/^[\x{002d}0-9a-zÃ¡Ã©Ã­Ã±Ã³ÃºÃ¼]{1,63}$/iu'),
        'IO'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿Ä’Ä&ÄýÄ!Ä0ÄýÄ9ÄýÄÄ"Ä:ÄÄ"ÄÄxÄýÄ¡Ä£Ä¥Ä§Ä­Ä©Ä¯Ä«Ä±ÄµÄ·ÄºÄ¾Ä¼ÅÅÅÆÅ Å9ÅýÅÅýÅÄ¸Å"Å"ÅÅ:ÅýÅ¡ÅxÅ¥Å£Å§Å­Å¯Å±Å©Å³Å«ÅµÅ·ÅºÅ¾Å¼]{1,63}$/iu'),
        'IS'  => array(1 => '/^[\x{002d}0-9a-zÃ¡Ã©Ã½ÃºÃ­Ã³Ã¾Ã¦Ã¶Ã°]{1,63}$/iu'),
        'JP'  => 'Hostname/Jp.php',
        'KR'  => array(1 => '/^[\x{AC00}-\x{D7A3}]{1,17}$/iu'),
        'LI'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿Å]{1,63}$/iu'),
        'LT'  => array(1 => '/^[\x{002d}0-9Ä&ÄýÄ"ÄÄ¯Å¡Å³Å«Å¾]{1,63}$/iu'),
        'MD'  => array(1 => '/^[\x{002d}0-9Ä’Ã¢Ã®ÅxÅ£]{1,63}$/iu'),
        'MUSEUM' => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿ÄýÄ’Ä&Ä!Ä9ÄýÄýÄÄÄÄ"Ä:ÄxÄ¡Ä£Ä§Ä«Ä¯Ä±Ä·ÄºÄ¼Ä¾ÅÅÅ ÅÆÅ9ÅýÅÅÅ"ÅÅ"Å:ÅxÅ¡Å£Å¥Å§Å«Å¯Å±Å³ÅµÅ·ÅºÅ¼Å¾Ç}ÇýÇÇ\x{01E5}\x{01E7}\x{01E9}\x{01EF}É"\x{0292}áºýáº’áº&á»³]{1,63}$/iu'),
        'NET' => 'Hostname/Com.php',
        'NO'  => array(1 => '/^[\x{002d}0-9a-zÃ Ã¡Ã¤-Ã©ÃªÃ±-Ã´Ã¶Ã¸Ã¼ÄýÄÅÅ9Å¡Å§Å¾]{1,63}$/iu'),
        'NU'  => 'Hostname/Com.php',
        'ORG' => array(1 => '/^[\x{002d}0-9a-zÃ¡Ã©Ã­Ã±Ã³ÃºÃ¼]{1,63}$/iu',
            2 => '/^[\x{002d}0-9a-zÃ³Ä&Ä!Ä"ÅÅÅ:ÅºÅ¼]{1,63}$/iu',
            3 => '/^[\x{002d}0-9a-zÃ¡Ã¤Ã¥Ã¦Ã©Ã«Ã­Ã°Ã³Ã¶Ã¸ÃºÃ¼Ã½Ã¾]{1,63}$/iu',
            4 => '/^[\x{002d}0-9a-zÃ¡Ã©Ã­Ã³Ã¶ÃºÃ¼ÅÅ±]{1,63}$/iu',
            5 => '/^[\x{002d}0-9a-zÄ&ÄýÄÄ"Ä¯Å¡Å«Å³Å¾]{1,63}$/iu',
            6 => '/^[\x{AC00}-\x{D7A3}]{1,17}$/iu',
            7 => '/^[\x{002d}0-9a-zÄýÄýÄÄ£Ä«Ä·Ä¼Å ÅýÅÅ¡Å«Å¾]{1,63}$/iu'),
        'PE'  => array(1 => '/^[\x{002d}0-9a-zÃ±Ã¡Ã©Ã­Ã³ÃºÃ¼]{1,63}$/iu'),
        'PL'  => array(1 => '/^[\x{002d}0-9a-zÄýÄýÄÄ£Ä«Ä·Ä¼Å ÅýÅÅ¡Å«Å¾]{1,63}$/iu',
            2 => '/^[\x{002d}Ð°-Ð¸Ðº-ÑÆ\x{0450}ÑÑ"ÑÜÑ"ÑaÑSÑx]{1,63}$/iu',
            3 => '/^[\x{002d}0-9a-zÃ¢Ã®Ä’ÅxÅ£]{1,63}$/iu',
            4 => '/^[\x{002d}0-9Ð°-ÑýÑ\x{04C2}]{1,63}$/iu',
            5 => '/^[\x{002d}0-9a-zÃ Ã¡Ã¢Ã¨Ã©ÃªÃ¬Ã­Ã®Ã²Ã³Ã´Ã¹ÃºÃ»Ä9Ä¡Ä§Å¼]{1,63}$/iu',
            6 => '/^[\x{002d}0-9a-zÃ Ã¤Ã¥Ã¦Ã©ÃªÃ²Ã³Ã´Ã¶Ã¸Ã¼]{1,63}$/iu',
            7 => '/^[\x{002d}0-9a-zÃ³Ä&Ä!Ä"ÅÅÅ:ÅºÅ¼]{1,63}$/iu',
            8 => '/^[\x{002d}0-9a-zÃ Ã¡Ã¢Ã£Ã§Ã©ÃªÃ­Ã²Ã³Ã´ÃµÃºÃ¼]{1,63}$/iu',
            9 => '/^[\x{002d}0-9a-zÃ¢Ã®Ä’ÅxÅ£]{1,63}$/iu',
            10=> '/^[\x{002d}0-9a-zÃ¡Ã¤Ã©Ã­Ã³Ã´ÃºÃ½ÄýÄýÄºÄ¾ÅÆÅ"Å¡Å¥Å¾]{1,63}$/iu',
            11=> '/^[\x{002d}0-9a-zÃ§Ã«]{1,63}$/iu',
            12=> '/^[\x{002d}0-9Ð°-Ð¸Ðº-ÑÆÑÑÜÑ"ÑaÑ:Ñx]{1,63}$/iu',
            13=> '/^[\x{002d}0-9a-zÄ!ÄýÄÅ¡Å¾]{1,63}$/iu',
            14=> '/^[\x{002d}0-9a-zÃ¢Ã§Ã¶Ã»Ã¼ÄxÄ±Åx]{1,63}$/iu',
            15=> '/^[\x{002d}0-9a-zÃ¡Ã©Ã­Ã±Ã³ÃºÃ¼]{1,63}$/iu',
            16=> '/^[\x{002d}0-9a-zÃ¤ÃµÃ¶Ã¼Å¡Å¾]{1,63}$/iu',
            17=> '/^[\x{002d}0-9a-zÄ0ÄýÄ¥ÄµÅýÅ­]{1,63}$/iu',
            18=> '/^[\x{002d}0-9a-zÃ¢Ã¤Ã©Ã«Ã®Ã´]{1,63}$/iu',
            19=> '/^[\x{002d}0-9a-zÃ Ã¡Ã¢Ã¤Ã¥Ã¦Ã§Ã¨Ã©ÃªÃ«Ã¬Ã­Ã®Ã¯Ã°Ã±Ã²Ã´Ã¶Ã¸Ã¹ÃºÃ»Ã¼Ã½Ä!ÄýÅÅÅ"Å:Å¡]{1,63}$/iu',
            20=> '/^[\x{002d}0-9a-zÃ¤Ã¥Ã¦ÃµÃ¶Ã¸Ã¼Å¡Å¾]{1,63}$/iu',
            21=> '/^[\x{002d}0-9a-zÃ Ã¡Ã§Ã¨Ã©Ã¬Ã­Ã²Ã³Ã¹Ãº]{1,63}$/iu',
            22=> '/^[\x{002d}0-9a-zÃ Ã¡Ã©Ã­Ã³Ã¶ÃºÃ¼ÅÅ±]{1,63}$/iu',
            23=> '/^[\x{002d}0-9ÎýÎ¬-Ï}]{1,63}$/iu',
            24=> '/^[\x{002d}0-9a-zÃ Ã¡Ã¢Ã¥Ã¦Ã§Ã¨Ã©ÃªÃ«Ã°Ã³Ã´Ã¶Ã¸Ã¼Ã¾Å]{1,63}$/iu',
            25=> '/^[\x{002d}0-9a-zÃ¡Ã¤Ã©Ã­Ã³Ã¶ÃºÃ¼Ã½ÄýÄýÄ:ÅÆÅ"Å¡Å¥Å¯Å¾]{1,63}$/iu',
            26=> '/^[\x{002d}0-9a-zÂ·Ã Ã§Ã¨Ã©Ã­Ã¯Ã²Ã³ÃºÃ¼]{1,63}$/iu',
            27=> '/^[\x{002d}0-9Ð°-Ñ`ÑRÑ}Ñý\x{0450}\x{045D}]{1,63}$/iu',
            28=> '/^[\x{002d}0-9Ð°-ÑýÑÑÑ~]{1,63}$/iu',
            29=> '/^[\x{002d}0-9a-zÄ&ÄýÄÄ"Ä¯Å¡Å«Å³Å¾]{1,63}$/iu',
            30=> '/^[\x{002d}0-9a-zÃ¡Ã¤Ã¥Ã¦Ã©Ã«Ã­Ã°Ã³Ã¶Ã¸ÃºÃ¼Ã½Ã¾]{1,63}$/iu',
            31=> '/^[\x{002d}0-9a-zÃ Ã¢Ã¦Ã§Ã¨Ã©ÃªÃ«Ã®Ã¯Ã±Ã´Ã¹Ã»Ã¼Ã¿Å]{1,63}$/iu',
            32=> '/^[\x{002d}0-9Ð°-Ñ0Ñ`Ñ9ÑRÑýÑ}ÑýÑÑÑÑÒ]{1,63}$/iu',
            33=> '/^[\x{002d}0-9×ý-×ª]{1,63}$/iu'),
        'PR'  => array(1 => '/^[\x{002d}0-9a-zÃ¡Ã©Ã­Ã³ÃºÃ±Ã¤Ã«Ã¯Ã¼Ã¶Ã¢ÃªÃ®Ã´Ã»Ã Ã¨Ã¹Ã¦Ã§ÅÃ£Ãµ]{1,63}$/iu'),
        'PT'  => array(1 => '/^[\x{002d}0-9a-zÃ¡Ã Ã¢Ã£Ã§Ã©ÃªÃ­Ã³Ã´ÃµÃº]{1,63}$/iu'),
        'RU'  => array(1 => '/^[\x{002d}0-9Ð°-ÑýÑ]{1,63}$/iu'),
        'SA'  => array(1 => '/^[\x{002d}.0-9\x{0621}-\x{063A}\x{0641}-\x{064A}\x{0660}-\x{0669}]{1,63}$/iu'),
        'SE'  => array(1 => '/^[\x{002d}0-9a-zÃ¤Ã¥Ã©Ã¶Ã¼]{1,63}$/iu'),
        'SH'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿Ä’Ä&ÄýÄ!Ä0ÄýÄ9ÄýÄÄ"Ä:ÄÄ"ÄÄxÄýÄ¡Ä£Ä¥Ä§Ä­Ä©Ä¯Ä«Ä±ÄµÄ·ÄºÄ¾Ä¼ÅÅÅÆÅ Å9ÅýÅÅýÅÄ¸Å"Å"ÅÅ:ÅýÅ¡ÅxÅ¥Å£Å§Å­Å¯Å±Å©Å³Å«ÅµÅ·ÅºÅ¾Å¼]{1,63}$/iu'),
        'SI'  => array(
            1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿]{1,63}$/iu',
            2 => '/^[\x{002d}0-9a-zÄýÄ’Ä&Ä!Ä0Ä9ÄýÄýÄÄÄ"ÄÄ"Ä:ÄýÄxÄ¡Ä£Ä¥Ä§Ä©Ä«Ä­Ä¯Ä±ÄµÄ·ÄºÄ¼Ä¾Å¬ÅÅÅ ÅÆÅ0Å9ÅýÅýÅÅÅ"ÅÅ"Å:ÅýÅ¡Å¥Å§Å©Å«Å­Å¯Å±Å³ÅµÅ·ÅºÅ¼Å¾]{1,63}$/iu',
            3 => '/^[\x{002d}0-9a-zÈ"È:]{1,63}$/iu'),
        'SJ'  => array(1 => '/^[\x{002d}0-9a-zÃ Ã¡Ã¤-Ã©ÃªÃ±-Ã´Ã¶Ã¸Ã¼ÄýÄÅÅ9Å¡Å§Å¾]{1,63}$/iu'),
        'TH'  => array(1 => '/^[\x{002d}0-9a-z\x{0E01}-\x{0E3A}\x{0E40}-\x{0E4D}\x{0E50}-\x{0E59}]{1,63}$/iu'),
        'TM'  => array(1 => '/^[\x{002d}0-9a-zÃ -Ã¶Ã¸-Ã¿ÄýÄ’Ä&Ä!Ä0Ä9ÄýÄýÄÄÄÄ"Ä:ÄýÄ¡Ä£Ä¥Ä§Ä«Ä¯ÄµÄ·ÄºÄ¼Ä¾Å¬ÅÅÅ ÅÆÅ9ÅÅÅ"ÅÅ"Å:ÅýÅxÅ¡Å£Å¥Å§Å«Å­Å¯Å±Å³ÅµÅ·ÅºÅ¼Å¾]{1,63}$/iu'),
        'TW'  => 'Hostname/Cn.php',
        'TR'  => array(1 => '/^[\x{002d}0-9a-zÄxÄ±Ã¼ÅxÃ¶Ã§]{1,63}$/iu'),
        'VE'  => array(1 => '/^[\x{002d}0-9a-zÃ¡Ã©Ã­Ã³ÃºÃ¼Ã±]{1,63}$/iu'),
        'VN'  => array(1 => '/^[Ã¬ÃýÃÃ’ÃÆÃ0Ã`ÃRÃýÃÃÃÃ"Ã"ÃaÃýÃ Ã¡Ã¢Ã£Ã¨Ã©ÃªÃ¬Ã­Ã²Ã³Ã´ÃµÃ¹ÃºÃ½ÄÄ’ÄýÄÄ¨Ä©Å¨Å©Æ Æ¡Æ¯Æ°\x{1EA0}-\x{1EF9}]{1,63}$/iu'),
        'ä¸­å:½' => 'Hostname/Cn.php',
        'ä¸­åS9' => 'Hostname/Cn.php',
        'à¶½à¶à¶aà·ý' => array(1 => '/^[\x{0d80}-\x{0dff}]{1,63}$/iu'),
        'é¦"æ¸¯' => 'Hostname/Cn.php',
        'åý°æ¹¾' => 'Hostname/Cn.php',
        'åý°çý£' => 'Hostname/Cn.php',
        'Ø§Ù&Ø§Ø±Ø§Øª'   => array(1 => '/^[\x{0621}-\x{0624}\x{0626}-\x{063A}\x{0641}\x{0642}\x{0644}-\x{0648}\x{067E}\x{0686}\x{0698}\x{06A9}\x{06AF}\x{06CC}\x{06F0}-\x{06F9}]{1,30}$/iu'),
        'Ø§ÙØ§Ø±Ø¯Ù '    => array(1 => '/^[\x{0621}-\x{0624}\x{0626}-\x{063A}\x{0641}\x{0642}\x{0644}-\x{0648}\x{067E}\x{0686}\x{0698}\x{06A9}\x{06AF}\x{06CC}\x{06F0}-\x{06F9}]{1,30}$/iu'),
        'Ø§ÙØ³Ø¹ÙÆØ¯Ù`Ø©' => array(1 => '/^[\x{0621}-\x{0624}\x{0626}-\x{063A}\x{0641}\x{0642}\x{0644}-\x{0648}\x{067E}\x{0686}\x{0698}\x{06A9}\x{06AF}\x{06CC}\x{06F0}-\x{06F9}]{1,30}$/iu'),
        'à¹à¸à¸¢' => array(1 => '/^[\x{002d}0-9a-z\x{0E01}-\x{0E3A}\x{0E40}-\x{0E4D}\x{0E50}-\x{0E59}]{1,63}$/iu'),
        'Ñ¬Ñ' => array(1 => '/^[\x{002d}0-9Ð°-ÑýÑ]{1,63}$/iu'),
        'ØªÙÆÙ Ø³' => array(1 => '/^[\x{0621}-\x{0624}\x{0626}-\x{063A}\x{0641}\x{0642}\x{0644}-\x{0648}\x{067E}\x{0686}\x{0698}\x{06A9}\x{06AF}\x{06CC}\x{06F0}-\x{06F9}]{1,30}$/iu'),
        'Ù&ØµØ±' => array(1 => '/^[\x{0621}-\x{0624}\x{0626}-\x{063A}\x{0641}\x{0642}\x{0644}-\x{0648}\x{067E}\x{0686}\x{0698}\x{06A9}\x{06AF}\x{06CC}\x{06F0}-\x{06F9}]{1,30}$/iu'),
        'à®!à®²à®"à¯ýà®"à¯Æ' => array(1 => '/^[\x{0b80}-\x{0bff}]{1,63}$/iu'),
        'ÙýÙØ³Ø·Ù`Ù ' => array(1 => '/^[\x{0621}-\x{0624}\x{0626}-\x{063A}\x{0641}\x{0642}\x{0644}-\x{0648}\x{067E}\x{0686}\x{0698}\x{06A9}\x{06AF}\x{06CC}\x{06F0}-\x{06F9}]{1,30}$/iu'),
    );

    protected $idnLength = array(
        'BIZ' => array(5 => 17, 11 => 15, 12 => 20),
        'CN'  => array(1 => 20),
        'COM' => array(3 => 17, 5 => 20),
        'HK'  => array(1 => 15),
        'INFO'=> array(4 => 17),
        'KR'  => array(1 => 17),
        'NET' => array(3 => 17, 5 => 20),
        'ORG' => array(6 => 17),
        'TW'  => array(1 => 20),
        'Ø§Ù&Ø§Ø±Ø§Øª' => array(1 => 30),
        'Ø§ÙØ§Ø±Ø¯Ù ' => array(1 => 30),
        'Ø§ÙØ³Ø¹ÙÆØ¯Ù`Ø©' => array(1 => 30),
        'ØªÙÆÙ Ø³' => array(1 => 30),
        'Ù&ØµØ±' => array(1 => 30),
        'ÙýÙØ³Ø·Ù`Ù ' => array(1 => 30),
        'ä¸­å:½' => array(1 => 20),
        'ä¸­åS9' => array(1 => 20),
        'é¦"æ¸¯' => array(1 => 20),
        'åý°æ¹¾' => array(1 => 20),
        'åý°çý£' => array(1 => 20),
    );

    protected $tld;

    /**
     * Options for the hostname validator
     *
     * @var array
     */
    protected $options = array(
        'allow'       => self::ALLOW_DNS, // Allow these hostnames
        'useIdnCheck' => true,  // Check IDN domains
        'useTldCheck' => true,  // Check TLD elements
        'ipValidator' => null,  // IP validator to use
    );

    /**
     * Sets validator options
     *
     * @param integer $allow       OPTIONAL Set what types of hostname to allow (default ALLOW_DNS)
     * @param boolean $validateIdn OPTIONAL Set whether IDN domains are validated (default true)
     * @param boolean $validateTld OPTIONAL Set whether the TLD element of a hostname is validated (default true)
     * @param Ip      $ipValidator OPTIONAL
     * @see http://www.iana.org/cctld/specifications-policies-cctlds-01apr02.htm  Technical Specifications for ccTLDs
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $options = func_get_args();
            $temp['allow'] = array_shift($options);
            if (!empty($options)) {
                $temp['useIdnCheck'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['useTldCheck'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['ipValidator'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('ipValidator', $options)) {
            $options['ipValidator'] = null;
        }

        parent::__construct($options);
    }

    /**
     * Returns the set ip validator
     *
     * @return Ip
     */
    public function getIpValidator()
    {
        return $this->options['ipValidator'];
    }

    /**
     * @param Ip $ipValidator OPTIONAL
     * @return Hostname;
     */
    public function setIpValidator(Ip $ipValidator = null)
    {
        if ($ipValidator === null) {
            $ipValidator = new Ip();
        }

        $this->options['ipValidator'] = $ipValidator;
        return $this;
    }

    /**
     * Returns the allow option
     *
     * @return integer
     */
    public function getAllow()
    {
        return $this->options['allow'];
    }

    /**
     * Sets the allow option
     *
     * @param  integer $allow
     * @return Hostname Provides a fluent interface
     */
    public function setAllow($allow)
    {
        $this->options['allow'] = $allow;
        return $this;
    }

    /**
     * Returns the set idn option
     *
     * @return boolean
     */
    public function getIdnCheck()
    {
        return $this->options['useIdnCheck'];
    }

    /**
     * Set whether IDN domains are validated
     *
     * This only applies when DNS hostnames are validated
     *
     * @param boolean $useIdnCheck Set to true to validate IDN domains
     * @return Hostname
     */
    public function useIdnCheck ($useIdnCheck)
    {
        $this->options['useIdnCheck'] = (bool) $useIdnCheck;
        return $this;
    }

    /**
     * Returns the set tld option
     *
     * @return boolean
     */
    public function getTldCheck()
    {
        return $this->options['useTldCheck'];
    }

    /**
     * Set whether the TLD element of a hostname is validated
     *
     * This only applies when DNS hostnames are validated
     *
     * @param boolean $useTldCheck Set to true to validate TLD elements
     * @return Hostname
     */
    public function useTldCheck ($useTldCheck)
    {
        $this->options['useTldCheck'] = (bool) $useTldCheck;
        return $this;
    }

    /**
     * Defined by Interface
     *
     * Returns true if and only if the $value is a valid hostname with respect to the current allow option
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        // Check input against IP address schema
        if (preg_match('/^[0-9a-f:.]*$/i', $value) &&
            $this->getIpValidator()->setTranslator($this->getTranslator())->isValid($value)) {
            if (!($this->getAllow() & self::ALLOW_IP)) {
                $this->error(self::IP_ADDRESS_NOT_ALLOWED);
                return false;
            } else {
                return true;
            }
        }

        // Local hostnames are allowed to be partial (ending '.')
        if ($this->getAllow() & self::ALLOW_LOCAL) {
            if (substr($value, -1) === '.') {
                $value = substr($value, 0, -1);
                if (substr($value, -1) === '.') {
                    // Empty hostnames (ending '..') are not allowed
                    $this->error(self::INVALID_LOCAL_NAME);
                    return false;
                }
            }
        }

        $domainParts = explode('.', $value);

        // Prevent partial IP V4 addresses (ending '.')
        if ((count($domainParts) == 4) && preg_match('/^[0-9.a-e:.]*$/i', $value) &&
            $this->getIpValidator()->setTranslator($this->getTranslator())->isValid($value)) {
            $this->error(self::INVALID_LOCAL_NAME);
        }

        // Check input against DNS hostname schema
        if ((count($domainParts) > 1) && (strlen($value) >= 4) && (strlen($value) <= 254)) {
            $status = false;

            $origenc = iconv_get_encoding('internal_encoding');
            iconv_set_encoding('internal_encoding', 'UTF-8');
            do {
                // First check TLD
                $matches = array();
                if (preg_match('/([^.]{2,10})$/i', end($domainParts), $matches) ||
                    (array_key_exists(end($domainParts), $this->validIdns))) {
                    reset($domainParts);

                    // Hostname characters are: *(label dot)(label dot label); max 254 chars
                    // label: id-prefix [*ldh{61} id-prefix]; max 63 chars
                    // id-prefix: alpha / digit
                    // ldh: alpha / digit / dash

                    // Match TLD against known list
                    $this->tld = strtolower($matches[1]);
                    if ($this->getTldCheck()) {
                        if (!in_array($this->tld, $this->validTlds)) {
                            $this->error(self::UNKNOWN_TLD);
                            $status = false;
                            break;
                        }
                    }

                    /**
                     * Match against IDN hostnames
                     * Note: Keep label regex short to avoid issues with long patterns when matching IDN hostnames
                     * @see Hostname\Interface
                     */
                    $regexChars = array(0 => '/^[a-z0-9\x2d]{1,63}$/i');
                    if ($this->getIdnCheck() &&  isset($this->validIdns[strtoupper($this->tld)])) {
                        if (is_string($this->validIdns[strtoupper($this->tld)])) {
                            $regexChars += include ($this->validIdns[strtoupper($this->tld)]);
                        } else {
                            $regexChars += $this->validIdns[strtoupper($this->tld)];
                        }
                    }

                    // Check each hostname part
                    $check = 0;
                    foreach ($domainParts as $domainPart) {
                        // Decode Punycode domain names to IDN
                        if (strpos($domainPart, 'xn--') === 0) {
                            $domainPart = $this->decodePunycode(substr($domainPart, 4));
                            if ($domainPart === false) {
                                return false;
                            }
                        }

                        // Check dash (-) does not start, end or appear in 3rd and 4th positions
                        if ((strpos($domainPart, '-') === 0)
                            || ((strlen($domainPart) > 2) && (strpos($domainPart, '-', 2) == 2) && (strpos($domainPart, '-', 3) == 3))
                            || (strpos($domainPart, '-') === (strlen($domainPart) - 1))) {
                            $this->error(self::INVALID_DASH);
                            $status = false;
                            break 2;
                        }

                        // Check each domain part
                        $checked = false;
                        foreach ($regexChars as $regexKey => $regexChar) {
                            ErrorHandler::start();
                            $status = preg_match($regexChar, $domainPart);
                            ErrorHandler::stop();
                            if ($status > 0) {
                                $length = 63;
                                if (array_key_exists(strtoupper($this->tld), $this->idnLength)
                                    && (array_key_exists($regexKey, $this->idnLength[strtoupper($this->tld)]))) {
                                    $length = $this->idnLength[strtoupper($this->tld)];
                                }

                                if (iconv_strlen($domainPart, 'UTF-8') > $length) {
                                    $this->error(self::INVALID_HOSTNAME);
                                } else {
                                    $checked = true;
                                    break;
                                }
                            }
                        }

                        if ($checked) {
                            ++$check;
                        }
                    }

                    // If one of the labels doesn't match, the hostname is invalid
                    if ($check !== count($domainParts)) {
                        $this->error(self::INVALID_HOSTNAME_SCHEMA);
                        $status = false;
                    }
                } else {
                    // Hostname not long enough
                    $this->error(self::UNDECIPHERABLE_TLD);
                    $status = false;
                }
            } while (false);

            iconv_set_encoding('internal_encoding', $origenc);
            // If the input passes as an Internet domain name, and domain names are allowed, then the hostname
            // passes validation
            if ($status && ($this->getAllow() & self::ALLOW_DNS)) {
                return true;
            }
        } elseif ($this->getAllow() & self::ALLOW_DNS) {
            $this->error(self::INVALID_HOSTNAME);
        }

        // Check for URI Syntax (RFC3986)
        if ($this->getAllow() & self::ALLOW_URI) {
            if (preg_match("/^([a-zA-Z0-9-._~!$&\'()*+,;=]|%[[:xdigit:]]{2}){1,254}$/i", $value)) {
                return true;
            } else {
                $this->error(self::INVALID_URI);
            }
        }

        // Check input against local network name schema; last chance to pass validation
        ErrorHandler::start();
        $regexLocal = '/^(([a-zA-Z0-9\x2d]{1,63}\x2e)*[a-zA-Z0-9\x2d]{1,63}[\x2e]{0,1}){1,254}$/';
        $status = preg_match($regexLocal, $value);
        ErrorHandler::stop();

        // If the input passes as a local network name, and local network names are allowed, then the
        // hostname passes validation
        $allowLocal = $this->getAllow() & self::ALLOW_LOCAL;
        if ($status && $allowLocal) {
            return true;
        }

        // If the input does not pass as a local network name, add a message
        if (!$status) {
            $this->error(self::INVALID_LOCAL_NAME);
        }

        // If local network names are not allowed, add a message
        if ($status && !$allowLocal) {
            $this->error(self::LOCAL_NAME_NOT_ALLOWED);
        }

        return false;
    }

    /**
     * Decodes a punycode encoded string to it's original utf8 string
     * In case of a decoding failure the original string is returned
     *
     * @param  string $encoded Punycode encoded string to decode
     * @return string
     */
    protected function decodePunycode($encoded)
    {
        $found = preg_match('/([^a-z0-9\x2d]{1,10})$/i', $encoded);
        if (empty($encoded) || ($found > 0)) {
            // no punycode encoded string, return as is
            $this->error(self::CANNOT_DECODE_PUNYCODE);
            return false;
        }

        $separator = strrpos($encoded, '-');
        if ($separator > 0) {
            for ($x = 0; $x < $separator; ++$x) {
                // prepare decoding matrix
                $decoded[] = ord($encoded[$x]);
            }
        } else {
            $this->error(self::CANNOT_DECODE_PUNYCODE);
            return false;
        }

        $lengthd = count($decoded);
        $lengthe = strlen($encoded);

        // decoding
        $init  = true;
        $base  = 72;
        $index = 0;
        $char  = 0x80;

        for ($indexe = ($separator) ? ($separator + 1) : 0; $indexe < $lengthe; ++$lengthd) {
            for ($old_index = $index, $pos = 1, $key = 36; 1 ; $key += 36) {
                $hex   = ord($encoded[$indexe++]);
                $digit = ($hex - 48 < 10) ? $hex - 22
                       : (($hex - 65 < 26) ? $hex - 65
                       : (($hex - 97 < 26) ? $hex - 97
                       : 36));

                $index += $digit * $pos;
                $tag    = ($key <= $base) ? 1 : (($key >= $base + 26) ? 26 : ($key - $base));
                if ($digit < $tag) {
                    break;
                }

                $pos = (int) ($pos * (36 - $tag));
            }

            $delta   = intval($init ? (($index - $old_index) / 700) : (($index - $old_index) / 2));
            $delta  += intval($delta / ($lengthd + 1));
            for ($key = 0; $delta > 910 / 2; $key += 36) {
                $delta = intval($delta / 35);
            }

            $base   = intval($key + 36 * $delta / ($delta + 38));
            $init   = false;
            $char  += (int) ($index / ($lengthd + 1));
            $index %= ($lengthd + 1);
            if ($lengthd > 0) {
                for ($i = $lengthd; $i > $index; $i--) {
                    $decoded[$i] = $decoded[($i - 1)];
                }
            }

            $decoded[$index++] = $char;
        }

        // convert decoded ucs4 to utf8 string
        foreach ($decoded as $key => $value) {
            if ($value < 128) {
                $decoded[$key] = chr($value);
            } elseif ($value < (1 << 11)) {
                $decoded[$key]  = chr(192 + ($value >> 6));
                $decoded[$key] .= chr(128 + ($value & 63));
            } elseif ($value < (1 << 16)) {
                $decoded[$key]  = chr(224 + ($value >> 12));
                $decoded[$key] .= chr(128 + (($value >> 6) & 63));
                $decoded[$key] .= chr(128 + ($value & 63));
            } elseif ($value < (1 << 21)) {
                $decoded[$key]  = chr(240 + ($value >> 18));
                $decoded[$key] .= chr(128 + (($value >> 12) & 63));
                $decoded[$key] .= chr(128 + (($value >> 6) & 63));
                $decoded[$key] .= chr(128 + ($value & 63));
            } else {
                $this->error(self::CANNOT_DECODE_PUNYCODE);
                return false;
            }
        }

        return implode($decoded);
    }
}
