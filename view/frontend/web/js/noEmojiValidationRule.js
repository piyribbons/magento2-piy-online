define([
    'jquery',
    'mage/translate',
    'jquery/validate'
], function ($, $t) {
    'use strict';

    return function (target) {
        $.validator.addMethod(
            'validate-no-emojis',
            function (value) {
                let noEmojiRegex = /^(?:(?![\u{1F300}-\u{1F6FF}\u{1F900}-\u{1F9FF}\u{2600}-\u{27BF}\u{2B50}\u{2B06}]).)*$/u;
                return noEmojiRegex.test(value);
        }, $t('Please do not use any emojis.'));

        return target;
    };
});
