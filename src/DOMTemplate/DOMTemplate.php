<?php

namespace DOMTemplate;

//DOM Templating classes v18 © copyright (cc-by) Kroc Camen 2012-2015
//you may do whatever you want with this code as long as you give credit
//documentation at <camendesign.com/dom_templating>

/*	Basic API:
	
	new DOMTemplate (source, [namespaces])
	
	(string)				to output the HTML / XML, cast the DOMTemplate object to a string,
						i.e. `echo $template;`
	query (query)				make an XPath query
	set (queries, [asHTML])			change HTML by specifying an array of ('XPath' => 'value')
	setValue (query, value, [asHTML])	change a single HTML value with an XPath query
	addClass (query, new_class)		add a class to an HTML element
	remove (query)				remove one or more HTML elements, attributes or classes
	repeat (query)				return one (or more) elements as sub-templates
		
		next ()				append the sub-template to the list and reset its content
*/

/* class DOMTemplate : the overall template controller
   ====================================================================================================================== */
class DOMTemplate extends DOMTemplateNode {
	private $DOMDocument;			//internal reference to the PHP DOMDocument for the template's XML
	
	//what type of data are we processing?
	protected $type = self::HTML;
	const HTML = 0;
	const XML  = 1;
	
	/* new DOMTemplate : instantiation
	   -------------------------------------------------------------------------------------------------------------- */
	public function __construct (
		$source,			//a string of the HTML or XML to form the template
		$namespaces=array ()		//an array of XML namespaces if your document uses them,
						//in the format of `'namespace' => 'namespace URI'`
	) {
		//detect the content type; HTML or XML. HTML will need filtering during input and output
		//does this source have an XML prolog?
		$this->type = substr_compare ($source, '<?xml', 0, 4, true) === 0 ? self::XML : self::HTML;
		
		//load the template file to work with,
		//it _must_ have only one root (wrapping) element; e.g. `<html>`
		$this->DOMDocument = new \DOMDocument ();
		if (!$this->DOMDocument->loadXML (
			//if the source is HTML add an XML prolog to avoid mangling unicode characters,
			//see <php.net/manual/en/domdocument.loadxml.php#94291>. also convert it to XML for PHP DOM use
			$this->type == self::HTML
			? "<?xml version=\"1.0\" encoding=\"utf-8\"?>".self::toXML ($source)
			: $source,
			@LIBXML_COMPACT || @LIBXML_NONET
		)) trigger_error (
			"Source is invalid XML", E_USER_ERROR
		);
		//set the root node for all XPath searching
		//(handled all internally by `DOMTemplateNode`)
		parent::__construct ($this->DOMDocument->documentElement, $namespaces);
	}
	
	/* output the source code (cast the object to a string, i.e. `echo $template;`)
	   -------------------------------------------------------------------------------------------------------------- */
	public function __toString () {
		//if the input was HTML, remove the XML prolog on output
		return $this->type == self::HTML
		?	//we defer to DOMTemplateNode which returns the HTML for any node,
			//the top-level template only needs to consider the prolog
			preg_replace ('/^<\?xml[^<]*>\n/', '', parent::__toString ())
		:	parent::__toString ();
	}
    
    public function appendHeadElement($elementName, $attributes, $value = '') {
        $query_head = '/html/head';
        $this->appendElement($query_head, $elementName, $attributes, $value);
    }
    public function appendElement($query, $elementName, $attributes, $value = '') {
        $query_e = $this->query($query);
        $e = $query_e->item(0);
        $node = $this->DOMDocument->createElement($elementName, $value);
        foreach ($attributes as $key=>$value) {
            $node->setAttribute($key, $value);
        }
        $e->appendChild($node);
    }
    public function appendHtml($query, $html) {
        $query_e = $this->query($query);
        $e = $query_e->item(0);

        $f = $this->DOMDocument->createDocumentFragment();
        $f->appendXML($html);

        $e->appendChild($f);
    }
}