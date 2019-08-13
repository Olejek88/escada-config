if (!com) {	var com = {}; }
if (!com.barchart) { com.barchart = {}; }
if (!com.ddfplus) { com.ddfplus = {}; }

com.barchart.DataMaster = function() {
	var _ajax = new AJAX();
	var _Quotes = {}; // Hashtable of Data
	var _ServerTime = null;
	var _Listeners = {}; // Hashtable of listeners
	var _ExtraFields = new Array; // Additional fields requested
	var _PortfolioID; // Portfolio ID if displaying portfolio fields or totals
	var _PortfolioTotals = {};
	var _TimeFormat = null; // Additional fields requested
	var _timeElapsed = 0;

	var _run = function(sleepTime) {
		_timeElapsed += sleepTime;
		_LoadQuotes();
	};

	var _HandleQuotes = function() {
		try {
			if ((_ajax.http.readyState == 4) && (_ajax.http.status == 200)) {
				//_Quotes = eval('(' + _ajax.http.responseText + ')');

				var responseAry = _ajax.http.responseText.split(';');
				var len = responseAry.length;
				for (var i = 0; i < len; i++) {
					var ary = unescape(responseAry[i]).split('~');
					if (ary[0] == 'totals') { //special row for changing portfolio totals
						_PortfolioTotals['totalcost'] = ary[1];
						_PortfolioTotals['totalvalue'] = ary[2];
						_PortfolioTotals['totalreturn'] = ary[3];
						_PortfolioTotals['totalpctchange'] = ary[4];
						_PortfolioTotals['dailyreturn'] = ary[5];
						_PortfolioTotals['dailypctchange'] = ary[6];

						for (var key in _PortfolioTotals) {
							// Work with DOM extensions
							if (isFunction(_PortfolioTotals[key])) continue;
							if (document.getElementById(key))
								document.getElementById(key).innerHTML = _PortfolioTotals[key];
						}
					}
					else {
						_Quotes[ary[0]] = {
							'rowkey' : ary[0],
							'symbol' : ary[1],
							'last' : ary[2],
							'displaytime' : ary[3],
							'change' : ary[4],
							'pctchange' : ary[5],
							'changedir' : ary[6],
							'high' : ary[7],
							'low' : ary[8],
							'volume' : ary[9],
							'pctchangedir' : ary[10]
						};

						var end = 11;
						if (_PortfolioID) {
							end = 15;
							_Quotes[ary[0]]['portfolio.change'] = ary[11];
							_Quotes[ary[0]]['portfolio.pctchange'] = ary[12];
							_Quotes[ary[0]]['portfolio.pnl'] = ary[13];
							_Quotes[ary[0]]['portfolio.displaytime'] = ary[14];
						}
						var len2 = ary.length
						for (var j = end; j < len2; j++) {
							_Quotes[ary[0]][_ExtraFields[j-end]] = ary[j];
						}
					}
				}

				for (var rowkey in _Quotes) {
					// Work with DOM extensions
					if (isFunction(_Quotes[rowkey])) continue;
					var sym = _Quotes[rowkey]['symbol'];
					var ary = _Listeners[sym];

					if (ary) {
						for (var rk in ary) {
							// Work with DOM extensions
							if (isFunction(ary[rk])) continue;
							try {
								if (ary[rk].newQuote) {
									ary[rk].newQuote(_Quotes[rk], rk);
								}
								else if (ary[rk].NewQuote) {
									ary[rk].NewQuote(_Quotes[rk], rk);
								}
							}
							catch (e2) { 
								//alert(e2.message);
							}
						}
					}
				}

				if (_timeElapsed < 60000)
					setTimeout(function() { _run(10000); }, 10000);
				else
					setTimeout(function() { _run(60000); }, 60000);
			}
		}
		catch (e) { 
			//alert(e.message);
		}
		
	};

	var _LoadQuotes = function() {
		try {
			var s = '';
			for (var sym in _Listeners) {
				// Work with DOM extensions
				if (isFunction(_Listeners[sym])) continue;
				for (var rowkey in _Listeners[sym]) {
					// Work with DOM extensions
					if (isFunction(_Listeners[sym][rowkey])) continue;
					s += (s ? ',' : '') + rowkey + ':' + sym;
				}
			}

			var f = '';
			for (var i = 0; i < _ExtraFields.length; i++) {
				f += (f ? ',' : '') + _ExtraFields[i];
			}

			var p = _PortfolioID;

			var str = 'keys=' + escape(s);
			if (f)
				str += '&extrafields=' + escape(f);
			if (_TimeFormat)
				str += '&timeformat=' + escape(_TimeFormat);
			if (_PortfolioID)
				str += '&portfolio=' + escape(_PortfolioID);

			if (s.length > 1) {
				//s = s.substring(1);
				_ajax = new AJAX();
				_ajax.sendAsynchronousData('/data/json-quotes.phpx', str, _HandleQuotes);
			}
		}
		catch (e) {
			//alert(e.message);
		}
	};


	return {
		addFields : function(fields) {
			for (var i = 0; i < fields.length; i++) {
				_ExtraFields.push(fields[i]);
			}
		},

		addSymbols : function(listener, rowkeys, symbols, portfolio) {
			if (!symbols)
				symbols = rowkeys;

			var len = rowkeys.length;
			for (var i = 0; i < len; i++) {
				var sym = symbols[i];
				//var symObj = new com.ddfplus.Symbol(sym);

				//if (symObj.getType() == 'future')
				//	sym = symObj.getNormalized();
				if (!_Listeners[sym])
					_Listeners[sym] = new Array();
				_Listeners[sym][rowkeys[i]] = listener;
			}
			_PortfolioID = portfolio;
		},

		changeTimeFormat : function(timeFormat) {
			_TimeFormat = timeFormat;
		},

		getQuote : function(symbol) {
			return _Quotes[symbol];
		},

		getServerTime : function() {
			return _ServerTime;
		},

		start : function() {
			setTimeout(function() { _run(10000) }, 10000);
		},

		GetNormalizedQuote : function(q) {
			var session = ((!q.getCombinedSession().getLast()) ? q.getPreviousSession() : q.getCombinedSession());
			var previous = q.getPreviousSession();

			var change = 0;
			if (session.getPrevious()) {
				change = session.getLast() - session.getPrevious();
			}

			var rawchange = change;
			var pctchange = (session.getPrevious() != 0) ? (new Number(change / session.getPrevious() * 100)).toFixed(2) : 0;

			change = com.ddfplus.Decimal2String(change, q.getBaseCode(), 'FRACTION');
			if (change.match("[1-9]")) {
				if (change.substr(0,1) != '+' && change.substr(0,1) != '-')
					change = '+' + change;
				if (pctchange.substr(0,1) != '+' && pctchange.substr(0,1) != '-')
					pctchange = '+' + pctchange;
			}
			else {
				change = 'unch';
				pctchange = '0';
			}
	
			var time = session.getTradetime();
			if (!time)
				time = session.getTimestamp();
			if (time) {
				var d = new Date(time);
				if (q.getFlag() == 's')
					time = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear().toString().substr(2);
				else
					time = d.getHours() + ':' + ((d.getMinutes() < 10) ? '0' : '') + d.getMinutes();
			}
			else
				time = "00:00";

			// TODO: format prices with thousands separator (,)
			return {
				symbol : q.getSymbol(),
				name : q.getName(),
				activesession : session,
				last : com.ddfplus.Decimal2String(session.getLast(), q.getBaseCode(), 'FRACTION'),
				change : change,
				open : (session.getOpen()) ? com.ddfplus.Decimal2String(session.getOpen(), q.getBaseCode(), 'FRACTION') : '',
				high : com.ddfplus.Decimal2String(session.getHigh(), q.getBaseCode(), 'FRACTION'),
				low : com.ddfplus.Decimal2String(session.getLow(), q.getBaseCode(), 'FRACTION'),
				volume : (session.getVolume()) ? com.barchart.util.NumberFormat(session.getVolume(),0,true) : 0,
				openinterest : (previous.getOpenInterest()) ? previous.getOpenInterest() : 0,
				pctchange : pctchange,
				previous : com.ddfplus.Decimal2String(session.getPrevious(), q.getBaseCode(), 'FRACTION'),
				lastupdate : time,
				raw : {
					change : rawchange
				}
			}
		}
	}
}

com.barchart.QuoteHighlights = function(h) {
	var _highlights = (h) ? h : new Array();

	var _run = function() {
		_CheckHighlights();		
	};

	var _CheckHighlights = function () {
		var len = _highlights.length;
		for (var i = 0; i < len; i++) {
			_highlights[i].style.backgroundColor = '';
		}

		_highlights = null; //Free up the memory
		_highlights = new Array();
	};

	return {

		doHighlight : function(el) {
			if (!el)
				return;

			var b = true;
			var len = _highlights.length;
			for (var i = 0; i < len; i++) {
				if (_highlights[i] == el)
					b = false;
			}

			if (b) {
				if (el.className.match(/qb_shad/))
					el.style.backgroundColor = '#FFFFA1';
				else
					el.style.backgroundColor = '#FFFFC1';
				_highlights.push(el);

				setTimeout(function() { _run() }, 5000);
			}
		},

		start : function() {
			_run();					
		}
	}
}

