<?php
namespace Speed\Logger;

/**
 *
 * Class ConsoleLogger
 * @package Speed\Logger
 */
class ConsoleLogger extends Logger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if (!isset($this->verbosityLevelMap[$level])) {
            throw new \InvalidArgumentException(sprintf('The log level "%s" does not exist.', $level));
        }

        if ($this->formatLevelMap[$level] === self::ERROR) {
            $handle = STDERR;
        } else {
            $handle = STDOUT;
        }

        if ($this->verbose >= $this->verbosityLevelMap[$level]) {
            $format['log_time'] = '[' . strftime('%Y-%m-%d %T')  . ']';
            $format['log_level'] = '[' . $level  . ']';
            $format['log_message'] = $this->interpolate($message, $context);
            $output = $this->interpolate($this->logFormat,$format) .PHP_EOL;
            fwrite($handle, Color::set($output, $this->colorMap[$level]));
        }
    }

}