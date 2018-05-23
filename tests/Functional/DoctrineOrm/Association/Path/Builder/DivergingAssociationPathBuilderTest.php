<?php

namespace Xsolve\AssociateTests\Functional\DoctrineOrm\Association\Path\Builder;

use PHPUnit\Framework\TestCase;
use Xsolve\Associate\DoctrineOrm\Association\Path\Builder\DivergingAssociationPathBuilder;
use Xsolve\Associate\DoctrineOrm\Association\Path\DivergingAssociationPath;

class DivergingAssociationPathBuilderTest extends TestCase
{
    // TODO Assert alias.
    // TODO Assert load mode.

    public function testCase1()
    {
        $divergingAssociationPathBuilder = new DivergingAssociationPathBuilder();

        $divergingAssociationPath = $divergingAssociationPathBuilder
            ->associate('bar')
                ->aliasAs('bars')
                ->loadFull()
            ->associate('baz')
            ->associate('qux')
                ->aliasAs('qux')
                ->loadProxy()
            ->create();

        $this->assertInstanceOf(DivergingAssociationPath::class, $divergingAssociationPath);
        $this->assertSame(
            [
                '.bar.baz.qux',
            ],
            $this->convertDivergingAssociationPathToStrings($divergingAssociationPath)
        );
    }

    public function testCase2()
    {
        $divergingAssociationPathBuilder = new DivergingAssociationPathBuilder();

        $divergingAssociationPath = $divergingAssociationPathBuilder
            ->associate('bar')
            ->associate('baz')
            ->associate('qux')
                ->loadFull()
            ->create();

        $this->assertInstanceOf(DivergingAssociationPath::class, $divergingAssociationPath);
        $this->assertSame(
            [
                '.bar.baz.qux',
            ],
            $this->convertDivergingAssociationPathToStrings($divergingAssociationPath)
        );
    }

    public function testCase3()
    {
        $divergingAssociationPathBuilder = new DivergingAssociationPathBuilder();

        $divergingAssociationPath = $divergingAssociationPathBuilder
            ->diverge()
                ->associate('foo')
                ->diverge()
                    ->associate('bar')
                ->endDiverge()
                ->diverge()
                    ->associate('baz')
                        ->aliasAs('.foo.baz')
                        ->loadProxy()
                    ->associate('qux')
                ->endDiverge()
            ->endDiverge()
            ->diverge()
                ->associate('qux')
            ->endDiverge()
            ->create();

        $this->assertInstanceOf(DivergingAssociationPath::class, $divergingAssociationPath);
        $this->assertSame(
            [
                '.foo.bar',
                '.foo.baz.qux',
                '.qux',
            ],
            $this->convertDivergingAssociationPathToStrings($divergingAssociationPath)
        );
    }

    public function testCase4()
    {
        $divergingAssociationPathBuilder = new DivergingAssociationPathBuilder();

        $divergingAssociationPath = $divergingAssociationPathBuilder
            ->associate('foo')
            ->associate('bar')
            ->diverge()
                ->associate('baz')
                ->associate('qux')
            ->endDiverge()
            ->diverge()
                ->associate('qux')
            ->endDiverge()
            ->create();

        $this->assertInstanceOf(DivergingAssociationPath::class, $divergingAssociationPath);
        $this->assertSame(
            [
                '.foo.bar.baz.qux',
                '.foo.bar.qux',
            ],
            $this->convertDivergingAssociationPathToStrings($divergingAssociationPath)
        );
    }

    public function testCase5()
    {
        $divergingAssociationPathBuilder = new DivergingAssociationPathBuilder();

        $divergingAssociationPath = $divergingAssociationPathBuilder
            ->diverge()
                ->associate('foo')
            ->endDiverge()
            ->diverge()
                ->associate('bar')
            ->endDiverge()
            ->diverge()
                ->associate('baz')
                ->associate('qux')
            ->endDiverge()
            ->diverge()
                ->associate('qux')
            ->endDiverge()
            ->create();

        $this->assertInstanceOf(DivergingAssociationPath::class, $divergingAssociationPath);
        $this->assertSame(
            [
                '.foo',
                '.bar',
                '.baz.qux',
                '.qux',
            ],
            $this->convertDivergingAssociationPathToStrings($divergingAssociationPath)
        );
    }

    public function testCase6()
    {
        $divergingAssociationPathBuilder = new DivergingAssociationPathBuilder();

        $divergingAssociationPath = $divergingAssociationPathBuilder
            ->diverge()
                ->associate('foo')
                ->diverge()
                    ->associate('bar')
                    ->diverge()
                        ->associate('baz')
                        ->associate('qux')
                        ->diverge()
                            ->associate('qux')
            ->create();

        $this->assertInstanceOf(DivergingAssociationPath::class, $divergingAssociationPath);
        $this->assertSame(
            [
                '.foo.bar.baz.qux.qux',
            ],
            $this->convertDivergingAssociationPathToStrings($divergingAssociationPath)
        );
    }

    /**
     * @param DivergingAssociationPath $divergingAssociationPath
     *
     * @return string[]
     */
    protected function convertDivergingAssociationPathToStrings(DivergingAssociationPath $divergingAssociationPath): array
    {
        $path = '';
        foreach ($divergingAssociationPath->getAssociations() as $association) {
            $path .= sprintf('.%s', $association->getRelationshipName());
        }

        if (!$divergingAssociationPath->hasChildDivergingAssociationPath()) {
            return [$path];
        }

        $childPaths = [];
        foreach ($divergingAssociationPath->getChildDivergingAssociationPaths() as $childDivergingAssociationPath) {
            $childPaths = array_merge(
                $childPaths,
                $this->convertDivergingAssociationPathToStrings($childDivergingAssociationPath)
            );
        }
        foreach (array_keys($childPaths) as $childPathIndex) {
            $childPaths[$childPathIndex] = $path . $childPaths[$childPathIndex];
        }

        return $childPaths;
    }
}
