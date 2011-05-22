<?php

class Output_Indent{
    
    const STATE_IS_CLEAR = 2;
    
    const STATE_STARTED = 1;
    
    const STATE_NEWL = 4;
    
    
    /**
     * indentation string
     * 
     * @var string
     */
    private $indent;
    
    private $state = 0;
    
    private $ignoreEnd = 0;
    
    public function __construct($indent=2){
        if(is_int($indent)){
            $indent = str_repeat(' ',$indent);
        }
        
        $this->indent = $indent;
        
        $state = $this->state;
        $ignoreEnd =& $this->ignoreEnd;
        $fn = function($chunk,$mode) use ($indent,&$state,&$ignoreEnd){
            //$chunk = '['.$mode.':'.$chunk.']';
            
            if($mode & PHP_OUTPUT_HANDLER_START){
                if(substr($chunk,0,2) !== "\r\n"){
                    $chunk = "\r\n".$chunk;
                }
                $state |= Output_Indent::STATE_STARTED;
            }
            
            if($state & Output_Indent::STATE_NEWL){
                $chunk = "\r\n".$chunk;
                $state &= ~Output_Indent::STATE_NEWL;
            }
            if(substr($chunk,-2) === "\r\n"){ // ends with newline
                $chunk = substr($chunk,0,-2); // remove and remember for next output
                $state |= Output_Indent::STATE_NEWL;
            }
            
            
            
            if(substr($chunk,-2) === "\r\n"){
                $state |= Output_Indent::STATE_IS_CLEAR;
            }else{
                $state &= ~Output_Indent::STATE_IS_CLEAR;
            }
            if($ignoreEnd !== 0){
                $chunk = substr($chunk,0,-$ignoreEnd);
                /*if(substr($chunk,-strlen($ignoreEnd)) === $ignoreEnd){
                    $chunk = substr($chunk,0,-strlen($ignoreEnd));
                }*/
                $ignoreEnd = NULL;
            }
            $chunk = str_replace(array("\r\n"/*,"\n","\r"*/),"\r\n".$indent,$chunk);
            
            if($mode & PHP_OUTPUT_HANDLER_END){
                /*if(substr($chunk,-2) === "\r\n"){
                    $chunk = substr($chunk,0,-2);
                }*/
                if($state & Output_Indent::STATE_STARTED){
                    //$chunk .= "\r\n";
                }
            }
            
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
        if(!$this->isClear()){
            $text = "\r\n".$text;
        }
        echo $text."\r\n";
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
        if($this->state & self::STATE_STARTED){
            //$this->ignoreEnd = 7;
            //echo "[FLUSH]";
            flush();
            ob_flush();
        }
        return $this;
    }
    
    /**
     * check whether cursor is at the beginning of a line
     * 
     * @return boolean
     */
    public function isClear(){
        return !!($this->state & self::STATE_NEWL);
        
        if($this->state & self::STATE_STARTED){
            $this->flush();
            return !!($this->state & self::STATE_CLEAR);
            
            return false;
            
            $old = $this->ignoreEnd;
            $this->ignoreEnd = 2;
            echo "\r\n";
            
            $ret = ($this->ignoreEnd === '');
            
            $this->ignoreEnd = $old;
            
            return $ret;
        }
        return false;
    }
    
    public function __destruct(){
        //echo '[END]';
        if(ob_end_flush() === false){
            throw new Exception('Unable to end output buffer');
        }
    }
}