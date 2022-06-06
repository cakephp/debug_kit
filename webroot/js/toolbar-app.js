export class Toolbar {

    constructor(options) {
        this.toolbar = options.toolbar;
        this.panelButtons = options.panelButtons;
        this.content = options.content;
        this.panelClose = options.panelClose;
        this.keyboardScope = options.keyboardScope;
        this.currentRequest = options.currentRequest;
        this.originalRequest = options.originalRequest;
        this.baseUrl = options.baseUrl;
        this.webroot = options.webroot;

        this._currentPanel = null;
        this._lastPanel = null;
        this._state = 0;
        this._localStorageAvailable = null;
        this.currentRequest = null;
        this.originalRequest = null;
        this.ajaxRequests = [];
        this.states = [
            'collapse',
            'toolbar'
        ];
    }

    initialize() {
        this.windowOrigin();
        this.mouseListener();
        this.keyboardListener();
        this.loadState();
        this.cacheClearListener();

        let self = this;
        window.addEventListener( 'message', function( event ) {
            self.onMessage( event );
        }, false );
    }

    // ========== STATE ==========

    toggle() {
        let state = this.nextState();
        this.updateButtons( state );
        this.updateToolbarState( state );
        window.parent.postMessage( state, window.location.origin );
    }

    state() {
        return this.states[this._state];
    }

    nextState() {
        this._state++;
        if( this._state === this.states.length ) {
            this._state = 0;
        }
        this.saveState();
        return this.state();
    }

    localStorageAvailable() {
        if( this._localStorageAvailable === null ) {
            if( !window.localStorage ) {
                this._localStorageAvailable = false;
            } else {
                try {
                    window.localStorage.setItem( 'testKey', '1' );
                    window.localStorage.removeItem( 'testKey' );
                    this._localStorageAvailable = true;
                } catch( error ) {
                    this._localStorageAvailable = false;
                }
            }
        }

        return this._localStorageAvailable;
    }

    saveState() {
        if( !this.localStorageAvailable() ) {
            return;
        }
        window.localStorage.setItem( 'toolbar_state', this._state );
    }

    loadState() {
        if( !this.localStorageAvailable() ) {
            return;
        }
        let old = window.localStorage.getItem( 'toolbar_state' );
        if( !old ) {
            old = '0';
        }
        if( old === '0' ) {
            return this.hideContent();
        }
        if( old === '1' ) {
            return this.toggle();
        }
    }

    updateToolbarState( state ) {
        if( state === 'toolbar' ) {
            this.toolbar.addClass( 'open' );
        }
        if( state === 'collapse' ) {
            this.toolbar.removeClass( 'open' );
        }
    }

    updateButtons( state ) {
        if( state === 'toolbar' ) {
            this.panelButtons.show();
        }
        if( state === 'collapse' ) {
            this.panelButtons.hide();
        }
    }

    isExpanded() {
        return this.content.hasClass( 'enabled' );
    }

    hideContent() {
        // slide out - css animation
        this.content.removeClass( 'enabled' );
        // remove the active state on buttons
        this.currentPanelButton().removeClass( 'panel-active' );
        let _this = this;

        // Hardcode timer as one does.
        setTimeout( function() {
            _this._currentPanel = null;
            window.parent.postMessage( _this.state(), window.location.origin );
        }, 250 );
    }

    // ========== PANEL  ==========

    loadPanel( id ) {
        if( id === undefined ) {
            return;
        }

        let url = this.baseUrl + 'debug-kit/panels/view/' + id;
        let contentArea = this.content.find( '#panel-content' );
        let _this = this;
        let timer;
        let loader = document.getElementById('loader');

        if( this._lastPanel !== id ) {
            timer = setTimeout( function() {
                loader.classList.add( 'loading' );
            }, 500 );
        }

        this._currentPanel = id;
        this._lastPanel = id;

        window.parent.postMessage( 'expand', window.location.origin );

        $.get( url, function( response ) {
            clearTimeout( timer );
            loader.classList.remove( 'loading' );

            // Slide panel into place - css transitions.
            _this.content.addClass( 'enabled' );
            contentArea.html( response );
            _this.bindVariableSort();
            _this.bindDebugBlock();
            _this.bindNeatArray();
        } );
    }

    currentPanel() {
        return this._currentPanel;
    }

    currentPanelButton() {
        return this.toolbar.find( '[data-id="' + this.currentPanel() + '"]' );
    }

    // ========== PANEL LISTENERS ==========

    bindVariableSort() {
        let sortButton = this.content.find( '.neat-array-sort' );
        let _this = this;
        sortButton.click( function() {
            if( !this.checked ) {
                document.cookie = 'debugKit_sort=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=' + _this.webroot;
            } else {
                document.cookie = 'debugKit_sort=1; path=' + _this.webroot;
            }
            _this.loadPanel( _this.currentPanel() );
        } );
    }

    bindDebugBlock() {
        if( window.__cakeDebugBlockInit ) {
            window.__cakeDebugBlockInit();
        }
    }

    bindNeatArray() {
        let lists = this.content.find( '.depth-0' );
        lists.find( 'ul' ).hide()
            .parent().addClass( 'expandable collapsed' );

        lists.on( 'click', 'li', function( event ) {
            event.stopPropagation();
            let el = $( this );
            el.children( 'ul' ).toggle();
            el.toggleClass( 'expanded' )
                .toggleClass( 'collapsed' );
        } );
    }

    // ========== REQUESTS PANEL ==========

    onMessage( event ) {
        if( typeof ( event.data ) === 'string' && event.data.indexOf( 'ajax-completed$$' ) === 0 ) {
            this.onRequest( JSON.parse( event.data.split( '$$' )[1] ) );
        }
    }

    onRequest( request ) {
        this.ajaxRequests.push( request );
        $('.panel-summary:contains(xhr)').text(this.ajaxRequests.length + ' xhr');
    }

    // ========== TOOLBAR SCROLL ==========

    scroll( direction ) {
        let scrollValue = 300;
        let operator = direction === 'left' ? '-=' : '+=';
        let buttons = this.toolbar.find( '.toolbar-inner li' );
        let cakeButton = this.toolbar.find( '#panel-button' );
        let firstButton = buttons.first();
        let lastButton = buttons.last();

        // If the toolbar is scrolled to the left, don't go farther.
        if( direction === 'right' && firstButton.position().left === 0 ) {
            return;
        }

        let buttonWidth = lastButton.width();
        // If the last button's right side is left of the cake button, don't scroll further.
        if( direction === 'left' && lastButton.offset().left + buttonWidth < cakeButton.offset().left ) {
            return;
        }
        let css = { left: operator + scrollValue };
        $( '.toolbar-inner li', this.button ).animate( css );
    }

    // ========== GENERAL LISTENERS ==========

    keyboardListener() {
        let _this = this;
        this.keyboardScope.keydown( function( event ) {
            // Check for Esc key
            if( event.keyCode === 27 ) {
                // Close active panel
                if( _this.isExpanded() ) {
                    return _this.hideContent();
                }
                // Collapse the toolbar
                if( _this.state() === 'toolbar' ) {
                    return _this.toggle();
                }
            }
            // Check for left arrow
            if( event.keyCode === 37 && _this.isExpanded() ) {
                _this.panelButtons.removeClass( 'panel-active' );
                let prevPanel = _this.currentPanelButton().prev();
                if( prevPanel.hasClass( 'panel' ) ) {
                    prevPanel.addClass( 'panel-active' );
                    return _this.loadPanel( prevPanel.data( 'id' ) );
                }
            }
            // Check for right arrow
            if( event.keyCode === 39 && _this.isExpanded() ) {
                _this.panelButtons.removeClass( 'panel-active' );
                let nextPanel = _this.currentPanelButton().next();
                if( nextPanel.hasClass( 'panel' ) ) {
                    nextPanel.addClass( 'panel-active' );
                    return _this.loadPanel( nextPanel.data( 'id' ) );
                }
            }
        } );
    }

    mouseListener() {
        let _this = this;
        this.toolbar.find( '.panel-button-left' ).on( 'click', function() {
            _this.scroll( 'left' );
            return false;
        } );
        this.toolbar.find( '.panel-button-right' ).on( 'click', function() {
            _this.scroll( 'right' );
            return false;
        } );

        this.panelButtons.on( 'click', function( e ) {
            _this.panelButtons.removeClass( 'panel-active' );
            e.preventDefault();
            e.stopPropagation();
            let id = this.getAttribute( 'data-id' );
            let samePanel = _this.currentPanel() === id;

            if( _this.isExpanded() && samePanel ) {
                _this.hideContent();
            }
            if( samePanel ) {
                return false;
            }
            this.classList.add( 'panel-active' );
            _this.loadPanel( id );
        } );

        this.toolbar.on( 'click', function() {
            _this.toggle();
            return false;
        } );

        this.panelClose.on( 'click', function() {
            _this.hideContent();
            return false;
        } );
    }

    cacheClearListener() {
        $(document).on('click', '.js-debugkit-clear-cache', function(e) {
            e.preventDefault();
            let el = $(this);
            let baseUrl = el.attr('data-url');
            let csrfToken = el.attr('data-csrf-token');
            let name = el.data('name');
            let messageEl = el.parents('table').siblings('.inline-message');

            function showMessage(elem, text) {
                elem.show().html( text );
                setTimeout(function(){
                    elem.fadeOut();
                }, 2000);
            }

            $.ajax({
                headers: {'X-CSRF-TOKEN': csrfToken},
                url: baseUrl,
                data: {name: name},
                dataType: 'json',
                type: 'POST',
                success: function(data) {
                    showMessage( messageEl, name + ' ' + data.data.message );
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    showMessage( messageEl, 'There was an error clearing ' + name +
                        '<br>' + errorThrown );
                }
            });
        });
    }

    // ========== MISC ==========

    windowOrigin() {
        // IE does not have access to window.location.origin
        if( !window.location.origin ) {
            window.location.origin = window.location.protocol + '//' +
                window.location.hostname +
                ( window.location.port ? ':' + window.location.port : '' );
        }
    }

}
