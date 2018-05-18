<?php

namespace Xsolve\LegacyAssociateTests\Functional\Model;

class Baz
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string[]
     */
    public function getWords(): array
    {
        return explode(' ', $this->text);
    }

    /**
     * @return array
     */
    public function getTextStats(): array
    {
        return ['wordCount' => count($this->getWords())];
    }
}
