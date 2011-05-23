<?php

require_once 'Output/Indent.php';

class RealisticTest extends PHPUnit_Framework_TestCase {
    
    public function testOne(){
        ob_start();
        
        $indent = new Output_Indent('  ');
        $this->subOne();
        unset($indent);
        
        $content = ob_get_flush();
        $this->assertEquals("\r\n  a\r\n    b\r\n  c",$content); 
    }
    
    private function subOne(){
        echo "a\r\n".
             "  b\r\n".
             "c";
    }
}
