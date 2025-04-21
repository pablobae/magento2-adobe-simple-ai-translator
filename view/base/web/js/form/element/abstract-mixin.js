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
define(
    [
        'jquery',
        'wysiwygAdapter',
        'Pablobae_SimpleAiTranslator/js/translator-service',
        'mage/translate'
    ],
    function ($, wysiwygAdapter, TranslatorService, $t) {
        'use strict';

        return function (OriginalComponent) {
            return OriginalComponent.extend({
                /**
                 * Retrieve store id from the page URL
                 * @returns {string|null}
                 */
                getStoreIdFromPath: function () {
                    const pathname = window.location.pathname;
                    const storePattern = /\/store\/(\d+)/;
                    const match = pathname.match(storePattern);

                    return match && match[1] ? match[1] : '0'; // Default store
                },
                /**
                 * Call the translate API and update the field value
                 * @param {Object} data - The data object
                 * @param {Event} event - The click event
                 */
                translate: function (data, event) {
                    var self = this;
                    var text = this.value();
                    var storeId = this.getStoreIdFromPath();
                    var button = event.currentTarget;

                    // Check if the field is a WYSIWYG editor
                    if (wysiwygAdapter.get(this.uid)) {
                        text = wysiwygAdapter.get(this.uid).getContent({format: 'text'});
                    }

                    // Don't translate if text is empty
                    if (!text || text.trim() === '') {
                        alert($t('No content to translate.'));
                        return;
                    }

                    // Add loading state to the button
                    $(button).addClass('loading');

                    TranslatorService.translate(text, storeId, function (status, response) {
                        // Remove loading state from the button
                        $(button).removeClass('loading');

                        if (status === 'success') {
                            // Update WYSIWYG editor
                            if (wysiwygAdapter.get(self.wysiwygId)) {
                                var wysiwyg = wysiwygAdapter.get(self.wysiwygId).setContent(response);
                            }
                            self.value(response);
                        } else {
                            alert('ERROR: ' + response);
                        }
                    }, button);
                }
            });
        };
    }
);
