<?php

namespace Differ\tests\GenDiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff($fixture1Name, $fixture2Name, $expectedFileName, $format)
    {
        $fixture1 = $this->getPathToFixture($fixture1Name);
        $fixture2 = $this->getPathToFixture($fixture2Name);
        $expectedPath = $this->getPathToFixture($expectedFileName);

        $this->assertStringEqualsFile($expectedPath, genDiff($fixture1, $fixture2, $format));
    }

    public function diffDataProvider()
    {
        return [
            ['file1.json', 'file2.json', 'expectedStylish', 'stylish'],
            ['file1.yaml', 'file2.yaml', 'expectedStylish', 'stylish'],
            ['file1.json', 'file2.yaml', 'expectedStylish', 'stylish'],
            ['file1.yaml', 'file2.json', 'expectedStylish', 'stylish'],

            ['file1.json', 'file2.json', 'expectedPlain', 'plain'],
            ['file1.yaml', 'file2.yaml', 'expectedPlain', 'plain'],
            ['file1.json', 'file2.yaml', 'expectedPlain', 'plain'],
            ['file1.yaml', 'file2.json', 'expectedPlain', 'plain'],

            ['file1.json', 'file2.json', 'expectedJson', 'json'],
            ['file1.yaml', 'file2.yaml', 'expectedJson', 'json'],
            ['file1.json', 'file2.yaml', 'expectedJson', 'json'],
            ['file1.yaml', 'file2.json', 'expectedJson', 'json'],
        ];
    }

    private function getPathToFixture($fixtureName)
    {
        return __DIR__ . "/fixtures/" . $fixtureName;
    }
}
