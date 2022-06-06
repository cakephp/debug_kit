import './jquery.js';
import {Toolbar} from './toolbar-app.js';

( function( $ ) {
    'use strict';

    let elem = document.getElementById( '__debug_kit_app' );
    if( elem ) {
        window.__debugKitId = elem.getAttribute( 'data-id' );
        window.__debugKitBaseUrl = elem.getAttribute( 'data-url' );
        window.__debugKitWebroot = elem.getAttribute( 'data-webroot' );
    }

    $( document ).ready( function() {
        window.debugKitToolbar = new Toolbar( {
            toolbar: $( '#toolbar' ),
            content: $( '#panel-content-container' ),
            panelButtons: $( '.panel' ),
            panelClose: $( '#panel-close' ),
            keyboardScope: $( document ),
            currentRequest: window.__debugKitId,
            originalRequest: window.__debugKitId,
            baseUrl: window.__debugKitBaseUrl,
            webroot: window.__debugKitWebroot,
        } );

        window.debugKitToolbar.initialize();
    } );
}( jQuery ) );
