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
 * This is a class that overrides Zend_Xml_Security to mark the heuristicScan()
 * method as public, allowing us to test it.
 *
 * @see Zend_Xml_Security
 */
require_once 'Zend/Xml/TestAsset/Security.php';

require_once 'Zend/Xml/Exception.php';

/**
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Xml
 * @group      ZF2015-06
 */
class Zend_Xml_MultibyteTest extends \PHPUnit\Framework\TestCase
{
    public static function main()
    {
        $suite = new \PHPUnit\Framework\TestSuite(self::class);
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    public function multibyteEncodings()
    {
        return [
            'UTF-16LE' => ['UTF-16LE', pack('CC', 0xFF, 0xFE), 3],
            'UTF-16BE' => ['UTF-16BE', pack('CC', 0xFE, 0xFF), 3],
            'UTF-32LE' => ['UTF-32LE', pack('CCCC', 0xFF, 0xFE, 0x00, 0x00), 4],
            'UTF-32BE' => ['UTF-32BE', pack('CCCC', 0x00, 0x00, 0xFE, 0xFF), 4],
        ];
    }

    public function getXmlWithXXE()
    {
        return <<<XML
            <?xml version="1.0" encoding="{ENCODING}"?>
            <!DOCTYPE methodCall [
              <!ENTITY pocdata SYSTEM "file:///etc/passwd">
            ]>
            <methodCall>
                <methodName>retrieved: &pocdata;</methodName>
            </methodCall>
            XML;
    }

    /**
     * Invoke Zend_Xml_Security::heuristicScan with the provided XML.
     *
     * @param string $xml
     */
    public function invokeHeuristicScan($xml)
    {
        return Zend_Xml_TestAsset_Security::heuristicScan($xml);
    }

    /**
     * @dataProvider multibyteEncodings
     * @group heuristicDetection
     *
     * @param mixed $encoding
     * @param mixed $bom
     * @param mixed $bomLength
     */
    public function testDetectsMultibyteXXEVectorsUnderFPMWithEncodedStringMissingBOM($encoding, $bom, $bomLength)
    {
        $xml = $this->getXmlWithXXE();
        $xml = str_replace('{ENCODING}', $encoding, $xml);
        $xml = iconv('UTF-8', $encoding, $xml);
        $this->assertNotSame(0, strncmp($xml, $bom, $bomLength));
        $this->expectException(\Zend_Xml_Exception::class, 'ENTITY');
        $this->invokeHeuristicScan($xml);
    }

    /**
     * @dataProvider multibyteEncodings
     *
     * @param mixed $encoding
     * @param mixed $bom
     */
    public function testDetectsMultibyteXXEVectorsUnderFPMWithEncodedStringUsingBOM($encoding, $bom)
    {
        $xml = $this->getXmlWithXXE();
        $xml = str_replace('{ENCODING}', $encoding, $xml);
        $orig = iconv('UTF-8', $encoding, $xml);
        $xml = $bom . $orig;
        $this->expectException(\Zend_Xml_Exception::class, 'ENTITY');
        $this->invokeHeuristicScan($xml);
    }

    public function getXmlWithoutXXE()
    {
        return <<<XML
            <?xml version="1.0" encoding="{ENCODING}"?>
            <methodCall>
                <methodName>retrieved: &pocdata;</methodName>
            </methodCall>
            XML;
    }

    /**
     * @dataProvider multibyteEncodings
     *
     * @param mixed $encoding
     */
    public function testDoesNotFlagValidMultibyteXmlAsInvalidUnderFPM($encoding)
    {
        $xml = $this->getXmlWithoutXXE();
        $xml = str_replace('{ENCODING}', $encoding, $xml);
        $xml = iconv('UTF-8', $encoding, $xml);

        try {
            $result = $this->invokeHeuristicScan($xml);
            $this->assertNull($result);
        } catch (Exception $e) {
            $this->fail('Security scan raised exception when it should not have');
        }
    }

    /**
     * @dataProvider multibyteEncodings
     * @group mixedEncoding
     *
     * @param mixed $encoding
     * @param mixed $bom
     */
    public function testDetectsXXEWhenXMLDocumentEncodingDiffersFromFileEncoding($encoding, $bom)
    {
        $xml = $this->getXmlWithXXE();
        $xml = str_replace('{ENCODING}', 'UTF-8', $xml);
        $xml = iconv('UTF-8', $encoding, $xml);
        $xml = $bom . $xml;
        $this->expectException(\Zend_Xml_Exception::class, 'ENTITY');
        $this->invokeHeuristicScan($xml);
    }
}
