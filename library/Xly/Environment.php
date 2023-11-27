<?php
/**
 * @filesource Environment.php
 * @brief      load environment
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace Xly;

class Environment {

    protected $filePath;

    public function __construct($filePath) {
        $this->filePath = $filePath;
    }

    public function load() {
        $lines = $this->readLinesFromFile($this->filePath);
        foreach ($lines as $line) {
            if (!$this->isComment($line) && $this->looksLikeSetter($line)) {
                $this->setEnvironmentVariable($line);
            }
        }

        return $lines;
    }

    public function setEnvironmentVariable($name, $value = null) {
        list($name, $value) = $this->normaliseEnvironmentVariable($name, $value);

        if (function_exists('putenv')) {
            putenv("$name=$value");
        }

        $_ENV[$name]    = $value;
        $_SERVER[$name] = $value;
    }

    /**
     * Read lines from the file, auto detecting line endings.
     *
     * @param string $filePath
     *
     * @return array
     */
    protected function readLinesFromFile($filePath) {
        // Read file into an array of lines with auto-detected line endings
        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        ini_set('auto_detect_line_endings', $autodetect);

        return $lines;
    }

    protected function normaliseEnvironmentVariable($name, $value) {
        list($name, $value) = $this->splitCompoundStringIntoParts($name, $value);
        list($name, $value) = $this->sanitiseVariableName($name, $value);
        list($name, $value) = $this->sanitiseVariableValue($name, $value);

        $value = $this->resolveNestedVariables($value);

        return [$name, $value];
    }

    protected function isComment($line) {
        return strpos(ltrim($line), '#') === 0;
    }

    protected function looksLikeSetter($line) {
        return strpos($line, '=') !== false;
    }

    /**
     * Split the compound string into parts.
     *
     * If the `$name` contains an `=` sign, then we split it into 2 parts, a `name` & `value`
     * disregarding the `$value` passed in.
     *
     * @param string $name
     * @param string $value
     *
     * @return array
     */
    protected function splitCompoundStringIntoParts($name, $value) {
        if (strpos($name, '=') !== false) {
            list($name, $value) = array_map('trim', explode('=', $name, 2));
        }

        return [$name, $value];
    }

    /**
     * Strips quotes from the environment variable value.
     *
     * @param string $name
     * @param string $value
     *
     * @return array
     * @throws \Dotenv\Exception\InvalidFileException
     *
     */
    protected function sanitiseVariableValue($name, $value) {
        $value = trim($value);
        if (!$value) {
            return [$name, $value];
        }

        if ($this->beginsWithAQuote($value)) { // value starts with a quote
            $quote        = $value[0];
            $regexPattern = sprintf(
                '/^
                %1$s          # match a quote at the start of the value
                (             # capturing sub-pattern used
                 (?:          # we do not need to capture this
                  [^%1$s\\\\] # any character other than a quote or backslash
                  |\\\\\\\\   # or two backslashes together
                  |\\\\%1$s   # or an escaped quote e.g \"
                 )*           # as many characters that match the previous rules
                )             # end of the capturing sub-pattern
                %1$s          # and the closing quote
                .*$           # and discard any string after the closing quote
                /mx',
                $quote
            );
            $value        = preg_replace($regexPattern, '$1', $value);
            $value        = str_replace("\\$quote", $quote, $value);
            $value        = str_replace('\\\\', '\\', $value);
        } else {
            $parts = explode(' #', $value, 2);
            $value = trim($parts[0]);

            // Unquoted values cannot contain whitespace
            if (preg_match('/\s+/', $value) > 0) {
                throw new InvalidFileException('Dotenv values containing spaces must be surrounded by quotes.');
            }
        }

        return [$name, trim($value)];
    }

    /**
     * Resolve the nested variables.
     *
     * Look for {$varname} patterns in the variable value and replace with an
     * existing environment variable.
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function resolveNestedVariables($value) {
        if (strpos($value, '$') !== false) {
            $loader = $this;
            $value  = preg_replace_callback(
                '/\${([a-zA-Z0-9_]+)}/',
                function ($matchedPatterns) use ($loader) {
                    $nestedVariable = $loader->getEnvironmentVariable($matchedPatterns[1]);
                    if ($nestedVariable === null) {
                        return $matchedPatterns[0];
                    } else {
                        return $nestedVariable;
                    }
                },
                $value
            );
        }

        return $value;
    }

    /**
     * Strips quotes and the optional leading "export " from the environment variable name.
     *
     * @param string $name
     * @param string $value
     *
     * @return array
     */
    protected function sanitiseVariableName($name, $value) {
        $name = trim(str_replace(['export ', '\'', '"'], '', $name));

        return [$name, $value];
    }

    /**
     * Determine if the given string begins with a quote.
     *
     * @param string $value
     *
     * @return bool
     */
    protected function beginsWithAQuote($value) {
        return strpbrk($value[0], '"\'') !== false;
    }

}
