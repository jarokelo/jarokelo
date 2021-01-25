var localConfig = require('../config.json');

exports.config = {
    seleniumAddress: localConfig.seleniumAddress + ':' + localConfig.seleniumPort + '/wd/hub',

    // Capabilities to be passed to the webdriver instance.
    capabilities: {
        'browserName': localConfig.browser || 'phantomjs'
    },

    params: {
        baseUrl: localConfig.host + localConfig.baseUrl
    },

    // Options to be passed to Jasmine-node.
    jasmineNodeOpts: {
        showColors: true,
        defaultTimeoutInterval: 30000
    }
};
