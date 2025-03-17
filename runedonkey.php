<?php
require 'wordpattern.php';

enum TextType
{
    case Runes;
    case Runeglish;
    case Latin;
}

enum Actions
{
    case GemSum;
    case WordLength;
    case RuneLength;
    case RuneglishLength;
    case RunePattern;
    case RunePatternNoDoublet;
}

class RuneDonkey {
    public function GetValuesFromString($value, $textType, $whatToDo): array
    {
        $valuesToGetFromDB = [];
        $wordArray = [];

        $delimiters = $this->getDelimitersFromString($value);

        if (count($delimiters) == 0) {
            $wordArray = [$value];
        } else {
            $pattern = '/[' . preg_quote(implode('', $delimiters), '/') . ']+/';
            $wordArray = preg_split($pattern, $value);
        }

        for ($i = 0; $i < count($wordArray); $i++) {
            $word = $wordArray[$i];
            $word = trim($word);

            // We don't do anything for tune text types.
            if ($textType == TextType::Runeglish) {
                $word = $this->prepLatinToRune($word);
                $word = $this->transposeLatinToRune($word);
            } elseif ($textType == TextType::Latin) {
                $word = $this->prepLatinToRune($word);
                $word = $this->transposeLatinToRune($word);
            }

            $action = $this->getActionFromWhatToDo($whatToDo);

            switch ($action) {
                case Actions::GemSum:
                    $sum = $this->getSumOfRunes($word);
                    array_push($valuesToGetFromDB, $sum);
                    break;
                case Actions::WordLength:
                case Actions::RuneLength:
                case Actions::RuneglishLength:
                    $length = mb_strlen($word);
                    array_push($valuesToGetFromDB, $length);
                    break;
                case Actions::RunePattern:
                    $wordPattern = new WordPattern();
                    $pattern = $wordPattern->generatePattern($word);
                    array_push($valuesToGetFromDB, $pattern);
                    break;
                case Actions::RunePatternNoDoublet:
                    $word = $this->removeDoublets($word);
                    $wordPattern = new WordPattern();
                    $pattern = $wordPattern->generatePattern($word);
                    array_push($valuesToGetFromDB, $pattern);
                    break;
            }
        }

        return $valuesToGetFromDB;
    }

    private function getSumOfRunes($runeString): int
    {
        $sum = 0;
        $runes = mb_str_split($runeString);
        foreach ($runes as $rune) {
            $sum += $this->getValueFromRune($rune);
        }
        return $sum;
    }

    private function removeDoublets($word): array|string|null
    {
        $retval = '';
        $previousCharacter = '';
        $runes = mb_str_split($word);
        foreach ($runes as $rune) {
            if ($rune !== $previousCharacter) {
                $retval .= $rune;
                $previousCharacter = $rune;
            }
        }

        return $retval;
    }

    private function getActionFromWhatToDo($whatToDo): Actions
    {
        $action = Actions::GemSum;

        switch ($whatToDo) {
            case "dict_word_length":
                $action = Actions::WordLength;
                break;
            case "dict_rune_length":
                $action = Actions::RuneLength;
                break;
            case "dict_runeglish_length":
                $action = Actions::RuneglishLength;
                break;
            case "rune_pattern":
                $action = Actions::RunePattern;
                break;
            case "rune_pattern_no_doublet":
                $action = Actions::RunePatternNoDoublet;
                break;
        }

        return $action;
    }

    private function prepLatinToRune(string $text): string
    {
        $text = strtoupper($text);

        $text = str_replace('QU', 'CW', $text);
        $text = str_replace('Z', 'S', $text);
        $text = str_replace('K', 'C', $text);
        $text = str_replace('Q', 'C', $text);
        $text = str_replace('V', 'U', $text);

        $arrayString = mb_str_split($text);

        $result = '';

        for ($i = 0; $i < count($arrayString); $i++) {
            $xchar = $arrayString[$i];

            switch ($xchar) {
                case 'I':
                    if (($i + 1 < count($arrayString)) && $arrayString[$i + 1] == 'O') {
                        $result .= 'IO';
                        $i++;
                    } elseif (($i + 1 < count($arrayString)) && $arrayString[$i + 1] == 'A') {
                        $result .= 'IO';
                        $i++;
                    } else {
                        $result .= 'I';
                    }
                    break;

                default:
                    $result .= $xchar;
                    break;
            }
        }

        return $result;
    }

