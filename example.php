<?php

require_once __DIR__ . '/vendor/autoload.php';

class Foo
{
    /**
     * @var Bar|null
     */
    protected $bar;

    /**
     * @param Bar|null $bar
     */
    public function __construct(Bar $bar = null)
    {
        $this->bar = $bar;
    }

    /**
     * @return Bar|null
     */
    public function getBar()
    {
        return $this->bar;
    }
}

class Bar
{
    /**
     * @var Baz[]
     */
    public $bazs;

    /**
     * @param Baz[] $bazs
     */
    public function __construct(array $bazs)
    {
        $this->bazs = $bazs;
    }
}

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

$foos = [
    $foo1 = new Foo(
        $bar1 = new Bar([
            $baz1 = new Baz('lorem ipsum'),
            $baz2 = new Baz('dolor'),
        ])
    ),
    $foo2 = new Foo(
        $bar2 = new Bar([
            $baz1,
            $baz3 = new Baz('sit amet malef'),
            $baz4 = new Baz('dolor sit'),
        ])
    ),
    $foo3 = new Foo(),
];

$facade = new \Xsolve\Associate\Facade();
$basicCollector = $facade->getBasicCollector();

$bars = $basicCollector->collect($foos, ['bar']);
var_dump($bars);
die;

$bazs = $basicCollector->collect($foos, ['bar', 'bazs']);
var_dump($bazs);

$texts = $basicCollector->collect($foos, ['bar', 'bazs', 'text']);
var_dump($texts);

$words = $basicCollector->collect($foos, ['bar', 'bazs', 'words']);
var_dump($words);

$wordCounts = $basicCollector->collect($foos, ['bar', 'bazs', 'textStats', '[wordCount]']);
var_dump($wordCounts);
