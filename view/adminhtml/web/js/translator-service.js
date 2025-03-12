define(['jquery'], function ($) {
    'use strict';

    return {
        /**
         * Shared method to handle translation logic
         * @param {String} text - The text to translate
         * @param {String} storeId - The store scope for translation
         * @param {Function} callback - A callback function to handle the response
         * @param {Object} element - The DOM element to show loading state
         */
        translate: function (text, storeId, callback, element) {
            // If element is provided, add loading class
            if (element) {
                $(element).addClass('loading');
            }

            $.ajax({
                url: '/admin/simpletranslator/translate',
                type: 'POST',
                data: {
                    text: text,
                    storeId: storeId
                },
                success: function (response) {
                    // Remove loading class
                    if (element) {
                        $(element).removeClass('loading');
                    }

                    if (response.success && typeof callback === 'function') {
                        callback('success', response.translatedValue);
                    } else {
                        console.error('Translation failed: ' + response.message);
                        callback('error', 'Translation failed: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    // Remove loading class
                    if (element) {
                        $(element).removeClass('loading');
                    }

                    console.error('TranslatorService error: ' + (xhr.responseJSON ? xhr.responseJSON.message : error));
                    callback('error', xhr.responseJSON ? xhr.responseJSON.message : error);
                }
            });
        }
    };
});
