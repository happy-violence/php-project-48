<?php

namespace  Tests;

use PHPUnit\Framework\TestCase;

use function App\Differ\genDiff;
//use function App\Differ\readFile;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $fixturesPath = __DIR__ . '/fixtures/';

        $correctString1 = file_get_contents($fixturesPath . 'test1.txt');
        $correctString1WithoutQuotesAndCommas = str_replace(['"', ','], '', $correctString1);

        $correctString2 = file_get_contents($fixturesPath . 'test2.txt');
        $correctString2WithoutQuotesAndCommas = str_replace(['"', ','], '', $correctString2);

        $correctString3 = file_get_contents($fixturesPath . 'positiveTestResult.txt');
        $correctString3WithoutQuotesAndCommas = str_replace(['"', ','], '', $correctString3);

        $correctString4 = file_get_contents($fixturesPath . 'testForPlain.txt');

        $correctString5 = file_get_contents($fixturesPath . 'testResultForJsonFormat.txt');

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

        $this->assertEquals($correctString4, genDiff($jsonFilePath1, $jsonFilePath2, 'plain'));
        $this->assertEquals($correctString3WithoutQuotesAndCommas, genDiff($jsonFilePath1, $jsonFilePath2, 'stylish'));

        $this->expectExceptionMessage("Unknown format. Please choose stylish, plain or json format");
        genDiff($jsonFilePath1, $jsonFilePath2, 'abracadabra');

        $this->assertEquals($correctString5, genDiff($ymlFilePath1, $ymlFilePath2, 'json'));
    }

    public function testStylish(): void
    {
        $fixturesPath = __DIR__ . '/fixtures/';
        $correctString3 = file_get_contents($fixturesPath . 'positiveTestResult.txt');
        $correctString3WithoutQuotesAndCommas = str_replace(['"', ','], '', $correctString3);
        $jsonFilePath1 = $fixturesPath . 'file1.json';
        $jsonFilePath2 = $fixturesPath . 'file2.json';

        $this->assertEquals($correctString3WithoutQuotesAndCommas, genDiff($jsonFilePath1, $jsonFilePath2, 'stylish'));
    }
}
