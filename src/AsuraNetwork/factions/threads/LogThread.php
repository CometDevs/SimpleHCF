<?php

namespace AsuraNetwork\factions\threads;

use AsuraNetwork\Loader;
use DateTimeZone;
use pocketmine\thread\Thread;
use RuntimeException;
use Threaded;

class LogThread extends Thread{

    private string $logFile;
    private Threaded $buffer;
    private bool $running;
    private bool $timestamp;
    private DateTimeZone $timezone;
    private string $format = "[%s] [%s] %s";


    public function __construct(string $logFile, bool $timestamp = true){
        $this->logFile = $logFile;
        $this->timestamp = $timestamp;
        $this->buffer = new Threaded();
        touch($this->logFile);
        $this->timezone = new DateTimeZone(Loader::$config["time-zone"]??"America/Chicago");
    }

    public function registerClassLoaders(): void{
    }

    public function start(int $options = PTHREADS_INHERIT_ALL): bool{
        $this->running = true;
        return parent::start($options);
    }

    public function shutdown(): void{
        $this->synchronized(function (){
           $this->running = false;
        });
    }

    public function write(string $buffer): void{
        $this->buffer[] = $buffer;
        $this->notify();
    }

    public function onRun(): void{
        $logResource = fopen($this->logFile, 'ab');
        if (!is_resource($logResource)){
            throw new RuntimeException('Cannot open log file: ' . $logResource);
        }
        while ($this->running){
            $this->writeStream($logResource);
            $this->synchronized(function (){
               if ($this->running){
                   $this->wait();
               }
            });
        }
        $this->writeStream($logResource);
        fclose($logResource);
    }

    protected function writeStream($stream): void{
        $time = new \DateTime('now', $this->timezone);
        while ($this->buffer->count() > 0){
            /** @var string $line */
            $line = $this->buffer->pop();
            if ($this->timestamp){
                $line = sprintf($this->format, $time->format("Y-m-d"), $time->format('H:i:s.v'), $line) . PHP_EOL;
            }
            fwrite($stream, $line);
        }
    }
}