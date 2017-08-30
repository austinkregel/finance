'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var FETCH_INTERVAL = 5000;
var PRETTY_PRINT_JSON = true;
var https = require('https');
var _ = require('underscore');

var Server = function () {
    function Server() {
        _classCallCheck(this, Server);

        this.tickers = [];
        this.quote = {};
    }

    _createClass(Server, [{
        key: 'trackTicker',
        value: function trackTicker() {
            var _this = this;

            // run the first time immediately
            this.getQuote();

            // every N seconds
            setInterval(function () {
                _this.getQuote();
            }, FETCH_INTERVAL);

            return this.quote;
        }
    }, {
        key: 'getQuote',
        value: function getQuote() {
            var _this2 = this;

            var ticker = this.tickers.join(',');

            if (ticker === '') {
                return;
            }

            console.log({ ticker: ticker });

            https.get({
                port: 443,
                method: 'GET',
                hostname: 'www.google.com',
                path: '/finance/info?client=ig&q=' + ticker,
                timeout: 1000
            }, function (response) {
                response.setEncoding('utf8');
                var data = '';

                response.on('data', function (chunk) {
                    data += chunk;
                });

                response.on('end', function () {
                    if (data.length > 0) {
                        var dataObj;

                        try {
                            dataObj = JSON.parse(data.substring(3));
                        } catch (e) {
                            return false;
                        }

                        dataObj.forEach(function (data) {
                            _this2.quote[data.t] = {};
                            _this2.quote[data.t].ticker = data.t;
                            _this2.quote[data.t].exchange = data.e;
                            _this2.quote[data.t].price = data.l_cur; // jshint ignore:line
                            _this2.quote[data.t].change = data.c;
                            _this2.quote[data.t].change_percent = data.cp; // jshint ignore:line
                            _this2.quote[data.t].last_trade_time = data.lt; // jshint ignore:line
                            _this2.quote[data.t].dividend = data.div;
                            _this2.quote[data.t].yield = data.yld;
                        });
                    }
                });
            });
        }
    }, {
        key: 'concat',
        value: function concat(newTickets) {
            this.tickers = _.unique(this.tickers.concat(newTickets));
        }
    }, {
        key: 'getTickers',
        value: function getTickers() {
            return this.tickers;
        }
    }, {
        key: 'getQuotes',
        value: function getQuotes() {
            return this.quote;
        }
    }]);

    return Server;
}();

exports.default = Server;