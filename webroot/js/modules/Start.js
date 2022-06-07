import {Toolbar} from './Toolbar';
import Helper from "./Helper";

export default ( function() {
    'use strict';

    let init = function() {
        let elem = document.getElementById('__debug_kit_app');
        let __debugKitId, __debugKitBaseUrl, __debugKitWebroot;
        if (elem) {
            __debugKitId = elem.getAttribute('data-id');
            __debugKitBaseUrl = elem.getAttribute('data-url');
            __debugKitWebroot = elem.getAttribute('data-webroot');
        }

        return new Toolbar({
            toolbar: document.getElementsByClassName('.js-toolbar')[0],
            container: document.getElementsByClassName('.js-panel-content-container')[0],
            panelButtons: document.getElementsByClassName('.js-panel-button'),
            closeBtn: document.getElementsByClassName('.js-panel-close')[0],
            keyboardScope : document,
            currentRequest: __debugKitId,
            originalRequest: __debugKitId,
            baseUrl: __debugKitBaseUrl,
            webroot: __debugKitWebroot,
            isLocalStorageAvailable: Helper.isLocalStorageAvailable()
        });

    };

    return {
        init: init
    };

}() );
