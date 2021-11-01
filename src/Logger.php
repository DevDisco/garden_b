<?php


namespace App;

class Logger
{


    //sets the $projectDir path from services.yaml
    public function __construct(private string $projectDir, private string $logfile)
    {
    }

    public function showLogfilePath()
    {
        print $this->projectDir . $this->logfile;
    }

    public function toLogFile(string $message = "test", string $method = "")
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $message = date("y-m-d H:i:s") . "\n\tText:\t" . $message . "\n";
        $message .= "\tFile:\t" . str_replace($this->projectDir, "~", $backtrace[0]['file']) . "\n";
        $message .= "\tLine:\t" . $backtrace[0]['line'] . "\n";
        if ($method !== "") {
            $message .= "\tMethod:\t" . $method . "\n";
        }
        file_put_contents($this->projectDir . $this->logfile, $message, FILE_APPEND);
    }

    public function emptyLogFile()
    {
        file_put_contents($this->projectDir . $this->logfile,
            ""
        );
    }

    public function dividerToLogfile()
    {
        file_put_contents($this->projectDir . $this->logfile, "==========================\n", FILE_APPEND);
    }
}
