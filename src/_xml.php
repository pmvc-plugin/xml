<?php

namespace PMVC\PlugIn\xml;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\XmlConvert';

use PMVC\HashMapAll;

class XmlConvert
{
    public function __invoke()
    {
        return $this;
    }

    public function toObject($xml)
    {
        if (!is_object($xml)) {
           $xml = simplexml_load_string($xml); 
        }
        return $xml;
    }

    private function _processProps($obj, $ns=null)
    {
        $props = $obj->attributes($ns, true);
        $result = [];
        if (empty($ns)) {
            foreach( $props as $k => $v ) { 
                $k = strtolower(trim($k)); 
                $result[$k] = $v;
            }
        } else {
            foreach( $props as $k => $v ) { 
                $k = $ns.strtolower(trim($k)); 
                $result[$k] = $v;
            }
        }
        return $result;
    }

    private function _processChildren($obj, $ns=null)
    {
        $result = [];
        $children = $obj->children($ns, true);
        foreach( $children as $k => $v ) { 
            if (count($v)) {
                $result[] = $this->toArray($v, $ns);
            } else {
                if (!empty($ns)) {
                    $k = $ns. ':'. $k;
                }
                $result[] = [
                    '@name'=> $k,
                    '@children'=> (string)$v
                ];
            }
        }
        return $result;
    }

    public function toArray($xml, $namespace = null)
    {
        $obj = $this->toObject($xml);
        $namespaces = $obj->getDocNamespaces(true);
        $namespaces[null] = null;
        $children = []; 
        $props = []; 
        $name = strtolower((string)$obj->getName());
        if (!empty($namespace)) {
            $name = $namespace.':'.$name;
        }
        foreach( $namespaces as $ns=>$nsUrl ) { 
            if (!empty($ns) && is_null($namespace)) {
                $props['xmlns:'.$ns] = $nsUrl;
            }
            \PMVC\set(
                $props,
                $this->_processProps($obj, $ns)
            );
            \PMVC\set(
                $children,
                $this->_processChildren($obj, $ns)
            );
        }
        $result = new HashMapAll([ '@name' => $name ]);
        if (count($props)) {
            $result['@props'] = $props;
        }
        if (count($children)) {
            $result['@children'] = $children;
        }
        return $result;
    }
}
