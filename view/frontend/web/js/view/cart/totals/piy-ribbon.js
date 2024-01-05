define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals',
    'mage/translate'
], function (Component, quote, totals, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'PiyRibbons_PiyOnline/summary/piy-ribbon'
        },
        totals: quote.getTotals(),

        /**
         * @return {*|Boolean}
         */
        isDisplayed: function () {
            return this.isFullMode() && this.getPureValue() != 0;
        },

        /**
         * Get surcharge title
         *
         * @returns {null|String}
         */
        getTitle: function () {
            if (!this.totals()) {
                return null;
            }

            return $t('Personalised ribbon(s)');
        },

        /**
         * @return {Number}
         */
        getPureValue: function () {
            let amount = 0,
                ribbonCostTotal;

            if (this.totals()) {
                ribbonCostTotal = totals.getSegment('piy-ribbon');

                if (ribbonCostTotal) {
                    amount = ribbonCostTotal.value;
                }
            }

            return amount;
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        }
    });
});
