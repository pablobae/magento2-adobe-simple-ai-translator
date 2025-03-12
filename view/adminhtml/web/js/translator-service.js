define(['jquery'], function ($, uiRegistry) {
    'use strict';

    return {
        /**
         * Shared method to handle translation logic
         * @param {String} text - The text to translate
         * @param {String} storeId - The store scope for translation
         * @param {Function} callback - A callback function to handle the response
         */
        translate: function (text, storeId, callback) {
            $.ajax({
                url: '/admin/simpletranslator/translate',
                type: 'POST',
                data: {
                    text: text,
                    storeId: storeId
                },
                success: function (response) {
                    if (response.success && typeof callback === 'function') {
                        callback('success', response.translatedValue);
                    } else {
                        console.error('Translation failed:'.response.message);
                        callback('error', 'Translation failed:'.response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('TranslatorService error: ' + xhr.responseJSON.message);
                    callback('error', xhr.responseJSON.message)
                }
            });
        }
    };
});
