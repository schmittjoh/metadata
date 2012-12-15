<?php

namespace Metadata\Tests\Driver;

use Metadata\Driver\FileLocator;

class FileLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testFindFileForClass()
    {
        $locator = new FileLocator(array(
            'Metadata\Tests\Driver\Fixture\A' => __DIR__.'/Fixture/A',
            'Metadata\Tests\Driver\Fixture\B' => __DIR__.'/Fixture/B',
            'Metadata\Tests\Driver\Fixture\C' => __DIR__.'/Fixture/C',
        ));

        $ref = new \ReflectionClass('Metadata\Tests\Driver\Fixture\A\A');
        $this->assertEquals(realpath(__DIR__.'/Fixture/A/A.xml'), realpath($locator->findFileForClass($ref, 'xml')));

        $ref = new \ReflectionClass('Metadata\Tests\Driver\Fixture\B\B');
        $this->assertNull($locator->findFileForClass($ref, 'xml'));

        $ref = new \ReflectionClass('Metadata\Tests\Driver\Fixture\C\SubDir\C');
        $this->assertEquals(realpath(__DIR__.'/Fixture/C/SubDir.C.yml'), realpath($locator->findFileForClass($ref, 'yml')));
    }

    public function testTraits()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('No traits available');
        }

        $locator = new FileLocator(array(
            'Metadata\Tests\Driver\Fixture\T' => __DIR__.'/Fixture/T',
        ));

        $ref = new \ReflectionClass('Metadata\Tests\Driver\Fixture\T\T');
        $this->assertEquals(realpath(__DIR__.'/Fixture/T/T.xml'), realpath($locator->findFileForClass($ref, 'xml')));
    }

    public function testFindFileForGlobalNamespacedClass()
    {
        $locator = new FileLocator(array(
            '' => __DIR__.'/Fixture/D',
        ));

        require_once __DIR__.'/Fixture/D/D.php';
        $ref = new \ReflectionClass('D');
        $this->assertEquals(realpath(__DIR__.'/Fixture/D/D.yml'), realpath($locator->findFileForClass($ref, 'yml')));
    }

    public function testFindAllFiles()
    {
        $locator = new FileLocator(array(
            'Metadata\Tests\Driver\Fixture\A' => __DIR__.'/Fixture/A',
            'Metadata\Tests\Driver\Fixture\B' => __DIR__.'/Fixture/B',
            'Metadata\Tests\Driver\Fixture\C' => __DIR__.'/Fixture/C',
            'Metadata\Tests\Driver\Fixture\D' => __DIR__.'/Fixture/D'
        ));

        $this->assertCount(1, $xmlFiles = $locator->findAllClasses('xml'));
        $this->assertSame('Metadata\Tests\Driver\Fixture\A\A', $xmlFiles[0]);

        $this->assertCount(3, $ymlFiles = $locator->findAllClasses('yml'));
        $this->assertSame('Metadata\Tests\Driver\Fixture\B\B', $ymlFiles[0]);
        $this->assertSame('Metadata\Tests\Driver\Fixture\C\SubDir\C', $ymlFiles[1]);
        $this->assertSame('Metadata\Tests\Driver\Fixture\D\D', $ymlFiles[2]);
    }
}
