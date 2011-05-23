<?php

class Output_Indent{
    
    const STATE_STARTED = 1;
    
    const STATE_NEWL = 4;
    
    /**
     * indentation string
     * 
     * @var string
     */
    private $indent;
    
    private $state;
    
    public function __construct($indent=2){
        if(is_int($indent)){
            $indent = str_repeat(' ',$indent);
        }
        
        $this->indent = $indent;
        
        $state = $this->state = self::STATE_NEWL;
        
        $chunks = array();
        $fn = function($chunk,$mode) use ($indent,&$state,&$chunks){
            //$chunks[] = $chunk;
            
            /*if($mode & PHP_OUTPUT_HANDLER_START){
                $state |= Output_Indent::STATE_NEWL;
            }*/
            
            if(!($state & Output_Indent::STATE_STARTED) && substr($chunk,0,2) === "\r\n"){ // ignroe initial newline
                $chunk = (string)substr($chunk,2);
                $state |= Output_Indent::STATE_STARTED;
            }
            
            //$chunk = '['.$mode.':'.$chunk.']';
            
            if($state & Output_Indent::STATE_NEWL){
                $chunk = "\r\n".$chunk;
                $state &= ~Output_Indent::STATE_NEWL;
            }
            if(substr($chunk,-2) === "\r\n"){ // ends with newline
                $chunk = (string)substr($chunk,0,-2); // remove and remember for next output
                $state |= Output_Indent::STATE_NEWL;
            }else{
                $state &= ~Output_Indent::STATE_NEWL;
            }
            
            $chunk = str_replace("\r\n","\r\n".$indent,$chunk);
            
            if($mode & PHP_OUTPUT_HANDLER_END){
                //$chunk .= '['.var_export($chunks,true).']';
            }
            
            if($chunk !== ''){
                $state |= Output_Indent::STATE_STARTED;
            }
            
            //$chunk = '<<'.$state.':'.$chunk.'>>';
            
            return $chunk;
        };
        
        if(ob_start($fn,2) === false){
            throw new Exception('Unable to start output buffer');
        }
    }
    
    public function deeper(){
        return new Output_Indent($this->indent);
    }
    
    /**
     * print the given text on one line
     * 
     * @param string $text
     * @return Output_Indent $this (chainable)
     * @uses Output_Indent::isClear()
     */
    public function line($text){
        if($this->isClear()){
            echo $text."\r\n";
        }else{
            echo "\r\n".$text."\r\n";
        }
        return $this;
    }
    
    /**
     * make sure cursor is at the beginning of a line
     * 
     * @return Output_Indent $this (chainable)
     * @uses Output_Indent::isClear();
     */
    public function clear(){
        if(!$this->isClear()){
            echo "\r\n";
        }
        return $this;
    }
    
    public function flush(){
        ob_flush();
        return $this;
    }
    
    /**
     * check whether cursor is at the beginning of a line
     * 
     * @return boolean
     */
    public function isClear(){
        ob_flush(); // clear remaining output buffer (probably empty or just a single character)
        return !!($this->state & self::STATE_NEWL && $this->state & self::STATE_STARTED);
    }
    
    /**
     * check whether this output buffer did output anything at all
     * 
     * @return boolean
     */
    public function isEmpty(){
        ob_flush();
        return !($this->state & self::STATE_STARTED);
    }
    
    public function __destruct(){
        //echo '[END]';
        if(ob_end_flush() === false){
            throw new Exception('Unable to end output buffer');
        }
    }
}