<?php
	/*
	 * Short Description / title.
	 *
	 * Overview of what the file does. About a paragraph or two
	 *
	 * Copyright (c) 2010 Carl Sutton ( dogmatic69 )
	 *
	 * @filesource
	 * @copyright Copyright (c) 2010 Carl Sutton ( dogmatic69 )
	 * @link http://www.infinitas-cms.org
	 * @package {see_below}
	 * @subpackage {see_below}
	 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
	 * @since {check_current_milestone_in_lighthouse}
	 *
	 * @author {your_name}
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 */

	final class DebugKitEvents extends AppEvents {
		public function onAttachBehaviors(Event $Event) {
			if(!Configure::read('debug')) {
				return array();
			}

			if(is_subclass_of($Event->Handler, 'Model')) {
				$Event->Handler->Behaviors->attach('DebugKit.Timed');
			}
		}

		public function onRequireComponentsToLoad(Event $Event) {
			if(!Configure::read('debug')) {
				return array();
			}

			return 'DebugKit.Toolbar';
		}

		public function onRequireCssToLoad(Event $Event, $data = null) {
			if(!Configure::read('debug')) {
				return array();
			}

			return array(
				'DebugKit.debug_toolbar'
			);
		}

		public function onRequireJavascriptToLoad(Event $Event, $data = null) {
			if(!Configure::read('debug')) {
				return array();
			}

			return array(
				'DebugKit.js_debug_toolbar',
				'DebugKit.debug_kit'
			);
		}
	}
