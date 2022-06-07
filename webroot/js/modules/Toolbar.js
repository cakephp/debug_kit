export class Toolbar {

    constructor(options) {
        this.$toolbar = options.toolbar;
        this.$container = options.container;
        this.$panelButtons = options.panelButtons;
        this.$closeBtn = options.closeBtn;
        this.keyboardScope = options.keyboardScope;
        this.currentRequest = options.currentRequest;
        this.originalRequest = options.originalRequest;
        this.baseUrl = options.baseUrl;
        this.webroot = options.webroot;

        this.isLocalStorageAvailable = options.isLocalStorageAvailable;

        this.currentState = 0;
        this.states = [
            'collapse',
            'toolbar'
        ];
    }

    toggle() {
        let state = this.nextState();
        this.updateButtons(state);
        this.updateToolbarState(state);
        window.parent.postMessage(state, window.location.origin);
    }

    hide() {
        this.$container.classList.remove('is-active');
        this.currentPanelButton().classList.remove('is-active');
        let _this = this;

        // Hardcode timer as one does.
        setTimeout(function() {
            _this._currentPanel = null;
            window.parent.postMessage(_this.state(), window.location.origin);
        }, 250);
    }

    isExpanded() {
        return this.$container.classList.contains('is-active');
    }

    // ========== STATES ==========

    state() {
        return this.states[this.currentState];
    }

    nextState() {
        this.currentState++;
        if (this.currentState === this.states.length) {
            this.currentState = 0;
        }
        this.saveState();
        return this.state();
    }

    saveState() {
        if (!this.isLocalStorageAvailable) {
            return;
        }
        window.localStorage.setItem('toolbar_state', this.currentState);
    }

    loadState() {
        if (!this.isLocalStorageAvailable) {
            return;
        }
        let old = window.localStorage.getItem('toolbar_state');
        if (!old) {
            old = '0';
        }
        if (old === '0') {
            return this.hide();
        }
        if (old === '1') {
            return this.toggle();
        }
    }

    // ========== UPDATE ELEMENTES DEPENDING ON STATE ==========

    updateToolbarState(state) {
        if (state === 'toolbar') {
            this.$toolbar.classList.add('open');
        }
        if (state === 'collapse') {
            this.$toolbar.classList.remove('open');
        }
    }

    updateButtons(state) {
        if (state === 'toolbar') {
            this.$panelButtons.show();
        }
        if (state === 'collapse') {
            this.$panelButtons.hide();
        }
    }

    // ========== PANELS ==========

    currentPanelButton() {
        return this.$toolbar.find('[data-id="' + this.currentPanel() + '"]');
    }

    currentPanel() {
        return this._currentPanel;
    }

    loadPanel(id) {
        if (id === undefined) {
            return;
        }

        let url = this.baseUrl + 'debug-kit/panels/view/' + id;
        let contentArea = this.container.find('#panel-content');
        let _this = this;
        let timer;
        let loader = $('#loader');

        if (this._lastPanel !== id) {
            timer = setTimeout(function() {
                loader.addClass('loading');
            }, 500);
        }

        this._currentPanel = id;
        this._lastPanel = id;

        window.parent.postMessage('expand', window.location.origin);

        $.get(url, function(response) {
            clearTimeout(timer);
            loader.removeClass('loading');

            // Slide panel into place - css transitions.
            _this.container.addClass('enabled');
            contentArea.html(response);
            _this.bindVariableSort();

            _this.bindNeatArray();
        });
    }

    bindVariableSort() {
        let sortButton = this.container.find('.neat-array-sort');
        let _this = this;
        sortButton.click(function() {
            if (!$(this).prop('checked')) {
                document.cookie = 'debugKit_sort=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=' + _this.webroot;
            } else {
                document.cookie = 'debugKit_sort=1; path=' + _this.webroot;
            }
            _this.loadPanel(_this.currentPanel());
        });
    }

    bindNeatArray() {
        var lists = this.container.find('.depth-0');
        lists.find('ul').hide()
            .parent().addClass('expandable collapsed');

        lists.on('click', 'li', function(event) {
            event.stopPropagation();
            var el = $(this);
            el.children('ul').toggle();
            el.toggleClass('expanded')
                .toggleClass('collapsed');
        });
    }

    // ========== LISTENERS ==========

    mouseListeners() {
        let _this = this;
        this.$toolbar.find('.panel-button-left').on('click', function(e) {
            _this.scroll('left');
            return false;
        });
        this.$toolbar.find('.panel-button-right').on('click', function(e) {
            _this.scroll('right');
            return false;
        });

        this.$panelButtons.on('click', function(e) {
            _this.$panelButtons.classList.remove('is-active');
            e.preventDefault();
            e.stopPropagation();
            let id = this.getAttribute('data-id');
            let samePanel = _this.currentPanel() === id;

            if (_this.isExpanded() && samePanel) {
                _this.hide();
            }
            if (samePanel) {
                return false;
            }
            this.classList.add('is-active');
            _this.loadPanel(id);
        });

        this.$toolbar.on('click', function() {
            _this.toggle();
            return false;
        });

        this.$closeBtn.on('click', function() {
            _this.hide();
            return false;
        });
    }

    scroll(direction) {
        let scrollValue = 300;
        let operator = direction === 'left' ? '-=' : '+=';
        let buttons = this.toolbar.find('.toolbar-inner li');
        let cakeButton = this.toolbar.find('#panel-button');
        let firstButton = buttons.first();
        let lastButton = buttons.last();

        // If the toolbar is scrolled to the left, don't go farther.
        if (direction === 'right' && firstButton.position().left == 0) {
            return;
        }

        var buttonWidth = lastButton.width();
        // If the last button's right side is left of the cake button, don't scroll further.
        if (direction === 'left' && lastButton.offset().left + buttonWidth < cakeButton.offset().left) {
            return;
        }
        var css = {left: operator + scrollValue};
        $('.toolbar-inner li', this.button).animate(css);
    },

}
