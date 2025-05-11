<?php

namespace App;
class AppLogging
{
    /**
     * @var false|string
     */
    private string|false $date;
    /**
     * @var bool
     */
    private bool $user;
    /**
     * @var string
     */
    private string $userName;

    /**
     * @var string
     */
    private string $logFile = 'applog.log';
    /**
     * @var string|null
     */
    private ?string $pathLogFile = null;


    /**
     * AppLogging constructor.
     */
    public function __construct()
    {
        $this->date = date('Y-m-d H:i:s');
        $this->user = getUserIdSession();
        $this->userName = getUserEntitled();

        if ($this->checkLogFile()) {
            $this->pathLogFile = WEB_SYSTEM_PATH . $this->logFile;
        }
    }

    /**
     * @return bool
     */
    private function checkLogFile(): bool
    {

        if (!file_exists(WEB_SYSTEM_PATH . $this->logFile)) {
            if (false === fopen(WEB_SYSTEM_PATH . $this->logFile, 'a+')) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $text
     * @return bool
     */
    public function write($text): bool
    {
        if (!is_null($this->pathLogFile)) {
            $message = '[' . $this->date . '] | ' . $this->user . ' | ' . $this->userName . ' | ' . $text . ';' . PHP_EOL;

            $lastLine = $this->getLineInfos($this->tailCustom($this->pathLogFile));
            if ($lastLine) {

                if ($this->user == $lastLine['userId']
                    && $text == strstr($lastLine['text'], ';', true)) {
                    return false;
                }
            }

            $appLogFile = fopen($this->pathLogFile, 'a');
            fwrite($appLogFile, $message);
            fclose($appLogFile);
            return true;
        }
        return false;
    }

    /**
     * @param mixed $line
     * @return array|bool
     */
    public function getLineInfos(mixed $line): bool|array
    {

        if (!empty($line) && str_contains($line, '|')) {
            list($time, $userId, $userName, $text) = array_map('trim', explode('|', $line));
            return compact('time', 'userId', 'userName', 'text');
        }
        return false;
    }

    /**
     * @param string $filepath
     * @param int $lines
     * @param bool $adaptive
     * @return string|false
     */
    public function tailCustom(string $filepath, int $lines = 1, bool $adaptive = true): string|false
    {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;

        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) $buffer = 4096;
        else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

        // Start reading
        $output = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {

            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);

            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);

            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;

            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }

        // Close file and return
        fclose($f);
        return trim($output);
    }
}