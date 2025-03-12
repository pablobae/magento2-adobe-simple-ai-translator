var config = {
    config: {
        mixins: {
            'Magento_Ui/js/form/element/abstract': {
                'Pablobae_SimpleAiTranslator/js/form/element/abstract-mixin': true
            }
        }
    },
    map: {
        '*': {
            'ui/template/form/field': 'Pablobae_SimpleAiTranslator/templates/form/field'
        }
    }
};
