<?php

namespace PMVC\PlugIn\xml;

use PHPUnit_Framework_TestCase;
use PMVC\HashMapAll;

class XmlCovertTest extends PHPUnit_Framework_TestCase
{
    private $_plug='xml';

    function testXmlToArray()
    {
        $xmlString = '<?xml version="1.0" encoding="utf-8"?>'.
            "\n".
            '<foo><bar><aaa>bbb</aaa></bar></foo>'.
            "\n";
        $p = \PMVC\plug($this->_plug);
        $xml = $p->xml();
        $actual = $xml->toArray($xmlString);
        $expected = new HashMapAll([
            '@name'=>'foo',
            '@children'=>[
                [
                    '@name'=>'bar',
                    '@children'=> [[
                        '@name'=>'aaa',
                        '@children'=>'bbb',
                    ]]
                ]
            ]
        ]);
        $this->assertEquals($expected, $actual);
        $this->assertEquals($xmlString, $p->array()->toXml($actual));
    }

    function testHandleColon()
    {
        $xmlString = '<?xml version="1.0" encoding="utf-8"?>'.
            "\n".
            '<foo xmlns:bar="http://xxx"><bar:bar><bar:aaa>bbb</bar:aaa></bar:bar></foo>'.
            "\n";
        $p = \PMVC\plug($this->_plug);
        $xml = $p->xml();
        $actual = $xml->toArray($xmlString);
        $expected = new HashMapAll([
            '@name'=>'foo',
            '@props'=>[
                'xmlns:bar'=>'http://xxx'
            ],
            '@children'=>[
                [
                    '@name'=>'bar:bar',
                    '@children'=> [[
                        '@name'=>'bar:aaa',
                        '@children'=>'bbb',
                    ]]
                ]
            ]
        ]);
        $this->assertEquals($expected, $actual);
    }
}
