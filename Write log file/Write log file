<?php
class Write {
    
    const DATE_FORMAT = "Y-d-m H:i:s";
    
    private function __construct(){}
    
    
    /**
     * Generate log file.
     * @param string $text - Here post your text message.
     * @param string $file_name - If you need other fine name.
     * @param string $file_path - If you like use other path.
     * @param string $type - Using default "FILE_APPEND".
     * 
     * Description(FILE_APPEND): If file filename already exists, 
     * append the data to the file instead
     * of overwriting it. 
     * Or visit it: http://php.net/manual/en/function.file-put-contents.php 
     */
    public static function wLog($text, $file_name='debug.txt', $file_path='', $type = FILE_APPEND){
        \file_put_contents($file_path . $file_name, \date(self::DATE_FORMAT)." : ".$text."\n",$type);
    }
}