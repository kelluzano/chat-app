window._ = require('lodash');

try {
    require('bootstrap');
} catch (e) {}

window.$ = window.jQuery = require('jquery');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

require('admin-lte/plugins/bootstrap/js/bootstrap.bundle');
require('admin-lte');

