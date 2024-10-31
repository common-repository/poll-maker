(function($) {
    'use strict';
    $(document).find('.ays-qm-rate').on('click', function() {
        if ($(this).val() === 'Ok') {
            window.open('https://wordpress.org/plugins/quiz-maker/', '_blank');
        }
        $(this).parent('li')
            .parent('ul')
            .parent('form')
            .parent('div')
            .find('button.notice-dismiss')
            .trigger('click');
    });

})(jQuery);