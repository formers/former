<?php
namespace Former\TestCases;

ini_set('memory_limit', '400M');
date_default_timezone_set('UTC');

use Mockery;
use DOMNode;
use DOMDocument;
use DOMNodeList;
use PHPUnit\Framework\Exception;
use PHPUnit\Util\Xml;

/**
 * Base testing class
 */
abstract class FormerTests extends ContainerTestCase
{
	/**
	 * Setup the app for testing
	 */
	public function setUp(): void
	{
		parent::setUp();

		// Reset some parameters
		$this->resetLabels();
		$this->former->framework('TwitterBootstrap');
		$this->former->horizontal_open()->__toString();
	}

	/**
	 * Tear down the tests
	 *
	 * @return void
	 */
	public function tearDown(): void
	{
		$this->former->closeGroup();
		$this->former->close();
		Mockery::close();
	}

	/**
	 * Reset registered labels
	 *
	 * @return void
	 */
	public function resetLabels()
	{
		$this->former->labels = array();
		$this->former->ids    = array();
		$this->former->names  = array();
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// DUMMIES /////////////////////////////
	////////////////////////////////////////////////////////////////////

	protected $checkables = array(
		'Foo' => array(
			'data-foo' => 'bar',
			'value'    => 'bar',
			'name'     => 'foo',
		),
		'Bar' => array(
			'data-foo' => 'bar',
			'value'    => 'bar',
			'name'     => 'foo',
			'id'       => 'bar',
		),
	);

	protected $radioCheckables = array(
		'Foo' => array(
			'data-foo' => 'bar',
			'value'    => 'foo',
			'name'     => 'foo',
		),
		'Bar' => array(
			'data-foo' => 'bar',
			'value'    => 'bar',
			'name'     => 'foo',
			'id'       => 'bar',
		),
	);

	protected $testAttributes = array(
		'class'    => 'foo',
		'data-foo' => 'bar',
	);

	////////////////////////////////////////////////////////////////////
	///////////////////////////// MATCHERS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Match a field
	 *
	 * @param  array  $attributes
	 * @param  string $type
	 * @param  string $name
	 *
	 * @return array
	 */
	protected function matchField($attributes = array(), $type = 'text', $name = 'foo')
	{
		$attributes = array_merge($attributes, array('type' => $type, 'name' => $name));
		if ($type == 'hidden') {
			return array('tag' => 'input', 'attributes' => $attributes);
		}

		return array(
			'tag'        => 'input',
			'id'         => $name,
			'attributes' => $attributes,
		);
	}

	/**
	 * Match a label
	 *
	 * @param  string  $name
	 * @param  string  $field
	 * @param  boolean $required
	 *
	 * @return array
	 */
	protected function matchLabel($name = 'foo', $field = 'foo', $required = false)
	{
		$text = str_replace('[]', null, $name);
		if ($required) {
			$text .= '*';
		}

		return array(
			'tag'        => 'label',
			'content'    => $text,
			'attributes' => array(
				'for'   => $field,
				'class' => 'control-label',
			),
		);
	}

	/**
	 * Match a control group
	 *
	 * @return array
	 */
	protected function matchControlGroup()
	{
		return array(
			'tag'        => 'div',
			'attributes' => array(
				'class' => 'control-group',
			),
			'child'      => array(
				'tag'        => 'div',
				'attributes' => array('class' => 'controls'),
			),
		);
	}

	/**
	 * Match a button
	 *
	 * @param  string $class
	 * @param  string $text
	 * @param  array  $attributes
	 *
	 * @return array
	 */
	protected function matchButton($class, $text, $attributes = array())
	{
		$matcher = array(
			'tag'        => 'button',
			'content'    => $text,
			'attributes' => array(
				'class' => $class,
			),
		);

		// Supplementary attributes
		if ($attributes) {
			$matcher['attributes'] = array_merge($matcher['attributes'], $attributes);
		}

		return $matcher;
	}

	/**
	 * Match an input-type button
	 *
	 * @param  string $class
	 * @param  string $text
	 * @param  string $type
	 *
	 * @return array
	 */
	protected function matchInputButton($class, $text, $type = 'submit')
	{
		return array(
			'tag'        => 'input',
			'attributes' => array(
				'type'  => $type,
				'value' => $text,
				'class' => $class,
			),
		);
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// HELPERS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Build a list of HTML attributes from an array
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function attributes($attributes)
	{
		$html = array();

		foreach ((array) $attributes as $key => $value) {
			// For numeric keys, we will assume that the key and the value are the
			// same, as this will convert HTML attributes such as "required" that
			// may be specified as required="required", etc.
			if (is_numeric($key)) {
				$key = $value;
			}

			if (!is_null($value)) {
				$html[] = $key.'="'.$value.'"';
			} else {
				$html[] = $key;
			}
		}

		return (count($html) > 0) ? ' '.implode(' ', $html) : '';
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// ASSERTIONS //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Asserts that the input is a control group
	 *
	 * @param  string $input
	 *
	 * @return boolean
	 */
	protected function assertControlGroup($input)
	{
		$this->assertLabel($input);
		$this->assertHTML($this->matchControlGroup(), $input);
	}

	/**
	 * Asserts that the input is a label
	 *
	 * @param  string $input
	 *
	 * @return boolean
	 */
	protected function assertLabel($input, $name = 'foo', $required = false)
	{
		$this->assertHTML($this->matchLabel(ucfirst($name), $name, $required), $input);
	}

	/**
	 * Matches a Control Group
	 *
	 * @param  string $input
	 * @param  string $label
	 *
	 * @return boolean
	 */
	protected function controlGroup(
		$input = '<input type="text" name="foo" id="foo">',
		$label = '<label for="foo" class="control-label">Foo</label>'
	) {
		return '<div class="control-group">'.$label.'<div class="controls">'.$input.'</div></div>';
	}

	/**
	 * Matches a Form Group
	 *
	 * @param  string $input
	 * @param  string $label
	 *
	 * @return boolean
	 */
	protected function formGroup(
		$input = '<input type="text" name="foo" id="foo">',
		$label = '<label for="foo" class="control-label col-lg-2 col-sm-4">Foo</label>'
	) {
		return '<div class="form-group">'.$label.'<div class="col-lg-10 col-sm-8">'.$input.'</div></div>';
	}

	/**
	 * Matches a required Control Group
	 *
	 * @param  string $input
	 * @param  string $label
	 *
	 * @return boolean
	 */
	protected function controlGroupRequired($input, $label = '<label for="foo" class="control-label">Foo</label>')
	{
		return '<div class="control-group required">'.$label.'<div class="controls">'.$input.'</div></div>';
	}

	/**
	 * Assert that a piece of HTML matches an array
	 *
	 * @param  array  $matcher
	 * @param  string $input
	 *
	 * @return boolean
	 */
	public function assertHTML($matcher, $input)
	{
		$this->assertTag(
			$matcher,
			$input,
			"Failed asserting that the HTML matches the provided input :\n\t"
			.$input."\n\t"
			.json_encode($matcher));
	}

    /**
     * Assert that a piece of HTML matches an array
     *
     * @param  array  $matcher
     * @param  string $input
     *
     * @return boolean
     */
    public function assertNotHTML($matcher, $input)
    {
        $this->assertNotTag(
            $matcher,
            $input,
            "Failed asserting that the HTML does NOT match the provided input :\n\t"
            .$input."\n\t"
            .json_encode($matcher));
    }

    ////////////////////////////////////////////////////////////////////
    //////////////////////////// PHPUNIT LEGACY ////////////////////////
    ////////////////////////////////////////////////////////////////////
    // Following code is legacy code from phpunit to support assertTag and assertNotTag.


    /**
     * @param array   $matcher
     * @param string  $actual
     * @param string  $message
     * @param boolean $ishtml
     */
    public static function assertTag($matcher, $actual, $message = '', $ishtml = true)
    {
        $dom     = Xml::load($actual, $ishtml);
        $tags    = self::findNodes($dom, $matcher, $ishtml);
        $matched = count($tags) > 0 && $tags[0] instanceof DOMNode;

        self::assertTrue($matched, $message);
    }

    /**
     * @param array   $matcher
     * @param string  $actual
     * @param string  $message
     * @param boolean $ishtml
     */
    public static function assertNotTag($matcher, $actual, $message = '', $ishtml = true)
    {
        $dom     = Xml::load($actual, $ishtml);
        $tags    = self::findNodes($dom, $matcher, $ishtml);
        $matched = $tags !== false && count($tags) > 0 && $tags[0] instanceof DOMNode;

        self::assertFalse($matched, $message);
    }

    /**
     * Validate list of keys in the associative array.
     *
     * @param array $hash
     * @param array $validKeys
     *
     * @return array
     *
     * @throws PHPUnit_Framework_Exception
     */
    public static function assertValidKeys(array $hash, array $validKeys)
    {
        $valids = [];

        // Normalize validation keys so that we can use both indexed and
        // associative arrays.
        foreach ($validKeys as $key => $val) {
            is_int($key) ? $valids[$val] = null : $valids[$key] = $val;
        }

        $validKeys = array_keys($valids);

        // Check for invalid keys.
        foreach ($hash as $key => $value) {
            if (!in_array($key, $validKeys)) {
                $unknown[] = $key;
            }
        }

        if (!empty($unknown)) {
            throw new Exception(
                'Unknown key(s): ' . implode(', ', $unknown)
            );
        }

        // Add default values for any valid keys that are empty.
        foreach ($valids as $key => $value) {
            if (!isset($hash[$key])) {
                $hash[$key] = $value;
            }
        }

        return $hash;
    }

    /**
     * Parse out the options from the tag using DOM object tree.
     *
     * @param DOMDocument $dom
     * @param array       $options
     * @param bool        $isHtml
     *
     * @return array
     */
    public static function findNodes(DOMDocument $dom, array $options, $isHtml = true)
    {
        $valid = [
            'id',
            'class',
            'tag',
            'content',
            'attributes',
            'parent',
            'child',
            'ancestor',
            'descendant',
            'children',
            'adjacent-sibling',
        ];

        $filtered = [];
        $options  = self::assertValidKeys($options, $valid);

        // find the element by id
        if ($options['id']) {
            $options['attributes']['id'] = $options['id'];
        }

        if ($options['class']) {
            $options['attributes']['class'] = $options['class'];
        }

        $nodes = [];

        // find the element by a tag type
        if ($options['tag']) {
            if ($isHtml) {
                $elements = self::getElementsByCaseInsensitiveTagName(
                    $dom,
                    $options['tag']
                );
            } else {
                $elements = $dom->getElementsByTagName($options['tag']);
            }

            foreach ($elements as $element) {
                $nodes[] = $element;
            }

            if (empty($nodes)) {
                return false;
            }
        } // no tag selected, get them all
        else {
            $tags = [
                'a',
                'abbr',
                'acronym',
                'address',
                'area',
                'b',
                'base',
                'bdo',
                'big',
                'blockquote',
                'body',
                'br',
                'button',
                'caption',
                'cite',
                'code',
                'col',
                'colgroup',
                'dd',
                'del',
                'div',
                'dfn',
                'dl',
                'dt',
                'em',
                'fieldset',
                'form',
                'frame',
                'frameset',
                'h1',
                'h2',
                'h3',
                'h4',
                'h5',
                'h6',
                'head',
                'hr',
                'html',
                'i',
                'iframe',
                'img',
                'input',
                'ins',
                'kbd',
                'label',
                'legend',
                'li',
                'link',
                'map',
                'meta',
                'noframes',
                'noscript',
                'object',
                'ol',
                'optgroup',
                'option',
                'p',
                'param',
                'pre',
                'q',
                'samp',
                'script',
                'select',
                'small',
                'span',
                'strong',
                'style',
                'sub',
                'sup',
                'table',
                'tbody',
                'td',
                'textarea',
                'tfoot',
                'th',
                'thead',
                'title',
                'tr',
                'tt',
                'ul',
                'var',
                // HTML5
                'article',
                'aside',
                'audio',
                'bdi',
                'canvas',
                'command',
                'datalist',
                'details',
                'dialog',
                'embed',
                'figure',
                'figcaption',
                'footer',
                'header',
                'hgroup',
                'keygen',
                'mark',
                'meter',
                'nav',
                'output',
                'progress',
                'ruby',
                'rt',
                'rp',
                'track',
                'section',
                'source',
                'summary',
                'time',
                'video',
                'wbr',
            ];

            foreach ($tags as $tag) {
                if ($isHtml) {
                    $elements = self::getElementsByCaseInsensitiveTagName(
                        $dom,
                        $tag
                    );
                } else {
                    $elements = $dom->getElementsByTagName($tag);
                }

                foreach ($elements as $element) {
                    $nodes[] = $element;
                }
            }

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by attributes
        if ($options['attributes']) {
            foreach ($nodes as $node) {
                $invalid = false;

                foreach ($options['attributes'] as $name => $value) {
                    // match by regexp if like "regexp:/foo/i"
                    if (preg_match('/^regexp\s*:\s*(.*)/i', $value, $matches)) {
                        if (!preg_match($matches[1], $node->getAttribute($name))) {
                            $invalid = true;
                        }
                    } // class can match only a part
                    elseif ($name == 'class') {
                        // split to individual classes
                        $findClasses = explode(
                            ' ',
                            preg_replace("/\s+/", ' ', $value)
                        );

                        $allClasses = explode(
                            ' ',
                            preg_replace("/\s+/", ' ', $node->getAttribute($name))
                        );

                        // make sure each class given is in the actual node
                        foreach ($findClasses as $findClass) {
                            if (!in_array($findClass, $allClasses)) {
                                $invalid = true;
                            }
                        }
                    } // match by exact string
                    else {
                        if ($node->getAttribute($name) != $value) {
                            $invalid = true;
                        }
                    }
                }

                // if every attribute given matched
                if (!$invalid) {
                    $filtered[] = $node;
                }
            }

            $nodes    = $filtered;
            $filtered = [];

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by content
        if ($options['content'] !== null) {
            foreach ($nodes as $node) {
                $invalid = false;

                // match by regexp if like "regexp:/foo/i"
                if (preg_match('/^regexp\s*:\s*(.*)/i', $options['content'], $matches)) {
                    if (!preg_match($matches[1], self::getNodeText($node))) {
                        $invalid = true;
                    }
                } // match empty string
                elseif ($options['content'] === '') {
                    if (self::getNodeText($node) !== '') {
                        $invalid = true;
                    }
                } // match by exact string
                elseif (strstr(self::getNodeText($node), $options['content']) === false) {
                    $invalid = true;
                }

                if (!$invalid) {
                    $filtered[] = $node;
                }
            }

            $nodes    = $filtered;
            $filtered = [];

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by parent node
        if ($options['parent']) {
            $parentNodes = self::findNodes($dom, $options['parent'], $isHtml);
            $parentNode  = isset($parentNodes[0]) ? $parentNodes[0] : null;

            foreach ($nodes as $node) {
                if ($parentNode !== $node->parentNode) {
                    continue;
                }

                $filtered[] = $node;
            }

            $nodes    = $filtered;
            $filtered = [];

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by child node
        if ($options['child']) {
            $childNodes = self::findNodes($dom, $options['child'], $isHtml);
            $childNodes = !empty($childNodes) ? $childNodes : [];

            foreach ($nodes as $node) {
                foreach ($node->childNodes as $child) {
                    foreach ($childNodes as $childNode) {
                        if ($childNode === $child) {
                            $filtered[] = $node;
                        }
                    }
                }
            }

            $nodes    = $filtered;
            $filtered = [];

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by adjacent-sibling
        if ($options['adjacent-sibling']) {
            $adjacentSiblingNodes = self::findNodes($dom, $options['adjacent-sibling'], $isHtml);
            $adjacentSiblingNodes = !empty($adjacentSiblingNodes) ? $adjacentSiblingNodes : [];

            foreach ($nodes as $node) {
                $sibling = $node;

                while ($sibling = $sibling->nextSibling) {
                    if ($sibling->nodeType !== XML_ELEMENT_NODE) {
                        continue;
                    }

                    foreach ($adjacentSiblingNodes as $adjacentSiblingNode) {
                        if ($sibling === $adjacentSiblingNode) {
                            $filtered[] = $node;
                            break;
                        }
                    }

                    break;
                }
            }

            $nodes    = $filtered;
            $filtered = [];

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by ancestor
        if ($options['ancestor']) {
            $ancestorNodes = self::findNodes($dom, $options['ancestor'], $isHtml);
            $ancestorNode  = isset($ancestorNodes[0]) ? $ancestorNodes[0] : null;

            foreach ($nodes as $node) {
                $parent = $node->parentNode;

                while ($parent && $parent->nodeType != XML_HTML_DOCUMENT_NODE) {
                    if ($parent === $ancestorNode) {
                        $filtered[] = $node;
                    }

                    $parent = $parent->parentNode;
                }
            }

            $nodes    = $filtered;
            $filtered = [];

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by descendant
        if ($options['descendant']) {
            $descendantNodes = self::findNodes($dom, $options['descendant'], $isHtml);
            $descendantNodes = !empty($descendantNodes) ? $descendantNodes : [];

            foreach ($nodes as $node) {
                foreach (self::getDescendants($node) as $descendant) {
                    foreach ($descendantNodes as $descendantNode) {
                        if ($descendantNode === $descendant) {
                            $filtered[] = $node;
                        }
                    }
                }
            }

            $nodes    = $filtered;
            $filtered = [];

            if (empty($nodes)) {
                return false;
            }
        }

        // filter by children
        if ($options['children']) {
            $validChild   = ['count', 'greater_than', 'less_than', 'only'];
            $childOptions = self::assertValidKeys(
                $options['children'],
                $validChild
            );

            foreach ($nodes as $node) {
                $childNodes = $node->childNodes;

                foreach ($childNodes as $childNode) {
                    if ($childNode->nodeType !== XML_CDATA_SECTION_NODE &&
                        $childNode->nodeType !== XML_TEXT_NODE) {
                        $children[] = $childNode;
                    }
                }

                // we must have children to pass this filter
                if (!empty($children)) {
                    // exact count of children
                    if ($childOptions['count'] !== null) {
                        if (count($children) !== $childOptions['count']) {
                            break;
                        }
                    } // range count of children
                    elseif ($childOptions['less_than'] !== null &&
                            $childOptions['greater_than'] !== null) {
                        if (count($children) >= $childOptions['less_than'] ||
                            count($children) <= $childOptions['greater_than']) {
                            break;
                        }
                    } // less than a given count
                    elseif ($childOptions['less_than'] !== null) {
                        if (count($children) >= $childOptions['less_than']) {
                            break;
                        }
                    } // more than a given count
                    elseif ($childOptions['greater_than'] !== null) {
                        if (count($children) <= $childOptions['greater_than']) {
                            break;
                        }
                    }

                    // match each child against a specific tag
                    if ($childOptions['only']) {
                        $onlyNodes = self::findNodes(
                            $dom,
                            $childOptions['only'],
                            $isHtml
                        );

                        // try to match each child to one of the 'only' nodes
                        foreach ($children as $child) {
                            $matched = false;

                            foreach ($onlyNodes as $onlyNode) {
                                if ($onlyNode === $child) {
                                    $matched = true;
                                }
                            }

                            if (!$matched) {
                                break 2;
                            }
                        }
                    }

                    $filtered[] = $node;
                }
            }

            $nodes = $filtered;

            if (empty($nodes)) {
                return;
            }
        }

        // return the first node that matches all criteria
        return !empty($nodes) ? $nodes : [];
    }

    /**
     * Recursively get flat array of all descendants of this node.
     *
     * @param DOMNode $node
     *
     * @return array
     */
    protected static function getDescendants(DOMNode $node)
    {
        $allChildren = [];
        $childNodes  = $node->childNodes ? $node->childNodes : [];

        foreach ($childNodes as $child) {
            if ($child->nodeType === XML_CDATA_SECTION_NODE ||
                $child->nodeType === XML_TEXT_NODE) {
                continue;
            }

            $children    = self::getDescendants($child);
            $allChildren = array_merge($allChildren, $children, [$child]);
        }

        return isset($allChildren) ? $allChildren : [];
    }

    /**
     * Gets elements by case insensitive tagname.
     *
     * @param DOMDocument $dom
     * @param string      $tag
     *
     * @return DOMNodeList
     */
    protected static function getElementsByCaseInsensitiveTagName(DOMDocument $dom, $tag)
    {
        $elements = $dom->getElementsByTagName(strtolower($tag));

        if ($elements->length == 0) {
            $elements = $dom->getElementsByTagName(strtoupper($tag));
        }

        return $elements;
    }

    /**
     * Get the text value of this node's child text node.
     *
     * @param DOMNode $node
     *
     * @return string
     */
    protected static function getNodeText(DOMNode $node)
    {
        if (!$node->childNodes instanceof DOMNodeList) {
            return '';
        }

        $result = '';

        foreach ($node->childNodes as $childNode) {
            if ($childNode->nodeType === XML_TEXT_NODE ||
                $childNode->nodeType === XML_CDATA_SECTION_NODE) {
                $result .= trim($childNode->data) . ' ';
            } else {
                $result .= self::getNodeText($childNode);
            }
        }

        return str_replace('  ', ' ', $result);
    }
}
