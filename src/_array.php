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

    private function _cookKey($key)
    {
        if ( false !== strpos($key, ':')) {
            $key = 'xmlns:'.$key;
        }
        return $key;
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
            $k = $this->_cookKey($k);
            $xml->addAttribute($k,$v);
        }
    }

    public function addChild($xml, $k, $v = null)
    {
        if (!is_null($v)) {
            $result = $xml->addChild(
                $this->_cookKey($k),
                htmlspecialchars($v)
            );
        } else {
            $result = $xml->addChild(
                $this->_cookKey($k)
            );
        }
        return $result;
    }

    private function _processChildren($arr, $xml, $tag)
    {
        if (!isset($arr['@children'])) {
            return;
        }
        if (!is_array($arr['@children'])) {
            return;
        }
        foreach ($arr['@children'] as $childKey=>$child) {
            $child = $this->_reTag($child, $tag, $childKey);
            if (!is_array($child)) {
                $this->addChild(
                    $xml,
                    $childKey,
                    $child
                );
                continue;
            }
            if (isset($child['@children']) && !is_array($child['@children'])) {
                $dom = $this->addChild(
                    $xml,
                    $child['@name'],
                    $child['@children']
                );
            } else {
                $dom = $this->addChild(
                    $xml,
                    $child['@name']
                );
            }
            $this->_processOne($child, $dom, $tag);
        }
    }

    private function _processOne($arr, $xml, $tag)
    {
        $arr = $this->_reTag($arr, $tag);
        $this->_processProps($arr, $xml);
        $this->_processChildren($arr, $xml, $tag);
    }

    private function _reTag($arr, $tag, $key=null)
    {
      if ($tag) {
        $oldArr = $arr;
        $oldName = \PMVC\get($arr, '@name', $key);
        if ($oldName !== $tag) {
          if (!is_array($arr)) {
            $arr = [];
          }
          if (!isset($arr['@children']) && !isset($arr['@name'])) {
            $arr['@children'] = $oldArr;
          }
          $arr['@name'] = $tag;
          if (strlen($oldName)) {
            $arr['@props']['name'] = $oldName;
          }
        }
      }
      return $arr;
    }

    private function _toXmlObj($arr, $tag=null)
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $arr = $this->_reTag($arr, $tag);
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
        $this->_processOne($arr, $xml, $tag);
        return $xml;
    }

    public function toXml($arr, $tag=null)
    {
        $xml = $this->_toXmlObj($arr, $tag);
        return $xml->asXML();
    }

    public function toHtml($arr, $tag=null)
    {
      $xml = $this->_toXmlObj($arr, $tag);
      $dom = dom_import_simplexml($xml);
      return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
    } 
}
