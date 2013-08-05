<?php
class DummyButton
{
  public function __construct($text)
  {
    $this->text = $text;
  }

  public function __toString()
  {
    return '<button type="button" class="btn">' .$this->text. '</button>';
  }
}
