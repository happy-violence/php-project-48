<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $fixturesPath = __DIR__ . '/fixtures/';

        $correctString1 = file_get_contents($fixturesPath . 'test1.txt');
        $correctString1WithoutQuotesAndCommas = str_replace(['"', ','], '', $correctString1);

        $correctString2 = file_get_contents($fixturesPath . 'test2.txt');
        $correctString2WithoutQuotesAndCommas = str_replace(['"', ','], '', $correctString2);

        $correctString3 = file_get_contents($fixturesPath . 'positiveResultForStylish.txt');
        $correctString3WithoutQuotesAndCommas = str_replace(['"', ','], '', $correctString3);

        $jsonFilePath1 = $fixturesPath . 'file1.json';
        $jsonFilePath2 = $fixturesPath . 'file2.json';
        $jsonFilePath3 = $fixturesPath . 'emptyFile1.json';
        $jsonFilePath4 = $fixturesPath . 'fileee.json';

        $ymlFilePath1 = $fixturesPath . 'file1.yml';
        $ymlFilePath2 = $fixturesPath . 'file2.yaml';
        $ymlFilePath3 = $fixturesPath . 'emptyFile3.yml';
        $ymlFilePath4 = $fixturesPath . 'filedfw.yml';

        $jpgFilePath1 = $fixturesPath . 'file2.jpg';

        $this->assertEquals($correctString1WithoutQuotesAndCommas, genDiff($jsonFilePath1, $jsonFilePath3));

        $this->expectExceptionMessage("'{$jsonFilePath4}' is not readable");
        genDiff($jsonFilePath1, $jsonFilePath4);
        $this->expectExceptionMessage("'{$ymlFilePath4}' is not readable");
        genDiff($ymlFilePath1, $ymlFilePath4);
        $this->expectExceptionMessage("File {$jpgFilePath1} not supported. Choose 'json', 'yaml' or 'yml' extension");
        genDiff($jsonFilePath1, $jpgFilePath1);

        $this->assertEquals($correctString3WithoutQuotesAndCommas, genDiff($jsonFilePath1, $jsonFilePath2));

        $this->assertEquals($correctString2WithoutQuotesAndCommas, genDiff($ymlFilePath1, $ymlFilePath3));

        $this->assertEquals($correctString1WithoutQuotesAndCommas, genDiff($ymlFilePath1, $ymlFilePath2));

        $this->expectExceptionMessage("Unknown format. Please choose stylish, plain or json format");
        genDiff($jsonFilePath1, $jsonFilePath2, 'abracadabra');
    }

    public function testStylish(): void
    {
        $fixturesPath = __DIR__ . '/fixtures/';
        $jsonFilePath1 = $fixturesPath . 'file1.json';
        $jsonFilePath2 = $fixturesPath . 'file2.json';
        $correctString = file_get_contents($fixturesPath . 'positiveResultForStylish.txt');
        $correctString3WithoutQuotesAndCommas = str_replace(['"', ','], '', $correctString);

        $this->assertEquals($correctString3WithoutQuotesAndCommas, genDiff($jsonFilePath1, $jsonFilePath2));
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
