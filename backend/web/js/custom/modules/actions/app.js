/**
 * by Maxim Shumakov 2017-04-14
 */

     /**
      *  Начало описания системы
      */

/*
##################################################################
*/

/**
 * [App - основная сущность системы]
 * @type {Object}
 */
var App = {
    // private property and methods
    init: function() {
        $(window).on('load', function () {
/*
            var $preloader = $('#page-preloader'),
                $spinner   = $preloader.find('.spinner');
            $spinner.fadeOut();
            $preloader.delay(1300).fadeOut('slow');
*/
        });

        $('.settings-model').click(function() {
            $('.model-settings').modal('show');
        });

        $('.search-model').click(function() {
            if ($('.model-search').modal('show')) {
                $('#order-filtr-input').focus();
            }
        });

        $('.push-orders').click(function() {
            $('.model-orders').modal('show');
        });

        $('.push-objects').click(function() {
            $('.model-objects').modal('show');
        });

        $('.push-analytics').click(function() {
            $('.model-analytics').modal('show');
        });

        $('.list-tasks').click(function() {
            $('.model-settings').modal('show');
        });

        /**
         * Добавлена возможность конфигурирования выбора пользователя
         */
        $('#authorUuid').on('mousedown', function(){

            $('#authorUuid').hide();

            $('.model-search-author').modal('show');

            $('#orders-authorUuid').on("change",function() {

                var uuid = $(this).val();
                var user = $("#orders-authorUuid option:selected").text();

                $('#authorUuid').val(uuid);
            });

            $('#authorUuid').show();
        });

        $('#userUuid').on('mousedown', function(){

            $('#userUuid').hide();

            $('.model-search-user').modal('show');

            $('#orders-userUuid').on("change",function() {

                var uuid = $(this).val();
                var user = $("#orders-userUuid option:selected").text();

                $('#userUuid').val(uuid);
            });

            $('#userUuid').show();
        });


        /**
         * [Enabling Entities]
         */
         Communications.init();
    },
    start: function() {
        $(document).ready(function(){
            App.init();
        });
    }
}

/**
 * [Vision - вспомогательная сущность системы]
 * @type {Object}
 */
var Vision = {
    // public property and methods
}

/**
 * [Logger - вспомогательная сущность системы]
 * @type {Object}
 */
var Logger = {
    // protected property and methods
}

/*
##################################################################
 */

     /**
      *  Конец описания системы
      */

/**
 * Запуск системы
 */
App.start();
