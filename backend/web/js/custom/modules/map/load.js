/**
 * by Maxim Shumakov 2017-04-14
 */

     /**
      *  Загрузка
      */

/*
##################################################################
*/

var Load = {
    init: function(){
        Load.main();
    },
    main: function(){
        Load.refreshJournal();
        Load.refreshList();
        Load.url();
    },
    refreshJournal: function() {
        /**
         * [Синхронизация данных и отображение журнала]
         * @return {[type]} [description]
         */
        $('.sync').click(function() {
            $('.glyphicon-refresh').css({
                'transform':'rotate(720deg)',
                'transition': 'all 1s ease-in-out',
                '-webkit-transition': 'all 1s ease-in-out',
                '-moz-transition': 'all 1s ease-in-out',
                '-o-transition': 'all 1s ease-in-out'
            });
            $("#refresh").click();
            $('.spin').css({
                'display': 'block',
                'top': '90%',
                'left': '47%'
            });
            $('.close-panel-journal').css('display','none');

            $('#map').css({
                'width': '100%',
                'height': '100vh'
            });

            $('.spin').css('display', 'none');
            $('#map').css({
                'width': '100%',
                'height': '100vh'
            });
            $('.panel-last-event').css('display','block');
            $('.close-panel-list').css('display','block');
            console.log('Синхронизация завершена.');

            console.log('Данные синхронизируются...');
        });

        /**
         * [Закрытие журнала]
         * @return {[type]} [description]
         */
        $('.close-panel-journal').click(function() {
            $('#map').css({
                'width': '100%',
                'height': '100vh'
            });
            $('.panel-last-event').css('display','none');
            $('.close-panel-journal').css('display','none');
        });
    },
    refreshList: function() {
        if (window.location.pathname !== '/'){
            $('.active-orders, .active-equpments').click(function() {
                return window.location = '/';
            });
        } else {
            $('.active-orders').click(function() {
                $("#refreshList").click();
                $('.spin').css({
                    'display': 'block',
                    'top': '25%',
                    'left': '10%'
                });
                $('.orders-list-all').css('display','none');
                $('.equipment-list-all').css('display','none');


                $('.spin').css('display', 'none');
                $('.orders-list-all').css('display','block');
                console.log('Синхронизация завершена.');

                console.log('Данные синхронизируются...');
            });

            $('.active-equpments').click(function() {
                $("#refreshList").click();
                $('.spin').css({
                    'display': 'block',
                    'top': '25%',
                    'left': '10%'
                });
                $('.orders-list-all').css('display','none');
                $('.equipment-list-all').css('display','none');

                $('.spin').css('display', 'none');
                $('.equipment-list-all').css('display','block');
                console.log('Синхронизация завершена.');

                console.log('Данные синхронизируются...');
            });
        }
    },
    url: function() {
        // if (window.location.pathname !== '/'){
        //     $('.sync').click(function() {
        //         $('.glyphicon-refresh').css({
        //             'transform':'rotate(720deg)',
        //             'transition': 'all 3s ease-in-out',
        //             '-webkit-transition': 'all 3s ease-in-out',
        //             '-moz-transition': 'all 3s ease-in-out',
        //             '-o-transition': 'all 3s ease-in-out',
        //         })
        //     });
        // }
    }
};

// TODO: Установить модуль включения для "склеивания" js файлов

/*
##################################################################
 */

     /**
      *  Конец
      */
