<?php return array(

  // Whether labels should be automatically computed from name
  'automatic_label'   => true,

  // The default form type
  'default_form_type' => 'horizontal',

  // Whether Former should fetch errors from Session
  'fetch_errors'      => true,

  // The framework to be used by Former
  'framework'         => 'TwitterBootstrap',

  // Whether Former should try to apply Validator rules as attributes
  'live_validation'   => true,

  // Whether checkboxes should always be present in the POST data,
  // no matter if you checked them or not
  'push_checkboxes'   => false,

  // The value a checkbox will have in the POST array if unchecked
  'unchecked_value'   => '',

  // The class to be added to required fields
  'required_class'    => 'required',

  // A facultative text to append to the labels of required fields
  'required_text'     => '<sup>*</sup>',

  // Where Former should look for translations
  'translate_from'    => 'validation.attributes',
);
