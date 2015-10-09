<?php

namespace DOMTemplate;

/* class DOMTemplateRepeater : the business-end of `DOMTemplateNode->repeat`!
   ====================================================================================================================== */
class DOMTemplateRepeater extends DOMTemplateNode {
	private $refNode;		//the templated node will be added after this node
	private $template;		//a copy of the original node to work from each time
	
	protected $type;
	
	public function __construct ($DOMNode, $namespaces=array ()) {
		//we insert the templated item after the reference node,
		//which will always be the last item that was templated
		$this->refNode  = $DOMNode;
		//take a copy of the original node that we will use as a starting point each time we iterate
		$this->template = $DOMNode->cloneNode (true);
		//initialise the template with the current, original node
		parent::__construct ($DOMNode, $namespaces);
	}
	
	public function next () {
		//when we insert the newly templated item, use it as the reference node for the next item and so on.
		$this->refNode = ($this->refNode->parentNode->lastChild === $this->DOMNode)
			? $this->refNode->parentNode->appendChild ($this->DOMNode)
			//if there's some kind of HTML after the reference node, we can use that to insert our item
			//inbetween. this means that the list you are templating doesn't have to be wrapped in an element!
			: $this->refNode->parentNode->insertBefore ($this->DOMNode, $this->refNode->nextSibling)
		;
		//reset the template
		$this->DOMNode = $this->template->cloneNode (true);
		return $this;
	}
}