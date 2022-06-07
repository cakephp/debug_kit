function Toolbar(options) {
  this.toolbar = options.toolbar;
  this.panelButtons = options.panelButtons;
  this.container = options.content;
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

  onMessage: function(event) {
    if (typeof(event.data) === 'string' && event.data.indexOf('ajax-completed$$') === 0) {
      this.onRequest(JSON.parse(event.data.split('$$')[1]));
    }
  },

  onRequest: function(request) {
    this.ajaxRequests.push(request);
    $('.panel-summary:contains(xhr)').text("" + this.ajaxRequests.length + ' xhr');
  },

  initialize: function() {
    this.mouseListener();
    this.loadState();

    var self = this;
    window.addEventListener('message', function(event) {
      self.onMessage(event);
    }, false);
  }
};
