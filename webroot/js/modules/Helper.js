export default ( function() {
    'use strict';

    let isLocalStorageAvailable = function() {
        if (!window.localStorage) {
            return false;
        } else {
            try {
                window.localStorage.setItem('testKey', '1');
                window.localStorage.removeItem('testKey');
                return true;
            } catch (error) {
                return false;
            }
        }
    };

    return {
        isLocalStorageAvailable: isLocalStorageAvailable
    };

}());
