var setupTimer;

$(function() {

    $('body.autorefresh').each(function() {
        var body = $(this);
        var refreshInterval;
        var refreshHash = body.attr('data-refresh-hash');
        var refreshUrl = body.attr('data-refresh-url');
        refreshInterval = window.setInterval(function() {
            $.get(refreshUrl, null, function(hash) {
                if (hash != refreshHash) {
                    window.clearInterval(refreshInterval);
                    window.location.reload();
                }
            });
        }, 600000);
    })

    $('.fx-autosubmit').each(function() {
        var form = $(this);
        form.find('input').change(function() {
            form.submit();
        });
    });

    setupTimer = function() {
        $('.fx-end-of-preliminary-proceedings').each(function() {
            var source = $(this);
            var display = source.after('<p class="type-huge highlighted jollylodger mrgn-top-6 mrgn-btm-6"></p>').next();
            var endOfPreliminaryProceedings = parseInt(source.attr('data-end-of-preliminary-proceedings'));
            source.removeClass('fx-end-of-preliminary-proceedings');

            var timerInterval;

            var updateTimer = function() {
                var time = new Date();
                var left = endOfPreliminaryProceedings - Math.ceil(time.getTime() / 1000);

                if (left <= 0) {
                    window.clearInterval(timerInterval);
                    window.location.href = '/game/status/';
                    return;
                }

                var seconds = left % 60;
                var minutes = (left - seconds) / 60;
                if (seconds < 10) seconds = '0' + seconds;
                if (minutes < 10) minutes = '0' + minutes;
                display.text(minutes + ':' + seconds);
            };

            updateTimer();
            timerInterval = window.setInterval(updateTimer, 10);
        })
    };
    setupTimer();

});
