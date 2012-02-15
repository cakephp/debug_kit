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

	final class DebugKitEvents extends AppEvents{
		public function onAttachBehaviors($event){
			if(is_subclass_of($event->Handler, 'Model')){
				$event->Handler->Behaviors->attach('DebugKit.Timed');
			}
		}

		public function onRequireComponentsToLoad(){
			return 'DebugKit.Toolbar';
		}

		public function onRequireCssToLoad($event, $data = null) {
			return array(
				'/debug_kit/css/debug_toolbar'
			);
		}

		public function onRequireJavascriptToLoad($event, $data = null) {
			return array(
				'/debug_kit/js/js_debug_toolbar'
			);
		}

		public function onSetupConfig(){
			return Configure::load('DebugKit.config');
		}
	}
