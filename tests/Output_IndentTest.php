<?php

define('NL',"\r\n");

require_once 'Output/Indent.php';

class Output_IndentTest extends PHPUnit_Framework_TestCase {
    
    public function testOne(){
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
}
