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

    public function testIsReadableFile(): void
    {
        $yamlFilePath1 = $this->getFixturePath('file1.yaml');
        $jsonFilePath1 = $this->getFixturePath('file1.json');
        $jsonNotExistFilePath = $this->getFixturePath('fileee.json');
        $yamlNotExistFilePath = $this->getFixturePath('filedfw.yml');

        $this->expectExceptionMessage("'{$jsonNotExistFilePath}' is not readable");
        genDiff($jsonFilePath1, $jsonNotExistFilePath);
        $this->expectExceptionMessage("'{$yamlNotExistFilePath}' is not readable");
        genDiff($yamlFilePath1, $yamlNotExistFilePath);
    }

    public function testExtension(): void
    {
        $jsonFilePath1 = $this->getFixturePath('file1.json');
        $jpgFilePath = $this->getFixturePath('file.jpg');

        $this->expectExceptionMessage("Extension is not supported. Choose 'json', 'yaml' or 'yml' extension");
        genDiff($jsonFilePath1, $jpgFilePath);
    }

    public function testFormat(): void
    {
        $jsonFilePath1 = $this->getFixturePath('file1.json');
        $jsonFilePath2 = $this->getFixturePath('file2.json');

        $this->expectExceptionMessage("Unknown format. Please choose stylish, plain or json format");
        genDiff($jsonFilePath1, $jsonFilePath2, 'abracadabra');
    }

    #[DataProvider('formatsProvider')]
    public function testStylish(string $format): void
    {
        $path1 = $this->getFixturePath("file1.{$format}");
        $path2 = $this->getFixturePath("file2.{$format}");
        $expected = $this->getFixturePath('positiveResultForStylish.txt');
        $this->assertStringEqualsFile($expected, genDiff($path1, $path2));
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
