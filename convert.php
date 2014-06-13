<?php
/**
 * Author: Michal Schweichler
 * email: michal.schweichler[at]gmail.com
 * www: http://michalschweichler.co.uk
 *
 * Date: 13/06/14
 * Time: 15:00
 */
error_reporting(0);
set_time_limit(3600);
$inputFile = $argv[1];
$outputFile = $argv[2];

$input = array();
$output = array();

if ($input = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
    foreach ($input as $line) {
        preg_match('/([0-9]*):([0-9]*):([0-9]*) (.*)/', $line, $match);
        $m = $match[1];
        $s = $match[2];
        $f = $match[3];
        $text = $match[4];

        $newH = floor($m / 60);
        $newM = $m % 60;
        $newS = $s;
        $newF = $f;
        $newText = $text;

        if (count($output) == 0) {
            if ($newS - 5 <= 0) {
                $output[0]['start'] = '00:00:00:00';
            } else {
                $output[0]['start'] = sprintf('%1$02d:%2$02d:%3$02d:%4$02d', $newH, $newM, ($newS - 5), $newF);
            }
            $output[0]['end'] = sprintf('%1$02d:%2$02d:%3$02d:%4$02d', $newH, $newM, $newS, $newF);
            $output[0]['text'] = $newText;
        } else {
            $length = count($output);
            $output[$length]['start'] = $output[$length - 1]['end'];
            $output[$length]['end'] = sprintf('%1$02d:%2$02d:%3$02d:%4$02d', $newH, $newM, $newS, $newF);
            $output[$length]['text'] = $newText;
        }
    }
} else {
    echo 'Error while opening file ' . $inputFile;
}


$fileHeader =
    '@ This is a DS Subtitles file' . PHP_EOL .
    '@ Creator: Belle Nuit Subtitler 1.7.8' . PHP_EOL .
    '@ Date: 23.07.2011' . PHP_EOL . PHP_EOL .

    '@ Header' . PHP_EOL . PHP_EOL .

    '<font> Arial' . PHP_EOL .
    '<font size> 36' . PHP_EOL .
    '<kerning> 1' . PHP_EOL .
    '<leading> 2' . PHP_EOL .
    '<alignment> center' . PHP_EOL . PHP_EOL .

    '<use face> on' . PHP_EOL .
    '<face color> 92 92 92' . PHP_EOL .
    '<face opacity> 100' . PHP_EOL .
    '<face softness> 0' . PHP_EOL . PHP_EOL .

    '<use edge> on' . PHP_EOL .
    '<edge color> 6 6 6' . PHP_EOL .
    '<face opacity> 1' . PHP_EOL .
    '<edge opacity> 1' . PHP_EOL .
    '<edge softness> 60' . PHP_EOL .
    '<edge width> 3' . PHP_EOL . PHP_EOL .

    '<use shadow> on' . PHP_EOL .
    '<shadow color> 50 50 50' . PHP_EOL .
    '<shadow opacity> 100' . PHP_EOL .
    '<shadow softness> 40' . PHP_EOL .
    '<shadow offset> 3' . PHP_EOL .
    '<shadow angle> 45' . PHP_EOL . PHP_EOL . PHP_EOL .


    '@ Subtitles' . PHP_EOL . PHP_EOL .

    '<begin subtitles>' . PHP_EOL . PHP_EOL;

file_put_contents($outputFile, $fileHeader); // add header

foreach ($output as $key => $newLine) {
    $time = $newLine['start'] . ' ' . $newLine['end'];
    $text = $newLine['text'];
    $string = $time . PHP_EOL . $text . PHP_EOL . PHP_EOL;

    file_put_contents($outputFile, $string, FILE_APPEND); // add subtitle line
}

$fileFooter = '<end subtitles>';
file_put_contents($outputFile, $fileFooter, FILE_APPEND); // add footer