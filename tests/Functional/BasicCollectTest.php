<?php

namespace Xsolve\AssociateTests\Functional;

use PHPUnit\Framework\TestCase;
use Xsolve\Associate\Facade;
use Xsolve\AssociateTests\Functional\Model\Foo;
use Xsolve\AssociateTests\Functional\Model\Bar;
use Xsolve\AssociateTests\Functional\Model\Baz;

class BasicCollectTest extends TestCase
{
    /**
     * @param array $baseObjects
     * @param array $associationPath
     * @param array $expectedAssociatedObjects
     *
     * @dataProvider provideData_basicTraversal
     */
    public function test_basicTraversal(
        array $baseObjects,
        array $associationPath,
        array $expectedAssociatedObjects
    ) {
        $facade = new Facade();
        $collector = $facade->getBasicCollector();

        $actualAssociatedObjects = $collector->collect($baseObjects, $associationPath);

        $this->assertArraysAreSimilar($expectedAssociatedObjects, $actualAssociatedObjects);
    }

    /**
     * @return array
     */
    public function provideData_basicTraversal()
    {
        $text1 = 'lorem ipsum';
        $words1 = explode(' ', $text1);

        $text2 = 'dolor';
        $words2 = explode(' ', $text2);

        $text3 = 'sit amet malef';
        $words3 = explode(' ', 'sit amet malef');

        $text4 = 'dolor sit';
        $words4 = explode(' ', $text4);

        $foo1 = new Foo(
            $bar1 = new Bar([
                $baz1 = new Baz($text1),
                $baz2 = new Baz($text2),
            ])
        );

        $foo2 = new Foo(
            $bar2 = new Bar([
                $baz1,
                $baz3 = new Baz($text3),
                $baz4 = new Baz($text4),
            ])
        );

        $foo3 = new Foo();

        return [
            [
                [$foo1, $foo2, $foo3],
                ['bar'],
                [$bar1, $bar2],
            ],
            [
                [$bar1, $bar2],
                ['bazs'],
                [$baz1, $baz2, $baz3, $baz4],
            ],
            [
                [$foo1, $foo2, $foo3],
                ['bar', 'bazs'],
                [$baz1, $baz2, $baz3, $baz4],
            ],
            [
                [$foo1, $foo2, $foo3],
                ['bar', 'bazs', 'text'],
                [$text1, $text2, $text3, $text4],
            ],
            [
                [$foo1, $foo2, $foo3],
                ['bar', 'bazs', 'words'],
                array_merge($words1, $words2, $words3, $words4),
            ],
            [
                [$foo1, $foo2, $foo3],
                ['bar', 'bazs', 'textStats', '[wordCount]'],
                [count($words1), count($words2), count($words3), count($words4)],
            ],
        ];
    }

    /**
     * @param array  $expected
     * @param array  $actual
     * @param string $message
     */
    protected function assertArraysAreSimilar(array $expected, array $actual, string $message = '')
    {
        $this->assertTrue(
            $this->areArraysSimilar($expected, $actual),
            $message
        );
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return bool
     */
    protected function areArraysSimilar(array $array1, array $array2): bool
    {
        if (count($array1) !== count($array2)) {
            return false;
        }

        $matchedKeys = [];
        foreach ($array1 as $value1) {
            foreach ($array2 as $key => $value2) {
                if (
                    !array_key_exists($key, $matchedKeys)
                    && $value2 === $value1
                ) {
                    $matchedKeys[$key] = true;
                }
            }
        }

        return count($matchedKeys) == count($array2);
    }
}
