<?php
            $API_KEY = "cu8jie9r01qt63vh5q70cu8jie9r01qt63vh5q7g";
            $ticker = isset($_POST["ticker"]) ? htmlspecialchars($_POST["ticker"]) : "";

            function fetchFromAPI($url) {
                global $API_KEY;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'X-Finnhub-Token: ' . $API_KEY
                ));

                $response = curl_exec($ch);

                // if ($response === false) {
                //     echo "cURL Error: " . curl_error($ch);
                //     curl_close($ch);
                //     return null;
                // }

                curl_close($ch);
                return json_decode($response, true);
            }

            function getNews($ticker, $scope) {
                $present = date("Y-m-d");
                $past = date("Y-m-d", strtotime($present . '- '.$scope.' days')); //adjusts ammount of news, API will not fetch news from over a year ago.
                $newsUrl = "https://finnhub.io/api/v1/company-news?symbol=" . $ticker . "&from=" . $past . "&to=" . $present;
                return fetchFromAPI($newsUrl);
            }

            function getProfile($ticker) {
                $profileUrl = "https://finnhub.io/api/v1/stock/profile2?symbol=" . $ticker;
                return fetchFromAPI($profileUrl);
            }

            function getFinancials($ticker) {
                $financialsUrl = "https://finnhub.io/api/v1/stock/financials-reported?symbol=" . $ticker;
                return fetchFromAPI($financialsUrl);
            }

            function getTrends($ticker) {
                $trendsUrl = "https://finnhub.io/api/v1/stock/recommendation?symbol=" . $ticker;
                return fetchFromAPI($trendsUrl);
            }

            function getQuote($ticker) {
                $quoteUrl = "https://finnhub.io/api/v1/quote?symbol=" . $ticker;
                return fetchFromAPI($quoteUrl);
            }

            function parseNews($newsData){
                echo "News:";
                echo "<pre>";
                if ($newsData) {
                    foreach ($newsData as $newsItem) {
                        echo "Title: " . $newsItem['headline'] . "\n";
                        echo "Source: " . $newsItem['source'] . "\n";
                        echo "URL: <a href='" . $newsItem['url'] . "' target='_blank'>" . $newsItem['url'] . "</a>\n\n";
                    }
                } else {
                    echo "No news available within selected scope.\n";
                }

            }

            function confirm($ticker) {
                $profileUrl = "https://finnhub.io/api/v1/stock/profile2?symbol=" . $ticker;
                if (fetchFromAPI($profileUrl)) {
                    return true;
                }
                return false;
            }



            function parseFinancials($financialsData){

                echo "Financials:";
                echo "<pre>";
                if ($financialsData && isset($financialsData['data'][0]['report']['bs'])) {
                    $balanceSheet = $financialsData['data'][0]['report']['bs'];
                    foreach ($balanceSheet as $item) {
                        echo $item['label'] . ": " . $item['value'] . "\n";
                    }
                } else {
                    echo "Failed to fetch financials or balance sheet data.\n";
                }
                echo "</pre>";
            }


            function parseTrends($trendsData){

                echo "Analyst Recommendations:";
                echo "<pre>";
                if ($trendsData) {
                    echo "Strong Buy    Buy    Hold    Sell    Strong Sell    Period\n";
                    foreach ($trendsData as $trend) {
                        echo $trend["strongBuy"] . "            " . $trend["buy"] . "     " . $trend["hold"] . "       " . $trend["sell"] . "       " . $trend["strongSell"] . "              " . $trend["period"] . "\n";
                    }
                } else {
                    echo "Failed to fetch Analyst Recommendations.\n";
                }
                echo "</pre>";
            }

            function parseQuote($quoteData){
                /*echo "Stock Quote:";
                echo "<pre>";
                if ($quoteData) {
                    echo "Current Price: " . $quoteData['c'] . "\n";
                    echo "High Price of the day: " . $quoteData['h'] . "\n";
                    echo "Low Price of the day: " . $quoteData['l'] . "\n";
                    echo "Open Price of the day: " . $quoteData['o'] . "\n";
                    echo "Previous Close Price: " . $quoteData['pc'] . "\n";
                    echo "Timestamp: " . date("Y-m-d H:i:s", $quoteData['t']) . "\n";
                } else {
                    echo "Failed to fetch stock quote data.\n";
                }
                echo "</pre>";
                */
                    return [
                        'c' => $quoteData['c'] ?? null,  // Current price
                        'h' => $quoteData['h'] ?? null,  // High price
                        'l' => $quoteData['l'] ?? null,  // Low price
                        'o' => $quoteData['o'] ?? null,  // Open price
                        'pc' => $quoteData['pc'] ?? null, // Previous close
                        't' => $quoteData['t'] ?? null   // Timestamp
                    ];
                }
?>
