<?php

namespace App;
class AppLogging
{
    /**
     * @var false|string
     */
    private $date;
    /**
     * @var bool
     */
    private $user;
    /**
     * @var string
     */
    private $userName;
    /**
     * @var string
     */
    private $message;
    /**
     * @var string
     */
    private $logFile = 'applog.log';
    /**
     * @var string
     */
    private $pathLogFile = null;


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
    private function checkLogFile()
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
    public function write($text)
    {
        if (!is_null($this->pathLogFile)) {
            $this->message = '[' . $this->date . '] | ' . $this->user . ' | ' . $this->userName . ' | ' . $text . ';' . PHP_EOL;

            $lastLine = $this->getLineInfos($this->tailCustom($this->pathLogFile, 1));
            if ($lastLine) {

                if ($this->user == $lastLine['userId']
                    && $text == strstr($lastLine['text'], ';', true)) {
                    return false;
                }
            }

            $appLogFile = fopen($this->pathLogFile, 'a');
            fwrite($appLogFile, $this->message);
            fclose($appLogFile);
            return true;
        }
        return false;
    }

    /**
     * @param $line
     * @return array|bool
     */
    public function getLineInfos($line)
    {

        if (!empty($line) && false !== strpos($line, '|')) {
            list($time, $userId, $userName, $text) = array_map('trim', explode('|', $line));
            return compact('time', 'userId', 'userName', 'text');
        }
        return false;
    }

    /**
     * @param $filepath
     * @param int $lines
     * @param bool $adaptive
     * @return bool|string
     */
    public function tailCustom($filepath, $lines = 1, $adaptive = true)
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
        $chunk = '';

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