<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $fixturesPath = __DIR__ . '/fixtures/';

        $correctString1 = file_get_contents($fixturesPath . 'positiveResultForStylish.txt');
        $testYaml1AndYaml2 = str_replace(['"', ','], '', $correctString1);

        $correctString2 = file_get_contents($fixturesPath . 'test2.txt');
        $testYaml1AndEmptyYaml = str_replace(['"', ','], '', $correctString2);

        $correctString3 = file_get_contents($fixturesPath . 'test3.txt');
        $testJson1AndEmptyJson = str_replace(['"', ','], '', $correctString3);

        $correctString3 = file_get_contents($fixturesPath . 'positiveResultForStylish.txt');
        $positiveResultForStylish = str_replace(['"', ','], '', $correctString3);

        $jsonFilePath1 = $fixturesPath . 'file1.json';
        $jsonFilePath2 = $fixturesPath . 'file2.json';
        $jsonEmptyFilePath = $fixturesPath . 'emptyFile.json';
        $jsonNotExistFilePath = $fixturesPath . 'fileee.json';

        $ymlFilePath1 = $fixturesPath . 'file1.yml';
        $ymlFilePath2 = $fixturesPath . 'file2.yaml';
        $ymlEmptyFilePath = $fixturesPath . 'emptyFile.yml';
        $ymlNotExistFilePath = $fixturesPath . 'filedfw.yml';

        $jpgFilePath1 = $fixturesPath . 'file.jpg';

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
        $fixturesPath = __DIR__ . '/fixtures/';
        $jsonFilePath1 = $fixturesPath . 'file1.json';
        $jsonFilePath2 = $fixturesPath . 'file2.json';
        $correctString = file_get_contents($fixturesPath . 'positiveResultForStylish.txt');
        $positiveResultForStylish = str_replace(['"', ','], '', $correctString);

        $this->assertEquals($positiveResultForStylish, genDiff($jsonFilePath1, $jsonFilePath2));
    }

    public function testPlain(): void
    {
        $fixturesPath = __DIR__ . '/fixtures/';
        $jsonFilePath1 = $fixturesPath . 'file1.json';
        $jsonFilePath2 = $fixturesPath . 'file2.json';
        $correctString = file_get_contents($fixturesPath . 'positiveResultForPlain.txt');

        $this->assertEquals($correctString, genDiff($jsonFilePath1, $jsonFilePath2, 'plain'));
    }

    public function testJson(): void
    {
        $fixturesPath = __DIR__ . '/fixtures/';
        $ymlFilePath1 = $fixturesPath . 'file1.yml';
        $ymlFilePath2 = $fixturesPath . 'file2.yaml';
        $correctString = file_get_contents($fixturesPath . 'positiveResultForJson.txt');

        $this->assertEquals($correctString, genDiff($ymlFilePath1, $ymlFilePath2, 'json'));
    }
}