    private function getRuneFromChar($value): string
    {
        $retval = '';
        switch ($value) {
            case "ING":
            case "NG":
                $retval = "ᛝ";
                break;
            case "OE":
                $retval = "ᛟ";
                break;
            case "EO":
                $retval = "ᛇ";
                break;
            case "IO":
            case "IA":
                $retval = "ᛡ";
                break;
            case "EA":
                $retval = "ᛠ";
                break;
            case "AE":
                $retval = "ᚫ";
                break;
            case "TH":
                $retval = "ᚦ";
                break;
            case "F":
                $retval = "ᚠ";
                break;
            case "V":
            case "U":
                $retval = "ᚢ";
                break;
            case "O":
                $retval = "ᚩ";
                break;
            case "R":
                $retval = "ᚱ";
                break;
            case "Q":
            case "K":
            case "C":
                $retval = "ᚳ";
                break;
            case "G":
                $retval = "ᚷ";
                break;
            case "W":
                $retval = "ᚹ";
                break;
            case "H":
                $retval = "ᚻ";
                break;
            case "N":
                $retval = "ᚾ";
                break;
            case "I":
                $retval = "ᛁ";
                break;
            case "J":
                $retval = "ᛄ";
                break;
            case "P":
                $retval = "ᛈ";
                break;
            case "X":
                $retval = "ᛉ";
                break;
            case "Z":
            case "S":
                $retval = "ᛋ";
                break;
            case "T":
                $retval = "ᛏ";
                break;
            case "B":
                $retval = "ᛒ";
                break;
            case "E":
                $retval = "ᛖ";
                break;
            case "M":
                $retval = "ᛗ";
                break;
            case "L":
                $retval = "ᛚ";
                break;
            case "D":
                $retval = "ᛞ";
                break;
            case "A":
                $retval = "ᚪ";
                break;
            case "Y":
                $retval = "ᚣ";
                break;
            case " ":
                $retval = "•";
                break;
            case ".":
                $retval = "⊹";
                break;
            default:
                $retval = $value;
                break;
        }

        return $retval;
    }

    function transposeLatinToRune(string $text): string
    {
        $text = strtoupper($text);
        $result = '';

        $arrayString = mb_str_split($text);

        for ($i = 0; $i < count($arrayString); $i++) {
            $xchar = $text[$i];
            if (!$this->isRune($xchar, true)) {
                switch ($xchar) {
                    case 'A':
                        if (($i + 1 < count($arrayString)) && $arrayString[$i + 1] == 'E') {
                            $result .= $this->getRuneFromChar('AE');
                            $i++;
                        } else {
                            $result .= $this->getRuneFromChar('A');
                        }
                        break;

                    case 'E':
                        if (($i + 1 < count($arrayString)) && $arrayString[$i + 1] == 'A') {
                            $result .= $this->getRuneFromChar('EA');
                            $i++;
                        } elseif (($i + 1 < count($arrayString)) && $arrayString[$i + 1] == 'O') {
                            $result .= $this->getRuneFromChar('EO');
                            $i++;
                        } else {
                            $result .= $this->getRuneFromChar('E');
                        }
                        break;

                    case 'O':
                        if (($i + 1 < count($arrayString)) && $arrayString[$i + 1] == 'E') {
                            $result .= $this->getRuneFromChar('OE');
                            $i++;
                        } else {
                            $result .= $this->getRuneFromChar('O');
                        }
                        break;

                    case 'T':
                        if (($i + 1 < count($arrayString)) && $arrayString[$i + 1] == 'H') {
                            $result .= $this->getRuneFromChar('TH');
                            $i++;
                        } else {
                            $result .= $this->getRuneFromChar('T');
                        }
                        break;

                    case 'I':
                        if (($i + 1 < count($arrayString)) && $arrayString[$i + 1] == 'O') {
                            $result .= $this->getRuneFromChar('IO');
                            $i++;
                        } elseif (($i + 2 < count($arrayString)) && $arrayString[$i + 1] == 'N' && $arrayString[$i + 2] == 'G') {
                            $result .= $this->getRuneFromChar('ING');
                            $i += 2;
                        } elseif (($i + 1 < count($arrayString)) && $arrayString[$i + 1] == 'A') {
                            $result .= $this->getRuneFromChar('IA');
                            $i++;
                        } else {
                            $result .= $this->getRuneFromChar('I');
                        }
                        break;

                    case 'N':
                        if (($i + 1 < count($arrayString)) && $arrayString[$i + 1] == 'G') {
                            $result .= $this->getRuneFromChar('NG');
                            $i++;
                        } else {
                            $result .= $this->getRuneFromChar('N');
                        }
                        break;

                    default:
                        $result .= $this->getRuneFromChar($xchar);
                        break;
                }
            } else {
                $result .= $this->getRuneFromChar($xchar);
            }
        }

        return $result;
    }

