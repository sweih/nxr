<?php
//
// phpOpenTracker - The Website Traffic and Visitor Analysis Solution
//
// Copyright 2000 - 2005 Sebastian Bergmann. All rights reserved.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//   http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//

/**
 * phpOpenTracker Logging Engine - Search Engines
 *
 * @author      Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright   Copyright &copy; 2000-2005 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license     http://www.apache.org/licenses/LICENSE-2.0 The Apache License, Version 2.0
 * @category    phpOpenTracker
 * @package     SearchEngines
 * @since       phpOpenTracker-Search_Engines 1.0.0
 */
class phpOpenTracker_LoggingEngine_Plugin_search_engines extends phpOpenTracker_LoggingEngine_Plugin {
  /**
  * Constructor.
  *
  * @access public
  */
  function phpOpenTracker_LoggingEngine_Plugin_search_engines($parameters) {
    parent::phpOpenTracker_LoggingEngine_Plugin($parameters);

    $this->config['plugins']['search_engines']['table'] = isset($this->config['plugins']['search_engines']['table']) ? $this->config['plugins']['search_engines']['table'] : 'pot_search_engines';
  }

  /**
  * @return array
  * @access public
  */
  function post() {
    $referer = $this->container['referer_orig'];

    if ($this->container['first_request'] &&
        !empty($referer)) {
      if (!$ignoreRules = @file(POT_CONFIG_PATH . 'search_engines.ignore.ini')) {
        return phpOpenTracker::handleError(
          sprintf(
            'Cannot open "%s".',
            POT_CONFIG_PATH . 'search_engines.ignore.ini'
          ),
          E_USER_ERROR
        );
      }

      if (!$matchRules = @file(POT_CONFIG_PATH . 'search_engines.match.ini')) {
        return phpOpenTracker::handleError(
          sprintf(
            'Cannot open "%s".',
            POT_CONFIG_PATH . 'search_engines.match.ini'
          ),
          E_USER_ERROR
        );
      }

      $ignore = false;

      foreach ($ignoreRules as $ignoreRule) {
        if (preg_match(trim($ignoreRule), $referer)) {
          $ignore = true;
          break;
        }
      }

      if (!$ignore) {
        foreach ($matchRules as $matchRule) {
          if (preg_match(trim($matchRule), $referer, $tmp)) {
            $keywords = $tmp[1];
          }
        }

        $searchEngineName = phpOpenTracker_Parser::match(
          $referer,
          phpOpenTracker_Parser::readRules(
            POT_CONFIG_PATH . 'search_engines.group.ini'
          )
        );
      }

      if (isset($keywords) &&
          isset($searchEngineName) &&
          $searchEngineName != $referer) {
        $this->db->query(
          sprintf(
            "INSERT INTO %s
                         (accesslog_id,
                          search_engine, keywords)
                  VALUES ('%d',
                          '%s', '%s')",

            $this->config['plugins']['search_engines']['table'],
            $this->container['accesslog_id'],
            $this->db->prepareString($searchEngineName),
            $this->db->prepareString($keywords)
          )
        );

        $this->container['referer']      = '';
        $this->container['referer_orig'] = '';
        $this->container['referer_id']   = 0;
      }
    }

    return array();
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
