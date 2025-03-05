<?php
class WordPattern
{
    private $patternDictionary = [];
    private $runes = [];
    private $counter = 1;

    public $wordToPattern = '';

    public $pattern = '';

    public function generatePattern($value)
    {
        $this->wordToPattern = $value;
        $characters = mb_str_split($value);
        foreach ($characters as $character) {
            if ($character === "'") {
                $this->runes[] = "'";
                continue;
            }

            $found = false;
            foreach ($this->patternDictionary as $key => $val) {
                if ($val === $character) {
                    $this->runes[] = (string)$key;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->runes[] = (string)$this->counter;
                $this->patternDictionary[$this->counter] = $character;
                $this->counter++;
            }
        }

        $impoded = implode(",", $this->runes);

        $this->pattern = $impoded;

        return $impoded;
    }
}