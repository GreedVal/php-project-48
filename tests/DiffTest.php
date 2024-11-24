<?php

namespace Differ\tests\GenDiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * инициализирую провайдер.
     *
     * @dataProvider diffDataProvider
     */

    public function testGenDiff($fixture1Name, $fixture2Name, $expectedFileName, $format)
    {
        $fixture1 = $this->getPathToFixture($fixture1Name);
        $fixture2 = $this->getPathToFixture($fixture2Name);
        $expectedPath = $this->getPathToFixture($expectedFileName);

        $expected = $this->normalizeLineEndings(file_get_contents($expectedPath));
        $actual = $this->normalizeLineEndings(genDiff($fixture1, $fixture2, $format));

        $this->assertSame($expected, $actual);

    }

    public static function diffDataProvider()
    {
        return [
            ['file1.json', 'file2.json', 'expectedStylish', 'stylish'],
            ['file1.yaml', 'file2.yaml', 'expectedStylish', 'stylish'],
            ['file1.json', 'file2.yaml', 'expectedStylish', 'stylish'],
            ['file1.yaml', 'file2.json', 'expectedStylish', 'stylish'],
            ['file1.yml', 'file2.yml', 'expectedStylish', 'stylish'],
            ['file1.json', 'file2.yml', 'expectedStylish', 'stylish'],
            ['file1.yml', 'file2.json', 'expectedStylish', 'stylish'],

            ['file1.json', 'file2.json', 'expectedPlain', 'plain'],
            ['file1.yaml', 'file2.yaml', 'expectedPlain', 'plain'],
            ['file1.json', 'file2.yaml', 'expectedPlain', 'plain'],
            ['file1.yaml', 'file2.json', 'expectedPlain', 'plain'],
            ['file1.yml', 'file2.yml', 'expectedPlain', 'plain'],
            ['file1.json', 'file2.yml', 'expectedPlain', 'plain'],
            ['file1.yml', 'file2.json', 'expectedPlain', 'plain'],

            ['file1.json', 'file2.json', 'expectedJson', 'json'],
            ['file1.yaml', 'file2.yaml', 'expectedJson', 'json'],
            ['file1.json', 'file2.yaml', 'expectedJson', 'json'],
            ['file1.yaml', 'file2.json', 'expectedJson', 'json'],
            ['file1.yml', 'file2.yml', 'expectedJson', 'json'],
            ['file1.json', 'file2.yml', 'expectedJson', 'json'],
            ['file1.yml', 'file2.json', 'expectedJson', 'json'],

        ];
    }

    private function normalizeLineEndings($content)
    {
        return str_replace("\r\n", "\n", $content);
    }

    private function getPathToFixture($fixtureName)
    {
        return __DIR__ . "/fixtures/" . $fixtureName;
    }
}
