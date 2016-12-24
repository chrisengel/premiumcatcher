<?php
header("refresh:601");
echo "<html>\n";
echo "<head>\n";
echo "<title>  Bitcoin Futures Exchange Premiums     </title>\n";
echo "\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" />";
echo "<script type=\"text/javascript\" language=\"javascript\" src=\"script.js\"></script>";
echo "	<meta property=\"og:title\" content=\"Bitcoin Futures/Forwards Exchange Premiums, Forward Curve\"/>\n";
echo "		<meta property=\"og:description\" content=\"Want to compare the premiums of different bitcoin futures contracts? OKcoin, BitMEX, and CryptoFacilities offer contracts that are of varying lengths and types. Use this tool to compare the premiums for arbitrage purposes or just curiosity and analysis.\" />\n";
echo "		<meta property=\"og:url\" content=\"http://www.austeritysucks.com/premiumcatcher.php\"/>\n";
echo "							<meta property=\"og:image\" content=\"http://www.austeritysucks.com/premiumcatcher.png\" />\n";
echo "					<meta property=\"og:type\" content=\"article\"\n";
echo "		/>\n";
echo "		<meta property=\"og:site_name\" content=\"Bitcoin Futures Premiums\"/>";


echo "</head>\n";
echo "<center>\n";
echo "<body style='background-color:#d3d3d3'>";

$date = date('Y-m-d H:i:s');
print_r($date."(UTC)");
echo "<br>";
echo "<b>Refresh the page if data not displaying below (Requests are throttled)</b>. Every 10 minutes it will autorefresh.";
echo "<br>";
echo "<b>Want to learn how to trade using this info?</b> For <a href='http://austeritysucks.com/swapmans-futures-arbitrage-walkthrough.html' target='_blank_'>basic bitcoin premium arbitrage walkthrough see here.</a> <br>For more general information, learn how to arbitrage <a href='http://www.investopedia.com/terms/c/cash-and-carry-arbitrage.asp' target='_blank'>premiums (positive delta)</a> and <a href='http://www.investopedia.com/terms/r/reverse-cash-and-carry-arbitrage.asp' target='_blank'>discounts (negative delta)</a>";
echo "<br>";
echo "Confused about what this stuff means? <a href='http://www.investopedia.com/articles/07/contango_backwardation.asp' target='_blank'>Learn about the Futures Curve and what it means for hedgers and speculators.</a>";
echo "<br>";
echo "Additionally, you can highlight the graph and column names to view <span class=\"hotspot\" onmouseover=\"tooltip.show('You will see a helpful little description just like this. Do the same below to help you out if rusty or new. The tooltips only appear on first exchange, not the rest.');\" onmouseout=\"tooltip.hide();\">tooltip descriptions marked with (?)</span>";
echo "<br>";


// ini_set('display_errors',1);
// error_reporting(E_ALL);

$btcffrjson = file_get_contents('https://api.bitfinex.com/v1/lendbook/BTC?limit_bids=1&limit_asks=0');
$btcffrarray = json_decode($btcffrjson, true);
if (isset($btcffrarray)) {
$btcffr = $btcffrarray['bids'][0]['rate'];
} else {
$btcffr = "N/A";
}
$usdffrjson = file_get_contents('https://api.bitfinex.com/v1/lendbook/USD?limit_bids=0&limit_asks=1');
$usdffrarray = json_decode($usdffrjson, true);
if (isset($usdffrarray)) {
$usdffr = $usdffrarray['asks'][0]['rate'];
} else {
$usdffr = "N/A";
}
echo "<br>";
print_r("For purposes of discount-rate estimation and BTC & USD borrow/lend rates in covered interest parity arbitrage modeling, you can use: ");
echo "<br>";
print_r("Bitfinex BTC yield (APY): ".$btcffr."%, USD yield (APY): ".$usdffr."%");

echo "<br>";

//Grab different dates for instruments

$cfinstrumentjson = file_get_contents('https://www.cryptofacilities.com/derivatives/api/v2/instruments');
$cfinstrumentarray = json_decode($cfinstrumentjson, true);
$cfinstruments=$cfinstrumentarray['instruments'];
$cfinstrumentcount=count($cfinstruments);

