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
        $correctString1 = "{\n" .
            "    - follow => false,\n" .
            "      host => hexlet.io,\n" .
            "    - proxy => 123.234.53.22,\n" .
            "    - timeout => 50,\n" .
            "    + timeout => 20,\n" .
            "    + verbose => true\n" .
        "}";

        $correctString2 = "{\n" .
            "    - follow: false,\n" .
            "    - host: hexlet.io,\n" .
            "    - proxy: 123.234.53.22,\n" .
            "    - timeout: 50\n" .
        "}";

        $filePath1 = __DIR__ . "/fixtures/file1.json";
        $filePath2 = __DIR__ . "/fixtures/file2.json";
        $filePath3 = __DIR__ . "/fixtures/emptyFile1.json";
        $filePath4 = __DIR__ . "/fixtures/fileee.json";

        // Сначала идет ожидаемое значение (expected)
        // И только потом актуальное (actual)
        $this->assertEquals($correctString2, genDiff($filePath1, $filePath3));
        $this->expectExceptionMessage("'{$filePath4}' is not readable");
        genDiff($filePath1, $filePath4);
        $this->assertEquals($correctString1, genDiff($filePath1, $filePath2));
    }
}