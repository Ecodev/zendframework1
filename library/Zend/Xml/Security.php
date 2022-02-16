<?php
/**
 * Zend Framework.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Xml_Security
{
    public const ENTITY_DETECT = 'Detected use of ENTITY in XML, disabled to prevent XXE/XEE attacks';

    /**
     * Heuristic scan to detect entity in XML.
     *
     * @param  string $xml
     */
    protected static function heuristicScan($xml)
    {
        foreach (self::getEntityComparison($xml) as $compare) {
            if (strpos($xml, $compare) !== false) {
                throw new Zend_Xml_Exception(self::ENTITY_DETECT);
            }
        }
    }

    /**
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     *
     * @return bool
     */
    public static function loadXmlErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if (substr_count($errstr, 'DOMDocument::loadXML()') > 0) {
            return true;
        }

        return false;
    }

    /**
     * Scan XML string for potential XXE and XEE attacks.
     *
     * @param   string $xml
     * @param   DomDocument $dom
     *
     * @return  bool|DomDocument|SimpleXMLElement
     */
    public static function scan($xml, ?DOMDocument $dom = null)
    {
        $loadEntities = null;
        $useInternalXmlErrors = null;
        // If running with PHP-FPM we perform an heuristic scan
        // We cannot use libxml_disable_entity_loader because of this bug
        // @see https://bugs.php.net/bug.php?id=64938
        if (self::isPhpFpm()) {
            self::heuristicScan($xml);
        }

        if (null === $dom) {
            $simpleXml = true;
            $dom = new DOMDocument();
        }

        if (!self::isPhpFpm()) {
            $useInternalXmlErrors = libxml_use_internal_errors(true);
        }

        // Load XML with network access disabled (LIBXML_NONET)
        // error disabled with @ for PHP-FPM scenario
        set_error_handler(array(\Zend_Xml_Security::class, 'loadXmlErrorHandler'), E_WARNING);

        $result = $dom->loadXml($xml, LIBXML_NONET);
        restore_error_handler();

        if (!$result) {
            // Entity load to previous setting
            if (!self::isPhpFpm()) {
                libxml_use_internal_errors($useInternalXmlErrors);
            }

            return false;
        }

        // Scan for potential XEE attacks using ENTITY, if not PHP-FPM
        if (!self::isPhpFpm()) {
            foreach ($dom->childNodes as $child) {
                if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                    if ($child->entities->length > 0) {
                        require_once 'Exception.php';

                        throw new Zend_Xml_Exception(self::ENTITY_DETECT);
                    }
                }
            }
        }

        // Entity load to previous setting
        if (!self::isPhpFpm()) {
            libxml_use_internal_errors($useInternalXmlErrors);
        }

        if (isset($simpleXml)) {
            $result = simplexml_import_dom($dom);
            if (!$result instanceof SimpleXMLElement) {
                return false;
            }

            return $result;
        }

        return $dom;
    }

    /**
     * Scan XML file for potential XXE/XEE attacks.
     *
     * @param  string $file
     * @param  DOMDocument $dom
     *
     * @return DomDocument|SimpleXMLElement
     */
    public static function scanFile($file, ?DOMDocument $dom = null)
    {
        if (!file_exists($file)) {
            require_once 'Exception.php';

            throw new Zend_Xml_Exception(
                "The file $file specified doesn't exist"
            );
        }

        return self::scan(file_get_contents($file), $dom);
    }

    /**
     * Return true if PHP is running with PHP-FPM.
     *
     * This method is mainly used to determine whether or not heuristic checks
     * (vs libxml checks) should be made, due to threading issues in libxml;
     * under php-fpm, threading becomes a concern.
     *
     * However, PHP versions 5.5.22+ and 5.6.6+ contain a patch to the
     * libxml support in PHP that makes the libxml checks viable; in such
     * versions, this method will return false to enforce those checks, which
     * are more strict and accurate than the heuristic checks.
     *
     * @return bool
     */
    public static function isPhpFpm()
    {
        return false;
    }

    /**
     * Determine and return the string(s) to use for the <!ENTITY comparison.
     *
     * @param string $xml
     *
     * @return string[]
     */
    protected static function getEntityComparison($xml)
    {
        $encodingMap = self::getAsciiEncodingMap();

        return array_map(
            array(self::class, 'generateEntityComparison'),
            self::detectXmlEncoding($xml, self::detectStringEncoding($xml))
        );
    }

    /**
     * Determine the string encoding.
     *
     * Determines string encoding from either a detected BOM or a
     * heuristic.
     *
     * @param string $xml
     *
     * @return string File encoding
     */
    protected static function detectStringEncoding($xml)
    {
        $encoding = self::detectBom($xml);

        return $encoding ?: self::detectXmlStringEncoding($xml);
    }

    /**
     * Attempt to match a known BOM.
     *
     * Iterates through the return of getBomMap(), comparing the initial bytes
     * of the provided string to the BOM of each; if a match is determined,
     * it returns the encoding.
     *
     * @param string $string
     *
     * @return false|string returns encoding on success
     */
    protected static function detectBom($string)
    {
        foreach (self::getBomMap() as $criteria) {
            if (0 === strncmp($string, $criteria['bom'], $criteria['length'])) {
                return $criteria['encoding'];
            }
        }

        return false;
    }

    /**
     * Attempt to detect the string encoding of an XML string.
     *
     * @param string $xml
     *
     * @return string Encoding
     */
    protected static function detectXmlStringEncoding($xml)
    {
        foreach (self::getAsciiEncodingMap() as $encoding => $generator) {
            $prefix = call_user_func($generator, '<' . '?xml');
            if (0 === strncmp($xml, $prefix, strlen($prefix))) {
                return $encoding;
            }
        }

        // Fallback
        return 'UTF-8';
    }

    /**
     * Attempt to detect the specified XML encoding.
     *
     * Using the file's encoding, determines if an "encoding" attribute is
     * present and well-formed in the XML declaration; if so, it returns a
     * list with both the ASCII representation of that declaration and the
     * original file encoding.
     *
     * If not, a list containing only the provided file encoding is returned.
     *
     * @param string $xml
     * @param string $fileEncoding
     *
     * @return string[] Potential XML encodings
     */
    protected static function detectXmlEncoding($xml, $fileEncoding)
    {
        $encodingMap = self::getAsciiEncodingMap();
        $generator = $encodingMap[$fileEncoding];
        $encAttr = call_user_func($generator, 'encoding="');
        $quote = call_user_func($generator, '"');
        $close = call_user_func($generator, '>');

        $closePos = strpos($xml, (string) $close);
        if (false === $closePos) {
            return array($fileEncoding);
        }

        $encPos = strpos($xml, (string) $encAttr);
        if (false === $encPos
            || $encPos > $closePos
        ) {
            return array($fileEncoding);
        }

        $encPos += strlen($encAttr);
        $quotePos = strpos($xml, (string) $quote, $encPos);
        if (false === $quotePos) {
            return array($fileEncoding);
        }

        $encoding = self::substr($xml, $encPos, $quotePos);

        return array(
            // Following line works because we're only supporting 8-bit safe encodings at this time.
            str_replace('\0', '', $encoding), // detected encoding
            $fileEncoding,                    // file encoding
        );
    }

    /**
     * Return a list of BOM maps.
     *
     * Returns a list of common encoding -> BOM maps, along with the character
     * length to compare against.
     *
     * @see https://en.wikipedia.org/wiki/Byte_order_mark
     *
     * @return array
     */
    protected static function getBomMap()
    {
        return array(
            array(
                'encoding' => 'UTF-32BE',
                'bom' => pack('CCCC', 0x00, 0x00, 0xFE, 0xFF),
                'length' => 4,
            ),
            array(
                'encoding' => 'UTF-32LE',
                'bom' => pack('CCCC', 0xFF, 0xFE, 0x00, 0x00),
                'length' => 4,
            ),
            array(
                'encoding' => 'GB-18030',
                'bom' => pack('CCCC', 0x84, 0x31, 0x95, 0x33),
                'length' => 4,
            ),
            array(
                'encoding' => 'UTF-16BE',
                'bom' => pack('CC', 0xFE, 0xFF),
                'length' => 2,
            ),
            array(
                'encoding' => 'UTF-16LE',
                'bom' => pack('CC', 0xFF, 0xFE),
                'length' => 2,
            ),
            array(
                'encoding' => 'UTF-8',
                'bom' => pack('CCC', 0xEF, 0xBB, 0xBF),
                'length' => 3,
            ),
        );
    }

    /**
     * Return a map of encoding => generator pairs.
     *
     * Returns a map of encoding => generator pairs, where the generator is a
     * callable that accepts a string and returns the appropriate byte order
     * sequence of that string for the encoding.
     *
     * @return array
     */
    protected static function getAsciiEncodingMap()
    {
        return array(
            'UTF-32BE' => array(self::class, 'encodeToUTF32BE'),
            'UTF-32LE' => array(self::class, 'encodeToUTF32LE'),
            'UTF-32odd1' => array(self::class, 'encodeToUTF32odd1'),
            'UTF-32odd2' => array(self::class, 'encodeToUTF32odd2'),
            'UTF-16BE' => array(self::class, 'encodeToUTF16BE'),
            'UTF-16LE' => array(self::class, 'encodeToUTF16LE'),
            'UTF-8' => array(self::class, 'encodeToUTF8'),
            'GB-18030' => array(self::class, 'encodeToUTF8'),
        );
    }

    /**
     * Binary-safe substr.
     *
     * substr() is not binary-safe; this method loops by character to ensure
     * multi-byte characters are aggregated correctly.
     *
     * @param string $string
     * @param int $start
     * @param int $end
     *
     * @return string
     */
    protected static function substr($string, $start, $end)
    {
        $substr = '';
        for ($i = $start; $i < $end; ++$i) {
            $substr .= $string[$i];
        }

        return $substr;
    }

    /**
     * Generate an entity comparison based on the given encoding.
     *
     * This patch is internal only, and public only so it can be used as a
     * callable to pass to array_map.
     *
     * @internal
     *
     * @param string $encoding
     *
     * @return string
     */
    public static function generateEntityComparison($encoding)
    {
        $encodingMap = self::getAsciiEncodingMap();
        $generator = $encodingMap[$encoding] ?? $encodingMap['UTF-8'];

        return call_user_func($generator, '<!ENTITY');
    }

    /**
     * Encode an ASCII string to UTF-32BE.
     *
     * @internal
     *
     * @param string $ascii
     *
     * @return string
     */
    public static function encodeToUTF32BE($ascii)
    {
        return preg_replace('/(.)/', "\0\0\0\\1", $ascii);
    }

    /**
     * Encode an ASCII string to UTF-32LE.
     *
     * @internal
     *
     * @param string $ascii
     *
     * @return string
     */
    public static function encodeToUTF32LE($ascii)
    {
        return preg_replace('/(.)/', "\\1\0\0\0", $ascii);
    }

    /**
     * Encode an ASCII string to UTF-32odd1.
     *
     * @internal
     *
     * @param string $ascii
     *
     * @return string
     */
    public static function encodeToUTF32odd1($ascii)
    {
        return preg_replace('/(.)/', "\0\\1\0\0", $ascii);
    }

    /**
     * Encode an ASCII string to UTF-32odd2.
     *
     * @internal
     *
     * @param string $ascii
     *
     * @return string
     */
    public static function encodeToUTF32odd2($ascii)
    {
        return preg_replace('/(.)/', "\0\0\\1\0", $ascii);
    }

    /**
     * Encode an ASCII string to UTF-16BE.
     *
     * @internal
     *
     * @param string $ascii
     *
     * @return string
     */
    public static function encodeToUTF16BE($ascii)
    {
        return preg_replace('/(.)/', "\0\\1", $ascii);
    }

    /**
     * Encode an ASCII string to UTF-16LE.
     *
     * @internal
     *
     * @param string $ascii
     *
     * @return string
     */
    public static function encodeToUTF16LE($ascii)
    {
        return preg_replace('/(.)/', "\\1\0", $ascii);
    }

    /**
     * Encode an ASCII string to UTF-8.
     *
     * @internal
     *
     * @param string $ascii
     *
     * @return string
     */
    public static function encodeToUTF8($ascii)
    {
        return $ascii;
    }
}
