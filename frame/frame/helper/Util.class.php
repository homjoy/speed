<?php

/**
 * https://github.com/igorw/retry
 */

namespace Frame\Helper;

class Util {

    static function retry($retries, $fn)
    {
        if (!is_callable($fn)) throw new \Exception("fn should be callable");
        beginning:

        if ($retries <= 0) throw new \Exception('retry error');

        try {
            return $fn();
        } catch (\Frame\Exception\RetryException $e) {
            $retries--;
            goto beginning;
        }
    }

    static function getClosure($fn)
    {
        $source = new \ReflectionFunction($fn);
        $class = $source->getFileName();
        $beginLine = $source->getStartLine();
        $endLine = $source->getEndline();

        return "{$class} {$beginLine} : {$endLine}"; 
    }

    static function ssort($data) {
        $seed = rand(0, count($data) - 1);
        $x = array_slice($data, 0, $seed);
        $y = array_slice($data, $seed);

        return array_merge($y, $x);
    }

}
