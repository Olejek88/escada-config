/**
 * by Maxim Shumakov 2017-04-24
 */

     /**
      *  Начало
      */

/*
##################################################################
*/

var Result = {
    init: function() {
        Result.main();
    },
    main: function() {
        Result.loadTasks();
    },
    loadTasks: function() {
        Result.default();

    },
    default: function() {
/*
        $('.spin').css({
            'display': 'block',
            'top': '400px',
            'left': '50%'
        });
*/
        setTimeout(function() {
//            $('.spin').css('display', 'none');
//            $('.content-body-orders').css('display','block');
            $preloader = $('#page-preloader');
            $preloader.fadeOut(1300);
            $preloader.find('.cssload-preloader').fadeOut(1300);
            console.log('Синхронизация завершена.');
        }, 500);

        console.log('Данные синхронизируются...');
    }
}

/*
##################################################################
 */

     /**
      *  Конец
      */
