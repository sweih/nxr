<?php
//
// +---------------------------------------------------------------------+
// | phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
// +---------------------------------------------------------------------+
// | Copyright (c) 2000-2003 Sebastian Bergmann. All rights reserved.    |
// +---------------------------------------------------------------------+
// | This source file is subject to the phpOpenTracker Software License, |
// | Version 1.0, that is bundled with this package in the file LICENSE. |
// | If you did not receive a copy of this file, you may either read the |
// | license online at http://phpOpenTracker.de/license/1_0.txt, or send |
// | a note to license@phpOpenTracker.de, so we can mail you a copy.     |
// +---------------------------------------------------------------------+
//
// $Id: MyTemplate.php,v 1.1 2003/09/02 11:18:56 sven_weih Exp $
//

/**
* MyTemplate
*
* This is my template class. I like it. You don't have to :-)
*
* @author  Sebastian Bergmann <sb@phpOpenTracker.de>
* @version $Revision: 1.1 $
* @access  public
*/
class MyTemplate {
  /**
  * @var  string
  */
  var $template = '';

  /**
  * @var  array
  */
  var $keys = array();

  /**
  * @var  array
  */
  var $values = array();

  /**
  * Constructor
  *
  * @param  string  file
  * @access public
  */
  function MyTemplate($file = '') {
    $this->setFile($file);
  }

  /**
  * Set template file
  *
  * @param  string  file
  * @access public
  */
  function setFile($file) {
    if ($file != '' && file_exists($file)) {
      $this->template = implode('', @file($file));
      return true;
    } else {
      return false;
    }
  }

  /**
  * Assign template variable(s)
  *
  * @param  mixed   key(s)
  * @param  mixed   value(s)
  * @access public
  */
  function setVar($keys, $values) {
    if (is_array($keys) && is_array($values) && sizeof($keys) == sizeof($values)) {
      foreach ($keys as $key) {
        $this->keys[] = '{' . $key . '}';
      }

      $this->values = array_merge($this->values, $values);
    } else {
      $this->keys[]   = '{' . $keys . '}';
      $this->values[] = $values;
    }
  }

  /**
  * Parse template
  *
  * @return string  parsed template
  * @access public
  */
  function parse() {
    if (!empty($this->template)) {
      return str_replace($this->keys, $this->values, $this->template);
    } else {
      trigger_error(
        'No template file loaded or template is empty.',
        E_USER_WARNING
      );
    }
  }

  /**
  * Parse and print template
  *
  * @access public
  */
  function pParse() {
    echo $this->parse();
  }
}
?>
