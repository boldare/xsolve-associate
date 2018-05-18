<?php

namespace Xsolve\Associate\AssociationPath;

class AssociationPathsCollection
{
    /**
     * @var AssociationPath[]
     */
    protected $associationPaths;

    /**
     * @param AssociationPath[] $associationPaths
     */
    public function __construct(array $associationPaths = [])
    {
        $this->associationPaths = $this->sortUniqueAssociatedPaths($associationPaths);
    }

    /**
     * @return AssociationPath[]
     */
    public function getAllAssociationPaths(): array
    {
        return $this->associationPaths;
    }

    /**
     * @param self $anotherAssociationPathCollection
     *
     * @return self
     */
    public function merge(self $anotherAssociationPathCollection): self
    {
        return new self(array_merge(
            $this->associationPaths,
            $anotherAssociationPathCollection->associationPaths
        ));
    }

    /**
     * @param AssociationPath $associationPath
     *
     * @return AssociationPath|null
     */
    public function getParentAssociationPath(AssociationPath $associationPath)
    {
        foreach (array_reverse($this->associationPaths) as $anotherAssociationPath) {
            /* @var AssociationPath $anotherAssociationPath */
            if (
                $associationPath->getDepth() > $anotherAssociationPath->getDepth()
                && $associationPath->isPartiallyEqual($anotherAssociationPath)
            ) {
                return $anotherAssociationPath;
            }
        }
    }

    /**
     * @param AssociationPath $associationPath
     *
     * @return bool
     */
    public function hasAssociationPathOrItsChild(AssociationPath $associationPath): bool
    {
        foreach (array_reverse($this->associationPaths) as $anotherAssociationPath) {
            /* @var AssociationPath $anotherAssociationPath */
            if (
                $associationPath->getDepth() <= $anotherAssociationPath->getDepth()
                && $associationPath->isPartiallyEqual($anotherAssociationPath)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param AssociationPath[] $associationPaths
     *
     * @return AssociationPath[]
     */
    protected function sortUniqueAssociatedPaths(array $associationPaths): array
    {
        if (empty($associationPaths)) {
            return [];
        }

        usort($associationPaths, [AssociationPath::class, 'compare']);

        $uniqueAssociationPaths = [reset($associationPaths)];
        $i = 1;
        $iMax = count($associationPaths);
        while ($i < $iMax) {
            if (!$associationPaths[$i - 1]->isEqual($associationPaths[$i])) {
                $uniqueAssociationPaths[] = $associationPaths[$i];
            }

            ++$i;
        }

        return $uniqueAssociationPaths;
    }
}
