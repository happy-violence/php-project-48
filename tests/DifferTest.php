<?php

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function getPath(string $file): string
    {
        return (__DIR__ . '/fixtures/' . $file);
    }

    #[DataProvider('jsonAndYamlProvider')]
    public function testGenDiff(
        string $expected,
        string $argument1,
        string $argument2,
        string $format = 'stylish'
    ): void {
        $expected = $this->getPath($expected);
        $argument1 = $this->getPath($argument1);
        $argument2 = $this->getPath($argument2);

        $this->assertStringEqualsFile($expected, genDiff($argument1, $argument2));
    }

    public static function jsonAndYamlProvider(): array
    {
        return [
            ['positiveResultForStylish.txt', 'file1.json', 'file2.json'],
            ['positiveResultForStylish.txt', 'file1.yml', 'file2.yaml'],
            ['test3.txt', 'file1.yml', 'emptyFile.yml'],
            ['test3.txt', 'file1.json', 'emptyFile.json'],
            ['test2.txt', 'file2.yaml', 'emptyFile.yml'],
            ['test2.txt', 'file2.json', 'emptyFile.json'],
        ];
    }

    public function testBorderlineCases(): void
    {
        $ymlFilePath1 = $this->getPath('file1.yml');
        $jsonFilePath1 = $this->getPath('file1.json');
        $jsonFilePath2 = $this->getPath('file2.json');
        $jsonNotExistFilePath = $this->getPath('fileee.json');
        $ymlNotExistFilePath = $this->getPath('filedfw.yml');

        $jpgFilePath1 = $this->getPath('file.jpg');

        $this->expectExceptionMessage("'{$jsonNotExistFilePath}' is not readable");
        genDiff($jsonFilePath1, $jsonNotExistFilePath);
        $this->expectExceptionMessage("'{$ymlNotExistFilePath}' is not readable");
        genDiff($ymlFilePath1, $ymlNotExistFilePath);
        $this->expectExceptionMessage("File {$jpgFilePath1} not supported. Choose 'json', 'yaml' or 'yml' extension");
        genDiff($jsonFilePath1, $jpgFilePath1);

        $this->expectExceptionMessage("Unknown format. Please choose stylish, plain or json format");
        genDiff($jsonFilePath1, $jsonFilePath2, 'abracadabra');
    }

    public function testStylish(): void
    {
        $jsonFilePath1 = $this->getPath('file1.json');
        $jsonFilePath2 = $this->getPath('file2.json');
        $expected = $this->getPath('positiveResultForStylish.txt');
        $this->assertStringEqualsFile($expected, genDiff($jsonFilePath1, $jsonFilePath2, 'stylish'));
    }

    public function testPlain(): void
    {
        $jsonFilePath1 = $this->getPath('file1.json');
        $jsonFilePath2 = $this->getPath('file2.json');
        $expected = $this->getPath('positiveResultForPlain.txt');

        $this->assertStringEqualsFile($expected, genDiff($jsonFilePath1, $jsonFilePath2, 'plain'));
    }

    public function testJson(): void
    {
        $ymlFilePath1 = $this->getPath('file1.yml');
        $ymlFilePath2 = $this->getPath('file2.yaml');
        $correctString = $this->getPath('positiveResultForJson.txt');

        $this->assertStringEqualsFile($correctString, genDiff($ymlFilePath1, $ymlFilePath2, 'json'));
    }
}
