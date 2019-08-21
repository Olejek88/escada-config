(function ($) {
  $.fn.datasort = function(options) {
    
    var defaults = {
          dataType    : 'text',               //defined in rel of each TH
          sortDown    : 'sort_dn',            //sort down class
          sortUp      : 'sort_up',            //sort up class
          sortNone    : 'sort_none',          //no sort class
          sortAll     : 'sort_all',           //all table sort class- defined only if included
          pages       : 'pages',              //paging table class
          curPage     : 'curPage',            //current selected page class
          chgPage     : 'chgPage',            //unselected page class
          selPage     : 'selPage',            //page select class- all paging links should have this class
          pre         : 'ds_',                //column names prefix (ex: ds_symbol)
          zebraEven   : 'qb_shad',            //even rows class
          zebraOdd    : 'qb_line',            //odd rows class
          hidden      : 'noView',             //hidden rows used for multiple page javascript sorting
          noSort      : 'sort_disabled',      //disabled column sort class
          tablePre    : 'dt',                 //datatable prefix (id='dt1')
          ajaxSort    : '/datasort.php',      //script to use for AJAX sorting
          flipcharts  : null                  //flipcharts form ID
          },
        settings = $.extend({}, defaults, options),
        months = {
          JAN         : 0,
          FEB         : 1,
          MAR         : 2,
          APR         : 3,
          MAY         : 4,
          JUN         : 5,
          JUL         : 6,
          AUG         : 7,
          SEP         : 8,
          OCT         : 9,
          NOV         : 10,
          DEC         : 11
        },
        dsort = function(a, b) {
          var get = function (i) {
            return $(i).children(settings.sortElement).text();
          };

          a = parseCell($.trim(get(a)));
          b = parseCell($.trim(get(b)));

          var o = settings.order;
          if(a == null) return 1*o;
          else if(b == null) return -1*o;
          return (a < b) ? (1*o) : (a > b) ? (-1*o) : 0;
        },
        parseCell = function(s) {
          //data type set in rel attribute of th
          if(!s) return null;

          //special cases that need to be removed
          s = s.replace(/N\/A/i, '0');

          //datatype definitions used for JS sorting
          switch(settings.dataType) {
            case 'price':
              t = s.replace(/,|%/g, '')
                .replace(/^(\d+)[\-|\.](\d+)s?$/g, '\$1.\$2');
              return parseFloat(t);
            case 'int':
              t = s.replace(/,/g, '');
              return parseFloat(t);
            case 'rates':
              t = s.replace(/,|%/g, '');
              return parseFloat(t);
            case 'decimal':
              t = s.replace(/,/g, '');
              return parseFloat(t);
            case 'sdecimal': // shortened decimal divided and added k M ect.
              t = s.replace(/^(\d+)[\.](\d+)[K|M]$/gi, '\$1.\$2');
              t = parseFloat(t);
              if(s.indexOf('K') >= 0) t *= 1000;
              else if(s.indexOf('M') >= 0) t *= 1000000;
              return t;
            case 'change':
              t = s.replace(/^(unch)$/i, '0')
                  .replace(/^([\-|\+]?)(\d+)[\-|\.](\d+)s?$/g, '\$1\$2.\$3');
              return parseFloat(t);
            case 'pctchange':
              t = s.replace(/^(unch)$/i, '0')
                  .replace(/,|%/g, '');
              return parseFloat(t);
            case 'percent':
              t = s.replace(/,|%$/, '');
              return parseFloat(t);
            case 'decimalPercent':
              t = s.replace(/,|%$/, '');
              return parseFloat(t);
            case 'time':
              if((t = s.match(/^(\d{2}):(\d{2})$/))) {
                d = new Date();
                d.setHours(t[1]);
                d.setMinutes(t[2]);
                return d.getTime();
              } else if((t = s.match(/^(\d{2})\/(\d{2})\/(\d{2})$/))) {
                d = new Date(t[3], t[1], t[2]);
                return d.getTime();
              } else return 0;
            case 'futuremonth':
              t = s.match(/^([A-Z]{3})\s(\d{2})$/i);
              if(t) {
                m = t[1].toUpperCase();
                d = new Date(t[2], months[m]);
                return d.getTime();
              } else return 0;
            case 'contract':
              t = s.replace(/^([A-Z0-9]{2})([A-Z]{1})([0-9]{2})\s\(.*\)$/i, '\$1\$3\$2');
              return t.toUpperCase();
            case 'strike':
              t = s.replace(/^(\d+)[\-|\.](\d+)$/g, '\$1.\$2');
              return parseFloat(t);
            case 'bolddate':
              t = s.replace(/^(unch)$/i, '0')
                  .replace(/(,|%)/, '')
                  .replace(/^[\+|\-](\d+)[\-|\.](\d+)$/g, '\$1.\$2');
              return parseFloat(t);
            case 'opinion':
              t = s.replace(/^(Hold)$/i, '0')
                  .replace(/^(\d+)% Buy$/i, '\$1')
                  .replace(/^(\d+)% Sell$/i, '\-\$1');
              return parseFloat(t);
            case 'signal':
              t = s.replace(/^Buy$/i, '-1')
                  .replace(/^Hold$/i, '0')
                  .replace(/^Sell$/i, '1');
              return parseFloat(t);
            case 'signalrating':
              t = s.replace(/%$/, '');
              return parseFloat(t);
            case 'strength':
              t = s.replace(/^\s$/, '0')
                  .replace(/^Minimum$/i, '1')
                  .replace(/^Weak$/i, '2')
                  .replace(/^Average$/i, '3')
                  .replace(/^Strong$/i, '4')
                  .replace(/^Maximum$/i, '5');
              return parseFloat(t);
            case 'direction':
              t = s.replace(/^\s$/, '0')
                  .replace(/^(Bearish|Weakest)$/, '1')
                  .replace(/^(Falling|Weakening)$/, '2')
                  .replace(/^(Steady|Average)$/, '3')
                  .replace(/^(Rising|Strengthening)$/, '4')
                  .replace(/^(Bullish|Strongest)$/, '5');
              return parseFloat(t);
            case 'money':
              t = s.replace(/^\$/, '')
                  .replace(/^(\d+)[\-|\.](\d+)$/g, '\$1.\$2');
              return parseFloat(t);
            case 'moneychange':
              t = s.replace(/(,|\$)/, '')
                  .replace(/^[\-]?(\d+)[\-|\.](\d+)$/g, '\$1.\$2');
              return parseFloat(t);
            case 'portfolioprice':
              t = s.replace(/^(\d+)[\-|\.](\d+)$/g, '\$1.\$2');
              return parseFloat(t);
            case 'portfoliotime':
              if((t = s.match(/^(\d{2}):(\d{2})[\s\(L\)]?$/))) {
                d = new Date();
                d.setHours(t[1]);
                d.setMinutes(t[2]);
                return d.getTime();
              } else if((t = s.match(/^(\d{2})\/(\d{2})\/(\d{2})[\s\(L\)]?$/))) {
                d = new Date(t[3], t[1], t[2]);
                return d.getTime();
              } else return 0;
            default:
              return s.toUpperCase();
          }
        },
        loading = function(t) {
          $('#l' + t).css({
            opacity: 0.5,
            top: $('table#' + t).offset().top,
            width: $('table#' + t).outerWidth(),
            height: $('table#' + t).outerHeight()
          }).toggle();
        },
        editClasses = function(t, p, s) {
          t.parent().find('th:not(.' + settings.noSort + ')').removeClass(settings.sortUp).removeClass(settings.sortDown).addClass(settings.sortNone);
          t.removeClass(settings.sortNone).addClass(s);
          if(p > 0) {
            $('.' + settings.curPage).removeClass(settings.curPage).addClass(settings.chgPage);
            $('.' + settings.selPage + '[data-pageNum="' + p + '"]').removeClass(settings.chgPage).addClass(settings.curPage);
          }
        },
        getQs = function() {
          var a = window.location.search.substr(1).split('&');
          var b = {};
          for (var i = 0; i < a.length; ++i) {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
          }
          return b;
        },
        changeUrl = function(t, p) {
          var f = window.location.pathname; 
          var q = getQs();
          var i = 0;
          var re = new RegExp(settings.tablePre);
          t = t.replace(re, '');
          if(p == 1) delete q['_dtp' + t];
          else q['_dtp' + t] = p;
          for(o in q) {
            if(i > 0) f += '&';
            else f += '?';
            f += o + '=' + q[o];
            i++;
          }
          window.history.replaceState(null, this.title, f);
        },
        extractSortClass = function(c) {
          var iop = c.indexOf(settings.pre);
          var sp = c.indexOf(' ', iop);
          c = (sp > 0) ? c.substring(iop, sp) : c.substring(iop);
          return c;
        }

    this.each(function() {
      $('thead th:not(.' + settings.noSort + ')').each(function() {
        $(this).trigger('click');
      });
    })

    $('thead th:not(.' + settings.noSort + ')').bind('sort', function() {
        var $table = $(this).parent().parent().parent().attr('id');
        var that = $('table#' + $table + ' tbody tr');

        loading($table);
        var $t = $(this);
        settings.dataType = $t.attr('rel');
        var s = settings.sortDown;
        var p = $('.' + settings.curPage).attr('data-pageNum');
        var sz = $('table#' + $table).attr('data-pageSize');
        settings.order = 1;
        
        if($t.hasClass(settings.sortDown)) {
          s = settings.sortUp;
          settings.order = -1;
        } else if(!($t.hasClass(settings.sortUp)) && 
                    (settings.dataType == 'text' || settings.dataType == 'signal')) {
          s = settings.sortUp;
          settings.order = -1;
        }

        var c = extractSortClass($t.attr('class'));
        syms = "";
        if($('table#' + $table).hasClass('js')) { //javascript sort
          settings.sortElement = 'td.' + c;
          that.sort(dsort);
          regex = new RegExp($table + '_', 'g');
          $.each(that, function(index, element) { 
            var $e = $(element);
            //Update flip charts symbol order
            if(settings.flipcharts != null) {
              if(syms != "") syms += ",";
              syms += $e.attr('id').replace(regex, '');
            }
            //End flip charts update
            if(index % 2 == 0)
              $e.find('td').removeClass(settings.zebraOdd).addClass(settings.zebraEven);
            else
              $e.find('td').removeClass(settings.zebraEven).addClass(settings.zebraOdd);

            $e.removeClass(settings.hidden);
            if(p > 0)
              if(index < ((p-1)*sz) && index >= (p*sz))
                $e.addClass(settings.hidden);

            that.parent().append($e); 
          });
          $('#' + settings.flipcharts + ' input[name=symbols]').val(syms); //Update flip charts symbol order
          loading($table);
          editClasses($t, p, s);
        } else { //ajax call to specified script sort
          info = $('table#' + $table).attr('data-info');
          rules = $('table#' + $table).attr('data-fieldRules');
          extra = $('table#' + $table).attr('data-extraFields');
          var links = false;
          if($('table#' + $table + ' thead th.' + settings.pre + 'links').length > 0)
            links = true;

          r = new RegExp(settings.pre)
          c = c.replace(r, '');
          if(settings.order < 0) c = '+' + c;
          else c = '-' + c;
          $.post(settings.ajaxSort, { dt_info : info, dt_rules: rules, dt_extra: extra, page: p, pageSz: sz, sort: c, links: links }, function(data) {
            loading($table);
            $('table#' + $table + ' tbody').html(data);

            //Update flip charts symbol order
            if(settings.flipcharts != null) {
              regex = new RegExp($table + '_', 'g');
              var tbl = $.makeArray($('table#' + $table + ' tbody tr'));
              $.each(tbl, function(index, element) {
                var $e = $(element);
                if(syms != "") syms += ",";
                syms += $e.attr('id').replace(regex, '');
              });
              $('#' + settings.flipcharts + ' input[name=symbols]').val(syms);
            }
            //End flip charts update

            editClasses($t, p, s);
          });
        }
        if(p) changeUrl($table, p);
    });

    $('thead th:not(.' + settings.noSort + ')').click(function() {
        //For multiple table sort on same page
        if($(this).parent().parent().parent().hasClass(settings.sortAll)) {
          var c = extractSortClass($(this).attr('class'));
          $('.' + c).each(function() {
            $(this).trigger('sort');
          });
        } else $(this).trigger('sort');
    });

    //pagination
    $('.' + settings.selPage + ':not(.' + settings.curPage + ')').live('click', function() {
      $pages = $(this).parent().parent().parent().parent();

      tableId = $pages.attr('data-tableId');
      loading(tableId);
      
     var pageSize = $('table#' + tableId).attr('data-pageSize');
     var pageNum = $(this).attr('data-pageNum');

     syms = "";
     regex = new RegExp(tableId + '_', 'g');
     if($('table#' + tableId).hasClass('js')) { //javascript paging, uses hidden rows
       var start = (pageNum-1) * pageSize;
       var end = pageNum * pageSize;
       var tbl = $.makeArray($('table#' + tableId + ' tbody tr'));
       $.each(tbl, function(i, e) {
         $e = $(e);
         $e.removeClass(settings.hidden);
         if(pageNum > 0)
           if((i-3) < start || (i-3) >= end) 
             $e.addClass(settings.hidden);
         //Update flip charts symbol order
          if(settings.flipcharts != null) {
            if(syms != "") syms += ",";
            syms += $e.attr('id').replace(regex, '');
          }
          //End flip charts update
       });
       $('#' + settings.flipcharts + ' input[name=symbols]').val(syms);
       loading(tableId);
     } else { //ajax paging, requests the page from specified script
        info = $('table#' + tableId).attr('data-info');
        rules = $('table#' + tableId).attr('data-fieldRules');
        extra = $('table#' + tableId).attr('data-extraFields');
        var links = false;
        if($('table#' + tableId + ' thead th.' + settings.pre + 'links').length > 0)
          links = true;
        
        s = settings.sortUp;

        c = $('table#' + tableId + ' thead th.' + settings.sortUp).attr('class');

        if(c == undefined) {
          c = $('table#' + tableId + ' thead th.' + settings.sortDown).attr('class');
          s = settings.sortDown;
        }
        if(c != undefined) {
          c = extractSortClass(c);
          r = new RegExp(settings.pre)
          c = c.replace(r, '');
          if(s == settings.sortUp) c = '+' + c;
          else c = '-' + c;
        }
        $.post(settings.ajaxSort, { dt_info : info, dt_rules: rules, dt_extra: extra, page: pageNum, pageSz: pageSize, sort: c, links: links }, function(data) {
          loading(tableId);
          $('table#' + tableId + ' tbody').html(data);

          //Hide links column if greater than large table size
          largeTable = $('table#' + tableId).attr('data-largeTable');
          if($('table#' + tableId + ' tbody tr').length > largeTable)
            $('table#' + tableId + ' thead th.' + settings.pre + 'links').hide();
          else {
            $linksHead = $('table#' + tableId + ' thead th.' + settings.pre + 'links');
            if($linksHead.length)
              $linksHead.show();
            else if(links) {
              $('table#' + tableId + ' thead tr').append('<th align="center" class="ds_links sort_disabled noprint">Links</th>')
            }
          }

          //Update flip charts symbol order
          if(settings.flipcharts != null) {
            regex = new RegExp(tableId + '_', 'g');
            var tbl = $.makeArray($('table#' + tableId + ' tbody tr'));
            $.each(tbl, function(index, element) {
              var $e = $(element);
              if(syms != "") syms += ",";
              syms += $e.attr('id').replace(regex, '');
            });
            $('#' + settings.flipcharts + ' input[name=symbols]').val(syms);
          }
          //End flip charts update
        });
     }
     $('.' + settings.curPage).removeClass(settings.curPage).addClass(settings.chgPage);
     $(this).removeClass(settings.chgPage).addClass(settings.curPage);

     changeUrl(tableId, pageNum);
    });
  };
})(jQuery);