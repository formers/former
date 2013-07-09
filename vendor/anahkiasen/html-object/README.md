HTMLObject
===========

HTMLObject is a set of classes to create and manipulate HTML objects abstractions.

## Static calls to the classes

```php
echo Element::p('text')->class('foobar');
// <p class="foobar">text</p>
```

```php
$list = List::ul(array('foo', 'bar'));

$link = Link::create('#', 'Someone');
$list->getChild(0)->addClass('active')->setValue('by '.$link);
// <ul>
//   <li class="active">foo</li>
//   <li>by <a href="#">Someone</a></li>
// </ul>
```

```php
echo Link::create('#foo', 'link')->class('btn btn-success')->blank();
// <a href="#foo" class="btn btn-primary" target="_blank">link</a>
```

## Extending the core classes

The core classes are meant to be extended and used to create complex patterns. All classes implement tree-crawling properties such as the following :

```php
$element = Element::figure();

$element->nest('content') // <figure>content</figure>

$element->nest('p', 'content') // <figure><p>content</p></figure>

$image = Image::create('img.jpg')->alt('foo'); // <img src="img.jpg" alt="foo" />
$element->setChild($image, 'thumb');

$element->getChild('thumb') // HtmlObject\Image
$element->nest(array(
  'caption' => Element::figcaption()->nest(array(
    'text' => Element::p('foobar'),
  )),
));

$element->getChild('caption.text')->getValue() // foobar
// OR
$element->captionText->getValue() // foobar
$element->captionText->getParent(0) // figure->caption
$element->captionText->getParent(1) // figure

$element->wrap('div') // <div><figure>...</figure></div>
$element->wrapValue('div') // <figure><div>...</div></figure>
```

You can see examples implementations in the [examples](examples) folder.

### Properties injection

If your class use properties that are at meant to be added to the final array of attributes, you can inject them using the `injectProperties` method. Say you have a `Link` class that has an `url` property, you can overwrite the method like this, and the `$this->url` will get added in the `href` attribute :

```php
protected function injectProperties()
{
  return array(
    'href' => $this->url,
  );
}
```

Or if the property bears the property's name you can simply add it to the array of automatically injected properties :

```
protected $injectedProperties = array('href', 'title');

// Will be added as href="#foo"
protected $href = '#foo';

// Will be added as title="title"
protected $title = 'title';
```

### Altering a precreated tree

HtmlObject allows to use the `open` and `close` to open tags but when your tag has children you sometimes want to open the tree at a particular point to inject data at runtime, you can do it like this :

```php
$mediaObject = Element::div([
  'title' => Element::h2('John Doe'),
  'body'  => Element::div(),
]);

echo $mediaObject->openOn('body').'My name is John Doe'.$mediaObject->close();
```

```html
<div>
  <h2>John Doe</h2>
  <div>My name is John Doe</div>
</div>
```

### Configuration

You can change whether to follow xHMTL or HTML5 specification by doing the following :

```php
Tag::$config['doctype'] = '{xhtml|html}';
```
