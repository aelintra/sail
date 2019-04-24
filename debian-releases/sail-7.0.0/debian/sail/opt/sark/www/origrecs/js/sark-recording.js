$(document).ready(function() {
    $('.nojs').removeClass('nojs');
 //   $(".tip-tip").tipTip();

    var player = $('#player');

    /* Zebra tables */
    $.fn.stripeTable = function() {
        $('table tr:nth-child(odd)', this).addClass('alt');
        return this;
    };

    $(document).stripeTable();

    $('.datepicker').datepicker({
        minDate: '-12M',
        maxDate: 0,
        dateFormat: 'dd-mm-yy'
    });

    $dialog = $('<div></div>')
        .html('Please wait while your request is processed')
        .append(
            $('<div/>').progressbar({value:100})
                .children('.ui-progressbar-value')
                .css({
                    'background-image':'url(images/pbar-ani.gif)',
                    'height':'1em',
                    'margin':'2em 0 0.5em 0'
                })
        )
        .dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) { $('.ui-dialog-titlebar-close', ui.dialog).hide(); },
            title: 'Loading'
    });


    $(document).ajaxStart(function() {
        $dialog.dialog('open');
    }).ajaxStop(function() {
        $dialog.dialog('close');
    });



    normaliseMinutes = function(mins) {
        hours = Math.floor(mins / 60);
        mins -= (hours*60);
        return (hours < 10 ? '0' + hours : hours) + ':' + (mins < 10 ? '0' + mins : mins);
    };


    $('#slider-range').slider({
            range: true,
            min: 0,
            max: 1439,
            values: [ 0, 1439 ],
            slide: function( event, ui ) {
                start = normaliseMinutes(ui.values[ 0 ]);
                end = normaliseMinutes(ui.values[ 1 ]);
                $( "#amount" ).val( start + " - " + end );

                $('input[name=start_time]').val(start);
                $('input[name=end_time]').val(end);
            }
    });
    $('#amount').val( normaliseMinutes($('#slider-range').slider( 'values', 0 )) +
        ' - ' + normaliseMinutes($('#slider-range').slider( 'values', 1 )) );

    $('input[name=start_time]').val(normaliseMinutes($('#slider-range').slider( 'values', 0 )));
    $('input[name=end_time]').val(normaliseMinutes($('#slider-range').slider( 'values', 1 )));


    $('#trigger').toggle(
                function() {
                    $('span', this).text('show').first().removeClass('ui-icon-arrowthick-1-n').addClass('ui-icon-arrowthick-1-s')
                        .parent().toggleClass('active').next().slideUp('slow');
                },
                function() {
                    $('span', this).text('hide').first().removeClass('ui-icon-arrowthick-1-s').addClass('ui-icon-arrowthick-1-n')
                        .parent().toggleClass('active').next().slideDown('slow');
                }
    );

    $('form').submit(function(e) {
        e.preventDefault();

        player.jPlayer('stop');

        postData = $('input').serializeArray();
        postData.push({'name':'ajax', 'value':true});

        $('#results').slideUp().load($(this).attr('action'), postData, function() {
            $(this).stripeTable().slideDown().find('.ui-icon-stop').addClass('ui-state-disabled');
        });
    });


    player.jPlayer({
        swfPath: 'js/jPlayer/',
        supplied: 'wav',
        preload: 'none'
    }).bind($.jPlayer.event.play, function() {
        $('.playing').removeClass('playing').find('.ui-icon-stop').addClass('ui-state-disabled');
        $('.player.ui-state-active').parent().parent().addClass('playing').find('.ui-icon-stop').removeClass('ui-state-disabled');
    }).bind($.jPlayer.event.ended, function() {
        $('.playing').removeClass('playing');
        $('.player.ui-state-active').removeClass('ui-state-active').next().addClass('ui-state-disabled');
    });


    $('#results .ui-icon').live('hover', function() {
        $(this).toggleClass('ui-state-hover');
    });

    $('.player.ui-icon-play').live('click', function(event) {
        event.preventDefault();

        if ($(this).hasClass('ui-state-active') == false) {
            player.jPlayer('stop');

            player.jPlayer('setMedia', {
                wav: $(this).siblings('a:last').attr('href')
            });
        }

        $('.player').removeClass('ui-state-active ui-icon-pause').addClass('ui-icon-play').next().addClass('ui-state-enabled');
        $('.playing').removeClass('playing').find('.ui-icon-stop').addClass('ui-state-disabled');

        $(this).removeClass('ui-icon-play').addClass('ui-icon-pause ui-state-active');

        player.jPlayer('play');
    });


    $('.player.ui-icon-pause').live('click', function(event) {
        event.preventDefault();

        player.jPlayer('pause');
        $(this).removeClass('ui-icon-pause').addClass('ui-icon-play');
    });

    $('#results .ui-icon-stop').live('click', function(event) {
        event.preventDefault();
        if ($(this).hasClass('ui-state-disabled')) return;

        $(this).addClass('ui-state-disabled');
        $('.playing').removeClass('playing').find('.ui-icon-pause').removeClass('ui-state-active ui-icon-pause').addClass('ui-icon-play');
        player.jPlayer('stop');
    });

});