# Quote Stream

Push JSON stock quotes in real-time over websockets.

## Usage

**Apple Inc. (AAPL)**
### <http://localhost:4000/#AAPL>

Simply replace **AAPL** in the URL with a **valid ticker symbol**. Stock quotes are pulled from <https://www.google.com/finance> and are real-time.

## Additional Details

By default stock quotes are fetched every **5 seconds** and pushed to the client. The fetch interval can be adjusted as well as if you want the JSON response pretty-printed or not.

## Example JSON Response

    {
        "ticker": "AAPL",
        "exchange": "NASDAQ",
        "price": "116.60",
        "change": "-0.46",
        "change_percent": "-0.39",
        "last_trade_time": "Oct 21, 4:00PM EDT",
        "dividend": "0.57",
        "yield": "1.96"
    }

## License & Legal

Copyright 2016 Justin Keller

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