for( $x = 0; $x <= $cfinstrumentcount; $x++ ) {
	$cflasttradingtime=$cfinstruments[$x]['lastTradingTime'];
	$cfsymbol=substr($cfinstruments[$x]['symbol'],0,5);
	$cfticker=$cfinstruments[$x]['symbol'];
	$cftype=$cfinstruments[$x]['type'];
	if ($cftype == "futures" and $cfsymbol == "f-xbt") {
		$cfexpiry = strtotime($cflasttradingtime);
		$currentts = strtotime($cfinstrumentarray['serverTime']);
		$timetofunding = $cfexpiry-$currentts;
		$daysfunding=$timetofunding/60/60/24;
		if ($daysfunding<7) {
		$cfweekly=strtoupper(substr($cfticker,0,11)).substr($cfticker,11,5).strtoupper(substr($cfticker,16,2));
                $cfweeklyleft=$daysfunding;
                $cfweeklydate=substr($cflasttradingtime,0,10);
}elseif ($daysfunding < 14 and $daysfunding>7) {
		$cfbiweekly=strtoupper(substr($cfticker,0,11)).substr($cfticker,11,5).strtoupper(substr($cfticker,16,2));
                $cfbiweeklyleft=$daysfunding;
                $cfbiweeklydate=substr($cflasttradingtime,0,10);
}elseif ($daysfunding > 14 and $daysfunding<90) {
		$cfquarterly=strtoupper(substr($cfticker,0,11)).substr($cfticker,11,5).strtoupper(substr($cfticker,16,2));
                $cfquarterlyleft=$daysfunding;
                $cfquarterlydate=substr($cflasttradingtime,0,10);
}elseif ($daysfunding > 90 and $daysfunding<180) {
		$cfsemiannual=strtoupper(substr($cfticker,0,11)).substr($cfticker,11,5).strtoupper(substr($cfticker,16,2));
                $cfsemiannualleft=$daysfunding;
                $cfsemiannualdate=substr($cflasttradingtime,0,10);
}elseif ($daysfunding > 180 and $daysfunding<270) {
		$cftriquarterly=strtoupper(substr($cfticker,0,11)).substr($cfticker,11,5).strtoupper(substr($cfticker,16,2));
                $cftriquarterlyleft=$daysfunding;
                $cftriquarterlydate=substr($cflasttradingtime,0,10);
}

}

}




	$getokcindex = file_get_contents('https://www.okcoin.com/api/v1/future_index.do?symbol=btc_usd');
	$okcindex = json_decode($getokcindex, true);
    $theokcindex = $okcindex['future_index'];




	$getokcweekly = file_get_contents('https://www.okcoin.com/api/v1/future_ticker.do?symbol=btc_usd&contract_type=this_week');
	$okcweekly = json_decode($getokcweekly, true);
$theokcweekly = $okcweekly['ticker']['last'];



$weeklyspread = round(($theokcweekly - $theokcindex),2);
$weeklyspreadperc = ($weeklyspread / $theokcindex)*100;
$weeklyspreadperc2 = round($weeklyspreadperc, 2);
$theokcweeklybid = $okcweekly['ticker']['buy'];
$theokcweeklyask = $okcweekly['ticker']['sell'];
$theokcweeklyspread = round(($theokcweeklyask - $theokcweeklybid),2);
$theokcweeklyspreadperc = round(($theokcweeklyspread / $theokcweekly)*100,2); 

$dayofweek = date('w', strtotime(getdate()));
if ($dayofweek == 6) {
$okcwdays = 7;
$okcbdays = 14;
} else {
$okcwdays = (6-$dayofweek)+2;
$okcbdays = $okcwdays + 7;
}
$weeklyspreadpa2 = (pow(($theokcweekly/$theokcindex), (365/$cfweeklyleft))-1)*100;
$weeklyspreadpa = round($weeklyspreadpa2, 2);

	$getokcbiweekly = file_get_contents('https://www.okcoin.com/api/v1/future_ticker.do?symbol=btc_usd&contract_type=next_week');
	$okcbiweekly = json_decode($getokcbiweekly, true);
$theokcbiweekly = round(($okcbiweekly['ticker']['last']),2);
$biweeklyspread = round(($theokcbiweekly - $theokcindex),2);
$biweeklyspreadperc = ($biweeklyspread / $theokcindex)*100;
$biweeklyspreadperc2 = round($biweeklyspreadperc, 2);
$biweeklyspreadpa2 = (pow(($theokcbiweekly/$theokcindex), (365/$cfbiweeklyleft))-1)*100;
$biweeklyspreadpa = round($biweeklyspreadpa2, 2);
$theokcbiweeklybid = $okcbiweekly['ticker']['buy'];
$theokcbiweeklyask = $okcbiweekly['ticker']['sell'];
$theokcbiweeklyspread = round(($theokcbiweeklyask - $theokcbiweeklybid),2);
$theokcbiweeklyspreadperc = round(($theokcbiweeklyspread / $theokcbiweekly)*100, 2); 




	$getokcquarterly = file_get_contents('https://www.okcoin.com/api/v1/future_ticker.do?symbol=btc_usd&contract_type=quarter');
	$okcquarterly = json_decode($getokcquarterly, true);
