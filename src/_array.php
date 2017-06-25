<?php

namespace PMVC\PlugIn\xml;

use DOMDocument;
use SimpleXMLElement;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\ArrayConvert';

class ArrayConvert
{
    public function __invoke()
    {
        return $this;
    }

    private function _processProps($arr, $xml)
    {
        if (!isset($arr['@props'])) {
            return;
        }
        if (!is_array($arr['@props'])) {
            return !trigger_error('Props should be array. '.print_r($arr['@props'], true));
        }
        foreach ($arr['@props'] as $k=>$v) {
            $xml->addAttribute($k,$v);
        }
    }

    private function _processChildren($arr, $xml)
    {
        if (!isset($arr['@children'])) {
            return;
        }
        if (!is_array($arr['@children'])) {
            return;
        }
        foreach ($arr['@children'] as $childKey=>$child) {
            if (!is_array($child)) {
                $xml->addChild($childKey, $child); 
                continue;
            }
            if (isset($child['@children']) && !is_array($child['@children'])) {
                $dom = $xml->addChild(
                    $child['@name'],
                    $child['@children']
                ); 
            } else {
                $dom = $xml->addChild($child['@name']); 
            }
            $this->_processOne($child, $dom);
        }
    }

    private function _processOne($arr, $xml)
    {
        $this->_processProps($arr, $xml);
        $this->_processChildren($arr, $xml);
    }

    public function toXml($arr)
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        if (isset($arr['@children']) &&
            !is_array($arr['@children'])) 
        {
            $root = $dom->createElement(
                $arr['@name'],
                $arr['@children']
            );
        } else {
            $root = $dom->createElement($arr['@name']);
        }
        $dom->appendChild($root);
        $xml = simplexml_import_dom($dom);
        $this->_processOne($arr, $xml);
        return $xml->asXML();
    }
 
}
