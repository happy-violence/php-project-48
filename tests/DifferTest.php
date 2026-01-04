<?php

namespace  Tests;

use PHPUnit\Framework\TestCase;
use function App\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $correctString1 = \App\Parsers\readFile('/home/svetlana/projects/hexlet/php-project-48/tests/fixtures/test1.txt');
        $correctString1WithoutQuotesAndCommas = str_replace(['"', ','], '',$correctString1);

        $correctString2 = \App\Parsers\readFile('/home/svetlana/projects/hexlet/php-project-48/tests/fixtures/test2.txt');
        $correctString2WithoutQuotesAndCommas = str_replace(['"', ','], '',$correctString2);

        $correctString3 = \App\Parsers\readFile('/home/svetlana/projects/hexlet/php-project-48/tests/fixtures/positiveTestResult.txt');
        $correctString3WithoutQuotesAndCommas = str_replace(['"', ','], '',$correctString3);

        $correctString4 = \App\Parsers\readFile('/home/svetlana/projects/hexlet/php-project-48/tests/fixtures/testForPlain.txt');

        $correctString5 = \App\Parsers\readFile('/home/svetlana/projects/hexlet/php-project-48/tests/fixtures/testResultForJsonFormat.txt');

        $jsonFilePath1 = __DIR__ . "/fixtures/file1.json";
        $jsonFilePath2 = __DIR__ . "/fixtures/file2.json";
        $jsonFilePath3 = __DIR__ . "/fixtures/emptyFile1.json";
        $jsonFilePath4 = __DIR__ . "/fixtures/fileee.json";

        $ymlFilePath1 = __DIR__ . "/fixtures/file1.yml";
        $ymlFilePath2 = __DIR__ . "/fixtures/file2.yaml";
        $ymlFilePath3 = __DIR__ . "/fixtures/emptyFile3.yml";
        $ymlFilePath4 = __DIR__ . "/fixtures/filedfw.yml";

        $this->assertEquals($correctString1WithoutQuotesAndCommas, genDiff($jsonFilePath1, $jsonFilePath3));

        $this->expectExceptionMessage("'{$jsonFilePath4}' is not readable");
        genDiff($jsonFilePath1, $jsonFilePath4);

        $this->assertEquals($correctString3WithoutQuotesAndCommas, genDiff($jsonFilePath1, $jsonFilePath2));

        $this->assertEquals($correctString2WithoutQuotesAndCommas, genDiff($ymlFilePath1, $ymlFilePath3));

        $this->expectExceptionMessage("'{$ymlFilePath4}' is not readable");
        genDiff($ymlFilePath1, $ymlFilePath4);

        $this->assertEquals($correctString1WithoutQuotesAndCommas, genDiff($ymlFilePath1, $ymlFilePath2));

        $this->assertEquals($correctString4, genDiff($jsonFilePath1, $jsonFilePath2, 'plain'));
        $this->assertEquals($correctString3WithoutQuotesAndCommas, genDiff($jsonFilePath1, $jsonFilePath2, 'stylish'));

        $this->expectExceptionMessage("Unknown format. Please choose stylish, plain or json format");
        genDiff($jsonFilePath1, $jsonFilePath2, 'abracadabra');

        $this->assertEquals($correctString5, genDiff($ymlFilePath1, $ymlFilePath2, 'json'));
    }
}
