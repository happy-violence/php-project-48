<?php

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function getFixturePath(string $file): string
    {
        return implode([__DIR__, '/fixtures/', $file]);
    }

    #[DataProvider('jsonAndYamlProvider')]
    public function testGenDiff(
        string $expected,
        string $argument1,
        string $argument2,
        string $format = 'stylish'
    ): void {
        $expected = $this->getFixturePath($expected);
        $argument1 = $this->getFixturePath($argument1);
        $argument2 = $this->getFixturePath($argument2);

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
        $ymlFilePath1 = $this->getFixturePath('file1.yml');
        $jsonFilePath1 = $this->getFixturePath('file1.json');
        $jsonFilePath2 = $this->getFixturePath('file2.json');
        $jsonNotExistFilePath = $this->getFixturePath('fileee.json');
        $ymlNotExistFilePath = $this->getFixturePath('filedfw.yml');

        $jpgFilePath1 = $this->getFixturePath('file.jpg');

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
        $jsonFilePath1 = $this->getFixturePath('file1.json');
        $jsonFilePath2 = $this->getFixturePath('file2.json');
        $expected = $this->getFixturePath('positiveResultForStylish.txt');
        $this->assertStringEqualsFile($expected, genDiff($jsonFilePath1, $jsonFilePath2, 'stylish'));
    }

    public function testPlain(): void
    {
        $jsonFilePath1 = $this->getFixturePath('file1.json');
        $jsonFilePath2 = $this->getFixturePath('file2.json');
        $expected = $this->getFixturePath('positiveResultForPlain.txt');

        $this->assertStringEqualsFile($expected, genDiff($jsonFilePath1, $jsonFilePath2, 'plain'));
    }

    public function testJson(): void
    {
        $ymlFilePath1 = $this->getFixturePath('file1.yml');
        $ymlFilePath2 = $this->getFixturePath('file2.yaml');
        $correctString = $this->getFixturePath('positiveResultForJson.txt');

        $this->assertStringEqualsFile($correctString, genDiff($ymlFilePath1, $ymlFilePath2, 'json'));
    }
}
