import Start from './modules/Start';
import Keyboard from './modules/Keyboard';

document.addEventListener( 'DOMContentLoaded', function() {
    'use strict';

    let toolbar = Start.init();
    Keyboard.init(toolbar);
});
