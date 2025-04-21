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
