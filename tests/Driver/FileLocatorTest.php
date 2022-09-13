<?php

declare(strict_types=1);

namespace Metadata\Tests\Driver;

use Metadata\Driver\FileLocator;
use Metadata\Tests\Driver\Fixture\A\A;
use Metadata\Tests\Driver\Fixture\B\B;
use Metadata\Tests\Driver\Fixture\C\SubDir\C;
use Metadata\Tests\Driver\Fixture\T\T;
use PHPUnit\Framework\TestCase;

class FileLocatorTest extends TestCase
{
    public function testFindFileForClass()
    {
        $locator = new FileLocator([
            'Metadata\Tests\Driver\Fixture\A' => __DIR__ . '/Fixture/A',
            'Metadata\Tests\Driver\Fixture\B' => __DIR__ . '/Fixture/B',
            'Metadata\Tests\Driver\Fixture\C' => __DIR__ . '/Fixture/C',
        ]);

        $ref = new \ReflectionClass(A::class);
        $this->assertEquals(realpath(__DIR__ . '/Fixture/A/A.xml'), realpath($locator->findFileForClass($ref, 'xml')));

        $ref = new \ReflectionClass(B::class);
        $this->assertNull($locator->findFileForClass($ref, 'xml'));

        $ref = new \ReflectionClass(C::class);
        $this->assertEquals(realpath(__DIR__ . '/Fixture/C/SubDir.C.yml'), realpath($locator->findFileForClass($ref, 'yml')));
    }

    public function testPossibleFilesForClass()
    {
        $locator = new FileLocator([
            'Metadata\Tests\Driver\Fixture\A' => __DIR__ . '/Fixture/A',
            'Metadata\Tests\Driver\Fixture\B' => __DIR__ . '/Fixture/B',
            'Metadata\Tests\Driver\Fixture\C' => __DIR__ . '/Fixture/C',
        ]);

        $ref = new \ReflectionClass(A::class);
        $this->assertEquals([__DIR__ . '/Fixture/A/A.xml' => true], $locator->getPossibleFilesForClass($ref, 'xml'));

        $ref = new \ReflectionClass(B::class);
        $this->assertEquals([__DIR__ . '/Fixture/B/B.xml' => false], $locator->getPossibleFilesForClass($ref, 'xml'));

        $ref = new \ReflectionClass(C::class);
        $this->assertEquals([__DIR__ . '/Fixture/C/SubDir.C.yml' => true], $locator->getPossibleFilesForClass($ref, 'yml'));
    }

    public function testTraits()
    {
        $locator = new FileLocator([
            'Metadata\Tests\Driver\Fixture\T' => __DIR__ . '/Fixture/T',
        ]);

        $ref = new \ReflectionClass(T::class);
        $this->assertEquals(realpath(__DIR__ . '/Fixture/T/T.xml'), realpath($locator->findFileForClass($ref, 'xml')));
    }

    public function testFindFileForGlobalNamespacedClass()
    {
        $locator = new FileLocator([
            '' => __DIR__ . '/Fixture/D',
        ]);

        require_once __DIR__ . '/Fixture/D/D.php';
        $ref = new \ReflectionClass('D');
        $this->assertEquals(realpath(__DIR__ . '/Fixture/D/D.yml'), realpath($locator->findFileForClass($ref, 'yml')));
    }

    public function testFindAllFiles()
    {
        $locator = new FileLocator([
            'Metadata\Tests\Driver\Fixture\A' => __DIR__ . '/Fixture/A',
            'Metadata\Tests\Driver\Fixture\B' => __DIR__ . '/Fixture/B',
            'Metadata\Tests\Driver\Fixture\C' => __DIR__ . '/Fixture/C',
            'Metadata\Tests\Driver\Fixture\D' => __DIR__ . '/Fixture/D',
        ]);

        $this->assertCount(1, $xmlFiles = $locator->findAllClasses('xml'));
        $this->assertSame('Metadata\Tests\Driver\Fixture\A\A', $xmlFiles[0]);

        $this->assertCount(3, $ymlFiles = $locator->findAllClasses('yml'));
        $this->assertSame('Metadata\Tests\Driver\Fixture\B\B', $ymlFiles[0]);
        $this->assertSame('Metadata\Tests\Driver\Fixture\C\SubDir\C', $ymlFiles[1]);
        $this->assertSame('Metadata\Tests\Driver\Fixture\D\D', $ymlFiles[2]);
    }
}
