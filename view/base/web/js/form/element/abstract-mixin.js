define(
    [
        'jquery',
        'ko',
        'Pablobae_SimpleAiTranslator/js/translator-service'
    ],
    function ($, ko, TranslatorService) {
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

                    return match && match[1] ? match[1] : '0'; //default store
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

                    // If not in default store view, uncheck the "Use Default Value" checkbox
                    if (storeId !== '0') {
                        var useDefaultCheckbox = $('input[name="use_default[' + this.index + ']"]');
                        if (useDefaultCheckbox.length && useDefaultCheckbox.is(':checked')) {
                            // Find the associated Knockout component
                            var koContext = ko.contextFor(useDefaultCheckbox[0]);
                            if (koContext && koContext.$data && koContext.$data.source) {
                                // Update the Knockout observable directly
                                var field = this.index;
                                if (koContext.$data.source.set) {
                                    koContext.$data.source.set('data.use_default.' + field, false);
                                }
                                // Also update the DOM element for good measure
                                useDefaultCheckbox.prop('checked', false).trigger('change');

                                // Ensure the input is enabled
                                this.disabled(false);
                            } else {
                                // Fallback to just changing the checkbox
                                useDefaultCheckbox.prop('checked', false).trigger('change');
                            }
                        }
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
