require([
    'jquery',
    'domReady'
], function ($) {
    'use strict';

    let elementLoadInterval = setInterval(function () {
        if ($('.piy-ribbon-text-input').length) {

            clearInterval(elementLoadInterval);

            // Initially hide all ribbon text input wrappers and emoji pickers
            $('.piy-ribbon-input-wrapper').hide();
            $('emoji-picker').hide();

            // Toggle the ribbon text input wrappers on click
            $('a.action-edit-ribbon').on('click', function (e) {
                e.preventDefault();
                $(this).siblings('.piy-ribbon-input-wrapper').slideToggle();
                $(this).toggleClass('opened');
            });

            $('.emoji-picker-trigger').on('click', function () {
                $(this).parents('.piy-ribbon-input-wrapper').find('emoji-picker').slideToggle();
            });

            $('emoji-picker').each(function () {

                document.getElementById($(this).attr('id')).addEventListener('emoji-click', event => {

                    let emoji = event.detail.unicode,
                        $target = $(event.target),
                        $piyRibbonTextInputField = $target.parents('.piy-ribbon-input-wrapper').find('.piy-ribbon-text-input input'),
                        currentTextVal = $piyRibbonTextInputField.val();

                    $piyRibbonTextInputField.val(currentTextVal + emoji);
                });
            });

        }
    }, 250);
});
