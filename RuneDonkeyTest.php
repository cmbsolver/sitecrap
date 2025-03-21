<?php
use PHPUnit\Framework\TestCase;

require 'wordpattern.php';
require 'runedonkey.php';

class RuneDonkeyTest extends TestCase
{
    public function testGetValuesFromStringGemSum()
    {
        $runeDonkey = new RuneDonkey();
        $result = $runeDonkey->GetValuesFromString('ᚦᛁᛋ•ᛁᛋ•ᚪ•ᛏᛖᛋᛏ', TextType::Runes, 'gem_sum');
        $this->assertEquals([89, 84, 97, 238], $result);
    }

    public function testGetValuesFromStringWordLength()
    {
        $runeDonkey = new RuneDonkey();
        $result = $runeDonkey->GetValuesFromString('ᚦᛁᛋ•ᛁᛋ•ᚪ•ᛏᛖᛋᛏ', TextType::Runes, 'dict_word_length');
        $this->assertEquals([3, 2, 1, 4], $result);
    }

    public function testGetValuesFromStringWordLength2()
    {
        $runeDonkey = new RuneDonkey();
        $result = $runeDonkey->GetValuesFromString('ᛏᛖᛋᛏ', TextType::Runes, 'dict_word_length');
        $this->assertEquals([4], $result);
    }

    public function testGetValuesFromStringRunePattern()
    {
        $runeDonkey = new RuneDonkey();
        $result = $runeDonkey->GetValuesFromString('ᚠᚢᚦ', TextType::Runes, 'rune_pattern');
        $this->assertEquals(['1,2,3'], $result);
    }

    public function testGetValuesFromStringRunePatternNoDoublet()
    {
        $runeDonkey = new RuneDonkey();
        $result = $runeDonkey->GetValuesFromString('ᚠᚠᚢᚦ', TextType::Runes, 'rune_pattern_no_doublet');
        $this->assertEquals(['1,2,3'], $result);
    }

    public function testGetSumOfRunes()
    {
        $runeDonkey = new RuneDonkey();
        $reflection = new \ReflectionClass($runeDonkey);
        $method = $reflection->getMethod('getSumOfRunes');
        $method->setAccessible(true);

        $result = $method->invokeArgs($runeDonkey, ['ᚱᛠᛚᛚᚣ']);
        $this->assertEquals(369, $result);
    }

    public function testRemoveDoublets()
    {
        $runeDonkey = new RuneDonkey();
        $reflection = new \ReflectionClass($runeDonkey);
        $method = $reflection->getMethod('removeDoublets');
        $method->setAccessible(true);

        $result = $method->invokeArgs($runeDonkey, ['ᚱᛠᛚᛚᚣ']);
        $this->assertEquals('ᚱᛠᛚᚣ', $result);
    }

    public function testGetActionFromWhatToDo()
    {
        $runeDonkey = new RuneDonkey();
        $reflection = new \ReflectionClass($runeDonkey);
        $method = $reflection->getMethod('getActionFromWhatToDo');
        $method->setAccessible(true);

        $result = $method->invokeArgs($runeDonkey, ['dict_rune_length']);
        $this->assertEquals(Actions::RuneLength, $result);
    }

    public function testPrepLatinToRune()
    {
        $runeDonkey = new RuneDonkey();
        $reflection = new \ReflectionClass($runeDonkey);
        $method = $reflection->getMethod('prepLatinToRune');
        $method->setAccessible(true);

        $result = $method->invokeArgs($runeDonkey, ['This is a really complicated string to convert']);
        $this->assertEquals('THIS IS A REALLY COMPLICATED STRING TO CONUERT', $result);
    }

    public function testTransposeFromLatin()
    {
        $runeDonkey = new RuneDonkey();
        $reflection = new \ReflectionClass($runeDonkey);
        $method = $reflection->getMethod('transposeLatinToRune');
        $method->setAccessible(true);

        $result = $method->invokeArgs($runeDonkey, ['THIS IS A TEST']);
        $this->assertEquals('ᚦᛁᛋ•ᛁᛋ•ᚪ•ᛏᛖᛋᛏ', $result);
    }

    public function testGetRuneFromChar()
    {
        $runeDonkey = new RuneDonkey();
        $reflection = new \ReflectionClass($runeDonkey);
        $method = $reflection->getMethod('getRuneFromChar');
        $method->setAccessible(true);

        $result = $method->invokeArgs($runeDonkey, ['ING']);
        $this->assertEquals('ᛝ', $result);
    }

    public function testPrivateGetDelimitersFromString()
    {
        $runeDonkey = new RuneDonkey();
        $reflection = new \ReflectionClass($runeDonkey);
        $method = $reflection->getMethod('getDelimitersFromString');
        $method->setAccessible(true);

        $result = $method->invokeArgs($runeDonkey, ['ᚦᛁᛋ•ᛁᛋ•ᚪ•ᛏᛖᛋᛏ']);
        $this->assertEquals(['•'], $result);
    }

    public function testIsRune()
    {
        $runeDonkey = new RuneDonkey();
        $reflection = new \ReflectionClass($runeDonkey);
        $method = $reflection->getMethod('isRune');
        $method->setAccessible(true);

        $result = $method->invokeArgs($runeDonkey, ['ᚦ']);
        $this->assertEquals(true, $result);
    }

    public function testIsLatin()
    {
        $runeDonkey = new RuneDonkey();
        $reflection = new \ReflectionClass($runeDonkey);
        $method = $reflection->getMethod('isLatin');
        $method->setAccessible(true);

        $result = $method->invokeArgs($runeDonkey, ['A']);
        $this->assertEquals(true, $result);
    }

    public function testPrivateGetValueFromRune()
    {
        $runeDonkey = new RuneDonkey();
        $reflection = new \ReflectionClass($runeDonkey);
        $method = $reflection->getMethod('getValueFromRune');
        $method->setAccessible(true);

        $result = $method->invokeArgs($runeDonkey, ['ᚦ']);
        $this->assertEquals(5, $result);
    }
}