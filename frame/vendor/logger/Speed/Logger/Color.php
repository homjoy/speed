<?php
/**
 * copy from https://gist.github.com/superbrothers/3431198
 * php-ansi-color
 *
 * Original
 *     https://github.com/loopj/commonjs-ansi-color
 *
 * @code
 * <?php
 * require_once "ansi-color.php";
 *
 * use PhpAnsiColor\Color;
 *
 * // Print the word "Error" to stdout in red
 * error_log(Color::set("Error", "red"));
 *
 * // Print the word "Error" in red and underlined
 * error_log(Color::set("Error", "red+underline"));
 *
 * // Print the word "Success" in bold green, followed by a message
 * error_log(Color::set("Success", "green+bold"), "Something was successful!");
 * @endcode
 */
namespace Speed\Logger;

/**
 * Class Color
 * @package Speed\Logger
 */
class Color
{
    protected static $ANSI_CODES = array(
        "off"        => 0,
        "bold"       => 1,
        "italic"     => 3,
        "underline"  => 4,
        "blink"      => 5,
        "inverse"    => 7,
        "hidden"     => 8,
        "black"      => 30,
        "red"        => 31,
        "green"      => 32,
        "yellow"     => 33,
        "blue"       => 34,
        "magenta"    => 35,
        "cyan"       => 36,
        "white"      => 37,
        "black_bg"   => 40,
        "red_bg"     => 41,
        "green_bg"   => 42,
        "yellow_bg"  => 43,
        "blue_bg"    => 44,
        "magenta_bg" => 45,
        "cyan_bg"    => 46,
        "white_bg"   => 47
    );

    /**
     * 设置字符串的颜色
     * @param $str
     * @param $color
     * @return string
     */
    public static function set($str, $color)
    {
        $colorAttributes = explode("+", $color);
        $ansiStr = "";
        foreach ($colorAttributes as $attribute) {
            $ansiStr .= "\033[" . static::$ANSI_CODES[$attribute] . "m";
        }
        $ansiStr .= $str . "\033[" . static::$ANSI_CODES["off"] . "m";
        return $ansiStr;
    }

    /**
     * 使用正则给文本添加颜色
     * @param $fullText
     * @param $searchRegexp
     * @param $color
     * @return mixed
     */
    public static function replace($fullText, $searchRegexp, $color)
    {
        $newText = preg_replace_callback(
            "/($searchRegexp)/",
            function ($matches) use ($color) {
                return Color::set($matches[1], $color);
            },
            $fullText
        );

        return is_null($newText) ? $fullText : $newText;
    }
}