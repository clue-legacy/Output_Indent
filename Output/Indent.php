<?php

class Output_Indent{
    public function __construct($indent=2){
        if(is_int($indent)){
            $indent = str_repeat(' ',$indent);
        }
        
        $this->indent = $indent;
        
        $fn = function($chunk,$mode) use ($indent){
            if($mode & PHP_OUTPUT_HANDLER_START){
                $chunk = "\r\n".ltrim($chunk);
            }
            if($mode & PHP_OUTPUT_HANDLER_END){
                $chunk = rtrim($chunk);
            }
            $chunk = str_replace(array("\r\n"/*,"\n","\r"*/),"\r\n".$indent,$chunk);
            
            return $chunk;
        };
        
        if(ob_start($fn,2) === false){
            throw new Exception('Unable to start output buffer');
        }
    }
    
    public function deeper(){
        return new Output_Indent($this->indent);
    }
    
    public function line($text){
        echo $text."\r\n";
    }
    
    public function __destruct(){
        if(ob_end_flush() === false){
            throw new Exception('Unable to end output buffer');
        }
    }
}