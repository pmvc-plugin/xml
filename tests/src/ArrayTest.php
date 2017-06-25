<?php

namespace PMVC\PlugIn\xml;

use PHPUnit_Framework_TestCase;

class ArrayTest extends PHPUnit_Framework_TestCase
{
    private $_plug='xml';

    function testNameOnly()
    {
        $p = \PMVC\plug($this->_plug);
        $array = $p->array();
        $arr = [
            '@name'=>'foo'
        ];
        $actual = $array->toXml($arr);
        $expected = '<?xml version="1.0" encoding="utf-8"?>'.
            "\n".
            '<foo/>'.
            "\n";
        $this->assertEquals($expected, $actual);
    }

    function testOneTextOnly()
    {
        $p = \PMVC\plug($this->_plug);
        $array = $p->array();
        $arr = [
            '@name'=>'foo',
            '@children'=>'bar'
        ];
        $actual = $array->toXml($arr);
        $expected = '<?xml version="1.0" encoding="utf-8"?>'.
            "\n".
            '<foo>bar</foo>'.
            "\n";
        $this->assertEquals($expected, $actual);
    }

    function testChildWithSimpleArray()
    {
        $arr = [
            '@name'=>'foo',
            '@children'=>[
                'aaa'=>'bbb'
            ]
        ];
        $p = \PMVC\plug($this->_plug);
        $array = $p->array();
        $actual = $array->toXml($arr);
        $expected = '<?xml version="1.0" encoding="utf-8"?>'.
            "\n".
            '<foo><aaa>bbb</aaa></foo>'.
            "\n";
        $this->assertEquals($expected, $actual);
    }

    function testChildWithComplexArray()
    {
        $arr = [
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
        ];
        $p = \PMVC\plug($this->_plug);
        $array = $p->array();
        $actual = $array->toXml($arr);
        $expected = '<?xml version="1.0" encoding="utf-8"?>'.
            "\n".
            '<foo><bar><aaa>bbb</aaa></bar></foo>'.
            "\n";
        $this->assertEquals($expected, $actual);
    }

    function testSetProps()
    {
        $arr = [
            '@name'=>'foo',
            '@children'=>[
                [
                    '@name'=>'bar',
                    '@props'=>[
                        'c'=>1,
                        'd'=>2
                    ]
                ]
            ],
            '@props'=>[
                'a'=>1,
                'b'=>2
            ]
        ];
        $p = \PMVC\plug($this->_plug);
        $array = $p->array();
        $actual = $array->toXml($arr);
        $expected = '<?xml version="1.0" encoding="utf-8"?>'.
            "\n".
            '<foo a="1" b="2"><bar c="1" d="2"/></foo>'.
            "\n";
        $this->assertEquals($expected, $actual);
    }
}
