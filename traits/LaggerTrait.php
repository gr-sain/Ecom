<?php
trait LoggerTrait {
    public function log($message, $level = 'INFO') {
        $logFile = __DIR__ . '/../logs/app.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        
        // Create logs directory if it doesn't exist
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    public function logError($message) {
        $this->log($message, 'ERROR');
    }
    
    public function logWarning($message) {
        $this->log($message, 'WARNING');
    }
    
    public function logInfo($message) {
        $this->log($message, 'INFO');
    }
}
?>