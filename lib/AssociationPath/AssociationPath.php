<?php

namespace Xsolve\Associate\AssociationPath;

class AssociationPath
{
    /**
     * @var string[]
     */
    protected $associationNames;

    /**
     * @param string[] $associationNames
     */
    public function __construct(array $associationNames)
    {
        $this->associationNames = $associationNames;
    }

    /**
     * @return string[]
     */
    public function getAssociationNames()
    {
        return $this->associationNames;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return count($this->associationNames);
    }

    /**
     * @param self $anotherAssociationPath
     *
     * @return self
     *
     * @throws \Exception
     */
    public function getRelativeTo(self $anotherAssociationPath): self
    {
        if (
            $anotherAssociationPath->getDepth() > $this->getDepth()
            || !$this->isPartiallyEqual($anotherAssociationPath)
        ) {
            throw new \Exception();
        }

        return new self(array_slice($this->associationNames, $anotherAssociationPath->getDepth()));
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return empty($this->associationNames);
    }

    /**
     * @param AssociationPath $anotherAssociationPath
     *
     * @return bool
     */
    public function isPartiallyEqual(self $anotherAssociationPath): bool
    {
        $i = 0;
        $iMax = min($this->getDepth(), $anotherAssociationPath->getDepth());
        while ($i < $iMax) {
            if ($this->associationNames[$i] !== $anotherAssociationPath->associationNames[$i]) {
                return false;
            }

            $i += 1;
        }

        return true;
    }

    /**
     * @param AssociationPath $anotherAssociationPath
     *
     * @return bool
     */
    public function isEqual(self $anotherAssociationPath): bool
    {
        if ($this->getDepth() !== $anotherAssociationPath->getDepth()) {
            return false;
        }

        $i = 0;
        $iMax = $this->getDepth();
        while ($i < $iMax) {
            if ($this->associationNames[$i] !== $anotherAssociationPath->associationNames[$i]) {
                return false;
            }

            $i += 1;
        }

        return true;
    }

    /**
     * @param self $associationPath1
     * @param self $associationPath2
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function compare(self $associationPath1, self $associationPath2): int
    {
        if ($associationPath1->isEqual($associationPath2)) {
            return 0;
        }

        $comparison = $associationPath1->getDepth() <=> $associationPath2->getDepth();

        if (0 !== $comparison) {
            return $comparison;
        }

        $i = 0;
        $iMax = $associationPath1->getDepth();
        while ($i < $iMax) {
            $comparison = $associationPath1->associationNames[$i] <=> $associationPath2->associationNames[$i];

            if (0 !== $comparison) {
                return $comparison;
            }

            $i += 1;
        }

        throw new \Exception();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('.', $this->associationNames);
    }
}
