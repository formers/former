<?php return array(

	// Zurb Foundation 4 framework markup
	////////////////////////////////////////////////////////////////////

	// Map Former-supported viewports to Foundation 4 equivalents
	// Foundation 4 also has an experimental "medium" breakpoint
	// explained at http://foundation.zurb.com/docs/components/grid.html
	'viewports' => array(
		'large'  => 'large',
		'medium' => null,
		'small'  => 'small',
		'mini'   => null,
	),

	// Width of labels for horizontal forms expressed as viewport => grid columns
	'labelWidths' => array(
		'small' => 3,
	),

	// Classes to be applied to wrapped labels in horizontal forms
	'wrappedLabelClasses' => array('right','inline'),

	// HTML markup and classes used by Foundation 4 for icons
	'icon' => array(
		'tag'    => 'i',
		'set'    => 'general',
		'prefix' => 'foundicon',
	),

    // CSS for inline validation errors
    'error_classes' => array('class' => 'alert-box radius warning')

);
