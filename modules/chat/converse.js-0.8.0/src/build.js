({
    baseUrl: "../",
    name: "components/almond/almond.js",
    out: "../builds/converse.min.js",
    include: ['main'],
    tpl: {
        // Use Mustache style syntax for variable interpolation
        templateSettings: {
            evaluate : /\{\[([\s\S]+?)\]\}/g,
            interpolate : /\{\{([\s\S]+?)\}\}/g
        }
    },
    paths: {
        "jquery": "components/jquery/dist/jquery",
        "jed": "components/jed/jed",
        "locales": "locale/locales",
        "af": "locale/af/LC_MESSAGES/af",
        "de": "locale/de/LC_MESSAGES/de",
        "en": "locale/en/LC_MESSAGES/en",
        "es": "locale/es/LC_MESSAGES/es",
        "fr": "locale/fr/LC_MESSAGES/fr",
        "he": "locale/he/LC_MESSAGES/he",
        "hu": "locale/hu/LC_MESSAGES/hu",
        "id": "locale/id/LC_MESSAGES/id",
        "it": "locale/it/LC_MESSAGES/it",
        "ja": "locale/ja/LC_MESSAGES/ja",
        "nl": "locale/nl/LC_MESSAGES/nl",
        "pt_BR": "locale/pt_BR/LC_MESSAGES/pt_BR", 
        "ru": "locale/ru/LC_MESSAGES/ru",
        "zh": "locale/zh/LC_MESSAGES/zh",
        "jquery.browser": "components/jquery.browser/dist/jquery.browser",
        "underscore": "components/underscore/underscore",
        "backbone": "components/backbone/backbone",
        "backbone.browserStorage": "components/backbone.browserStorage/backbone.browserStorage",
        "backbone.overview": "components/backbone.overview/backbone.overview",
        "strophe": "components/strophe/strophe",
        "strophe.muc": "components/strophe.muc/index",
        "strophe.roster": "components/strophe.roster/index",
        "strophe.vcard": "components/strophe.vcard/index",
        "strophe.disco": "components/strophe.disco/index",
        "salsa20": "components/otr/build/dep/salsa20",
        "bigint": "src/bigint",
        "crypto.core": "components/otr/vendor/cryptojs/core",
        "crypto.enc-base64": "components/otr/vendor/cryptojs/enc-base64",
        "crypto.md5": "components/crypto-js-evanvosberg/src/md5",
        "crypto.evpkdf": "components/crypto-js-evanvosberg/src/evpkdf",
        "crypto.cipher-core": "components/otr/vendor/cryptojs/cipher-core",
        "crypto.aes": "components/otr/vendor/cryptojs/aes",
        "crypto.sha1": "components/otr/vendor/cryptojs/sha1",
        "crypto.sha256": "components/otr/vendor/cryptojs/sha256",
        "crypto.hmac": "components/otr/vendor/cryptojs/hmac",
        "crypto.pad-nopadding": "components/otr/vendor/cryptojs/pad-nopadding",
        "crypto.mode-ctr": "components/otr/vendor/cryptojs/mode-ctr",
        "crypto": "src/crypto",
        "eventemitter": "components/otr/build/dep/eventemitter",
        "otr": "components/otr/build/otr",
        "converse-dependencies": "src/deps-full",
        "moment":"components/momentjs/moment",
        "converse-templates":"src/templates",
        "tpl": "components/requirejs-tpl-jcbrand/tpl",
        "text": "components/requirejs-text/text"
    }
})
