<?php declare(strict_types=1);

namespace Saboohy\Limiter;

use Saboohy\Limiter\Logger;
use Saboohy\Limiter\TooManyRequestException;

final class RateLimiter
{
    /**
     * Client IP Address
     * 
     * @var string
     */
    private string $clientIp = "127.0.0.1";

    /**
     * Available Routes
     * 
     * @var array
     */
    private array $routes = [];

    private $logger;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * Getting Client Ip Address
     * 
     * @param string $client_ip
     * 
     * @return void
     */
    public function clientIp(string $client_ip = "127.0.0.1"): void
    {
        $this->clientIp = $client_ip;
    }

    /**
     * Getting Avalibale Route
     * 
     * @param string    $method
     * @param string    $path
     * @param int       $limit
     * @param int       $second
     * 
     * @return void
     */
    public function addRoute(string $method = "GET", string $path = "/", int $limit = 1, int $second = 60): void
    {
        $this->routes[] = [
            "method"    => $method,
            "path"      => $path,
            "limit"     => $limit,
            "second"    => $second
        ];
    }

    public function __destruct()
    {
        $clientIp       = $_SERVER["REMOTE_ADDR"];
        $currentPath    = $_SERVER["REQUEST_URI"];
        $currentMethod  = $_SERVER["REQUEST_METHOD"];

        $now = time();
        $startDate = time();

        foreach ($this->routes as $route) {
           
            if ( $route["method"] == $currentMethod && $route["path"] == $currentPath) {

                $lastLog = $this->logger->getClientLastLog($this->clientIp, $route["method"], $route["path"]);

                if ($lastLog) {
                    
                    $lastLine = end($lastLog);

                    if ($now - $lastLine["start_date"] <= $route["second"]) {
                        $startDate = $lastLine["start_date"];

                        $logsByStartDate = $this->logger->getClientLastLogsByStartDate($startDate, $this->clientIp, $route["method"], $route["path"]);

                        if (count($logsByStartDate) >= $route["limit"]) {
                            throw new TooManyRequestException("Error 429! Too Many Request Exception!");
                        }
                    }
                }

                $this->logger->putClientLog($now, $startDate, $this->clientIp, $route["method"], $route["path"]);

                break;
            }
        }
    }
}