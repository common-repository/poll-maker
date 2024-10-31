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

    function vote() {
        let btn = $(this);
        let pollId = btn.attr('data-id');
        let formId = btn.attr('data-form');
        let data = $('#' + formId).serializeFormJSON();
        if (!('answer' in data)) return;
        btn.off()
        console.log('katarvec_widget');

        data.action = 'ays_finish_poll';
        data.poll_id = pollId;
        $.post({
            url: poll_maker_ajax_public.ajax_url,
            dataType: 'json',
            data,
            success: function(res) {
                $('.answer-' + formId).remove();
                btn.remove();
                $('#' + formId).append(`<div class="results-apm"></div>`);
                let votesSum = res.answers.reduce((a, b) => a + +b.votes, 0);
                for (const answer of res.answers) {
                    let answerDiv = $(`<div class="answer-title flex-apm"></div>`)
                    let width = (answer.votes * 100 / votesSum).toFixed(0);
                    let answerBar = $(`<div class="answer-percent">${width>5?width+'%':''}</div>`)
                    answerBar.css({
                        width: width + '%'
                    })
                    answerDiv.append(`<span class="answer-text">${answer.answer.stripSlashes()}</span><span class="answer-votes">${answer.votes}</span>`).appendTo(`#${formId} .results-apm`);
                    $(`#${formId} .results-apm`).append(answerBar);
                }

            }
        });
        return false;
    }
    $('.ays-poll-vote-btn').on('click', vote);
    // let eventsClick = $._data($('.ays-poll-vote-btn').get(0), 'events').click;
    // if (eventsClick.length > 0) return
    // else $('.ays-poll-vote-btn').on('click', vote)
    // if (aysPollMakerEventAdded) return
    // else {
    //     $('.ays-poll-vote-btn').on('click', vote)
    //     window.aysPollMakerEventAdded = true;
    // }

})(jQuery)