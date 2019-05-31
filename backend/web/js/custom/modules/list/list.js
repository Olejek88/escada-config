/**
 * by Maxim Shumakov 2017-04-14
 */

     /**
      *  Начало описания сущностей наряда
      */

/*
##################################################################
*/

var List = {
    init: function(){
        List.main();
    },
    main: function(){
        List.open();
        List.close();
        List.info();
    },
    open: function(){
        if (window.location.pathname !== '/'){
            $('.push-orders').click(function() {
                return window.location = '/';
            });
            $('.push-objects').click(function() {
                return window.location = '/';
            });
            $('.push-analytics').click(function() {
                return window.location = '/';
            });
        }
    },
    close: function(){
        $('.close-box-menu').click(function(){
            $('.box-ord').css('display','none');
        });
    },
    url: function(name){
        if (window.location.pathname === '/'){
            Load.refreshList();
        } else {
            return window.location = '/';
        }
    },
    info: function() {
        $('.info-about-element').click(function(){
            // $('.data-info-block').css('display', 'block');
        });
    },
}

// TODO: Установить модуль включения для "склеивания" js файлов

/*
##################################################################
 */

     /**
      *  Конец описания сущностей наряда
      */
