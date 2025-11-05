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
        // Сначала идет ожидаемое значение (expected)
        // И только потом актуальное (actual)

        $array = [
            "- follow" => false,
            "host" => "hexlet.io",
            "- proxy" => "123.234.53.22",
            "- timeout" => 50,
            "+ timeout" => 20,
            "+ verbose" => true
        ];
        //$this->assertEquals('', genDiff('', ''));
        $this->assertEquals($array, genDiff(__DIR__ . "/../fixtures/file1.json", __DIR__ . "/../fixtures/file2.json"));
    }
}