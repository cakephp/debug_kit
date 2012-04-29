<?php
/**
 * Show (x)html validation errors in a tidy panel
 *
 * PHP version 4 and 5
 *
 * Copyright (c) 2009, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2009, Andy Dawson
 * @link          www.ad7six.com
 * @package       debug_kit
 * @subpackage    debug_kit.vendors
 * @since         v 1.0 (22-Jun-2009)
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * TidyPanel class
 *
 * @uses          DebugPanel
 * @package       debug_kit
 * @subpackage    debug_kit.panel
 */
class TidyPanel extends DebugPanel {

/**
 * title property
 *
 * @var string 'Tidy'
 * @access public
 */
	public $title = 'Tidy';

/**
 * elementName property
 *
 * @var string 'tidy'
 * @access public
 */
	public $elementName = 'tidy_panel';

/**
 * startup method
 *
 * @param mixed $controller
 * @return void
 * @access public
 */
	public function startup(Controller $controller) {
	$isHtml = (
			!isset($controller->params['ext']) ||
			(isset($controller->params['ext']) && $controller->params['ext'] == 'html')
		);
		if ($controller->Toolbar->RequestHandler->isAjax() || !$isHtml) {
			$this->enabled = false;
			unset($controller->Toolbar->panels['Tidy']);
		}
		$controller->helpers[] = 'DebugKit.Tidy';
	}
}