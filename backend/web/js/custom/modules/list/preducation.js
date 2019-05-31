/**
 * by Maxim Shumakov 2017-04-14
 */

     /**
      *  Начало описания сущностей прогнозирования
      */

/*
##################################################################
*/

var PredictionTableData = {
    init: function(){
        // PredictionTableData.table();
    },
    table: function(){
        var url = 'http://admin.test.loc/prediction';
        var urlHome = document.location.href;
        if(url === urlHome) {
            $('#example').DataTable();
        }
    }
}

var PredictionObject = {
    init: function(){
        PredictionTableData.init();
    }
}

// TODO: Установить модуль включения для "склеивания" js файлов

/*
##################################################################
 */

     /**
      *  Конец описания сущностей журнала
      */