$theokcquarterly = round(($okcquarterly['ticker']['last']),2);
$quarterlyspread = round(($theokcquarterly - $theokcindex),2);

$quarterlyspreadperc = ($quarterlyspread / $theokcindex)*100;
$quarterlyspreadperc2 = round($quarterlyspreadperc, 2);

$theokcquarterlybid = round(($okcquarterly['ticker']['buy']),2);
$theokcquarterlyask = round(($okcquarterly['ticker']['sell']),2);
$theokcquarterlyspread = round(($theokcquarterlyask - $theokcquarterlybid),2);
$theokcquarterlyspreadperc = round(($theokcquarterlyspread / $theokcquarterly)*100,2); 


$okcqdate = mktime(0, 0, 0, 3, 31, 2017, 0);
$today = time();
$difference = $okcqdate - $today;
if ($difference < 0) { $difference = 0; }
$okcqdays = floor($difference/60/60/24);

$quarterlyspreadpa2 = (pow(($theokcquarterly/$theokcindex), (365/$cfquarterlyleft))-1)*100;
$quarterlyspreadpa = round($quarterlyspreadpa2, 2);


// bitmex stuff

//quarterly

$dailyjson = file_get_contents('https://www.bitmex.com/api/v1/instrument?symbol=XBTH17&count=1&start=0&reverse=false');
$dailyarray = json_decode($dailyjson, true);
$bitmexdailyprice = $dailyarray[0]['lastPrice'];
$bitmexdailymark = $dailyarray[0]['fairPrice'];
$bitmexdailybid = $dailyarray[0]['bidPrice'];
$bitmexdailyask = $dailyarray[0]['askPrice'];

$bitmexdailybidaskspread = round(($bitmexdailyask - $bitmexdailybid),2);
$bitmexdailybidaskspreadperc = round(($bitmexdailybidaskspread / $bitmexdailyprice)*100,2); 

$bitmexindicative = $dailyarray[0]['indicativeSettlePrice'];
$bitmexdailyspread = round(($bitmexdailyprice - $bitmexindicative),2);
$bitmexdailyspreadperc = round(($bitmexdailyspread / $bitmexindicative)*100, 2);
$bitmexdailyspreadpa2 = round((pow(($bitmexdailyprice/$bitmexindicative), (365/$okcqdays))-1),2)*100;
$bitmexdailyspreadpa = round($bitmexdailyspreadpa2,2);
$bitmexdailyspreadtest = round((pow((1+($bitmexdailyspread / $bitmexindicative)), 365)-1),2)*100;



// CryptoFacilities stuff



$cfbpijson = file_get_contents('https://www.cryptofacilities.com/derivatives/api/cfbpi');
$cfbpiarray = json_decode($cfbpijson, true);
if (isset($cfbpiarray)) {
if ($cfbpiarray['result'] == "success") {
$cfbpi = $cfbpiarray['cf-bpi'];
} else {
$cfbpi = "0";
return;
} 
} else {
$cfbpi = "0";
return;
}

// cf contract weekly


$cfweeklyjson = file_get_contents('https://www.cryptofacilities.com/derivatives/api/ticker?tradeable='.$cfweekly.'&unit=USD');
$cfweeklyarray = json_decode($cfweeklyjson, true);

if (isset($cfweeklyarray)) {
if ($cfweeklyarray['result'] == "success") {
$cfweeklyprice = $cfweeklyarray['last'];
$cfweeklyspread = round(($cfweeklyprice - $cfbpi),2);
$cfweeklyspreadperc = round(($cfweeklyspread / $cfbpi)*100, 2);

$cfweeklyspreadpa2=(pow(($cfweeklyprice/$cfbpi), (365/$cfweeklyleft))-1)*100;
$cfweeklyspreadpa=round($cfweeklyspreadpa2,2);

$cfweeklybid = $cfweeklyarray['bid'];
$cfweeklyask = $cfweeklyarray['ask'];

$cfweeklybidaskspread = round(($cfweeklyask - $cfweeklybid),2);
$cfweeklybidaskspreadperc = round(($cfweeklybidaskspread / $cfweeklyprice)*100,2); 
$cfweeklymidprice=round(($cfweeklyask + $cfweeklybid)/2,2);

} else {
$cfweeklyprice = "0";
return;
} 
} else {
$cfweeklyprice ="0";
return;
}



//cf contract biweekly

$cfbiweeklyjson = file_get_contents('https://www.cryptofacilities.com/derivatives/api/ticker?tradeable='.$cfbiweekly.'&unit=USD');
$cfbiweeklyarray = json_decode($cfbiweeklyjson, true);

