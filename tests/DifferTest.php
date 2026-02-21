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

    public static function formatsProvider(): array
    {
        return [['json'], ['yaml']];
    }

    public function testBorderlineCases(): void
    {
        $ymlFilePath1 = $this->getFixturePath('file1.yaml');
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

    #[DataProvider('formatsProvider')]
    public function testStylish(string $format): void
    {
        $path1 = $this->getFixturePath("file1.{$format}");
        $path2 = $this->getFixturePath("file2.{$format}");
        $expected = $this->getFixturePath('positiveResultForStylish.txt');
        $this->assertStringEqualsFile($expected, genDiff($path1, $path2, 'stylish'));
    }

    #[DataProvider('formatsProvider')]
    public function testPlain(string $format): void
    {
        $path1 = $this->getFixturePath("file1.{$format}");
        $path2 = $this->getFixturePath("file2.{$format}");
        $expected = $this->getFixturePath('positiveResultForPlain.txt');
        $this->assertStringEqualsFile($expected, genDiff($path1, $path2, 'plain'));
    }

    #[DataProvider('formatsProvider')]
    public function testJson(string $format): void
    {
        $path1 = $this->getFixturePath("file1.{$format}");
        $path2 = $this->getFixturePath("file2.{$format}");
        $expected = $this->getFixturePath('positiveResultForJson.txt');
        $this->assertStringEqualsFile($expected, genDiff($path1, $path2, 'json'));
    }
}
