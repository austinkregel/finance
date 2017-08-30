var FETCH_INTERVAL = 5000;
var PRETTY_PRINT_JSON = true;
var https = require('https');
var _ = require('underscore');
export default class Server {
    constructor() {
        this.tickers = [];
        this.quote = {}
    }


    trackTicker() {
        // run the first time immediately
        this.getQuote();

        // every N seconds
        setInterval(() => {
            this.getQuote();
        }, FETCH_INTERVAL);

        return this.quote;
    }
    getQuote() {
        let ticker = this.tickers.join(',')

        if (ticker === '') {
            return;
        }

        console.log({ticker})

        https.get({
            port: 443,
            method: 'GET',
            hostname: 'www.google.com',
            path: '/finance/info?client=ig&q=' + ticker,
            timeout: 1000
        }, (response) => {
            response.setEncoding('utf8');
            var data = '';

            response.on('data', function (chunk) {
                data += chunk;
            });

            response.on('end', () => {
                if (data.length > 0) {
                    var dataObj;

                    try {
                        dataObj = JSON.parse(data.substring(3));
                    } catch (e) {
                        return false;
                    }

                    dataObj.forEach(data => {
                        this.quote[data.t] = {}
                        this.quote[data.t].ticker = data.t;
                        this.quote[data.t].exchange = data.e;
                        this.quote[data.t].price = data.l_cur; // jshint ignore:line
                        this.quote[data.t].change = data.c;
                        this.quote[data.t].change_percent = data.cp; // jshint ignore:line
                        this.quote[data.t].last_trade_time = data.lt; // jshint ignore:line
                        this.quote[data.t].dividend = data.div;
                        this.quote[data.t].yield = data.yld;
                    })
                }
            });
        });
    }
    concat(newTickets) {
        this.tickers = _.unique(this.tickers.concat(newTickets))
    }
    getTickers() {
        return this.tickers;
    }
    getQuotes() {
        return this.quote;
    }
}