if (isset($cfbiweeklyarray)) {
if ($cfbiweeklyarray['result'] == "success") {
$cfbiweeklyprice = $cfbiweeklyarray['last'];
$cfbiweeklyspread = round(($cfbiweeklyprice - $cfbpi),2);
$cfbiweeklyspreadperc = round(($cfbiweeklyspread / $cfbpi)*100, 2);

$cfbiweeklyspreadpa2=(pow(($cfbiweeklyprice/$cfbpi), (365/($cfbiweeklyleft)))-1)*100;
$cfbiweeklyspreadpa=round($cfbiweeklyspreadpa2,2);

$cfbiweeklybid = $cfbiweeklyarray['bid'];
$cfbiweeklyask = $cfbiweeklyarray['ask'];

$cfbiweeklybidaskspread = round(($cfbiweeklyask - $cfbiweeklybid),2);
$cfbiweeklybidaskspreadperc = round(($cfbiweeklybidaskspread / $cfbiweeklyprice)*100,2); 

$cfbiweeklymidprice = round(($cfbiweeklyask + $cfbiweeklybid)/2,2);

} else {
$cfbiweeklyprice ="0";
return;
} 
} else {
$cfbiweeklyprice ="0";
return;
}


// cf mar


$cfdec16json = file_get_contents('https://www.cryptofacilities.com/derivatives/api/ticker?tradeable='.$cfquarterly.'&unit=USD');
$cfdec16array = json_decode($cfdec16json, true);

if (isset($cfdec16array)) {
if ($cfdec16array['result'] == "success") {

$cfdec16price = $cfdec16array['last'];
$cfdec16spread = round(($cfdec16price - $cfbpi),2);
$cfdec16spreadperc = round(($cfdec16spread / $cfbpi)*100, 2);

$cfdec16spreadpa2=(pow(($cfdec16price/$cfbpi), (365/$cfquarterlyleft))-1)*100;
$cfdec16spreadpa=round($cfdec16spreadpa2,2);


$cfdec16bid = $cfdec16array['bid'];
$cfdec16ask = $cfdec16array['ask'];

$cfdec16bidaskspread = round(($cfdec16ask - $cfdec16bid),2);
$cfdec16bidaskspreadperc = round(($cfdec16bidaskspread / $cfdec16price)*100,2); 
$cfdec16midprice =round(($cfdec16ask + $cfdec16bid)/2,2);


} else {
$cfdec16price = "0";
return;
} 
} else {
$cfdec16price = "0";
return;
}

//cf contract semianually

$cfsemiannualjson = file_get_contents('https://www.cryptofacilities.com/derivatives/api/ticker?tradeable='.$cfsemiannual.'&unit=USD');
$cfsemiannualarray = json_decode($cfsemiannualjson, true);

if (isset($cfsemiannualarray)) {
if ($cfsemiannualarray['result'] == "success") {
$cfsemiannualprice = $cfsemiannualarray['last'];
$cfsemiannualspread = round(($cfsemiannualprice - $cfbpi),2);
$cfsemiannualspreadperc = round(($cfsemiannualspread / $cfbpi)*100, 2);

$cfsemiannualspreadpa2=(pow(($cfsemiannualprice/$cfbpi), (365/($cfsemiannualleft)))-1)*100;
$cfsemiannualspreadpa=round($cfsemiannualspreadpa2,2);

$cfsemiannualbid = $cfsemiannualarray['bid'];
$cfsemiannualask = $cfsemiannualarray['ask'];

$cfsemiannualbidaskspread = round(($cfsemiannualask - $cfsemiannualbid),2);
$cfsemiannualbidaskspreadperc = round(($cfsemiannualbidaskspread / $cfsemiannualprice)*100,2); 

$cfsemiannualmidprice = round(($cfsemiannualask + $cfsemiannualbid)/2,2);

} else {
$cfsemiannualprice ="0";
return;
} 
} else {
$cfsemiannualprice ="0";
return;
}

//cf contract triquarterly

$cftriquarterlyjson = file_get_contents('https://www.cryptofacilities.com/derivatives/api/ticker?tradeable='.$cftriquarterly.'&unit=USD');
$cftriquarterlyarray = json_decode($cftriquarterlyjson, true);

