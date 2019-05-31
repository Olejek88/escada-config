/**
 * by Maxim Shumakov 2017-04-14
 */

     /**
      *  Начало описания основных сущностей js-системы
      */

/*
##################################################################
*/

var Communications = {
    init: function() {
        // Объединение зависимых сущностей
        Communications.list();
        Communications.maps();
        Communications.profiles();
    },
    list: function() {
        List.init();
    },
    maps: function() {
        // Указать необходимые объявления
        Journal.init();
        Load.init();
        Result.init();
    },
    profiles: function() {
        // ProfilePanel.init();
    }

}

/*
##################################################################
 */

     /**
      *  Конец описания основных сущностей js-системы
      */