    private function getDelimitersFromString($value): array
    {
        $retval = [];
        $upperString = strtoupper($value);
        $arrayString = mb_str_split($upperString);
        foreach ($arrayString as $string) {
            if (!$this->isRune($string) && !$this->isLatin($string) && !in_array($string, $retval)) {
                array_push($retval, $string);
            }
        }

        return $retval;
    }

    private function getValueFromRune($rune): int
    {
        $retval = 0;
        switch ($rune) {
            case "ᛝ":
                $retval = 79;
                break;
            case "ᛟ":
                $retval = 83;
                break;
            case "ᛇ":
                $retval = 41;
                break;
            case "ᛡ":
                $retval = 107;
                break;
            case "ᛠ":
                $retval = 109;
                break;
            case "ᚫ":
                $retval = 101;
                break;
            case "ᚦ":
                $retval = 5;
                break;
            case "ᚠ":
                $retval = 2;
                break;
            case "ᚢ":
                $retval = 3;
                break;
            case "ᚩ":
                $retval = 7;
                break;
            case "ᚱ":
                $retval = 11;
                break;
            case "ᚳ":
                $retval = 13;
                break;
            case "ᚷ":
                $retval = 17;
                break;
            case "ᚹ":
                $retval = 19;
                break;
            case "ᚻ":
                $retval = 23;
                break;
            case "ᚾ":
                $retval = 29;
                break;
            case "ᛁ":
                $retval = 31;
                break;
            case "ᛄ":
                $retval = 37;
                break;
            case "ᛈ":
                $retval = 43;
                break;
            case "ᛉ":
                $retval = 47;
                break;
            case "ᛋ":
                $retval = 53;
                break;
            case "ᛏ":
                $retval = 59;
                break;
            case "ᛒ":
                $retval = 61;
                break;
            case "ᛖ":
                $retval = 67;
                break;
            case "ᛗ":
                $retval = 71;
                break;
            case "ᛚ":
                $retval = 73;
                break;
            case "ᛞ":
                $retval = 89;
                break;
            case "ᚪ":
                $retval = 97;
                break;
            case "ᚣ":
                $retval = 103;
                break;
            default:
                $retval = 0;
                break;
        }

        return $retval;
    }

    private function isRune($value): bool
    {
        $isRune = false;

        switch ($value) {
            case "ᛝ":
            case "ᛟ":
            case "ᛇ":
            case "ᛡ":
            case "ᛠ":
            case "ᚫ":
            case "ᚦ":
            case "ᚠ":
            case "ᚢ":
            case "ᚩ":
            case "ᚱ":
            case "ᚳ":
            case "ᚷ":
            case "ᚹ":
            case "ᚻ":
            case "ᚾ":
            case "ᛁ":
            case "ᛄ":
            case "ᛈ":
            case "ᛉ":
            case "ᛋ":
            case "ᛏ":
            case "ᛒ":
            case "ᛖ":
            case "ᛗ":
            case "ᛚ":
            case "ᛞ":
            case "ᚪ":
            case "ᚣ":
                $isRune = true;
        }

        return $isRune;
    }

    private function isLatin($value): bool
    {
        $isLatin = false;

        switch ($value) {
            case "A":
            case "B":
            case "C":
            case "D":
            case "E":
            case "F":
            case "G":
            case "H":
            case "I":
            case "J":
            case "K":
            case "L":
            case "M":
            case "N":
            case "O":
            case "P":
            case "Q":
            case "R":
            case "S":
            case "T":
            case "U":
            case "V":
            case "W":
            case "X":
            case "Y":
            case "Z":
                $isLatin = true;
        }

        return $isLatin;
    }
}

