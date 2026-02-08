<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function getPath(string $file): string
    {
        return (__DIR__ . '/fixtures/' . $file);
    }

    public function testGenDiff(): void
    {
        $testYaml1AndEmptyYaml = $this->getPath('test2.txt');
        $testJson1AndEmptyJson = $this->getPath('test3.txt');
        $positiveResultForStylish = $this->getPath('positiveResultForStylish.txt');

        $jsonFilePath1 = $this->getPath('file1.json');
        $jsonFilePath2 = $this->getPath('file2.json');
        $jsonEmptyFilePath = $this->getPath('emptyFile.json');
        $jsonNotExistFilePath = $this->getPath('fileee.json');

        $ymlFilePath1 = $this->getPath('file1.yml');
        $ymlFilePath2 = $this->getPath('file2.yaml');
        $ymlEmptyFilePath = $this->getPath('emptyFile.yml');
        $ymlNotExistFilePath = $this->getPath('filedfw.yml');

        $jpgFilePath1 = $this->getPath('file.jpg');

        $this->expectExceptionMessage("'{$jsonNotExistFilePath}' is not readable");
        genDiff($jsonFilePath1, $jsonNotExistFilePath);
        $this->expectExceptionMessage("'{$ymlNotExistFilePath}' is not readable");
        genDiff($ymlFilePath1, $ymlNotExistFilePath);
        $this->expectExceptionMessage("File {$jpgFilePath1} not supported. Choose 'json', 'yaml' or 'yml' extension");
        genDiff($jsonFilePath1, $jpgFilePath1);

        $this->assertStringEqualsFile($testJson1AndEmptyJson, genDiff($jsonFilePath1, $jsonEmptyFilePath));
        $this->assertStringEqualsFile($positiveResultForStylish, genDiff($jsonFilePath1, $jsonFilePath2));
        $this->assertStringEqualsFile($testYaml1AndEmptyYaml, genDiff($ymlFilePath1, $ymlEmptyFilePath));
        $this->assertStringEqualsFile($positiveResultForStylish, genDiff($ymlFilePath1, $ymlFilePath2));
        $this->expectExceptionMessage("Unknown format. Please choose stylish, plain or json format");
        genDiff($jsonFilePath1, $jsonFilePath2, 'abracadabra');
    }

    public function testStylish(): void
    {
        $jsonFilePath1 = $this->getPath('file1.json');
        $jsonFilePath2 = $this->getPath('file2.json');
        $positiveResultForStylish = $this->getPath('positiveResultForStylish.txt');
        $this->assertStringEqualsFile($positiveResultForStylish, genDiff($jsonFilePath1, $jsonFilePath2));
    }

    public function testPlain(): void
    {
        $jsonFilePath1 = $this->getPath('file1.json');
        $jsonFilePath2 = $this->getPath('file2.json');
        $correctString = $this->getPath('positiveResultForPlain.txt');

        $this->assertStringEqualsFile($correctString, genDiff($jsonFilePath1, $jsonFilePath2, 'plain'));
    }

    public function testJson(): void
    {
        $ymlFilePath1 = $this->getPath('file1.yml');
        $ymlFilePath2 = $this->getPath('file2.yaml');
        $correctString = $this->getPath('positiveResultForJson.txt');

        $this->assertStringEqualsFile($correctString, genDiff($ymlFilePath1, $ymlFilePath2, 'json'));
    }
}
