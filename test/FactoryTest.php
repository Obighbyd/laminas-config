<?php

/**
 * @see       https://github.com/laminas/laminas-config for the canonical source repository
 * @copyright https://github.com/laminas/laminas-config/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-config/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Config;

use Laminas\Config\Factory;

/**
 * @category   Laminas
 * @package    Laminas_Config
 * @subpackage UnitTests
 * @group      Laminas_Config
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFromIni()
    {
        $config = Factory::fromFile(__DIR__ . '/TestAssets/Ini/include-base.ini');

        $this->assertEquals('bar', $config['base']['foo']);
    }

    public function testFromXml()
    {
        $config = Factory::fromFile(__DIR__ . '/TestAssets/Xml/include-base.xml');

        $this->assertEquals('bar', $config['base']['foo']);
    }

    public function testFromIniFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Ini/include-base.ini',
            __DIR__ . '/TestAssets/Ini/include-base2.ini'
        );
        $config = Factory::fromFiles($files);

        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
    }

    public function testFromXmlFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Xml/include-base.xml',
            __DIR__ . '/TestAssets/Xml/include-base2.xml'
        );
        $config = Factory::fromFiles($files);

        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
    }

    public function testFromPhpFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Php/include-base.php',
            __DIR__ . '/TestAssets/Php/include-base2.php'
        );
        $config = Factory::fromFiles($files);

        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
    }

    public function testFromIniAndXmlAndPhpFiles()
    {
        $files = array (
            __DIR__ . '/TestAssets/Ini/include-base.ini',
            __DIR__ . '/TestAssets/Xml/include-base2.xml',
            __DIR__ . '/TestAssets/Php/include-base3.php',
        );
        $config = Factory::fromFiles($files);

        $this->assertEquals('bar', $config['base']['foo']);
        $this->assertEquals('baz', $config['test']['bar']);
        $this->assertEquals('baz', $config['last']['bar']);
    }

    public function testReturnsConfigObjectIfRequestedAndArrayOtherwise()
    {
        $files = array (
            __DIR__ . '/TestAssets/Ini/include-base.ini',
        );

        $configArray = Factory::fromFile($files[0]);
        $this->assertTrue(is_array($configArray));

        $configArray = Factory::fromFiles($files);
        $this->assertTrue(is_array($configArray));

        $configObject = Factory::fromFile($files[0], true);
        $this->assertInstanceOf('Laminas\Config\Config', $configObject);

        $configObject = Factory::fromFiles($files, true);
        $this->assertInstanceOf('Laminas\Config\Config', $configObject);
    }

    public function testNonExistentFileThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException');
        $config = Factory::fromFile('foo.bar');
    }

    public function testUnsupportedFileExtensionThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException');
        $config = Factory::fromFile(__DIR__ . '/TestAssets/bad.ext');
    }

    public function testFactoryCanRegisterCustomReaderInstance()
    {
        Factory::registerReader('dum', new Reader\TestAssets\DummyReader());

        $configObject = Factory::fromFile(__DIR__ . '/TestAssets/dummy.dum', true);
        $this->assertInstanceOf('Laminas\Config\Config', $configObject);

        $this->assertEquals($configObject['one'], 1);
    }

    public function testFactoryCanRegisterCustomReaderPlugn()
    {
        $dummyReader = new Reader\TestAssets\DummyReader();
        Factory::getReaderPluginManager()->setService('DummyReader', $dummyReader);

        Factory::registerReader('dum', 'DummyReader');

        $configObject = Factory::fromFile(__DIR__ . '/TestAssets/dummy.dum', true);
        $this->assertInstanceOf('Laminas\Config\Config', $configObject);

        $this->assertEquals($configObject['one'], 1);
    }


}