if (isset($cftriquarterlyarray)) {
if ($cftriquarterlyarray['result'] == "success") {
$cftriquarterlyprice = $cftriquarterlyarray['last'];
$cftriquarterlyspread = round(($cftriquarterlyprice - $cfbpi),2);
$cftriquarterlyspreadperc = round(($cftriquarterlyspread / $cfbpi)*100, 2);

$cftriquarterlyspreadpa2=(pow(($cftriquarterlyprice/$cfbpi), (365/($cftriquarterlyleft)))-1)*100;
$cftriquarterlyspreadpa=round($cftriquarterlyspreadpa2,2);

$cftriquarterlybid = $cftriquarterlyarray['bid'];
$cftriquarterlyask = $cftriquarterlyarray['ask'];

$cftriquarterlybidaskspread = round(($cftriquarterlyask - $cftriquarterlybid),2);
$cftriquarterlybidaskspreadperc = round(($cftriquarterlybidaskspread / $cftriquarterlyprice)*100,2); 

$cftriquarterlymidprice =round(($cftriquarterlyask + $cftriquarterlybid)/2,2);

} else {
$cftriquarterlyprice ="0";
return;
} 
} else {
$cftriquarterlyprice ="0";
return;
}


// Deribit 

$nextfriday = strtotime("next friday");
$currentts = strtotime("now");
$month=strtoupper(date('M', $nextfriday));
$day=date('d', $nextfriday);
$year=date('y', $nextfriday);
$weeklycont="BTC-".$day.$month.$year;


$derijson = file_get_contents('https://www.deribit.com/api/v1/public/getlasttrades?instrument='.$weeklycont);
$deriarray = json_decode($derijson, true);
$deriprice = $deriarray['result'][0]['price'];
$deriindex = $deriarray['result'][0]['indexPrice'];

$derijson2 = file_get_contents('https://www.deribit.com/api/v1/public/getorderbook?instrument='.$weeklycont);
$deriarray2 = json_decode($derijson2, true);

$deribid = $deriarray2['result']['bids'][0]['price'];
$deriask = $deriarray2['result']['asks'][0]['price'];

$deribidaskspread = round(($deriask - $deribid),2);
$deribidaskspreadperc = round(($deribidaskspread / $deriprice)*100,2); 

$derispread = round(($deriprice - $deriindex),2);
$derispreadperc = round(($derispread / $deriindex)*100, 2);
$derispreadpa2 = round((pow(($deriprice/$deriindex), (365/$cfweeklyleft))-1),2)*100;
$derispreadpa = round($derispreadpa2,2);


echo "<html xmlns='http://www.w3.org/1999/xhtml'>\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
echo "<title>Untitled Document</title>\n";
echo "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js\"></script>\n";
echo "<script src=\"https://code.highcharts.com/stock/highstock.js\"></script>\n";
echo "<script src=\"https://code.highcharts.com/stock/modules/exporting.js\"></script>\n";
echo "\n";
echo "<script>\n";
echo "$(function () {\n";
echo "    Highcharts.chart('container', {\n";
echo "        title: {\n";
echo "            text: 'Bitcoin Futures Curve',\n";
echo "            x: -20 //center\n";
echo "        },\n";
echo "        xAxis: {\n";
echo "            categories: ['Index', '$cfweeklydate', '$cfbiweeklydate', '$cfquarterlydate', '$cfsemiannualdate', '$cftriquarterlydate']\n";
echo "        },\n";
echo "        yAxis: {\n";
echo "            title: {\n";
echo "                text: 'USD ($)'\n";
echo "            },\n";
echo "            plotLines: [{\n";
echo "                value: 0,\n";
echo "                width: 1,\n";
echo "                color: '#808080'\n";
echo "            }]\n";
echo "        },\n";
echo "        tooltip: {\n";
echo "            valueSuffix: ' USD',\n";
echo "            shared: true\n";
echo "        },\n";
echo "        legend: {\n";
echo "            layout: 'vertical',\n";
echo "            align: 'right',\n";
echo "            verticalAlign: 'middle',\n";
echo "            borderWidth: 0\n";
echo "        },\n";
echo "        series: [{\n";
echo "            name: 'OKCoin',\n";
echo "            data: [".$theokcindex.", ".$theokcweekly.", ".$theokcbiweekly.", ".$theokcquarterly.", null, null]\n";
echo "        }, {\n";
echo "            name: 'CryptoFacilities',\n";
echo "            data: [".$cfbpi.", ".$cfweeklymidprice.", ".$cfbiweeklymidprice.", ".$cfdec16midprice.", ".$cfsemiannualmidprice.", ".$cftriquarterlymidprice."]\n";
echo "        }, {\n";
echo "            name: 'Deribit',\n";
echo "            visible: false,\n";
echo "            data: [".$deriindex.", ".$deriprice.", null, null, null, null]\n";
echo "        }, {\n";
echo "            connectNulls: true,\n";
echo "            name: 'BitMEX',\n";
echo "            visible: false,\n";
echo "            data: [".$bitmexindicative.", null , null, ".$bitmexdailyprice.", null, null]\n";
echo "        }]\n";
echo "    });\n";
echo "});\n";
echo "</script>\n";
echo "    <div id=\"container\" style=\"min-width: 400px; height: 400px; max-width: 800px; margin: 0 auto\"></div>\n";

