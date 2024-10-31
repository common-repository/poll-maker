(function($) {
    $(document).ready(function() {
        //let allRateLabels = $('.rating-poll label');
        // allRateLabels.each(function() {
        //     $(this).find('i').removeClass('far').addClass('fas').removeClass('fas').addClass('far')
        // })
        let active = false;
        $('.rating-poll label:not(.emoji)').parent().parent().on('mouseleave', function() {
            let allRateLabels = $(this).find('label')
            if (active) {
                let index = -1;
                allRateLabels.each(function() {
                    if ($(this).hasClass('active-answer')) {
                        index = allRateLabels.index(this);
                    }
                })
                for (let i = 0; i < allRateLabels.length; i++) {
                    if (i > index) {
                        allRateLabels.eq(i).find('i').removeClass('fas').addClass('far')
                    } else {
                        allRateLabels.eq(i).find('i').removeClass('far').addClass('fas')
                    }
                }
            } else {
                allRateLabels.each(function() {
                    $(this).find('i').removeClass('fas').addClass('far')
                })
            }
        })
        $('.rating-poll label:not(.emoji)').on({
            mouseover() {
                let allRateLabels = $(this).parent().parent().find('label')
                let index = allRateLabels.index(this);
                allRateLabels.each(function() {
                    $(this).find('i').removeClass('fas').addClass('far')
                })
                for (let i = 0; i <= index; i++) {
                    allRateLabels.eq(i).find('i').removeClass('far').addClass('fas')
                }
            },
            click() {
                $(this).parent().parent().find('label').each(function() {
                    $(this).removeClass('active-answer')
                })
                $(this).addClass('active-answer')
                active = true;
            }
        })
        $('.rating-poll label.emoji').parent().parent().on('mouseleave', function() {
            let _this = $(this);
            if (active) {
                let index = -1;
                _this.find('label.emoji').each(function() {
                    if ($(this).hasClass('active-answer')) {
                        index = _this.find('label.emoji').index(this);
                    }
                })
                for (let i = 0; i < _this.find('label.emoji').length; i++) {
                    if (i != index) {
                        _this.find('label.emoji').eq(i).find('i').removeClass('fas').addClass('far')
                    } else {
                        _this.find('label.emoji').eq(i).find('i').removeClass('far').addClass('fas')
                    }
                }
            } else {
                _this.find('label.emoji').each(function() {
                    $(this).find('i').removeClass('fas').addClass('far')
                })
            }
        })
        $('.rating-poll label.emoji').on({
            mouseover() {
                let _this = $(this);
                let thisLabels = _this.parent().parent().find('label.emoji')
                let index = thisLabels.index(this)

                thisLabels.each(function() {
                    $(this).find('i').removeClass('fas').addClass('far')
                })
                thisLabels.eq(index).find('i').removeClass('far').addClass('fas')
            },
            click() {
                let thisLabels = $(this).parent().parent().find('label.emoji')
                thisLabels.each(function() {
                    $(this).removeClass('active-answer')
                })
                $(this).addClass('active-answer')
                active = true;
            }
        })
        let allVoteLabels = $('.voting-poll label')
        allVoteLabels.parent().parent().on('mouseleave', function() {
            let index = -1;
            let labels = $(this).find('label')
            if (active) {
                labels.each(function() {
                    if ($(this).hasClass('active-answer')) {
                        index = labels.index(this);
                    }
                })
                for (let i = 0; i < labels.length; i++) {
                    if (i != index) {
                        labels.eq(i).find('i').removeClass('fas').addClass('far')
                    } else {
                        labels.eq(i).find('i').removeClass('far').addClass('fas')
                    }
                }
            } else {
                labels.each(function() {
                    $(this).find('i').removeClass('fas').addClass('far')
                })
            }
        })
        $('.voting-poll label').on({
            mouseover() {
                let _this = $(this);
                let index = _this.parent().parent().find('label').index(this);
                _this.parent().parent().find('label').each(function() {
                    $(this).find('i').removeClass('fas').addClass('far')
                })
                _this.parent().parent().find('label').eq(index).find('i').removeClass('far').addClass('fas')
            },
            click() {
                let _this = $(this);
                _this.parent().parent().find('label').each(function() {
                    $(this).removeClass('active-answer')
                })
                $(this).addClass('active-answer')
                active = true;
            }
        })
    })

})(jQuery);