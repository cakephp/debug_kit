<?php

if (CakePlugin::loaded('Croogo')) {
	Croogo::hookComponent('*', 'DebugKit.Toolbar');
}
