function Toolbar(options) {
  this.toolbar = options.toolbar;
  this.panelButtons = options.panelButtons;
  this.content = options.content;
  this.panelClose = options.panelClose;
  this.keyboardScope = options.keyboardScope;
  this.currentRequest = options.currentRequest;
  this.originalRequest = options.originalRequest;
  this.baseUrl = options.baseUrl;
  this.webroot = options.webroot;
}

Toolbar.prototype = {
  _currentPanel: null,
  _lastPanel: null,
  _state: 0,
  _localStorageAvailable: null,
  currentRequest: null,
  originalRequest: null,
  ajaxRequests: [],

  states: [
    'collapse',
    'toolbar'
  ],

  toggle: function() {
    var state = this.nextState();
    this.updateButtons(state);
    this.updateToolbarState(state);
    window.parent.postMessage(state, window.location.origin);
  },

  state: function() {
    return this.states[this._state];
  },

  nextState: function() {
    this._state++;
    if (this._state == this.states.length) {
      this._state = 0;
    }
    this.saveState();
    return this.state();
  },

  localStorageAvailable: function() {
    if (this._localStorageAvailable === null) {
      if (!window.localStorage) {
        this._localStorageAvailable = false;
      } else {
        try {
          window.localStorage.setItem('testKey', '1');
          window.localStorage.removeItem('testKey');
          this._localStorageAvailable = true;
        } catch (error) {
          this._localStorageAvailable = false;
        }
      }
    }

    return this._localStorageAvailable;
  },

  saveState: function() {
    if (!this.localStorageAvailable()) {
      return;
    }
    window.localStorage.setItem('toolbar_state', this._state);
  },

  loadState: function() {
    if (!this.localStorageAvailable()) {
      return;
    }
    var old = window.localStorage.getItem('toolbar_state');
    if (!old) {
      old = 0;
    }
    if (old == 0) {
      return this.hideContent();
    }
    if (old == 1) {
      return this.toggle();
    }
  },

  updateToolbarState: function(state) {
    if (state === 'toolbar') {
      this.toolbar.addClass('open');
    }
    if (state === 'collapse') {
      this.toolbar.removeClass('open');
    }
  },

  updateButtons: function(state) {
    if (state === 'toolbar') {
      this.panelButtons.show();
    }
    if (state === 'collapse') {
      this.panelButtons.hide();
    }
  },

  isExpanded: function() {
    return this.content.hasClass('enabled');
  },

  hideContent: function() {
    // slide out - css animation
    this.content.removeClass('enabled');
    // remove the active state on buttons
    this.currentPanelButton().removeClass('panel-active');
    var _this = this;

    // Hardcode timer as one does.
    setTimeout(function() {
      _this._currentPanel = null;
      window.parent.postMessage(_this.state(), window.location.origin);
    }, 250);
  },

  loadPanel: function(id) {
    if (id === undefined) {
      return;
    }

    var url = this.baseUrl + 'debug-kit/panels/view/' + id;
    var contentArea = this.content.find('#panel-content');
    var _this = this;
    var timer;
    var loader = $('#loader');

    if (this._lastPanel != id) {
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
      _this.content.addClass('enabled');
      contentArea.html(response);
      _this.bindNeatArray();
    });
  },

  bindNeatArray: function() {
    var sortButton = this.content.find('.neat-array-sort');
    var _this = this;
    sortButton.click(function() {
      if (!$(this).prop('checked')) {
        document.cookie = 'debugKit_sort=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=' + _this.webroot;
      } else {
        document.cookie = 'debugKit_sort=1; path=' + _this.webroot;
      }
      _this.loadPanel(_this.currentPanel());
    });

    var lists = this.content.find('.depth-0');
    lists.find('ul').hide()
      .parent().addClass('expandable collapsed');

    lists.on('click', 'li', function(event) {
      event.stopPropagation();
      var el = $(this);
      el.children('ul').toggle();
      el.toggleClass('expanded')
        .toggleClass('collapsed');
    });
  },

  currentPanel: function() {
    return this._currentPanel;
  },

  currentPanelButton: function() {
    return this.toolbar.find("[data-id='" + this.currentPanel() + "']");
  },

  keyboardListener: function() {
    var _this = this;
    this.keyboardScope.keydown(function(event) {
      // Check for Esc key
      if (event.keyCode === 27) {
        // Close active panel
        if (_this.isExpanded()) {
          return _this.hideContent();
        }
        // Collapse the toolbar
        if (_this.state() === 'toolbar') {
          return _this.toggle();
        }
      }
      // Check for left arrow
      if (event.keyCode === 37 && _this.isExpanded()) {
        _this.panelButtons.removeClass('panel-active');
        var prevPanel = _this.currentPanelButton().prev();
        if (prevPanel.hasClass('panel')) {
          prevPanel.addClass('panel-active');
          return _this.loadPanel(prevPanel.data('id'));
        }
      }
      // Check for right arrow
      if (event.keyCode === 39 && _this.isExpanded()) {
        _this.panelButtons.removeClass('panel-active');
        var nextPanel = _this.currentPanelButton().next();
        if (nextPanel.hasClass('panel')) {
          nextPanel.addClass('panel-active');
          return _this.loadPanel(nextPanel.data('id'));
        }
      }
    });
  },

  mouseListener: function() {
    var _this = this;
    this.toolbar.find('.panel-button-left').on('click', function(e) {
      _this.scroll('left');
      return false;
    });
    this.toolbar.find('.panel-button-right').on('click', function(e) {
      _this.scroll('right');
      return false;
    });

    this.panelButtons.on('click', function(e) {
      _this.panelButtons.removeClass('panel-active');
      e.preventDefault();
      e.stopPropagation();
      var id = $(this).attr('data-id');
      var samePanel = _this.currentPanel() === id;

      if (_this.isExpanded() && samePanel) {
        _this.hideContent();
      }
      if (samePanel) {
        return false;
      }
      $(this).addClass('panel-active');
      _this.loadPanel(id);
    });

    this.toolbar.on('click', function(e) {
      _this.toggle();
      return false;
    });

    this.panelClose.on('click', function(e) {
      _this.hideContent();
      return false;
    });
  },

  windowOrigin: function() {
    // IE does not have access to window.location.origin
    if (!window.location.origin) {
      window.location.origin = window.location.protocol + '//' +
        window.location.hostname +
        (window.location.port ? ':' + window.location.port : '');
    }
  },

  onMessage: function(event) {
    if (typeof(event.data) === 'string' && event.data.indexOf('ajax-completed$$') === 0) {
      this.onRequest(JSON.parse(event.data.split('$$')[1]));
    }
  },

  onRequest: function(request) {
    this.ajaxRequests.push(request);
    $('.panel-summary:contains(xhr)').text("" + this.ajaxRequests.length + ' xhr');
  },

  scroll: function(direction) {
    var scrollValue = 300;
    var operator = direction === 'left' ? '-=' : '+=';
    var buttons = this.toolbar.find('.toolbar-inner li');
    var cakeButton = this.toolbar.find('#panel-button');
    var firstButton = buttons.first();
    var lastButton = buttons.last();

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
    $('.toolbar-inner li', this.button).animate(css)
  },

  initialize: function() {
    this.windowOrigin();
    this.mouseListener();
    this.keyboardListener();
    this.loadState();

    var self = this;
    window.addEventListener('message', function(event) {
      self.onMessage(event);
    }, false);
  }
};
