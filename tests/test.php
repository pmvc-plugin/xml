<?php
namespace PMVC\PlugIn\xml;

use PHPUnit_Framework_TestCase;

class XmlTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'xml';
    function testPlugin()
    {
        ob_start();
        print_r(\PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($this->_plug,$output);
    }

}