echo "<a href='https://www.okcoin.com/?invid=2029811'><h1>OKCoin</h1></a>\n";
echo "<span class=\"hotspot\" onmouseover=\"tooltip.show('OKCoin index = Arithmetic mean of Bitstamp, Bitfinex, OKCoinUSD, Huobi CNY, OKCoin CNY, BTCC CNY (Just add prices of all these exchanges and divide by 6).');\" onmouseout=\"tooltip.hide();\">Index (?):</span> $".$theokcindex." <a href=\"https://www.okcoin.com/future/market.do?symbol=2\">[Components]</a>";
echo "<style type=\"text/css\">\n";
echo ".tg  {border-collapse:collapse;border-spacing:0;}\n";
echo ".tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}\n";
echo ".tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}\n";
echo ".tg .tg-c9cr{font-style:italic}\n";
echo ".tg .tg-e3zv{font-weight:bold}\n";
echo ".tg .tg-yw4l{vertical-align:top}\n";
echo "</style>\n";
echo "<table class=\"tg\">\n";
echo "  <tr>\n";
echo "    <th class=\"tg-c9cr\"><span class=\"hotspot\" onmouseover=\"tooltip.show('The contract type is categorized by the settlement/expiration date.');\" onmouseout=\"tooltip.hide();\">Contract Type(?)</span></th>\n";
echo "    <th class=\"tg-yw4l\"><span class=\"hotspot\" onmouseover=\"tooltip.show('Note: this shows the last traded price, not the midpoint of the orderbooks best bid and offer. In illiquid markets this can be a delayed number, beware of this.');\" onmouseout=\"tooltip.hide();\">Price(?)</span></th>\n";
echo "    <th class=\"tg-c9cr\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This shows the premium of the contract to the index in nominal dollar terms. Simply: (Price - Index)');\" onmouseout=\"tooltip.hide();\">Premium ($) (?) </span></th>\n";
echo "    <th class=\"tg-c9cr\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This shows the premium of the contract price to the Index in % terms. Formula: [(Price - Index)/Index]*100');\" onmouseout=\"tooltip.hide();\">Premium (%) (?)</span></th>\n";
echo "    <th class=\"tg-c9cr\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This shows the premium of the contract to the Index in annualized % terms. This is so you can make apples-to-apples comparisons in the premium returns for contracts of different maturities for arbitrage purposes. Formula: [(Price/Index)^(360/#days to settlement)-1]*100');\" onmouseout=\"tooltip.hide();\"> Premium (% APY) (?)</span></th>\n";
echo "    <th class=\"tg-c9cr\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This shows the spread between the bid and ask on the given contract, in nominal dollar terms.');\" onmouseout=\"tooltip.hide();\">Bid-Ask Spread ($) (?)</span></th>\n";
echo "    <th class=\"tg-c9cr\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This shows the spread between the bid and ask on the given contract, in percentage terms.');\" onmouseout=\"tooltip.hide();\">Bid-Ask (%) (?)</span></th>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This contract expires every Friday (like all weekly contracts) at 8:00AM UTC');\" onmouseout=\"tooltip.hide();\">Weekly (".$cfweeklydate.") (?)</span></td>\n";
echo "    <td class=\"tg-yw4l\">$".$theokcweekly."</td>\n";
echo "    <td class=\"tg-031e\">$".$weeklyspread."</td>\n";
echo "    <td class=\"tg-031e\">".$weeklyspreadperc2."%</td>\n";
echo "    <td class=\"tg-031e\">".$weeklyspreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".$theokcweeklyspread."</td>\n";
echo "    <td class=\"tg-031e\">".$theokcweeklyspreadperc."%</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This contract expires next Friday at 8:00AM UTC');\" onmouseout=\"tooltip.hide();\">BiWeekly (".$cfbiweeklydate.") (?)</span></td>\n";
echo "    <td class=\"tg-yw4l\">$".$theokcbiweekly."</td>\n";
echo "    <td class=\"tg-031e\">$".$biweeklyspread."</td>\n";
echo "    <td class=\"tg-031e\">".$biweeklyspreadperc2."%</td>\n";
echo "    <td class=\"tg-031e\">".$biweeklyspreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".$theokcbiweeklyspread."</td>\n";
echo "    <td class=\"tg-031e\">".$theokcbiweeklyspreadperc."%</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\"><span class=\"hotspot\" onmouseover=\"tooltip.show('The quarterly contract will expire on December 30, 2016');\" onmouseout=\"tooltip.hide();\">Quarterly (".$cfquarterlydate.") (?)</span></td>\n";
echo "    <td class=\"tg-yw4l\">$".$theokcquarterly."</td>\n";
echo "    <td class=\"tg-031e\">$".$quarterlyspread."</td>\n";
echo "    <td class=\"tg-031e\">".$quarterlyspreadperc2."%</td>\n";
echo "    <td class=\"tg-031e\">".$quarterlyspreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".$theokcquarterlyspread."</td>\n";
echo "    <td class=\"tg-031e\">".$theokcquarterlyspreadperc."%</td>\n";
echo "  </tr>\n";

