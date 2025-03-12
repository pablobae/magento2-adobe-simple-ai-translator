define(
    [
        'jquery',
        'Pablobae_SimpleAiTranslator/js/translator-service'
    ],
    function ($, TranslatorService) {
        'use strict';

        return function (OriginalComponent) {
            return OriginalComponent.extend({
                /**
                 * Retrieve store id from the page url
                 * @returns {string|null}
                 */
                getStoreIdFromPath: function () {
                    const pathname = window.location.pathname;
                    const storePattern = /\/store\/(\d+)/;
                    const match = pathname.match(storePattern);

                    return match && match[1] ? match[1] : 0; //default store
                },
                /**
                 * Call the translate API using the shared utility
                 * @param {Object} data - The data object
                 * @param {Event} event - The click event
                 */
                translate: function (data, event) {
                    var self = this;
                    var text = this.value();
                    var storeId = this.getStoreIdFromPath();
                    var button = event.currentTarget;

                    // Don't translate if text is empty
                    if (!text || text.trim() === '') {
                        return;
                    }

                    TranslatorService.translate(text, storeId, function (status, response) {
                        if (status === 'success') {
                            self.value(response);
                        } else {
                            alert('ERROR: ' + response);
                        }
                    }, button);
                }
            });
        };
    });
