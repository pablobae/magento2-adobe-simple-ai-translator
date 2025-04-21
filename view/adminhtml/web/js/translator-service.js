/*!
 * SimpleAiTranslator
 * Copyright (C) 2025 - Pablo César Baenas Castelló - https://www.pablobaenas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */
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

            const formKey = $('input[name="form_key"]').val();

            $.ajax({
                url: '/admin/simpletranslator/translate',
                type: 'POST',
                dataType: 'json',
                data: {
                    text: text,
                    storeId: storeId,
                    form_key: formKey
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (response) {
                    // Remove loading class
                    if (element) {
                        $(element).removeClass('loading');
                    }

                    if (response.success && typeof callback === 'function') {
                        callback('success', response.translation);
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
