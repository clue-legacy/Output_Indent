<?php

define('NL',"\r\n");

require_once 'Output/Indent.php';

class Output_IndentTest extends PHPUnit_Framework_TestCase {
    
    public function testTree(){
        echo 'root-a';
        echo NL.'root-b';
        $indent = new Output_Indent("\t");
        
        echo NL.'a';
        echo NL.'b';
        echo NL.'c';
        
        //$depper = clone $indent;
        //$deeper = new Output_Indent();
        $deeper = $indent->deeper();
        echo NL.'c1';
        echo NL.'c2';
        unset($deeper);
        
        //$this->assertEquals('',$output);
        
        unset($indent);
        
        echo NL.'end';
        
        //$this->assertEquals('',$output);
    }
    
    public function testLines(){
        ob_start();
        
        $indent = new Output_Indent(0);
        $indent->line('1');
        $indent->line('2');
        $indent->line('3');
        unset($indent);
        
        $content = ob_get_flush();
        $this->assertEquals("\r\n1\r\n2\r\n3",$content); 
        
    }
    
    public function testClear(){
        ob_start();
        
        $indent = new Output_Indent('#');
        for($i=0;$i<5;++$i){
            $indent = $indent->clear();
        }
        $indent->line('test');
        unset($indent);
        
        $content = ob_get_flush();
        $this->assertEquals("\r\n#test",$content); 
    }
    
    public function testIsClear(){
        $indent = new Output_Indent('  ');
        for($i=0;$i<5;++$i){
            $this->assertFalse($indent->isClear());
        }
        $indent->line('line');
        for($i=0;$i<5;++$i){
            $this->assertTrue($indent->isClear());
        }
    }
    
    public function testFlush(){
        ob_start();
        
        $indent = new Output_Indent('flush');
        echo 'in';
        for($i=0;$i<5;++$i){
            $indent = $indent->flush();
        }
        echo 'g';
        unset($indent);
        
        $content = ob_get_flush();
        $this->assertEquals("\r\nflushing",$content); 
    }
    
    public function testMixed(){
        ob_start();
        
        echo 'a';
        $indent = new Output_Indent(2);
        echo '1';
        $indent->line('2');
        echo '3';
        
        unset($indent);
        echo 'b';
        
        $content = ob_get_flush();
        $this->assertEquals("a\r\n  1\r\n  2\r\n  3b",$content); 
    }
    
    public function testEmpty(){
        ob_start();
        
        echo "a";
        new Output_Indent(8);
        echo "\r\nb";
        
        $content = ob_get_flush();
        $this->assertEquals("a\r\nb",$content); 
    }
    
    public function testEmptyFlush(){
        ob_start();
        
        echo "a";
        $indent = new Output_Indent(8);
        $indent->flush();
        $indent->flush();
        unset($indent);
        echo "\r\nb";
        
        $content = ob_get_flush();
        $this->assertEquals("a\r\nb",$content); 
    }
    
    public function testDeep(){
        ob_start();
        
        $indent = new Output_Indent('.');
        $indent2 = $indent->deeper();
        $indent3 = $indent->deeper();
        echo 'dots';
        unset($indent3);
        unset($indent2);
        unset($indent);
        
        $content = ob_get_flush();
        $this->assertEquals("\r\n...dots",$content); 
    }
}
