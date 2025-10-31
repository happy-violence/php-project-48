<?php

namespace  Tests;

use PHPUnit\Framework\TestCase;
use function App\Parser\parse;

// Класс UtilsTest наследует класс TestCase
// Имя класса совпадает с именем файла
class ParserTest extends TestCase
{
    public function testParse(): void
    {
        // Сначала идет ожидаемое значение (expected)
        // И только потом актуальное (actual)

        //$testFile1 = file_get_contents(__DIR__ . "/../fixtures/file1.json");
        //$testFile2 = file_get_contents(__DIR__ . "/../fixtures/file2.json");

        $this->assertEquals([], parse(''));
        $this->assertEquals([], parse(__DIR__ . "/../fixtures/file1.json"));
    }
}