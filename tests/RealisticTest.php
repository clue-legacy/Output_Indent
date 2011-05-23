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
    
    public function testOneRepeated(){
        $this->testOne();
        $this->testOne();
        $this->testOne();
    }
    
    public function testTwo(){
        ob_start();
        
        echo "two";
        $indent = new Output_Indent('  ');
        $this->subTwo();
        unset($indent);
        
        $content = ob_get_flush();
        $this->assertEquals("two\r\n  deeper\r\n    deepest",$content); 
    }
    
    private function subTwo(){
        echo "deeper";
        $indent = new Output_Indent(2);
        $this->subSubTwo();
    }
    
    private function subSubTwo(){
        echo "deepest";
    }
}
