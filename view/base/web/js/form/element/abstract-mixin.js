define(
    [
        'jquery',
        'wysiwygAdapter',
        'Pablobae_SimpleAiTranslator/js/translator-service'
    ],
    function ($, wysiwygAdapter, TranslatorService) {
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
                        text = wysiwygAdapter.get(this.uid).getContent({ format: 'text' });
                    }

                    // Don't translate if text is empty
                    if (!text || text.trim() === '') {
                        alert('No content to translate.');
                        return;
                    }

                    // Add loading state to the button
                    $(button).addClass('loading');

                    TranslatorService.translate(text, storeId, function (status, response) {
                        // Remove loading state from the button
                        $(button).removeClass('loading');

                        if (status === 'success') {
                            // Update WYSIWYG editor or regular field content
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
