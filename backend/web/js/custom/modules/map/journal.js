/**
 * by Maxim Shumakov 2017-04-14
 */

     /**
      *  Начало описания сущностей журнала
      */

/*
##################################################################
*/

var Journal = {
    init: function(){
        Journal.main();
    },
    main: function(){
        var state = {
            on: function() {
                $('#map').css({
                    'width': '100%',
                    'height': '100vh'
                });
                $('.panel-last-event').css('display','block');
            },
            off: function() {
                $('#map').css({
                    'width': '100%',
                    'height': '100vh'
                });
                $('.panel-last-event').css('display','none');
            }
        }
        if($('.panel-last-event').is(":visible") == false) {
            state.off();
        } else {
            state.on();
        }

        $('.close-panel-j').click(function() {
            $('.close-panel-list').css('display','none');
        });
    }
}

// TODO: Установить модуль включения для "склеивания" js файлов

/*
##################################################################
 */

     /**
      *  Конец описания сущностей журнала
      */
