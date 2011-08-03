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
}