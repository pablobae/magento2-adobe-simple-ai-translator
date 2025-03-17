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
                 * Check if the current page is a CMS Page or Block edit page
                 * @returns {boolean}
                 */
                isCmsOrBlockEditPage: function () {
                    const pathname = window.location.pathname;

                    // Define patterns for "Edit CMS Page" and "Edit Block Page"
                    const editCmsPagePattern = /\/cms\/page\/edit/; // Example: /admin/cms/page/edit/id/1/
                    const editBlockPagePattern = /\/cms\/block\/edit/; // Example: /admin/cms/block/edit/id/1/

                    return editCmsPagePattern.test(pathname) || editBlockPagePattern.test(pathname);
                },
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
                    var text = '';
                    var storeId = null;
                    var button = event.currentTarget;

                    if (this.isCmsOrBlockEditPage()) {
                        var storeIdSelect = document.querySelector('[name="store_id"]');

                        if (!storeIdSelect || storeIdSelect.value === '') {
                            alert($t('Please, select at least one store view from the Page in Websites.'));
                            return;
                        }
                        storeId = storeIdSelect.value;
                    } else {
                        storeId = this.getStoreIdFromPath();
                    }

                    // Check if the field is a WYSIWYG editor
                    if (wysiwygAdapter.get(this.uid)) {
                        text = wysiwygAdapter.get(this.uid).getContent({format: 'text'});
                    } else {
                        text = this.value();
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