com.barchart.QuoteTableListener = function(tableid, fields) {
	if (!fields)
		fields = ['last', 'change', 'pctchange', 'volume', 'displaytime', 'open', 'high', 'low'];
	return {
		newQuote: function(q, rowkey) {
			var sym = com.ddfplus.Symbol(q.symbol).getNormalized();
			if (!rowkey)
				rowkey = sym;
			var rowid = 'dt'+tableid+'_'+rowkey;

			for (var i = 0; i < fields.length; ++i) {
				var fn = fields[i];

				// These won't change, and usually have links we don't want to overwrite
				if (fn == 'symbol' || fn == 'name')
					continue;

				var el = document.getElementById(rowid+'_'+fn);
				if (q[fn] && el) {
					var text = q[fn];

					var t1 = el.innerHTML.toLowerCase().replace(/"/g, '');
					t1 = t1.replace(/\<span class=nowrap\>(.*)\<\/span\>/, "$1");
					var t2 = text.toString().toLowerCase().replace(/"/g, '');

					if (t1 != t2) {
						el.innerHTML = text;
						Barchart.Highlights.doHighlight(el);
					}

					el = null;
				}
			}
		}
	}
}

com.barchart.PortfolioTableListener = function(tableid, fields) {
	if (!fields)
		fields = ['last', 'change', 'portfolio.change', 'portfolio.pctchange', 'portfolio.pnl'];
	return {
		newQuote: function(q, rowkey) {
			var sym = com.ddfplus.Symbol(q.symbol).getNormalized();
			if (!rowkey)
				rowkey = sym;
			var rowid = 'dt'+tableid+'_'+rowkey;

			for (var i = 0; i < fields.length; ++i) {
				var fn = fields[i];

				// These won't change, and usually have links we don't want to overwrite
				if (fn == 'symbol' || fn == 'name')
					continue;

				var el = document.getElementById(rowid+'_'+fn);
				if (fn == 'portfolio.last') //portfolio.last gets populated with same data as last
					fn = 'last';
				if (q[fn] && el) {
					var text = q[fn];

					var t1 = el.innerHTML.toLowerCase().replace(/"/g, '');
					t1 = t1.replace(/\<span class=nowrap\>(.*)\<\/span\>/, "$1");
					var t2 = text.toString().toLowerCase().replace(/"/g, '');

					if (t1 != t2) {
						el.innerHTML = text;
						Barchart.Highlights.doHighlight(el);
					}

					el = null;
				}
			}
		}
	}
}

com.barchart.ExtendedQuote = function(sym, dwm, date) {
	var _symbol = sym;
	var _dwm = ((dwm) && ((dwm == 'w') || (dwm == 'm'))) ? dwm : null;
	var _date = (date) ? date : null;

	var _xml = AJAX.RetrieveXML('/data/extquote.phpx?' + $H({sym: _symbol, dwm: _dwm, date: _date}).toQueryString());

	var getNode = function(nodeId) {
		var nodes = _xml.getElementsByTagName('item');
		var len = nodes.length;
		for (var i = 0; i < len; i++) {
			if (nodes[i].getAttribute('id') == nodeId)
				return nodes[i];
		}

		return null;
	};


	return {
		getDWM : function() {
			return ((_dwm) ? _dwm : 'd');
		},

		getEarningsItem : function(key) {
			var node = getNode('E' + key);
			if (!node)
				return null;

			return {
				date: node.getAttribute('date'),
				value: node.getAttribute('value')
			};
		},

		getHighLowData : function() {
			var res = {};
			for (var i = 1; i < 7; i++) {
				var node1 = getNode('H' + i);
				var node2 = getNode('L' + i);
				if (node1 && node2)
					res['HL' + i] = {
						high: {
							date: node1.getAttribute('date'),
							price: node1.getAttribute('price'),
							numtimes: node1.getAttribute('numtimes'),
							pctfrom: node1.getAttribute('pctfrom')
						},

						low: {
							date: node2.getAttribute('date'),
							price: node2.getAttribute('price'),
							numtimes: node2.getAttribute('numtimes'),
							pctfrom: node2.getAttribute('pctfrom'),
							pctchange: node2.getAttribute('pctchange'),
							pctchangedate: node2.getAttribute('pctchangedate')
						}
					};
				else return null;
			}
			return res;
		},

		getOpinion : function() {
			var ts = {}; // Trendspotter
			var st = {}; // Short Term
			var mt = {}; // Medium Term
			var lt = {}; // Long Term

			var node = getNode('W1');
			if (!node)
				return null;

			ts = {
				signal: node.getAttribute('signal'),
				strength: node.getAttribute('strength'),
				direction: node.getAttribute('direction')
			};

			var keys = new Array('X1', 'X2', 'X3', 'X4', 'X5', 'Y1', 'Y2', 'Y3', 'Y4', 'Z1', 'Z2', 'Z3');
			for (var i = 0; i < keys.length; i++) {
				var n = getNode(keys[i]);
				var xx1 = keys[i].substring(0, 1);
				var xx2 = keys[i].substring(1, 2);
				var arr = null;
				if (xx1 == 'X')
					arr = st;
				else if (xx1 == 'Y')
					arr = mt;
				else if (xx1 == 'Z')
					arr = lt;

				arr[xx2] = {
					signal: n.getAttribute('signal'),
					strength: n.getAttribute('strength'),
					direction: n.getAttribute('direction')
				};
			}

			st.overall = {
				signal: getNode('T1').getAttribute('signal'),
				percent: getNode('T1').getAttribute('percent')
			};

			mt.overall = {
				signal: getNode('T2').getAttribute('signal'),
				percent: getNode('T2').getAttribute('percent')
			};

			lt.overall = {
				signal: getNode('T3').getAttribute('signal'),
				percent: getNode('T3').getAttribute('percent')
			};

			var avnode = getNode('AV');
			var srnode = getNode('SR');

			return {
				'TS': ts,
				'ST': st,
				'MT': mt,
				'LT': lt,
				overall: {
					signal: getNode('TT').getAttribute('signal'),
					percent: getNode('TT').getAttribute('percent'),
					strength: getNode('TZ').getAttribute('strength'),
					direction: getNode('TZ').getAttribute('direction'),

					yesterday: {
						signal: getNode('2T').getAttribute('signal'),
						percent: getNode('2T').getAttribute('percent')
					},

					lastweek: {
						signal: getNode('3T').getAttribute('signal'),
						percent: getNode('3T').getAttribute('percent')
					},

					lastmonth: {
						signal: getNode('4T').getAttribute('signal'),
						percent: getNode('4T').getAttribute('percent')
					}
				},
				averagevolume: {
					_20day: avnode.getAttribute('vol20day'),
					_50day: avnode.getAttribute('vol50day'),
					_100day: avnode.getAttribute('vol100day')
				},
				support: {
					price: srnode.getAttribute('price'),
					pivotpoint: srnode.getAttribute('pivotpoint'),
					support: srnode.getAttribute('support'),
					resistance: srnode.getAttribute('resistance')
				}
			};
		},

		getPERatio : function() {
			var node = getNode('PE');
			if (node == null)
				return null;
			return {
				value: node.getAttribute('value')
			}
		},

		getPivotPoints : function() {
			var node = getNode('PP');
			return {
				pivotpoint: node.getAttribute('pp'),
				support1: node.getAttribute('sup1'),
				support2: node.getAttribute('sup2'),
				resistance1: node.getAttribute('res1'),
				resistance2: node.getAttribute('res2')
			};
		},

		getPriceItem : function(daykey) {
			var node = getNode('P' + daykey);
			if (node)
				return {
					date: node.getAttribute('date'),
					open: node.getAttribute('open'),
					high: node.getAttribute('high'),
					low: node.getAttribute('low'),
					close: node.getAttribute('close'),
					change: node.getAttribute('change'),
					percentchange: node.getAttribute('pctchange'),
					volume: node.getAttribute('volume')
				};
			else
				return null;
		},

		getProjections : function() {
			var node2w = getNode('PRO_2W');
			var node3w = getNode('PRO_3W');
			var node4w = getNode('PRO_4W');
			var res = [];
			if (node2w && node3w && node4w){
				res['CP'] = this.getPriceItem('1').close;
				res['2WHI'] = node2w.getAttribute('high');
				res['2WLO'] = node2w.getAttribute('low');
				res['2WR1'] = node2w.getAttribute('retr1');
				res['2WR2'] = node2w.getAttribute('retr2');
				res['2WR3'] = node2w.getAttribute('retr3');
				res['3WHI'] = node3w.getAttribute('high');
				res['3WLO'] = node3w.getAttribute('low');
				res['3WR1'] = node3w.getAttribute('retr1');
				res['3WR2'] = node3w.getAttribute('retr2');
				res['3WR3'] = node3w.getAttribute('retr3');
				res['4WHI'] = node4w.getAttribute('high');
				res['4WLO'] = node4w.getAttribute('low');
				res['4WR1'] = node4w.getAttribute('retr1');
				res['4WR2'] = node4w.getAttribute('retr2');
				res['4WR3'] = node4w.getAttribute('retr3');
				res['PP'] = getNode('PP').getAttribute('pp');
				res['PPS1'] = getNode('PP').getAttribute('sup1');
				res['PPS2'] = getNode('PP').getAttribute('sup2');
				res['PPR1'] = getNode('PP').getAttribute('res1');
				res['PPR2'] = getNode('PP').getAttribute('res2');
				res['V1S1'] = getNode('V1').getAttribute('sig1');
				res['V1S2'] = getNode('V1').getAttribute('sig2');
				res['V1S3'] = getNode('V1').getAttribute('sig3');
				res['V2S1'] = getNode('V2').getAttribute('sig1');
				res['V2S2'] = getNode('V2').getAttribute('sig2');
				res['V2S3'] = getNode('V2').getAttribute('sig3');
				res['V3S1'] = getNode('V3').getAttribute('sig1');
				res['V3S2'] = getNode('V3').getAttribute('sig2');
				res['V3S3'] = getNode('V3').getAttribute('sig3');
				res['O1S1'] = getNode('O1').getAttribute('sig1');
				res['O1S2'] = getNode('O1').getAttribute('sig2');
				res['I1S1'] = getNode('I1').getAttribute('sig1');
				res['I1S2'] = getNode('I1').getAttribute('sig2');
				res['I1S3'] = getNode('I1').getAttribute('sig3');
				res['I1S4'] = getNode('I1').getAttribute('sig4');
				res['I1S5'] = getNode('I1').getAttribute('sig5');
				res['U1S1'] = getNode('U1').getAttribute('sig1');
				res['U1S2'] = getNode('U1').getAttribute('sig2');
				res['U1S3'] = getNode('U1').getAttribute('sig3');
				res['U1S4'] = getNode('U1').getAttribute('sig4');
				res['U1S5'] = getNode('U1').getAttribute('sig5');
				res['U1S6'] = getNode('U1').getAttribute('sig6');
				res['U1S7'] = getNode('U1').getAttribute('sig7');
			}
			else
				res = null;
			return res;
		},

		getSymbol : function() {
			return _symbol;
		},

		getTechnicals : function() {
			var res = {};
			for (var i = 1; i < 7; i++) {
				var node = getNode('M' + i);
				if (!node)
					return null;
				res['M' + i] = {
					ma: node.getAttribute('ma'),
					prcchg: node.getAttribute('prcchg'),
					pctchange: node.getAttribute('pctchange'),
					avgvol: node.getAttribute('avgvol')
				}
			}

			for (var i = 1; i < 4; i++) {
				var node = getNode('S' + i);
				res['S' + i] = {
					stocr: node.getAttribute('stocr'),
					stock: node.getAttribute('stock'),
					stocd: node.getAttribute('stocd'),
					atr: node.getAttribute('atr')
				}
			}

			for (var i = 1; i < 6; i++) {
				var node = getNode('R' + i);
				res['R' + i] = {
					relstr: node.getAttribute('relstr'),
					pctr: node.getAttribute('pctr'),
					hisvol: node.getAttribute('hisvol'),
					macd: node.getAttribute('macd')
				}
			}

			return res;
		}
	}
}

com.barchart.Performance = function(symbol, signal) {
	if (!symbol)
		return;

	var _symbol = symbol;
	var _signal = signal;

	var _xml = null;
	if (_signal)
		_xml = AJAX.RetrieveXML('/data/performance.phpx?sym=' + _symbol + '&sig=' + _signal);
	else
		_xml = AJAX.RetrieveXML('/data/performance.phpx?sym=' + _symbol);

	
//	var node = _xml.getElementsByTagName('performance')[0];
//	results = node.getAttribute('res');
//	alert(results);
//	if (results == 'FAIL')
///		return {
//			getError : function() {
//				error: results;
//			}
//		}
//	else
		return {
			getError : function() {
				var node = _xml.getElementsByTagName('performance')[0];
				return {
					error: node.getAttribute('res')
				}
			},
			
			getName : function(signal) {
				switch (signal) {
					case '1':  return 'TrendSpotter';
					case '2':  return '7 Day Average Directional Indicator';
					case '3':  return '10-8  Day Moving Average Hilo Channel';
					case '4':  return '20 Day Moving Average vs Price';
					case '5':  return '20 - 50 Day MACD Oscillator';
					case '6':  return '20 Day Bollinger Bands';
					case '7':  return '40 Day Commodity Channel Index';
					case '8':  return '50 Day Moving Average vs Price';
					case '9':  return '20 - 100 Day MACD Oscillator';
					case '10': return '50 Day Parabolic Time/Price';
					case '11': return '60 Day Commodity Channel Index';
					case '12': return '100 Day Moving Average vs Price';
					case '13': return '50 - 100 Day MACD Oscillator';
					case '14': return 'Opinion 50% Buy/Sell';
				}
			},

			getOverall : function() {
				var node = _xml.getElementsByTagName('avg')[0];
				return {
					days: node.getAttribute('days'),
					trades: node.getAttribute('trades'),
					profit: node.getAttribute('profit')
				};
			},

			getSignals : function() {
				var res = new Array();
				var nodes = _xml.getElementsByTagName('ind');
				var len = nodes.length;
				for (var i = 0; i < len; i++) {
					res.push({
						name: this.getName(nodes[i].getAttribute('sig')),
						signal: nodes[i].getAttribute('sig'),
						trades: nodes[i].getAttribute('trades'),
						days: nodes[i].getAttribute('days'),
						profit: nodes[i].getAttribute('profit')
					});
				}

				return res;
			},

			getTrades : function() {
				var res = new Array();
				var nodes = _xml.getElementsByTagName('trade');
				var len = nodes.length;
				for (var i = 0; i < len; i++) {
					res.push({
						entrydate: nodes[i].getAttribute('entrydate'),
						entryprice: nodes[i].getAttribute('entryprice'),
						entryaction: nodes[i].getAttribute('entryaction'),
						exitdate: nodes[i].getAttribute('exitdate'),
						exitprice: nodes[i].getAttribute('exitprice'),
						exitaction: nodes[i].getAttribute('exitaction'),
						days: nodes[i].getAttribute('numdays'),
						profit: nodes[i].getAttribute('profit'),
						maxprofit: nodes[i].getAttribute('maxprofit'),
						maxdrawdown: nodes[i].getAttribute('maxdrawdown'),
						pctchange: nodes[i].getAttribute('pctchange')
					});
				}

				return res;
			}		
		}
}

com.barchart.support = {
	Content : {
		CreateDetailedQuotePageHTML : function(q) {
			var temp = com.barchart.support.Content.CreateQuote5HTML(q);
			if (temp)
				var html =
					com.barchart.support.Content.CreateQuote5HTML(q) + '<p />' +
					com.barchart.support.Content.CreateEarningsHTML(q) + '<p />' + 
					com.barchart.support.Content.CreateHighLowHTML(q) + '<p />' +
					com.barchart.support.Content.CreatePerformanceHTML(q);
			else
				html = '<div class="error" style="text-align: center;">In order to properly form an accurate historical quote, the equity or commodity has to have at least<br />5 days of trading activity.</div>';
			
			return html;
		},

		AddCommas : function(nStr) {
			nStr += '';
			x = nStr.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		},

		CreateEarningsHTML : function(q) {
			var earnings = new Array();
			for (var i = 1; i < 5; i++) {
				var ei = q.getEarningsItem(i);
				if (ei)
					earnings['E' + i] = ei;
			}

			if (!earnings['E1'])
				return '';

			
			var html =
				'<center>' +
				'<div class="bar" style="width:525px"><h2>Historical Earnings</h2></div>' +
				'<table width="525" border="0" class="datatable_simple mpbox">' +
				'<tr class="datatable_header">' +
				'<td align="left" class="blue13">Earnings:</td>' +
				'<td align="right" id="dtaE1Date">' + ((earnings['E1']) ? earnings['E1'].date : '') + '</td>' +
				'<td align="right" id="dtaE2Date">' + ((earnings['E2']) ? earnings['E2'].date : '') + '</td>' +
				'<td align="right" id="dtaE3Date">' + ((earnings['E3']) ? earnings['E3'].date : '') + '</td>' +
				'<td align="right" id="dtaE4Date">' + ((earnings['E4']) ? earnings['E4'].date : '') + '</td>' +
				'</tr>' +
				'<tr>' +
				'<td></td>' +
				'<td align="right" id="dtaE1Value">' + ((earnings['E1']) ? earnings['E1'].value : '') + '</td>' +
				'<td align="right" id="dtaE2Value">' + ((earnings['E2']) ? earnings['E2'].value : '') + '</td>' +
				'<td align="right" id="dtaE3Value">' + ((earnings['E3']) ? earnings['E3'].value : '') + '</td>' + 
				'<td align="right" id="dtaE4Value">' + ((earnings['E4']) ? earnings['E4'].value : '') + '</td>' +
				'</tr>' +
				'<tr><td colspan="5"></td></tr>';

			var pe = q.getPERatio();
			if (pe) {
				html += 
					'<tr>' +
					'<td colspan="2" class="blue13">Price Earnings Ratio:</td>' +
					'<td id="dtaPE">' + pe.value + '</td>' +
					'<td>&nbsp;</td>' +
					'<td>&nbsp;</td>' + 
					'</tr>';
			}

			html += '</table></center>';
			return html;
		},

		CreateHighLowHTML : function(q) {
			var data = q.getHighLowData();
			var html = 
				'<div class="bar"><h2>Historical Highs and Lows</h2></div>' +
				'<table width="100%" border="0" cellpadding="1" cellspacing="1" class="datatable_simple mpbox">' +
				'<tr class="datatable_header">' +
				'<td align="center" class="blue13">-- Period --</td>' +
				'<td align="center" colspan="2" class="blue13">-- High --</td>' +
				'<td align="center" colspan="2" class="blue13">-- Low --</td>' +
				'<td align="center" colspan="2" class="blue13">-- Percent Change --</td>' +
				'</tr>';

			var cols = null;
			if (q.getDWM() == 'm')
				cols = new Array('6-Month', '2-Year', '5-Year', '10-Year', '20-Year');
			else if (q.getDWM() == 'w')
				cols = new Array('6-Week', '6-Month', '1-Year', '2-Year', '5-Year');
			else
				cols = new Array('5-Day', '1-Month', '3-Month', '6-Month', '12 Month', 'Year to Date');

			for (var i = 0; i < cols.length; i++) {
				var d = data['HL' + (i + 1)];
				var c = (i % 2 == 0) ? 'qb_shad' : 'qb_line';
				var pctColor = (d.low.pctchange.substr(0, 1) != '-') ? (d.low.pctchange.substr(0,1) == '+' ? 'qb_up' : 'qb_unc') : 'qb_down';

				html += 
					'<tr>' +
					'<td align="left" class="' + c + '">' + cols[i] + '</td>' +
					'<td align="right" class="' + c + '" align="right" id="dtaH' + (i + 1) + 'Price">' + d.high.price + '</td>' +
					'<td align="left" class="' + c + '" id="dtaH' + (i + 1) + 'Date">&nbsp;on&nbsp;' + d.high.date + '</td>' +
					'<td align="right" class="' + c + '" align="right" id="dtaL' + (i + 1) + 'Price">' + d.low.price + '</td>' +
					'<td align="left" class="' + c + '" id="dtaL' + (i + 1) + 'Date">&nbsp;on&nbsp;' + d.low.date + '</td>' +
					'<td align="right" class="' + c + ' ' + pctColor + '" align="right" id="dtaL' + (i + 1) + 'PctChange">' + d.low.pctchange + '</td>' +
					'<td align="left" class="' + c + '" id="dtaL' + (i + 1) + 'PctChangeDate">&nbsp;since&nbsp;' + d.low.pctchangedate + '</td>' +
					'</tr>';
			}

			html += '</table>';
			return html;
		},

		CreateDetOpinionsHTML : function(q, thisView, thisSym, thisDate) {
			var getDirectionText = function(sig, dir) {
				switch(sig) {
					case 'H':
						switch(dir) {
							case '1': return 'Bearish';
							case '2': return 'Falling';
							case '3': return 'Steady';
							case '4': return 'Rising';
							case '5': return 'Bullish';
							default: '&nbsp;';
						}
						break;
					default:
						switch(dir) {
							case '1': return 'Weakest';
							case '2': return 'Weakening';
							case '3': return 'Average';
							case '4': return 'Strengthening';
							case '5': return 'Strongest';
							default: '&nbsp;';
						}
				}
			};

			var getSignalClass = function(signal) {
				switch (signal) {
					case 'B': return 'qb_up';
					case 'S': return 'qb_down';
					case 'H': return 'qb_hold';
					default: return '';
				}
			};

			var getSignalAlignment = function(signal) {
				switch (signal) {
					case 'B': return 'left';
					case 'S': return 'right';
					case 'H': return 'center';
					default: return '';
				}
			};

			var getSignalText = function(signal) {
				switch(signal) {
					case 'B': return 'Buy';
					case 'S': return 'Sell';
					case 'H': return 'Hold';
					default: return '&nbsp;';
				}
			};

			getStrengthText = function(sig, str) {
				if (sig == 'H')
					return '&nbsp;';

				switch(str) {
					case '1': return 'Minimum';
					case '2': return 'Weak';
					case '3': return 'Average';
					case '4': return 'Strong';
					case '5': return 'Maximum';
					default: return '&nbsp;';
				}
			};

			var op = q.getOpinion();
			if (!op)
				return '<div class="error" style="text-align: center;">In order to properly form an accurate opinion, the equity or commodity has to have at least<br />6 months of trading activity.</div>';

			var signals = {
				'W' : {
					description : 'Composite Indicator',
					descriptions : new Array('TrendSpotter'),
					charts: new Array('style=classic&submitted=1&ov1=66'),
					sigdata : op.TS
				},
				'X' : {
					description : 'Short Term Indicators',
					descriptions: new Array('7 Day Average Directional Indicator', '10 - 8 Day Moving Average Hilo Channel', '20 Day Moving Average vs Price', '20 - 50 Day MACD Oscillator', '20 Day Bollinger Bands'),
					charts: new Array('style=classic&submitted=1&ov1=063', 'style=classic&submitted=1&ov1=024', 'style=classic&submitted=1&ov1=021&ov1a=20&ov1b=0&ov1c=0', 'style=classic&submitted=1&ov1=051&ov1a=20&ov1b=50&ov1c=1', 'style=classic&submitted=1&ov1=029'),
					sigdata : op.ST,
					volumetext : '20-Day Average Volume - ' + com.barchart.support.Content.AddCommas (op.averagevolume._20day)
				},
				'Y' : {
					description : 'Medium Term Indicators',
					descriptions: new Array('40 Day Commodity Channel Index', '50 Day Moving Average vs Price', '20 - 100 Day MACD Oscillator', '50 Day Parabolic Time/Price'),
					charts: new Array('style=classic&submitted=1&ov1=028&ov1a=40&ov1b=40&ov1c=0', 'style=classic&submitted=1&ov1=021&ov1a=50&ov1b=0&ov1c=0', 'style=classic&submitted=1&ov1=051&ov1a=20&ov1b=100&ov1c=1', 'style=classic&submitted=1&ov1=064'),
					sigdata : op.MT,
					volumetext : '50-Day Average Volume - ' + com.barchart.support.Content.AddCommas (op.averagevolume._50day)
				},
				'Z' : {
					description : 'Long Term Indicators',
					descriptions: new Array('60 Day Commodity Channel Index', '100 Day Moving Average vs Price', '50 - 100 Day MACD Oscillator'),
					charts: new Array('style=classic&submitted=1&ov1=028&ov1a=60&ov1b=60&ov1c=0', 'style=classic&submitted=1&ov1=021&ov1a=100&ov1b=0&ov1c=0', 'style=classic&submitted=1&ov1=051&ov1a=50&ov1b=100&ov1c=1'),
					sigdata : op.LT,
					volumetext : '100-Day Average Volume - ' + com.barchart.support.Content.AddCommas (op.averagevolume._100day)
				}

			}

			var html =	'<div class="whitebox">' +
						'<div class="bar">' +
						'<div class="fl"><h2>Detailed Opinion' + 
						'</h2></div><div class="fr">';
						if (thisView == 'opinion')
							html += '<span class="link"><img src="/shared/images/dbl_arrow.gif" width="19" height="12" alt="" /><a href="/historicalquote.php?sym=' + thisSym + '&view=detailedopinion&txtDate=' + thisDate + '">Show Signal Strength and Direction</a></span>';
						else
							html += '<span class="link"><img src="/shared/images/dbl_arrow.gif" width="19" height="12" alt="" /><a href="/historicalquote.php?sym=' + thisSym + '&view=opinion&txtDate=' + thisDate + '">Remove Signal Strength and Direction</a></span>';
			html +=
						'</div>' +
						'</div>' +
						'<div class="mpbox"><table width="100%" border="0" cellpadding="2" cellspacing="1">';

			for (var k in signals) {
				// Work with DOM extensions
				if (isFunction(signals[k])) continue;
				var ary = signals[k].descriptions;
				var chart = signals[k].charts;

				html += '<tr>';
				html += '<td align="center" class="qb_shad"><strong>' + signals[k].description + '</strong></td>';
				if (k == 'W')
					if (thisView == 'detailedopinion')
						html +=
							'<td align="center" nowrap="nowrap" class="qb_shad"><strong>Signal&nbsp;</strong></td>' +
							'<td align="center" nowrap="nowrap" class="qb_shad"><strong>Strength&nbsp;</strong></td>' +
							'<td align="center" nowrap="nowrap" class="qb_shad"><strong>Direction&nbsp;</strong></td>';
					else
						html += '<td colspan="3" align="center" nowrap="nowrap" class="qb_shad"><strong>Signal</strong></td>';
				else
					html += '<td colspan="3" class="qb_shad">&nbsp;</td>';

				html += '</tr>';

				for (var i = 0; i < ary.length; i++) {
					var o = (k == 'W') ? signals[k].sigdata : signals[k].sigdata[(i + 1)];
					if (thisView == 'detailedopinion')
						html += 
							'<tr>' +
							'<td class="qb_line"><a href="/chart.php?sym=' + q.getSymbol() + '&ed=' + thisDate + '&' + chart[i] + '"><img src="/shared/images/chart_icon.gif" alt="Get Chart" title="Get Chart" width="12" height="10" border="0" /></a>&nbsp;' + 
							'<a href="/chart.php?sym=' + q.getSymbol() + '&ed=' + thisDate + '&' + chart[i] + '">' + ary[i] + '</a></td>' +
							'<td align="' + getSignalAlignment(o.signal) + '" class="' + getSignalClass(o.signal) + ' qb_line">' + getSignalText(o.signal) + '</td>' +
							'<td align="center" class="' + getSignalClass(o.signal) + ' qb_line">' + getStrengthText(o.signal, o.strength) + '</td>' + 
							'<td align="center" class="' + getSignalClass(o.signal) + ' qb_line">' + getDirectionText(o.signal, o.direction) + '</td>' +
							'</tr>';
					else {
						html +=
							'<tr>' +
							'<td class="qb_line"><a href="/chart.php?sym=' + q.getSymbol() + '&ed=' + thisDate + '&' + chart[i] + '"><img src="/shared/images/chart_icon.gif" alt="Get Chart" title="Get Chart" width="12" height="10" border="0" /></a>&nbsp;' +
							'<a href="/chart.php?sym=' + q.getSymbol() + '&ed=' + thisDate + '&' + chart[i] + '">' + ary[i] + '</a></td>';
						html += (o.signal == 'B') ? '<td align="center" class="sig_buy qb_line" style="width: 130px">' + getSignalText(o.signal) + '</td>' : '<td class="qb_line" style="width: 130px">&nbsp;</td>';
						html += (o.signal == 'H') ? '<td align="center" class="sig_hold qb_line" style="width: 130px">' + getSignalText(o.signal) + '</td>' : '<td class="qb_line" style="width: 130px">&nbsp;</td>';
						html += (o.signal == 'S') ? '<td align="center" class="sig_sell qb_line" style="width: 130px">' + getSignalText(o.signal) + '</td>' : '<td class="qb_line" style="width: 130px">&nbsp;</td>';
						html +=	'</tr>';
					}
				}

				html += '<tr><td colspan="4">&nbsp;</td></tr>';

				if (signals[k].sigdata.overall) {
					html += '<tr><td align="center" colspan="4" style="font-size: 14px;"><b>' + signals[k].description + ' Average:&nbsp;<span class="' + getSignalClass(signals[k].sigdata.overall.signal) + '">' + signals[k].sigdata.overall.percent + ' ' + getSignalText(signals[k].sigdata.overall.signal) + '</span></b></td></tr>';
					html += '<tr><td colspan="4">' + signals[k].volumetext + '</td></tr>';
					html += '<tr><td colspan="4">&nbsp;</td></tr>';
				}
			}

			html += '<tr><td align="center" colspan="4" style="font-size: 14px;"><b>Overall Average:&nbsp;<span class="' + getSignalClass(op.overall.signal) + '">' + op.overall.percent + ' ' + getSignalText(op.overall.signal) + '</span></b></td></tr>';

			html +=
				'<tr><td colspan="4">&nbsp;</td></tr>' +
				'<tr>' +
				'<td colspan="4">' +
				'<table width="50%"  border="0" align="center" cellpadding="2" cellspacing="1" style="text-align:center">' +
				'<tr>' +
				'<td width="25%" class="qb_shad"><b>Price</b></td>' +
				'<td width="25%" class="qb_shad"><b>Support</b></td>' +
				'<td width="25%" class="qb_shad"><b>Pivot Point</b></td>' +
				'<td width="25%" class="qb_shad"><b>Resistance</b></td>' +
				'</tr>' +
				'<tr>' +
				'<td class="qb_line">' + op.support.price + '</td>' +
				'<td class="qb_line">' + op.support.support + '</td>' +
				'<td class="qb_line">' + op.support.pivotpoint + '</td>' +
				'<td class="qb_line">' + op.support.resistance + '</td>' +
				'</tr>' +
				'</table>' +
				'<p />' +
				'</td>' +
				'</tr>' +
				'<tr><td colspan="4" align="center">Click on the indicator for a graphical interpretation, or visit the <a href="education/studies.php">Education Center</a> for information on the studies.</td></tr>' +
				'</table>' +
				'</div></div>';

			return html;
		},

		CreatePerformanceHTML : function(q) {
			var data = q.getHighLowData();
			var html =
				'<div class="bar"><h2>Historical New Highs and Lows</h2></div>' +
				'<table width="100%" border="0" cellpadding="1" cellspacing="1" class="datatable_simple mpbox">' + 
				'<tr class="datatable_header">' +
				'<td align="left" class="blue13">For The Last</td>' +
				'<td align="center" class="blue13">Made New High</td>' +
				'<td align="center" class="blue13">Percent From</td>' +
				'<td align="center" class="blue13">Made New Low</td>' +
				'<td align="center" class="blue13">Percent From</td>' +
				'</tr>';

			var cols = null;
			if (q.getDWM() == 'm')
				cols = new Array('6-Month', '2-Year', '5-Year', '10-Year', '20-Year');
			else if (q.getDWM() == 'w')
				cols = new Array('6-Week', '6-Month', '1-Year', '2-Year', '3-Year');
			else
				cols = new Array('5-Day', '20-Day', '65-Day', '100-Day', '260-Day', 'Year-to-Date');

			for (var i = 0; i < cols.length; i++) {
				var d = data['HL' + (i + 1)];
				var c = (i % 2 == 0) ? 'qb_shad' : 'qb_line';
				var highColor = (d.high.pctfrom.substr(0, 1) != '-') ? (d.high.pctfrom.substr(0,1) == '+' ? 'qb_up' : 'qb_unc') : 'qb_down';
				var lowColor = (d.low.pctfrom.substr(0, 1) != '-') ? (d.low.pctfrom.substr(0,1) == '+' ? 'qb_up' : 'qb_unc') : 'qb_down';
				var addPctLow = (d.low.pctfrom != 'unch') ? '%' : '';
				var addPctHigh = (d.high.pctfrom != 'unch') ? '%' : '';
				html +=
					'<tr>' +
					'<td align="left" class="' + c + '">' + cols[i] + '</td>' +
					'<td align="center" class="' + c + '" id="dtaH' + (i + 1) + 'NumTimes">' + d.high.numtimes + ' time' + ((d.high.numtimes != 1) ? 's' : '') +'</td>' +
					'<td align="center" class="' + c + ' ' + highColor + '" id="dtaH' + (i + 1) + 'PctFrom">' + d.high.pctfrom + addPctHigh + '</td>' +
					'<td align="center" class="' + c + '" id="dtaL' + (i + 1) + 'NumTimes">' + d.low.numtimes + ' time' + ((d.low.numtimes != 1) ? 's' : '') + '</td>' +
					'<td align="center" class="' + c + ' ' + lowColor + '" id="dtaL' + (i + 1) + 'PctFrom">' + d.low.pctfrom + addPctLow + '</td>' +
					'</tr>';
			}

			html += '</table>';
			return html;
		},

		CreateProjectionsHTML : function(q) {
			var prj = q.getProjections();
			if (!prj)
				return '<div class="error" style="text-align: center;">In order to properly form a Trader\'s Cheat Sheet&#8482;, the equity or commodity has to have at least<br />6 months of trading activity.</div>';

			var convertPriceText = function(n) {
				var sign = (n.charAt(0) == '-') ? -1 : 1;
				var z = n.replace(/[^0-9]/g, '')
				return (new Number(z) * sign);
			}

			var proinfo = $H({
				'CP':   { tag: 'Current Price', code: 0 },
				'2WHI':	{ tag: '52 Week High', code: -1 },
				'2WLO': { tag: '52 Week Low', code: 1 },
				'2WR1': { tag: '38.2% Retracement from 52 Week Low', code: -99 },
				'2WR2': { tag: '50% Retracement from 52 Week High/Low', code: -99 },
				'2WR3': { tag: '38.2% Retracement from 52 Week High', code: -99 },
				'3WHI':	{ tag: '13 Week High', code: -1 },
				'3WLO': { tag: '13 Week Low', code: 1 },
				'3WR1': { tag: '38.2% Retracement from 13 Week Low', code: -99 },
				'3WR2': { tag: '50% Retracement from 13 Week High/Low', code: -99 },
				'3WR3': { tag: '38.2% Retracement from 13 Week High', code: -99 },
				'4WHI':	{ tag: '4 Week High', code: -1 },
				'4WLO': { tag: '4 Week Low', code: 1 },
				'4WR1': { tag: '38.2% Retracement from 4 Week Low', code: -99 },
				'4WR2': { tag: '50% Retracement from 4 Week High/Low', code: -99 },
				'4WR3': { tag: '38.2% Retracement from 4 Week High', code: -99 },
				'PP'  : { tag: 'Pivot Point', code: -99 },
				'PPS1': { tag: 'Pivot Point 1st Level Support', code: 1 },
				'PPS2': { tag: 'Pivot Point 2nd Level Support', code: 1 },
				'PPR1': { tag: 'Pivot Point 1st Level Resistance', code: -1 },
				'PPR2': { tag: 'Pivot Point 2nd Level Resistance', code: -1 },
				'V1S1': { tag: 'Price Crosses 9 Day Moving Average', code: 99 },
				'V1S2': { tag: 'Price Crosses 18 Day Moving Average', code: 99 },
				'V1S3': { tag: 'Price Crosses 40 Day Moving Average', code: 99 },
				'V2S1': { tag: 'Price Crosses 9-18 Day Moving Average', code: 99 },
				'V2S2': { tag: 'Price Crosses 9-40 Day Moving Average', code: 99 },
				'V2S3': { tag: 'Price Crosses 18-40 Day Moving Average', code: 99 },
				'V3S1': { tag: 'Price Crosses 9 Day Moving Average Stalls', code: 99 },
				'V3S2': { tag: 'Price Crosses 18 Day Moving Average Stalls', code: 99 },
				'V3S3': { tag: 'Price Crosses 40 Day Moving Average Stalls', code: 99 },
				'O1S1': { tag: '3-10-16 Day MACD Moving Average Stalls', code: -99 },
				'O1S2': { tag: '3-10 Day MACD Oscillator Stalls', code: 99 },
				'I1S1': { tag: '14 Day RSI at 20%', code: 1 },
				'I1S2': { tag: '14 Day RSI at 30%', code: 1 },
				'I1S3': { tag: '14 Day RSI at 50%', code: -99 },
				'I1S4': { tag: '14 Day RSI at 70%', code: -1 },
				'I1S5': { tag: '14 Day RSI at 80%', code: -1 },
				'U1S1': { tag: '14-3 Day Raw Stochastic at 20%', code: 99 },
				'U1S2': { tag: '14-3 Day Raw Stochastic at 30%', code: 99 },
				'U1S3': { tag: '14-3 Day Raw Stochastic at 50%', code: 99 },
				'U1S4': { tag: '14-3 Day Raw Stochastic at 70%', code: 99 },
				'U1S5': { tag: '14-3 Day Raw Stochastic at 80%', code: 99 },
				'U1S6': { tag: '14 Day %k Stochastic Stalls', code: 99 },
				'U1S7': { tag: '14 Day %d Stochastic Stalls', code: 99 }
			});
			
			var price = { num: convertPriceText(prj['CP']), txt: prj['CP'] };
			var prices = new Hash();

			proinfo.each(function(pair) {
				var k = pair.key;
				var value = convertPriceText(prj[k]);

				if (!prices.get(prj[k]))
					prices.set(prj[k], { price: prj[k], keys: new Array() });

				var item = { key: k, col: null };

				if (proinfo.get(k).code == 0)
					item.col = 'CP';
				else if (proinfo.get(k).code == -1)
					item.col = 'R1';
				else if (proinfo.get(k).code == 1)
					item.col = 'S1';
				else if ((proinfo.get(k).code == -99) && (value > price.num))
					item.col = 'S2';
				else if ((proinfo.get(k).code == -99) && (value < price.num))
					item.col = 'R2';
				else if ((proinfo.get(k).code == 99) && (value > price.num))
					item.col = 'R2';
				else if ((proinfo.get(k).code == 99) && (value < price.num))
					item.col = 'S2';

				prices.get(prj[k]).keys.push(item);
			});

			var list = new Array();
			prices.each(function(pair) {
				list.push(prices.get(pair.key));
			});

			list.sort(function(v1, v2) {
				// Swapped for reverse sort
				var a = convertPriceText(v2.price);
				var b = convertPriceText(v1.price);
				var res =  ((a < b) ? -1 : ((a > b) ? 1 : 0));
				return res;
			});

			var html =
				'<div class="bar"><h2>Historical Trader\'s Cheat Sheet</h2></div>' +
				'<table width="100%" border="0" cellpadding="1" cellspacing="1" class="mpbox">' +
				'<tr class="qb" style="padding: 2px;"><th align="center" class="qb_shad">Support/Resistance Levels</th><th align="center" class="qb_shad">Price</th><th align="center" class="qb_shad">Key Turning Points</th></tr>';

			var len = list.length;
			for (var idx = 0; idx < len; idx++) {
				var p = list[idx];

				var cp = (p.price == price.txt);

				var col1a = new Array();
				var col1b = new Array();
				var col2a = new Array();
				var col2b = new Array();

				var len2 = p.keys.length;
				for (var i = 0; i < len2; i++) {
					if (p.keys[i].col == 'R1')
						col1a.push(p.keys[i].key);
					else if (p.keys[i].col == 'S1')
						col1b.push(p.keys[i].key);
					else if (p.keys[i].col == 'R2')
						col2a.push(p.keys[i].key);
					else if (p.keys[i].col == 'S2')
						col2b.push(p.keys[i].key);
				}

				html += '<tr ' + ((cp) ? 'style="background-color: yellow; padding: 4px 0px;"' : '') + '>';
				html += '<td width="42%" valign="' + (((col1a.length == 0) && (col1b.length == 0)) ? 'middle' : 'top') + '" class="qb_regline">';
				if (cp)
					html += '<b>Current Price</b>';

				for (var i = 0; i < col1a.length; i++) {
					html += '<div class="flash_dn">' + proinfo.get(col1a[i]).tag + '</div>';
				}

				for (var i = 0; i < col1b.length; i++) {
					html += '<div class="flash_up">' + proinfo.get(col1b[i]).tag + '</div>';
				}

				if ((col1a.length == 0) && (col1b.length == 0))
					html += '&nbsp;';

				html += '</td>';

				html += '<td align="center" width="16%" class="qb_regline">' + ((cp) ? '<b>' : '') + p.price + ((cp) ? '</b>' : '') + '</td>';
				html += '<td width="42%" valign="' + (((col2a.length == 0) && (col2b.length == 0)) ? 'middle' : 'top') + '" class="qb_regline">';
				if (cp)
					html += '<b>Current Price</b>';


				for (var i = 0; i < col2a.length; i++) {
					html += '<div class="flash_up">' + proinfo.get(col2a[i]).tag + '</div>';
				}

				for (var i = 0; i < col2b.length; i++) {
					html += '<div class="flash_dn">' + proinfo.get(col2b[i]).tag + '</div>';
				}

				if ((col2a.length == 0) && (col2b.length == 0))
					html += '&nbsp;';

				html += '</td>';
				html += '</tr>';
			}
			html += '</table>';

			return html;
		},


		CreateQuote5HTML : function(q) {
			var html =
				'<div class="bar"><h2>Historical Detail - Past 5 Days</h2></div>' +
				'<table width="100%" border="0" cellpadding="1" cellspacing="1" class="mpbox datatable_simple">' +
				'<tr class="datatable_header">' + 
				'<td align="left" class="blue13">Date</td>' +
				'<td align="right" class="blue13">Open</td>' +
				'<td align="right" class="blue13">High</td>' +
				'<td align="right" class="blue13">Low</td>' +
				'<td align="right" class="blue13">Last</td>' +
				'<td align="right" class="blue13">Change</td>' +
				'<td align="right" class="blue13">% Change</td>' +
				'<td align="right" class="blue13">Volume</td>' +
				'</tr>';

			for (var i = 1; i < 6; i++) {
				var p = q.getPriceItem(i);
				if (!p)
					continue;
				var c = (i % 2 == 0) ? 'qb_shad' : 'qb_line';
				var chgColor = (p.change.substr(0, 1) != '-') ? (p.change.substr(0,1) == '+' ? 'qb_up' : 'qb_unc') : 'qb_down';
				var pctColor = (p.percentchange.substr(0, 1) != '-') ? (p.percentchange.substr(0,1) == '+' ? 'qb_up' : 'qb_unc') : 'qb_down';
				var addPct = (p.percentchange != 'unch') ? '%' : '';

				html += 
					'<tr>' +
					'<td align="left" width="12.5%" class="' + c + '" id="dtaDate' + i + '">' + p.date + '</td>' +
					'<td align="right" width="12.5%" class="' + c + '" id="dtaOpen' + i + '">' + p.open + '</td>' +
					'<td align="right" width="12.5%" class="' + c + '" id="dtaHigh' + i + '">' + p.high + '</td>' +
					'<td align="right" width="12.5%" class="' + c + '" id="dtaLow' + i + '">' + p.low + '</td>' +
					'<td align="right" width="12.5%" class="' + c + '" id="dtaLast' + i + '">' + p.close + '</td>' +
					'<td align="right" width="12.5%" class="' + c + ' ' + chgColor + '" id="dtaChange' + i + '">' + p.change + '</td>' +
					'<td align="right" width="12.5%" class="' + c + ' ' + pctColor + '" id="dtaPctChange' + i + '">' + p.percentchange + addPct + '</td>' +
					'<td align="right" width="12.5%" class="' + c + '" id="dtaVolume' + i + '">' + com.barchart.support.Content.AddCommas (p.volume) + '</td>' +
					'</tr>';
			}
			if (!p)
				return null;
			else
				html += '</tr>' + '</table>';

			return html;
		},

		CreateRatingsHTML : function(q) {
			var op = q.getOpinion();
			if (!op)
				return '';

			var html = '<div class="bar"><h2>Historical Ratings: Overall Strength and Direction</h2></div><table width="100%" border="0" cellspacing="1" cellpadding="3" class="mpbox">';

			html += '<tr><td width="10%" nowrap><strong>Strength:</strong></td>';
			if (op.overall.strength == '*') {
				html += '<td colspan="10" class="opinion_top">Top 1%</td>';
			}
			else {
				var str = new Number(op.overall.strength);
				for (var i = 0; i < 10; i++) {
					html += '<td class="' + ((i == str) ? 'opinion_on' : 'opinion') + '">&nbsp;</td>';
				}
				html +=	'<td width="1%">&nbsp;</td>';
			}
			html += '</tr>';

			html += '<tr><td width="10%" nowrap><strong>Direction:</strong></td>';
			if (op.overall.direction == '*') {
				html += '<td colspan="10" class="opinion_top">Top 1%</td>';
			}
			else {
				var dir = new Number(op.overall.direction);
				for (var i = 0; i < 10; i++) {
					html += '<td class="' + ((i == dir) ? 'opinion_on' : 'opinion') + '">&nbsp;</td>';
				}
				html +=	'<td width="1%">&nbsp;</td>';
			}
			html += '</tr>';


			html += '<tr><td>&nbsp;</td><td align="center"><strong>0-10</strong></td><td align="center"><strong>10-20</strong></td><td align="center"><strong>20-30</strong></td><td align="center"><strong>30-40</strong></td><td align="center"><strong>40-50</strong></td><td align="center"><strong>50-60</strong></td><td align="center"><strong>60-70</strong></td><td align="center"><strong>70-80</strong></td><td align="center"><strong>80-90</strong></td><td align="center"><strong>90-100</strong></td>' +
				'<td></td></tr>';

			html += '</table>';
			return html;
		},

		CreateSnapshotOpinionHTML : function(q) {
			var op = q.getOpinion();
			if (!op)
				return '<div class="error" style="text-align: center;">In order to properly form an accurate opinion, the equity or commodity has to have at least<br />6 months of trading activity.</div>';

			var html = '<div class="bar"><h2>Historical Snapshot Opinion</h2></div>' + 
				'<table width="100%" border="0" cellspacing="0" cellpadding="3" class="mpbox">';

			var aop = new Array(op.overall, op.overall.yesterday, op.overall.lastweek, op.overall.lastmonth);
			for (var i = 0; i < aop.length; i++) {
				var sigtext = '';
				var sigclass = '';


				switch(aop[i].signal) {
					case 'B': sigtext = 'Buy'; sigclass = 'opinion_buy'; break;
					case 'S': sigtext = 'Sell'; sigclass = 'opinion_sell'; break;
					case 'H': sigtext = 'Hold'; sigclass = 'opinion_hold'; break;
				}

				var text = (aop[i].signal == 'H') ? 'Hold' : (aop[i].percent + ' ' + sigtext);
				var pct = new Number(aop[i].percent.substring(0, aop[i].percent.length - 1));
				var img = '<img src="/shared/images/spacer.gif" class="' + sigclass + '" height="20" width="' + ((aop[i].signal == 'H') ? '100' : pct * 1.75) + '" />';

				if (i == 0)
					html += '<tr><td width="50%"><strong>Today\'s Opinion:</strong></td><td width="50%">&nbsp;</td></tr>';
				else if (i == 1)
					html += '<tr><td width="50%"><strong>Yesterday\'s Opinion:</strong></td><td width="50%">&nbsp;</td></tr>';
				else if (i == 2)
					html += '<tr><td width="50%"><strong>Last Week\'s Opinion:</strong></td><td width="50%">&nbsp;</td></tr>';
				else if (i == 3)
					html += '<tr><td width="50%"><strong>Last Month\'s Opinion:</strong></td><td width="50%">&nbsp;</td></tr>';

				if (aop[i].signal == 'S')
					html += '<tr><td align="right">' + text + ' ' + img + '</td><td></td></tr>';
				else if (aop[i].signal == 'H')
					html += '<tr><td colspan="2" align="center">' + img + ' ' + text + '</td></tr>';
				else if (aop[i].signal == 'B')
					html += '<tr><td></td><td align="left">' + img + ' ' + text + '</td></tr>';
			}
			html += '</table>';

			return html;
		},

		CreateTechnicalsHTML : function(q) {
			var data = q.getTechnicals();

			if (!data)
				return '<div class="error" style="text-align: center;">In order to properly form a technical report, the equity or commodity has to have at least<br />5 days of trading activity.</div>';
			

			var cols1 = null;
			var cols2 = null;
			var cols3 = null;

			if (q.getDWM() == 'm') {
				cols1 = new Array('6-Month', '2-Year', '5-Year', '10-Year', '20-Year');
				cols2 = new Array('9-Month', '14-Month', '20-Month');
				cols3 = new Array('9-Month', '14-Month', '20-Month', '50-Month', '100-Month');
			}
			else if (q.getDWM() == 'w') {
				cols1 = new Array('6-Week', '6-Month', '1-Year', '2-Year', '5-Year');
				cols2 = new Array('9-Week', '14-Week', '20-Week');
				cols3 = new Array('9-Week', '14-Week', '20-Week', '50-Week', '100-Week');
			}
			else {
				cols1 = new Array('5-Day', '20-Day', '50-Day', '100-Day', '200-Day', 'Year to Date');
				cols2 = new Array('9-Day', '14-Day', '20-Day');
				cols3 = new Array('9-Day', '14-Day', '20-Day', '50-Day', '100-Day');
			}

			var html = 
				'<div class="bar"><h2>Historical Technicals Summary</h2></div>' +
				'<div class="mpbox">' +
				'<table width="100%" border="0" cellpadding="1" cellspacing="1" class="datatable_simple">' +
				'<tr class="datatable_header" style="text-align:center">' +
				'<td align="center" class="blue13" width="20%">Period</td>' +
				'<td align="center" class="blue13" width="20%">Moving Average</td>' +
				'<td align="center" class="blue13" width="20%">Price Change</td>' +
				'<td align="center" class="blue13" width="20%">Percent Change</td>' +
				'<td align="center" class="blue13" width="20%">Average Volume</td>' +
				'</tr>';

			for (var i = 0; i < cols1.length; i++) {
				var d = data['M' + (i + 1)];
				var c = (i % 2 == 0) ? 'qb_shad' : 'qb_line';
				var prcColor = (d.prcchg.substr(0, 1) != '-') ? (d.prcchg.substr(0,1) == '+' ? 'qb_up' : 'qb_unc') : 'qb_down';
				var pctColor = (d.pctchange.substr(0, 1) != '-') ? (d.pctchange.substr(0,1) == '+' ? 'qb_up' : 'qb_unc') : 'qb_down';
				html += 
					'<tr style="text-align: center;">' +
					'<td align="center" class="' + c + '">' + cols1[i] + '</td>' +
					'<td align="center" class="' + c + '">' + d.ma + '</td>' + 
					'<td align="center" class="' + c + ' ' + prcColor + '">' + d.prcchg + '</td>' + 
					'<td align="center" class="' + c + ' ' + pctColor + '">' + d.pctchange + '</td>' + 
					'<td align="center" class="' + c + '">' + com.barchart.support.Content.AddCommas(d.avgvol) + '</td>' + 
					'</tr>';
			}

			html +=
				'</table><p />';


			html +=
				'<table width="100%" border="0" cellpadding="1" cellspacing="1" class="datatable_simple">' +
				'<tr class="datatable_header" style="text-align:center">' +
				'<td align="center" class="blue13" width="20%">Period</td>' +
				'<td align="center" class="blue13" width="20%">Raw Stochastic</td>' +
				'<td align="center" class="blue13" width="20%">Stochastic %K</td>' +
				'<td align="center" class="blue13" width="20%">Stochastic %D</td>' +
				'<td align="center" class="blue13" width="20%">Average True Range</td>' +
				'</tr>';

			

			for (var i = 0 ; i < cols2.length; i++) {
				var d = data['S' + (i + 1)];
				var c = (i % 2 == 0) ? 'qb_shad' : 'qb_line';

				html +=
					'<tr style="text-align:center">' +
					'<td align="center" class="' + c + '">' + cols2[i] + '</td>' +
					'<td align="center" class="' + c + '">' + d.stocr + '</td>' +
					'<td align="center" class="' + c + '">' + d.stock + '</td>' +
					'<td align="center" class="' + c + '">' + d.stocd + '</td>' +
					'<td align="center" class="' + c + '">' + d.atr + '</td>' +
					'</tr>';
			}
			html +=
				'</table><p />';


			html +=
				'<table width="100%" border="0" cellpadding="1" cellspacing="1" class="datatable_simple">' +
				'<tr class="datatable_header" style="text-align:center">' +
				'<td align="center" class="blue13" width="20%">Period</td>' +
				'<td align="center" class="blue13" width="20%">Relative Strength</td>' +
				'<td align="center" class="blue13" width="20%">Percent R</td>' +
				'<td align="center" class="blue13" width="20%">Historic Volatility</td>' +
				'<td align="center" class="blue13" width="20%">MACD Oscillator</td>' +
				'</tr>';

			
			for (var i = 0 ; i < cols3.length; i++) {
				var d = data['R' + (i + 1)];
				var c = (i % 2 == 0) ? 'qb_shad' : 'qb_line';
				var macdColor = (d.macd.substr(0, 1) != '-') ? (d.macd.substr(0,1) == '+' ? 'qb_up' : 'qb_unc') : 'qb_down';

				html +=
					'<tr style="text-align: center">' +
					'<td align="center" class="' + c + '">' + cols3[i] + '</td>' +
					'<td align="center" class="' + c + '">' + d.relstr + '</td>' +
					'<td align="center" class="' + c + '">' + d.pctr + '</td>' +
					'<td align="center" class="' + c + '">' + d.hisvol + '</td>' +
					'<td align="center" class="' + c + ' ' + macdColor + '">' + d.macd + '</td>' +
					'</tr>';
			}

			html += '</table></div>';

			return html;
		}
	}
}

com.ddfplus.BaseCode2UnitCode = function(bc) {
	switch(bc) {
		case '2': return -1;
		case '3': return -2;
		case '4': return -3;
		case '5': return -4;
		case '6': return -5;
		case '7': return -6;
		case '8': return 0;
		case '9': return 1;
		case 'A': return 2;
		case 'B': return 3;
		case 'C': return 4;
		case 'D': return 5;
		case 'E': return 6;
		case 'F': return 7;
		default: return 0;
	}
}

com.ddfplus.ConvertDDFDate = function(s) {
	var year = s.substring(0, 4) * 1;
	var month = (s.substring(4, 6) * 1) - 1;
	var day = s.substring(6, 8) * 1;
	var hour = s.substring(8, 10) * 1;
	var minute = s.substring(10, 12) * 1;
	var second = s.substring(12, 14) * 1;

	return new Date(year, month, day, hour, minute, second);
}

com.ddfplus.NumberFormat = function(f, digits, doSep) {
	var s = (new Number(f)).toFixed(digits);
	if (doSep) {
		var x = s.split('.');
		var x1 = x[0];
		var x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}
	else
		return s;
}

com.ddfplus.Decimal2String = function(val, bc, display) {
	var uc = com.ddfplus.BaseCode2UnitCode(bc);

	var sign = '';
	if (val < 0)
		sign = '-';

	if (uc >= 0) {			
		var s = com.ddfplus.NumberFormat(new Number(val), uc, true);

		if (display.search(/INTEGER/) > -1) {
			s = s.replace(/[^0-9]/g, '');
			s = s.replace(/^0+/g, '');
			if (s.length == 0)
				s = '0';
			s = sign + s;
		}

		return s;
	}


	if (display.search(/DECIMAL/) > -1) {
		var n = new Number(val);

		switch (bc) {
			case '2': return com.ddfplus.NumberFormat(n, 3, true);
			case '3': return com.ddfplus.NumberFormat(n, 4, true);
			case '4': return com.ddfplus.NumberFormat(n, 5, true);
			case '5': return com.ddfplus.NumberFormat(n, 6, true);
			case '6': return com.ddfplus.NumberFormat(n, 7, true);
			case '7': return com.ddfplus.NumberFormat(n, 8, true);
			default: return n;
		}
	}



	val = Math.abs(val);
	var whole = Math.floor(val);
	var numerator = val - whole;

	var digits = 0;

	if (uc == -1) {
		numerator *= 8;
		digits = 1;
	}
	else if (uc == -2) {
		numerator *= 16;
		digits = 2;
	}
	else if (uc == -3) {
		numerator *= 32;
		digits = 2;
	}
	else if (uc == -4) {
		if (display.substring(display.length - 2, display.length) == '64') {
			numerator *= 320;
			digits = 3;
		}
		else {
			numerator *= 64;
			digits = 2;
		}
	}
	else if (uc == -5) {
		numerator *= 320;
		digits = 3;
	}
	else if (uc == -6) {
		numerator *= 256;
		digits = 3;
	}
	
	// Chart server rounds badly
	numerator = Math.round(numerator);

	var s = 'xx0000000000' + numerator;				
	var sfract = s.substring(s.length - digits, s.length);

	if (display.search(/INTEGER/) > -1) {
		if (whole == 0)
			return (new String()).concat(sign, sfract);
		else
			return (new String()).concat(sign, whole, sfract);

	}
	else
		return (new String()).concat(sign, whole, '-', sfract);
}


com.ddfplus.String2Decimal = function(s, bc) {
	var uc = com.ddfplus.BaseCode2UnitCode(bc);

	if (uc >= 0) {
		var ival = s * 1;
		//return ival / Math.pow(10, uc);
		return Math.round(ival*Math.pow(10,uc))/Math.pow(10,uc);
	}
	else {
		var divisor = Math.pow(2, Math.abs(uc) + 2);
		var fracsize = String(divisor).length;
		var denomstart = s.length - fracsize;
		var numerend = denomstart;
		if (s.substring(numerend - 1, numerend) == '-')
			numerend--;
		var numerator = (s.substring(0, numerend)) * 1;
		var denominator = (s.substring(denomstart, s.length)) * 1;
		return numerator + (denominator / divisor);
	}
}


com.ddfplus.Symbol = function(s) {
	var _callput = null;
	var _strike = null;
	var _type = null;
	var _root = null;
	var _month = null;
	var _year = 0;

	var _months = {'F':'January', 'G':'February', 'H':'March', 'J':'April', 'K':'May', 'M':'June', 'N':'July', 'Q':'August', 'U':'September', 'V':'October', 'X':'November', 'Z':'December', '*1':'1st Front Month', '*2':'2nd Front Month', '*3':'3rd Front Month', '*4':'4th Front Month', '*5':'5th Front Month', '*6':'6th Front Month', '*7':'7th Front Month', '*8':'8th Front Month', '*9':'9th Front Month'};

	if (s.match(/[0-9]/)) {
		if (s.substring(s.length - 1, s.length).match(/[0-9]/)) {
			var r = '';
			_type = 'future';
			var y = '';
			for (var i = s.length - 1; i > -1; i--) {
				var c = s.substring(i, i + 1);
				if ((c.match(/[0-9]/)) && (_month == null))
					y = c.concat(y);
				else if (_month == null)
					_month = c;
				else 
					r = c.concat(r);
			}
			_year = new Number(y);
			_root = r;
		}
		else {
			_type = 'future_option';
			_callput = (s.substring(s.length - 1, s.length).match(/[CDEFG]/)) ? 'call' : 'put';
			var strike = '';
			var r = '';

			for (var i = s.length - 1; i > -1; i--) {
				var c = s.substring(i, i + 1);
				if (c.match(/[0-9]/))
					strike.concat(c);
				else if (_month == null)
					_month = c;
				else
					r.concat(c);
			}

			_root = r;
			_strike = strike;
		}
	}
	else {
		_type = 'stock';
	}

	var getFuturesYear = function() {
			var d = new Date();
			var current = d.getFullYear();
			var lastdigit = current % 10;
			_year = parseInt(_year);
			
			if (_year < 10) {
				var futyear = (current - lastdigit) + _year;
				if (_year < lastdigit)
					futyear += 10;
			} else if (_year < 100)
				futyear = (((current - (current % 100)) / 100) * 100) + _year;
			else
				futyear = _year;
			return futyear;
		};

	return {
		getMonth : function() {
			return _month;
		},

		getMonthName : function() {
			return _months[_month];
		},

		getRoot : function() {
			return _root;
		},

		getType : function() {
			return _type;
		},

		getYear : getFuturesYear,

		getNormalized : function(shortyear) {
			if (_type == 'future')
				return _root + _month + String(getFuturesYear()).substring((shortyear ? 3 : 2));
			else
				return s;
		}
	}
}

// Based off Jeremy's TreeSelect function (treeselect.js)

function Dropdown(element, options) {

	// Private members
	var showTimer = null;

	function trim(str) {
		str = str.replace(/^\s+/, '');
		for (var i = str.length - 1; i >= 0; i--) {
			if (/\S/.test(str.charAt(i))) {
				str = str.substring(0, i + 1);
				break;
			}
		}
		return str;
	}

	function findElementsByClassName(classname, root) {
		var res = [];
		var elts = (root||document).getElementsByTagName('*')
		var re = new RegExp('\\b'+classname+'\\b');
		var len = elts.length;
		for (var i = 0; i < len; ++i)
			if (elts[i].className.match(re))
				res[res.length] = elts[i];
		return res;
	}

	function addClass(el, c) {
		cl = el.className ? el.className.split(/ /) : [];
		for (var i = 0; i < cl.length; ++i)
			if (cl[i] == c)
				return;
		cl[cl.length] = c;
		el.className = cl.join(' ');
	}

	function removeClass(el, c) {
		if (!el.className) return;
		cl = el.className.split(/ /);
		var nc = [];
		for (var i = 0; i < cl.length; ++i)
			if (cl[i] != c)
				nc[nc.length] = cl[i];
		el.className = nc.join(' ');
	}

	function stopEvent(e) {
		var ev = window.event||e;
		if (ev.stopPropagation)
			ev.stopPropagation();
		else
			ev.cancelBubble = true;
	}

	function addEvent(el, name, handler) {
		if (el.addEventListener) {
			el.addEventListener(name, handler, false);
		} else {
			el.attachEvent('on'+name, handler);
		}
	}

	function removeEvent(el, name, handler) {
		if (el.removeEventListener) {
			el.removeEventListener(name, handler, false);
		} else {
			el.detachEvent('on'+name, handler);
		}
	}

	function openItem(li) {
		return function(e) {
			if (showTimer) {
				clearTimeout(showTimer);
				showTimer = null;
			}
			showTimer = setTimeout(function() {
				var show = [];
				if (li.getElementsByTagName('ul').length)
					show[0] = li.getElementsByTagName('ul')[0];
				var p = li.parentNode;
				while (p && p != element) {
					if (p.nodeName.toUpperCase() == 'UL')
						show[show.length] = p;
					p = p.parentNode;
				}
				var hide = [];
				var uls = element.getElementsByTagName('ul');
				for (var i = 0; i < uls.length; ++i) {
					var found = false;
					for (var j = 0; j < show.length; ++j)
						if (show[j] == uls[i]) {
							found = true;
							break;
						}
					if (!found)
						hide[hide.length] = uls[i];
				}

				for (var i = 0; i < show.length; ++i) {
					show[i].style.display = 'block';
					show[i].style.zIndex = 99;
					addClass(show[i].parentNode, 'active');
				}
				for (var i = 0; i < hide.length; ++i) {
					hide[i].style.display = 'none';
					hide[i].style.zIndex = 0;
					removeClass(hide[i].parentNode, 'active');
				}
				showTimer = null;
				}, 200);
			stopEvent(e);
			return false;
		}
	}

	function addListBehavior(li) {
		if (!li.id && li.getElementsByTagName('ul').length)
			addEvent(li, 'click', openItem(li));
		else
			addEvent(li, 'click', selectItem(li));
		addEvent(li, 'mouseover', openItem(li));
		var sublists = li.getElementsByTagName('ul');
		if (sublists.length)
			addClass(li, 'dropsubmenu');
	}

	function toggleList(e) {
		if (datalist.style.display == 'block')
			datalist.style.display = 'none';
		else {
			return showList(e);
		}
	}

	function showList(e) {
		datalist.style.display = 'block';
		datalist.style.zIndex = 99;
		if (document.getElementById('myChartFrame'))
			setHeight();
		addEvent(document, 'click', hideList);
		stopEvent(e);
	}

	function hideList(e) {
		datalist.style.zIndex = 0;
		var uls = datalist.getElementsByTagName('ul');
		for (var i = 0; i < uls.length; ++i) {
			uls[i].style.display = 'none';
			uls[i].style.zIndex = 0;
			removeClass(uls[i].parentNode, 'active');
		}
		datalist.style.display = 'none';
		removeEvent(document, 'click', hideList);
	}

	// Constructor
	var datalist = element.getElementsByTagName('ul', element)[0];
	var dropdown = findElementsByClassName('dropdownbutton', element)[0];

	addEvent(dropdown, 'click', toggleList);
/*
	var listitems = element.getElementsByTagName('li');
	for (var i = 0; i < listitems.length; ++i)
		addListBehavior(listitems[i]);
*/
	return {
		// Public members
		'element': element
   }
}



//Tool to hide and show divs with a sliding graphic.

var sliderTimer;

// toggleLayer is the only function used externally.
// id is the document ID of the element to be shown or hidden. height is optional.
function toggleLayer(id, height) {
	var elt = document.getElementById(id);
	//if (elt.style.display && elt.style.display != 'none')
	if (elt.offsetHeight && elt.style.display == 'block')
		slideClosed(id, .1, height);
	else
		slideOpen(id, .1, height);
}

// height is optional
function slideOpen(id, seconds, height) {
	if (sliderTimer) clearTimeout(sliderTimer);
	if (!seconds) seconds = .1;
	var elt = document.getElementById(id);
	elt.style.display = 'block';
	elt.style.visibility = 'visible';
	var height = (height) ? height : elt.offsetHeight;
	elt.style.height = '0px';
	elt.style.overflow = 'hidden';
	var steps = seconds * 1000 / 25;
	var step = Math.floor(height / steps);
	slideTo(elt, height, step, 25);
}

// height is optional
function slideClosed(id, seconds, height) {
	if (sliderTimer) clearTimeout(sliderTimer);
	if (!seconds) seconds = .1;
	var elt = document.getElementById(id);
	var height = (height) ? height : elt.offsetHeight;
	elt.style.overflow = 'hidden';
	var steps = seconds * 1000 / 25;
	var step = Math.floor(height / steps);
	slideTo(elt, 0, -step, 25);
}

function slideTo(elt, height, step, interval) {
	var currheight = elt.offsetHeight + step;
	if (step < 0) {
		if (currheight < 0) currheight = 0;
		elt.style.height = currheight + 'px';
		if (currheight > 0)
			sliderTimer = setTimeout(function() { slideTo(elt, height, step, interval) }, interval);
		else {
			sliderTimer = null;
			elt.style.display = 'none';
			elt.style.height = null;
		}
	} else {
		if (currheight > height) currheight = height;
		elt.style.height = currheight + 'px';
		if (currheight < height)
			sliderTimer = setTimeout(function() { slideTo(elt, height, step, interval) }, interval);
		else
			sliderTimer = null;
	}
}

function isFunction(value) {
	return Object.prototype.toString.call(value) == '[object Function]';
}

function findElementsByClassName(classname, root) {
	var res = [];
	var elts = (root||document).getElementsByTagName('*');
	var re = new RegExp('\\b'+classname+'\\b');

	var len = elts.length;
	for (var i = 0; i < len; ++i)
		if (elts[i].className && elts[i].className.match(re))
			res[res.length] = elts[i];
	return res;
}

function captureEnter(e) {
	var key;
	if(window.event)
		key = window.event.keyCode;     //IE
	else
		key = e.which;     //firefox

	if (key == 13) {
		Barchart.Stubs.GetQuote();
	}
}

