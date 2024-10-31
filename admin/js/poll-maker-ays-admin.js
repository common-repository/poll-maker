(function($) {
    'use strict';
    $('.ays_help').tooltip();
    $('.select2').select2({
        placeholder: 'Select category'
    });
    $('.ays-select').select2();
    $(document).on('click', 'a.add-question-image', function(e) {
        openMediaUploader(e, $(this));
    });

    $(document).on('click', '.ays-remove-question-img', function() {
        $(this).parent().find('img#ays-poll-img').attr('src', '');
        $(this).parent().find('input#ays-poll-image').val('');
        $(this).parent().fadeOut();
        $(document).find('.ays-field label a.add-question-image').text('Add Image');
    });
    let themes = [
        'personal',
        {
            'name': 'light',
            'main_color': '#0C6291',
            'text_color': '#0C6291',
            'icon_color': '#0C6291',
            'bg_color': '#FBFEF9',
        },
        {
            'name': 'dark',
            'main_color': '#FBFEF9',
            'text_color': '#FBFEF9',
            'icon_color': '#FBFEF9',
            'bg_color': '#222222',
        },
    ];

    function openMediaUploader(e, element) {
        e.preventDefault();
        let aysUploader = wp.media({
            title: 'Upload',
            button: {
                text: 'Upload'
            },
            multiple: false
        }).on('select', function() {
            let attachment = aysUploader.state().get('selection').first().toJSON();
            element.text('Edit Image');
            element.parent().parent().find('.ays-poll-question-image-container').fadeIn();
            element.parent().parent().find('img#ays-poll-img').attr('src', attachment.url);
            element.parent().parent().find('input#ays-poll-image').val(attachment.url);
        }).open();
        return false;
    }


    $('#ays-poll-vote-type').on('change', function() {
        switch ($(this).val()) {
            case 'hand':
                $('#vote-res').removeClass().addClass('far fa-thumbs-up')
                break;
            case 'emoji':
                $('#vote-res').removeClass().addClass('fas fa-smile')
                break;
            default:
                break;
        }
    })
    $('#ays-poll-rate-type').on('change', function() {
        switch ($(this).val()) {
            case 'star':
                $('#rate-res').removeClass().addClass('fas fa-star')
                break;
            case 'emoji':
                $('#rate-res').removeClass().addClass('fas fa-smile')
                break;
            default:
                break;
        }
    })

    $('.if-' + $('#ays-poll-type').val()).css('display', 'flex').find('select, input').attr('required', true);

    $('#ays-poll-type').on('change', function() {
        $('div[class|="if"]').hide().find('select, input').attr('required', false)
        $('.if-' + $(this).val()).css('display', 'flex').find('select, input').attr('required', true);
    });

    function rateType() {
        let val = $('#ays-poll-rate-value').val()
        $('#ays-poll-rate-value').empty().html(`<option value="" selected disabled>Select value</option>`)
        for (let i = 3; i < 6; i++) {
            if ($('#ays-poll-rate-type').val() == 'emoji' && i == 4) continue
            let option = $(`<option value="${i}" ${i==val?'selected':''}>${i}</option>`);
            $('#ays-poll-rate-value').append(option)
        }
        $('#ays-poll-rate-value').show();
    }
    rateType()
    $('#ays-poll-rate-type').on('change', rateType)
    $('#add-answer').on({
        mouseover: function() {
            $(this).removeClass('far').addClass('fas');
        },
        mouseout: function() {
            $(this).removeClass('fas').addClass('far')
        },
        click: function() {
            let answersCount = $('.if-choosing .col-sm-10').find('input[type="text"]').length;
            let id = 1 + answersCount;
            if (answersCount < 10) {
                $('.if-choosing .col-sm-10').append(`<div><input type="text" class="ays-text-input ays-text-input-short" name='ays-poll-answers[]' data-id='${id}'><input type="hidden" name="ays-poll-answers-ids[]" data-id='${id}' value="0"> <i class='fas fa-minus-square remove-answer' data-id='${id}'></i></div>`);
            }
        }
    });
    $(document).on('click', '.remove-answer', function() {
        let answerId = $(this).attr('data-id');
        $('.if-choosing .col-sm-10').find('input[data-id="' + answerId + '"]').remove();
        $(this).next().remove();
        $(this).remove()
    })
    $(document).on('mouseover', '.remove-answer', function() {
        $(this).removeClass('fas').addClass('far')
    })
    $(document).on('mouseout', '.remove-answer', function() {
        $(this).removeClass('far').addClass('fas');
    })

    $(document).find('.nav-tab-wrapper a.nav-tab').on('click', function(e) {
        let elemenetID = $(this).attr('href');
        $(document).find('.nav-tab-wrapper a.nav-tab').each(function() {
            if ($(this).hasClass('nav-tab-active')) {
                $(this).removeClass('nav-tab-active');
            }
        });
        $(this).addClass('nav-tab-active');
        $(document).find('.ays-poll-tab-content').each(function() {
            $(this).css('display', 'none');
        });
        $('.ays-poll-tab-content' + elemenetID).css('display', 'block');
        e.preventDefault();
    });

    function checkTheme() {
        let themeId = $('#ays-poll-theme').val();
        $('#ays-poll-text-color').val(themes[themeId].text_color).parent().parent().prev().css({
            'background-color': themes[themeId].text_color
        })
        $('#ays-poll-main-color').val(themes[themeId].main_color).parent().parent().prev().css({
            'background-color': themes[themeId].main_color
        })
        $('#ays-poll-bg-color').val(themes[themeId].bg_color).parent().parent().prev().css({
            'background-color': themes[themeId].bg_color
        })
        $('#ays-poll-icon-color').val(themes[themeId].icon_color).parent().parent().prev().css({
            'background-color': themes[themeId].icon_color
        })
    }
    //checkTheme();
    $('#ays-poll-theme').on('change', checkTheme)


    $(document).find('#ays-poll-main-color').wpColorPicker();
    $(document).find('#ays-poll-text-color').wpColorPicker();
    $(document).find('#ays-poll-icon-color').wpColorPicker();
    $(document).find('#ays-poll-bg-color').wpColorPicker();


})(jQuery);