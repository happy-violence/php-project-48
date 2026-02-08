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
        $testYaml1AndYaml2 = str_replace(['"', ','], '', file_get_contents($this->getPath('positiveResultForStylish.txt')));

        $testYaml1AndEmptyYaml = str_replace(['"', ','], '', file_get_contents($this->getPath('test2.txt')));

        $testJson1AndEmptyJson = str_replace(['"', ','], '', file_get_contents($this->getPath('test3.txt')));

        $positiveResultForStylish = str_replace(['"', ','], '', file_get_contents($this->getPath('positiveResultForStylish.txt')));

        $jsonFilePath1 = $this->getPath('file1.json');
        $jsonFilePath2 = $this->getPath('file2.json');
        $jsonEmptyFilePath = $this->getPath('emptyFile.json');
        $jsonNotExistFilePath = $this->getPath('fileee.json');

        $ymlFilePath1 = $this->getPath('file1.yml');
        $ymlFilePath2 = $this->getPath('file2.yaml');
        $ymlEmptyFilePath = $this->getPath('emptyFile.yml');
        $ymlNotExistFilePath = $this->getPath('filedfw.yml');

        $jpgFilePath1 = $this->getPath('file.jpg');

        $this->assertEquals($testJson1AndEmptyJson, genDiff($jsonFilePath1, $jsonEmptyFilePath));

        $this->expectExceptionMessage("'{$jsonNotExistFilePath}' is not readable");
        genDiff($jsonFilePath1, $jsonNotExistFilePath);
        $this->expectExceptionMessage("'{$ymlNotExistFilePath}' is not readable");
        genDiff($ymlFilePath1, $ymlNotExistFilePath);
        $this->expectExceptionMessage("File {$jpgFilePath1} not supported. Choose 'json', 'yaml' or 'yml' extension");
        genDiff($jsonFilePath1, $jpgFilePath1);

        $this->assertEquals($positiveResultForStylish, genDiff($jsonFilePath1, $jsonFilePath2));

        $this->assertEquals($testYaml1AndEmptyYaml, genDiff($ymlFilePath1, $ymlEmptyFilePath));

        $this->assertEquals($testYaml1AndYaml2, genDiff($ymlFilePath1, $ymlFilePath2));

        $this->expectExceptionMessage("Unknown format. Please choose stylish, plain or json format");
        genDiff($jsonFilePath1, $jsonFilePath2, 'abracadabra');
    }

    public function testStylish(): void
    {
        $jsonFilePath1 = $this->getPath('file1.json');
        $jsonFilePath2 = $this->getPath('file2.json');
        $correctString = file_get_contents($this->getPath('positiveResultForStylish.txt'));
        $positiveResultForStylish = str_replace(['"', ','], '', $correctString);

        $this->assertEquals($positiveResultForStylish, genDiff($jsonFilePath1, $jsonFilePath2));
    }

    public function testPlain(): void
    {
        $jsonFilePath1 = $this->getPath('file1.json');
        $jsonFilePath2 = $this->getPath('file2.json');
        $correctString = file_get_contents($this->getPath('positiveResultForPlain.txt'));

        $this->assertEquals($correctString, genDiff($jsonFilePath1, $jsonFilePath2, 'plain'));
    }

    public function testJson(): void
    {
        $ymlFilePath1 = $this->getPath('file1.yml');
        $ymlFilePath2 = $this->getPath('file2.yaml');
        $correctString = file_get_contents($this->getPath('positiveResultForJson.txt'));

        $this->assertEquals($correctString, genDiff($ymlFilePath1, $ymlFilePath2, 'json'));
    }
}
