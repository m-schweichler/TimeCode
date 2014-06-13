<!doctype html>
<html lang="en">
<?php
/**
 * Author: Michal Schweichler
 * email: michal.schweichler[at]gmail.com
 * www: http://michalschweichler.co.uk
 *
 * Date: 13/06/14
 * Time: 15:00
 */
?>
<head>
    <meta charset="UTF-8">
    <title>Timecode converter</title>
    <style>
        form {
            float: left;
        }

        textarea {
            width: 400px;
            height: 300px;
            border: 1px solid rgb(167, 167, 167);
            display: block;
            float: left;
            padding: 2px;
            margin: 0;
            /*resize: none;*/
            outline-offset: 0;
            font-family: monospace;
            font-size: 14px;
        }

        input#convert {
            display: block;
            float: left;
            margin: 120px 5px;
            width: 80px;
            height: 30px;
            padding: 0;
        }

        div#wrapper {
            width: 406px;
            float: left;
        }
    </style>
</head>
<body>
<?php
function convHtml($string)
{
    return htmlentities($string, ENT_QUOTES | ENT_IGNORE, "UTF-8");
}

$fileHeader =
    convHtml('@ This is a DS Subtitles file') . '<br>' .
    convHtml('@ Creator: Belle Nuit Subtitler 1.7.8') . '<br>' .
    convHtml('@ Date: 23.07.2011') . '<br>' . '<br>' .

    convHtml('@ Header') . '<br>' . '<br>' .

    convHtml('<font> Arial') . '<br>' .
    convHtml('<font size> 36') . '<br>' .
    convHtml('<kerning> 1') . '<br>' .
    convHtml('<leading> 2') . '<br>' .
    convHtml('<alignment> center') . '<br>' . '<br>' .

    convHtml('<use face> on') . '<br>' .
    convHtml('<face color> 92 92 92') . '<br>' .
    convHtml('<face opacity> 100') . '<br>' .
    convHtml('<face softness> 0') . '<br>' . '<br>' .

    convHtml('<use edge> on') . '<br>' .
    convHtml('<edge color> 6 6 6') . '<br>' .
    convHtml('<face opacity> 1') . '<br>' .
    convHtml('<edge opacity> 1') . '<br>' .
    convHtml('<edge softness> 60') . '<br>' .
    convHtml('<edge width> 3') . '<br>' . '<br>' .

    convHtml('<use shadow> on') . '<br>' .
    convHtml('<shadow color> 50 50 50') . '<br>' .
    convHtml('<shadow opacity> 100') . '<br>' .
    convHtml('<shadow softness> 40') . '<br>' .
    convHtml('<shadow offset> 3') . '<br>' .
    convHtml('<shadow angle> 45') . '<br>' . '<br>' . '<br>' .


    convHtml('@ Subtitles') . '<br>' . '<br>' .

    convHtml('<begin subtitles>') . '<br>' . '<br>';

$outputLines[] = $fileHeader;

if (!empty($_POST['input'])) {
    $inputLines = explode("\n", str_replace("\r", "", $_POST['input']));
    $output = array();

    foreach ($inputLines as $line) {
        preg_match('/([0-9]*):([0-9]*):([0-9]*) (.*)/', $line, $match);
        if ($match) {
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
                $output[0]['start'] = '00:00:00:00';
                $output[0]['end'] = sprintf('%1$02d:%2$02d:%3$02d:%4$02d', $newH, $newM, $newS, $newF);
                $output[0]['text'] = $newText;
            } else {
                $length = count($output);
                $output[$length]['start'] = $output[$length - 1]['end'];
                $output[$length]['end'] = sprintf('%1$02d:%2$02d:%3$02d:%4$02d', $newH, $newM, $newS, $newF);
                $output[$length]['text'] = $newText;
            }
        }
    }

    foreach ($output as $key => $newLine) {
        $time = $newLine['start'] . ' ' . $newLine['end'];
        $text = $newLine['text'];
        $string = convHtml($time) . '<br>' . convHtml($text) . '<br><br>';

        $outputLines[] = $string;
    }
}

$fileFooter = convHtml('<end subtitles>');
$outputLines[] = $fileFooter;

if (!empty($_POST['input'])) {
    echo
        '<form action="" method="post">' .
            '<h3>Input:</h3>' .
            '<textarea name="input" id="input" cols="50" rows="20">' . $_POST['input'] . '</textarea>' .
            '<input type="submit" id="convert" value="convert ->" />' .
        '</form>';

    echo
        '<div id="wrapper">' .
            '<h3>Output:</h3>' .
            '<textarea id="test">';

    foreach ($outputLines as $item) {
        echo str_replace('<br>', '&#13;&#10;', $item); // change br to \r\n /firstly output was echo'ed in div, now it's in textarea/
    }

    echo
            '</textarea>' .
        '</div>';

} else {
    echo
        '<form action="" method="post">' .
            '<h3>Input:</h3>' .
            '<textarea name="input" id="input" cols="50" rows="20">' .
                '0:10:00 First subtitle bla bla bla' . '&#13;&#10;' .
                '0:20:00 Second subtitle bla bla bla' . '&#13;&#10;' .
                '0:33:00 Third subtitle bla bla bla' . '&#13;&#10;' .
                '1:10:00 First subtitle bla bla bla' . '&#13;&#10;' .
                '20:20:00 Fourth subtitle bla bla bla' . '&#13;&#10;' .
                '61:13:00 Sixth subtitle bla bla bla' . '&#13;&#10;' .
                '78:33:00 Seventh subtitle bla bla bla' . '&#13;&#10;' .
            '</textarea>' .
            '<input type="submit" id="convert" value="convert ->" />' .
        '</form>';

    echo
        '<div id="wrapper">' .
            '<h3>Output:</h3>' .
            '<textarea id="test">' .
            '</textarea>' .
        '</div>';
}
?>
</body>
</html>
