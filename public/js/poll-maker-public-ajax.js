(function($) {
    String.prototype.stripSlashes = function() {
        return this.replace(/\\(.)/mg, "$1");
    }
    $.fn.serializeFormJSON = function() {
        let o = {},
            a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    function choose() {
        let btn = $(this);
        let pollId = btn.attr('data-id');
        let formId = btn.attr('data-form');
        let data = $('#' + formId).parent().serializeFormJSON();
        if (!('answer' in data)) return;
        btn.parent().parent().css({
            '-webkit-filter': 'blur(5px)',
            filter: 'blur(5px)',
        })
        btn.off()
        data.action = 'ays_finish_poll';
        data.poll_id = pollId;
        $.post({
            url: poll_maker_ajax_public.ajax_url,
            dataType: 'json',
            data,
            success: function(res) {
                btn.parent().parent().css({
                    '-webkit-filter': 'unset',
                    filter: 'unset',
                })
                $('.answer-' + formId).remove();
                btn.remove();
                $('#' + formId).append(`<div class="results-apm"></div>`);
                let votesSum = 0;
                let votesMax = 0;
                for (const answer of res.answers) {
                    votesSum += +answer.votes;
                    if (+answer.votes > votesMax) {
                        votesMax = +answer.votes
                    }
                }
                for (const answer of res.answers) {
                    let answerDiv = $(`<div class="answer-title flex-apm"></div>`)
                    let width = (answer.votes * 100 / votesSum).toFixed(0);
                    let answerBar = $(`<div class="answer-percent" data-percent="${width}"></div>`)
                    answerBar.css({
                        width: '1%'
                    })
                    answerDiv.append(`<span class="answer-text">${answer.answer.stripSlashes()}</span><span class="answer-votes">${answer.votes}</span>`).appendTo(`#${formId} .results-apm`);
                    $(`#${formId} .results-apm`).append(answerBar);

                }
                setTimeout(() => {
                    $('.answer-percent').each(function() {
                        let percent = $(this).attr('data-percent')
                        $(this).css({
                            width: (percent || 1) + '%'
                        });
                        setTimeout(() => {
                            $(this).text(`${percent>5?percent+'%':''}`)
                        }, 500);
                    })
                }, 100);
            }
        });

    }
    let emoji = [
        "<i class='far fa-dizzy'></i>",
        "<i class='far fa-smile'></i>",
        "<i class='far fa-meh'></i>",
        "<i class='far fa-frown'></i>",
        "<i class='far fa-tired'></i>",
    ];

    function rate() {
        let btn = $(this);
        let pollId = btn.attr('data-id');
        let formId = btn.attr('data-form');
        let data = $('#' + formId).parent().serializeFormJSON();
        if (!('answer' in data)) return;
        btn.parent().parent().css({
            '-webkit-filter': 'blur(5px)',
            filter: 'blur(5px)',
        })
        btn.off()
        data.action = 'ays_finish_poll';
        data.poll_id = pollId;
        $.post({
            url: poll_maker_ajax_public.ajax_url,
            dataType: 'json',
            data,
            success: function(res) {
                btn.parent().parent().css({
                    '-webkit-filter': 'unset',
                    filter: 'unset',
                })
                $('.answer-' + formId).remove();
                btn.remove();
                $('#' + formId).append(`<div class="results-apm"></div>`);
                let votesSum = res.answers.reduce((acc, val) => acc + +val['votes'], 0);
                for (let i = 0; i < res.answers.length; i++) {
                    const answer = res.answers[i];
                    let answerDiv = $(`<div class="answer-title flex-apm"></div>`)
                    let width = (answer.votes * 100 / votesSum).toFixed(0);
                    let answerBar = $(`<div class="answer-percent" data-percent="${width}"></div>`)
                    answerBar.css({
                        width: '1%'
                    })
                    let answerText = '';
                    if (res.view_type == 'emoji') {
                        answerText = emoji[res.answers.length / 2 + 1.5 - i];
                    } else {
                        for (let j = 0; j <= i; j++) {
                            answerText += "<i class='far fa-star'></i>"
                        }
                    }
                    answerDiv.append(`<span class="answer-text">${answerText}</span><span class="answer-votes">${answer.votes}</span>`).appendTo(`#${formId} .results-apm`);
                    $(`#${formId} .results-apm`).append(answerBar);
                }
                setTimeout(() => {
                    $('.answer-percent').each(function() {
                        let percent = $(this).attr('data-percent')
                        $(this).css({
                            width: (percent || 1) + '%'
                        });
                        setTimeout(() => {
                            $(this).text(`${percent>5?percent+'%':''}`)
                        }, 500);
                    })
                }, 100);
            }
        });
    }
    let hand = ["<i class='far fa-thumbs-up'></i>", "<i class='far fa-thumbs-down'></i>"]

    function vote() {
        let btn = $(this);
        let pollId = btn.attr('data-id');
        let formId = btn.attr('data-form');
        let data = $('#' + formId).parent().serializeFormJSON();
        if (!('answer' in data)) return;
        btn.parent().parent().css({
            '-webkit-filter': 'blur(5px)',
            filter: 'blur(5px)',
        })
        btn.off()
        data.action = 'ays_finish_poll';
        data.poll_id = pollId;
        $.post({
            url: poll_maker_ajax_public.ajax_url,
            dataType: 'json',
            data,
            success: function(res) {
                btn.parent().parent().css({
                    '-webkit-filter': 'unset',
                    filter: 'unset',
                })
                $('.answer-' + formId).remove();
                btn.remove();
                $('#' + formId).append(`<div class="results-apm"></div>`);
                let votesSum = res.answers.reduce((acc, val) => acc + +val['votes'], 0);
                for (let i = 0; i < res.answers.length; i++) {
                    const answer = res.answers[i];
                    let answerDiv = $(`<div class="answer-title flex-apm"></div>`)
                    let width = (answer.votes * 100 / votesSum).toFixed(0);
                    let answerBar = $(`<div class="answer-percent" data-percent="${width}"></div>`)
                    answerBar.css({
                        width: '1%'
                    })
                    let answerText = '';
                    if (res.view_type == 'hand') {
                        answerText = hand[i];
                    } else {
                        answerText = emoji[2 * i + 1]
                    }
                    answerDiv.append(`<span class="answer-text">${answerText}</span><span class="answer-votes">${answer.votes}</span>`).appendTo(`#${formId} .results-apm`);
                    $(`#${formId} .results-apm`).append(answerBar);
                }
                setTimeout(() => {
                    $('.answer-percent').each(function() {
                        let percent = $(this).attr('data-percent')
                        $(this).css({
                            width: (percent || 1) + '%'
                        });
                        setTimeout(() => {
                            $(this).text(`${percent>5?percent+'%':''}`)
                        }, 500);
                    })
                }, 100);
            }
        });
    }
    $('.ays-poll-btn.choosing-btn').on('click', choose);
    $('.ays-poll-btn.rating-btn').on('click', rate);
    $('.ays-poll-btn.voting-btn').on('click', vote);
})(jQuery)