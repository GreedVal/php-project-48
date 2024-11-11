<?php

namespace Differ\tests\GenDiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $this->checkDiff('file1.json', 'file2.json', 'expectedStylish', 'stylish');
        $this->checkDiff('file1.yaml', 'file2.yaml', 'expectedStylish', 'stylish');

        $this->checkDiff('file1.json', 'file2.json', 'expectedPlain', 'plain');
        $this->checkDiff('file1.yaml', 'file2.yaml', 'expectedPlain', 'plain');

        $this->checkDiff('file1.json', 'file2.json', 'expectedJson', 'json');
        $this->checkDiff('file1.yaml', 'file2.yaml', 'expectedJson', 'json');
    }

    private function checkDiff($fixture1Name, $fixture2Name, $expectedFileName, $format)
    {
        $fixture1 = $this->getPathToFixture($fixture1Name);
        $fixture2 = $this->getPathToFixture($fixture2Name);
        $actual = genDiff($fixture1, $fixture2, $format);
        $expected = file_get_contents($this->getPathToFixture($expectedFileName));

        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);

        $this->assertSame($expected, $actual);
    }

    private function getPathToFixture($fixtureName)
    {
        return __DIR__ . "/fixtures/" . $fixtureName;
    }
}
