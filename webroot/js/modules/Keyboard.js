export default ( function() {
    'use strict';

    let init = function(toolbar) {
        document.addEventListener('keypress', function(event) {
            // Esc key
            if (event.keyCode === 27) {
                // Close active panel
                if (toolbar.isExpanded()) {
                    return toolbar.hide();
                }
                // Collapse the toolbar
                if (toolbar.state() === 'toolbar') {
                    return toolbar.toggle();
                }
            }
            // Check for left arrow
            if (event.keyCode === 37 && toolbar.isExpanded()) {
                toolbar.panelButtons.classList.remove('is-active');
                let prevPanel = toolbar.currentPanelButton().prev();
                if (prevPanel.classList.contains('c-panel')) {
                    prevPanel.classList.add('is-active');
                    return toolbar.loadPanel(prevPanel.data('id'));
                }
            }
            // Check for right arrow
            if (event.keyCode === 39 && toolbar.isExpanded()) {
                toolbar.panelButtons.classList.remove('is-active');
                let nextPanel = toolbar.currentPanelButton().next();
                if (nextPanel.classList.contains('c-panel')) {
                    nextPanel.classList.add('is-active');
                    return toolbar.loadPanel(nextPanel.data('id'));
                }
            }
        });
    };

    return {
        init: init
    };

}() );