echo "</table>";

echo "<a href='https://www.cryptofacilities.com/derivatives/56e4abf4-1bef-4bf2-9ac0-1677da1c078d'><h1>CryptoFacilities</h1></a>\n";
echo "<span class=\"hotspot\" onmouseover=\"tooltip.show('CryptoFacilities uses their custom hourly volume-weighted average price (CF-HBPI), it performs a quarterly review of the index components and reweights appropriately based on their cumulative volume contribtion. The criteria excludes those who charge 0%, so it only has USD contributors, as of now: BTC-E, Bitfinex, Bitstamp, itBit, and Coinbase.');\" onmouseout=\"tooltip.hide();\">Index (?):</span> $".$cfbpi."  <a href=\"https://www.cryptofacilities.com/derivatives/resources#indiCalculate\">[Components]</a>";
echo "<style type=\"text/css\">\n";
echo ".tg  {border-collapse:collapse;border-spacing:0;}\n";
echo ".tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}\n";
echo ".tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}\n";
echo ".tg .tg-c9cr{font-style:italic}\n";
echo ".tg .tg-e3zv{font-weight:bold}\n";
echo ".tg .tg-yw4l{vertical-align:top}\n";
echo "</style>\n";
echo "<table class=\"tg\">\n";
echo "  <tr>\n";
echo "    <th class=\"tg-c9cr\">Contract Type</th>\n";
echo "    <th class=\"tg-yw4l\">Price</th>\n";
echo "    <th class=\"tg-c9cr\">Premium ($)</th>\n";
echo "    <th class=\"tg-c9cr\">Premium (%)</th>\n";
echo "    <th class=\"tg-c9cr\">Annualized Premium (%)</th>\n";
echo "    <th class=\"tg-c9cr\">Bid-Ask Spread ($)</th>\n";
echo "    <th class=\"tg-c9cr\">Bid-Ask (%)</th>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Weekly (".$cfweeklydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".$cfweeklyprice."</td>\n";
echo "    <td class=\"tg-031e\">$".$cfweeklyspread."</td>\n";
echo "    <td class=\"tg-031e\">".$cfweeklyspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$cfweeklyspreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".$cfweeklybidaskspread."</td>\n";
echo "    <td class=\"tg-031e\">".$cfweeklybidaskspreadperc."%</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">BiWeekly (".$cfbiweeklydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".$cfbiweeklyprice."</td>\n";
echo "    <td class=\"tg-031e\">$".$cfbiweeklyspread."</td>\n";
echo "    <td class=\"tg-031e\">".$cfbiweeklyspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$cfbiweeklyspreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".$cfbiweeklybidaskspread."</td>\n";
echo "    <td class=\"tg-031e\">".$cfbiweeklybidaskspreadperc."%</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Quarterly (".$cfquarterlydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".$cfdec16price."</td>\n";
echo "    <td class=\"tg-031e\">$".$cfdec16spread."</td>\n";
echo "    <td class=\"tg-031e\">".$cfdec16spreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$cfdec16spreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".$cfdec16bidaskspread."</td>\n";
echo "    <td class=\"tg-031e\">".$cfdec16bidaskspreadperc."%</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Semianually (".$cfsemiannualdate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".$cfsemiannualprice."</td>\n";
echo "    <td class=\"tg-031e\">$".$cfsemiannualspread."</td>\n";
echo "    <td class=\"tg-031e\">".$cfsemiannualspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$cfsemiannualspreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".$cfsemiannualbidaskspread."</td>\n";
echo "    <td class=\"tg-031e\">".$cfsemiannualbidaskspreadperc."%</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">TriQuarterly (".$cftriquarterlydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".$cftriquarterlyprice."</td>\n";
echo "    <td class=\"tg-031e\">$".$cftriquarterlyspread."</td>\n";
echo "    <td class=\"tg-031e\">".$cftriquarterlyspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$cftriquarterlyspreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".$cftriquarterlybidaskspread."</td>\n";
echo "    <td class=\"tg-031e\">".$cftriquarterlybidaskspreadperc."%</td>\n";
echo "  </tr>\n";
echo "</table>";
echo "<br>";
echo "<a href='https://www.deribit.com'><h1>Deribit</h1></a>\n";
echo "<span class=\"hotspot\" onmouseover=\"tooltip.show('Deribit index is based on multiple BTC/USD exchanges.');\" onmouseout=\"tooltip.hide();\">Index (?):</span> $".$deriindex." <a href=\"https://test.deribit.com/main#/prinx_chart\">[Components]</a>";
echo "<style type=\"text/css\">\n";
echo ".tg  {border-collapse:collapse;border-spacing:0;}\n";
echo ".tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}\n";
echo ".tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}\n";
echo ".tg .tg-c9cr{font-style:italic}\n";
echo ".tg .tg-e3zv{font-weight:bold}\n";
echo ".tg .tg-yw4l{vertical-align:top}\n";
echo "</style>\n";
echo "<table class=\"tg\">\n";
echo "  <tr>\n";
echo "    <th class=\"tg-c9cr\">Contract Type</th>\n";
echo "    <th class=\"tg-yw4l\">Price</th>\n";
echo "    <th class=\"tg-c9cr\">Premium ($)</th>\n";
echo "    <th class=\"tg-c9cr\">Premium (%)</th>\n";
echo "    <th class=\"tg-c9cr\">Annualized Premium (%)</th>\n";
echo "    <th class=\"tg-c9cr\">Bid-Ask Spread ($)</th>\n";
echo "    <th class=\"tg-c9cr\">Bid-Ask (%)</th>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Weekly (".$cfweeklydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".$deriprice."</td>\n";
echo "    <td class=\"tg-031e\">$".$derispread."</td>\n";
echo "    <td class=\"tg-031e\">".$derispreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$derispreadpa2."%</td>\n";
echo "    <td class=\"tg-031e\">$".$deribidaskspread."</td>\n";
echo "    <td class=\"tg-031e\">".$deribidaskspreadperc."%</td>\n";
echo "  </tr>\n";
echo "</table>";
echo "<br>";

echo "<a href='https://www.bitmex.com/register/RrmvSe'><h1>BitMEX</h1></a>\n";
echo "<span class=\"hotspot\" onmouseover=\"tooltip.show('BitMEX index is based on a third party 5-exchange XBX Index from Kaiko.');\" onmouseout=\"tooltip.hide();\">Index (?):</span> $".$bitmexindicative." <a href=\"https://tradeblock.com/markets/\">[Components]</a>";
echo "<style type=\"text/css\">\n";
echo ".tg  {border-collapse:collapse;border-spacing:0;}\n";
echo ".tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}\n";
echo ".tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}\n";
echo ".tg .tg-c9cr{font-style:italic}\n";
echo ".tg .tg-e3zv{font-weight:bold}\n";
echo ".tg .tg-yw4l{vertical-align:top}\n";
echo "</style>\n";
echo "<table class=\"tg\">\n";
echo "  <tr>\n";
echo "    <th class=\"tg-c9cr\">Contract Type</th>\n";
echo "    <th class=\"tg-yw4l\">Price</th>\n";
echo "    <th class=\"tg-c9cr\">Premium ($)</th>\n";
echo "    <th class=\"tg-c9cr\">Premium (%)</th>\n";
echo "    <th class=\"tg-c9cr\">Annualized Premium (%)</th>\n";
echo "    <th class=\"tg-c9cr\">Bid-Ask Spread ($)</th>\n";
echo "    <th class=\"tg-c9cr\">Bid-Ask (%)</th>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Quarterly (".$cfquarterlydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".$bitmexdailyprice."</td>\n";
echo "    <td class=\"tg-031e\">$".$bitmexdailyspread."</td>\n";
echo "    <td class=\"tg-031e\">".$bitmexdailyspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$bitmexdailyspreadpa2."%</td>\n";
echo "    <td class=\"tg-031e\">$".$bitmexdailybidaskspread."</td>\n";
echo "    <td class=\"tg-031e\">".$bitmexdailybidaskspreadperc."%</td>\n";
echo "  </tr>\n";
echo "</table>";
echo "<br>";
print_r("Want to show appreciation or support efforts to make this to look prettier? Send BTC to author: 3CnxCCrkfJGrjg6XCdVxGEbbcDgQCYGLr6");
echo "<img src='https://chart.googleapis.com/chart?cht=qr&chs=50x50&chl=3CnxCCrkfJGrjg6XCdVxGEbbcDgQCYGLr6'>";
echo "</body>";
echo "</html>";


?>

