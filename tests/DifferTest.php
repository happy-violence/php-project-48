<?php

namespace  Tests;

use PHPUnit\Framework\TestCase;
use function App\Differ\genDiff;

// Класс UtilsTest наследует класс TestCase
// Имя класса совпадает с именем файла
class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $correctString1 = \App\Parsers\readFile('/home/svetlana/projects/hexlet/php-project-48/tests/fixtures/test1.txt');

        $correctString2 = \App\Parsers\readFile('/home/svetlana/projects/hexlet/php-project-48/tests/fixtures/test2.txt');

        $correctString3 = \App\Parsers\readFile('/home/svetlana/projects/hexlet/php-project-48/tests/fixtures/positiveTestResult');

        $jsonFilePath1 = __DIR__ . "/fixtures/file1.json";
        $jsonFilePath2 = __DIR__ . "/fixtures/file2.json";
        $jsonFilePath3 = __DIR__ . "/fixtures/emptyFile1.json";
        $jsonFilePath4 = __DIR__ . "/fixtures/fileee.json";

        $ymlFilePath1 = __DIR__ . "/fixtures/file1.yml";
        $ymlFilePath2 = __DIR__ . "/fixtures/file2.yaml";
        $ymlFilePath3 = __DIR__ . "/fixtures/emptyFile3.yml";
        $ymlFilePath4 = __DIR__ . "/fixtures/filedfw.yml";

        // Сначала идет ожидаемое значение (expected)
        // И только потом актуальное (actual)
        $this->assertEquals($correctString1, genDiff($jsonFilePath1, $jsonFilePath3));
        $this->expectExceptionMessage("'{$jsonFilePath4}' is not readable");
        genDiff($jsonFilePath1, $jsonFilePath4);
        $this->assertEquals($correctString3, genDiff($jsonFilePath1, $jsonFilePath2));

        $this->assertEquals($correctString2, genDiff($ymlFilePath1, $ymlFilePath3));
        $this->expectExceptionMessage("'{$ymlFilePath4}' is not readable");
        genDiff($ymlFilePath1, $ymlFilePath4);
        $this->assertEquals($correctString1, genDiff($ymlFilePath1, $ymlFilePath2));
    }
}