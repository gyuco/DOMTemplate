<?php

namespace DOMTemplate;

/* class DOMTemplateRepeaterArray : allow repetition over multiple nodes simultaneously
   ====================================================================================================================== */
//this is just a wrapper to handle that `repeat` might be executed on more than one element simultaneously;
//for example, if you are producing a list that occurs more than once on a page (e.g. page number links in a forum)
class DOMTemplateRepeaterArray {
	private $nodes;
	
	public function __construct ($DOMNodeList, $namespaces=array ()) {
		//convert the XPath query result into extended `DOMTemplateNode`s (`DOMTemplateRepeater`) so that you can
		//modify the HTML with the same usual DOMTemplate API
		foreach ($DOMNodeList as $DOMNode) $this->nodes[] = new DOMTemplateRepeater ($DOMNode, $namespaces);
	}
	
	public function next () {
		//cannot use `foreach` here because you shouldn't modify the nodes whilst iterating them
		for ($i=0; $i<count ($this->nodes); $i++) $this->nodes[$i]->next (); return $this;
	}
	
	//refer to `DOMTemplateNode->set`
	public function set ($queries, $asHTML=false) {
		foreach ($this->nodes as $node) $node->set ($queries, $asHTML); return $this;
	}
	
	//refer to `DOMTemplateNode->setValue`
	public function setValue ($query, $value, $asHTML=false) {
		foreach ($this->nodes as $node) $node->setValue ($query, $value, $asHTML); return $this;
	}
	
	//refer to `DOMTemplateNode->addClass`
	public function addClass ($query, $new_class) {
		foreach ($this->nodes as $node) $node->addClass ($query, $new_class); return $this;
	}
	
	//refer to `DOMTemplateNode->remove`
	public function remove ($query) {
		foreach ($this->nodes as $node) $node->remove ($query); return $this;
	}
}