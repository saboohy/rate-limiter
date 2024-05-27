<?php declare(strict_types=1);

namespace Saboohy\Limiter;

final class Logger
{
    /**
     * Puts A Last Log
     * 
     * @param int $time
     * @param int $start_date
     * @param string $client_ip
     * @param string $method
     * @param string $path
     * 
     * @return void
     */
    public function putClientLog(int $time = 1, int $start_date = 1, string $client_ip = "127.0.0.1", string $method = "GET", string $path = "/"): void 
    {
        $file = fopen($this->logFile(), "a");
        $logData = sprintf("log_date: %s start_date: %s client: %s method: %s path: %s\n", $time, $start_date, $client_ip, $method, $path);
        fwrite($file, $logData);
        fclose($file);
    }

    /**
     * Gets Client's Last Logs
     * 
     * @param string $client_ip
     * @param string $method
     * @param string $path
     * 
     * @return array
     */
    public function getClientLastLog(string $client_ip = "127.0.0.1", string $method = "GET", string $path = "/"): array
    {

        $path = str_replace("/", "\/", $path);
        $pattern = "/^log_date:\s([0-9]{10})\sstart_date:\s([0-9]{10})\sclient:\s({$client_ip})\smethod: ({$method})\spath:\s({$path})$/";

        return $this->getLogs($pattern);
    }

    /**
     * Gets Client's Last Logs By Start Date
     * 
     * @param int $start_date
     * @param string $client_ip
     * @param string $method
     * @param string $path
     * 
     * @return array
     */
    public function getClientLastLogsByStartDate(int $start_date = 1, string $client_ip = "127.0.0.1", string $method = "GET", string $path = "/"): array
    {
        $path = str_replace("/", "\/", $path);
        $pattern = "/^log_date:\s([0-9]{10})\sstart_date:\s({$start_date})\sclient:\s({$client_ip})\smethod: ({$method})\spath:\s({$path})$/";

        return $this->getLogs($pattern);
    }

    /**
     * Daily Log File name
     * 
     * @return string
     */
    private function logFile(): string
    {
        $today = date("Ymd");
        $filename = sprintf("limit_logs_%s.txt", $today);

        return basepath("logs/{$filename}");
    }

    /**
     * Returns Logs
     * 
     * @param string $pattern
     * 
     * @return array
     */
    private function getLogs(string $pattern = ""): array
    {
        $result = [];

        $lines = file($this->logFile(), FILE_IGNORE_NEW_LINES);

        if ($lines) {
            foreach ($lines as $line) {
                $match = preg_match($pattern, $line, $matches);
                if ($match) {
                    $result[] = [
                        "log_date"      => (int) $matches[1],
                        "start_date"    => (int) $matches[2],
                        "method"        => $matches[4],
                        "path"          => $matches[5]
                    ];
                }
            }
        }

        return $result;
    }
}