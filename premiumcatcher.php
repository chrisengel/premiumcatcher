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

//get tickers

$cftickerjson = file_get_contents('https://www.cryptofacilities.com/derivatives/api/v3/tickers');
$cftickerarray = json_decode($cftickerjson, true);
$cftickers=$cftickerarray['tickers'];
$cftickercount=count($cftickers);


for( $x = 0; $x <= $cfinstrumentcount; $x++ ) {
	$cflasttradingtime=$cfinstruments[$x]['lastTradingTime'];
	$cfsymbol=substr($cfinstruments[$x]['symbol'],0,5);
	$cffullsymbol=$cfinstruments[$x]['symbol'];
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
                $cfweeklysymbol=$cffullsymbol;
                $cfweeklydate=substr($cflasttradingtime,0,10);
}elseif ($daysfunding < 14 and $daysfunding>7) {
		$cfbiweekly=strtoupper(substr($cfticker,0,11)).substr($cfticker,11,5).strtoupper(substr($cfticker,16,2));
                $cfbiweeklyleft=$daysfunding;
                $cfbiweeklydate=substr($cflasttradingtime,0,10);
                $cfbiweeklysymbol=$cffullsymbol;
}elseif ($daysfunding > 14 and $daysfunding<90) {
		$cfquarterly=strtoupper(substr($cfticker,0,11)).substr($cfticker,11,5).strtoupper(substr($cfticker,16,2));
                $cfquarterlyleft=$daysfunding;
                $cfquarterlydate=substr($cflasttradingtime,0,10);
                $cfquarterlysymbol=$cffullsymbol;
		$cfquarterlydateexp=substr($cfquarterlydate,0,5).substr($cfquarterlydate,6,2).(substr($cfquarterlydate,8,3)+14);
}elseif ($daysfunding > 90 and $daysfunding<180) {
		$cfsemiannual=strtoupper(substr($cfticker,0,11)).substr($cfticker,11,5).strtoupper(substr($cfticker,16,2));
                $cfsemiannualleft=$daysfunding;
                $cfsemiannualdate=substr($cflasttradingtime,0,10);
                $cfsemiannualsymbol=$cffullsymbol;
		$cfsemiannualdateexp=substr($cfsemiannualdate,0,5).substr($cfsemiannualdate,6,2).(substr($cfsemiannualdate,8,3)+14);
}elseif ($daysfunding > 180 and $daysfunding<270) {
		$cftriquarterly=strtoupper(substr($cfticker,0,11)).substr($cfticker,11,5).strtoupper(substr($cfticker,16,2));
                $cftriquarterlyleft=$daysfunding;
                $cftriquarterlydate=substr($cflasttradingtime,0,10);
                $cftriquarterlysymbol=$cffullsymbol;
		$cftriquarterlydateexp=substr($cftriquarterlydate,0,5).substr($cftriquarterlydate,6,2).(substr($cftriquarterlydate,8,3)+14);
}

}

}

// cryptofacilities does not always issue contracts symmetrically by time, so do some errrochecks

if (is_null($cfquarterlysymbol)) {
		$cfquarterly=$cfsemiannual;
                $cfquarterlyleft=$cfsemiannualleft;
                $cfquarterlydate=$cfsemiannualdate;
                $cfquarterlysymbol=$cfsemiannualsymbol;
		$cfquarterlydateexp=$cfsemiannualdateexp;
}

//grab volumes from tickers

for( $x = 0; $x <= $cftickercount; $x++ ) {

	$cftickersymbol=$cftickers[$x]['symbol'];
	if ($cftickersymbol == $cfweeklysymbol) {
		$cfwvol=$cftickers[$x]['vol24h'];
}elseif ($cftickersymbol == $cfbiweeklysymbol) {
		$cfbwvol=$cftickers[$x]['vol24h'];
}elseif ($cftickersymbol == $cfquarterlysymbol) {
		$cfqvol=$cftickers[$x]['vol24h'];
}elseif ($cftickersymbol == $cfsemiannualsymbol) {
		$cfsavol=$cftickers[$x]['vol24h'];
}elseif ($cftickersymbol == $cftriquarterlysymbol) {
		$cftqvol=$cftickers[$x]['vol24h'];
}


}

if (!isset($cfbiweekly)) { 
$cfbiweekly=$cfweekly;
$cfbiweeklyleft=$cfweeklyleft; 
$cfbiweeklydate=$cfweeklydate; 
}



	$getokcindex = file_get_contents('https://www.okcoin.com/api/v1/future_index.do?symbol=btc_usd');
	$okcindex = json_decode($getokcindex, true);
    $theokcindex = $okcindex['future_index'];




	$getokcweekly = file_get_contents('https://www.okcoin.com/api/v1/future_ticker.do?symbol=btc_usd&contract_type=this_week');
	$okcweekly = json_decode($getokcweekly, true);
$theokcweekly = $okcweekly['ticker']['last'];

$okcwvol = $okcweekly['ticker']['vol']/2;

$weeklyspread = round(($theokcweekly - $theokcindex),2);
$weeklyspreadperc = ($weeklyspread / $theokcindex)*100;
$weeklyspreadperc2 = round($weeklyspreadperc, 2);
$theokcweeklybid = $okcweekly['ticker']['buy'];
$theokcweeklyask = $okcweekly['ticker']['sell'];
$theokcweeklyspread = round(($theokcweeklyask - $theokcweeklybid),2);
$theokcweeklyspreadperc = round(($theokcweeklyspread / $theokcweekly)*100,2); 

$dayofweek = date('N', strtotime(gmdate("M d Y H:i:s")));

$okcwkexpire=strtotime("next Friday 8 AM UTC");
$nowgmt=gmdate("Y-m-d\TH:i:s\Z");
$nowgmt2=strtotime($nowgmt);
$okcdiff=$okcwkexpire-$nowgmt2;
$okcwkmins=$okcdiff/60;


$currenthours=gmdate("H");

if ($dayofweek == 6) {
$okcwdays = 6;
} elseif ($dayofweek == 7) {
$okcwdays = 5;
} elseif ($dayofweek == 5 and $currenthours > 8) {
$okcwdays = 7;
} elseif ($dayofweek == 5 and $currenthours < 8) {
$okcwdays = 1;
} else {
$okcwdays = (5-$dayofweek);
}
$okcbdays = $okcwdays + 7;

$okcbimins=$okcwkmins+(60*24*7);

$weeklyspreadpa2 = (pow(($theokcweekly/$theokcindex), (525600/$okcwkmins))-1)*100;
$weeklyspreadpa = round($weeklyspreadpa2, 2);


if ($weeklyspreadpa > 1000) { $weeklyspreadpa=1000; }

$weeklyspreadpd2 = (pow(($theokcweekly/$theokcindex), (1/$okcwdays))-1)*100;
$weeklyspreadpd = round($weeklyspreadpd2, 2);


	$getokcbiweekly = file_get_contents('https://www.okcoin.com/api/v1/future_ticker.do?symbol=btc_usd&contract_type=next_week');
	$okcbiweekly = json_decode($getokcbiweekly, true);
$theokcbiweekly = round(($okcbiweekly['ticker']['last']),2);
$biweeklyspread = round(($theokcbiweekly - $theokcindex),2);
$biweeklyspreadperc = ($biweeklyspread / $theokcindex)*100;
$biweeklyspreadperc2 = round($biweeklyspreadperc, 2);
$biweeklyspreadpa2 = (pow(($theokcbiweekly/$theokcindex), (525600/$okcbimins))-1)*100;
$biweeklyspreadpa = round($biweeklyspreadpa2, 2);

$biweeklyspreadpd2 = (pow(($theokcbiweekly/$theokcindex), (1/$okcbdays))-1)*100;
$biweeklyspreadpd = round($biweeklyspreadpd2, 2);

$theokcbiweeklybid = $okcbiweekly['ticker']['buy'];
$theokcbiweeklyask = $okcbiweekly['ticker']['sell'];
$theokcbiweeklyspread = round(($theokcbiweeklyask - $theokcbiweeklybid),2);
$theokcbiweeklyspreadperc = round(($theokcbiweeklyspread / $theokcbiweekly)*100, 2); 


$okcbwvol = $okcbiweekly['ticker']['vol']/2;



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

$quarterlyspreadpd2 = (pow(($theokcquarterly/$theokcindex), (1/$cfquarterlyleft))-1)*100;
$quarterlyspreadpd = round($quarterlyspreadpd2, 2);


$okcqvol = $okcquarterly['ticker']['vol']/2;


// bitmex stuff


$grabmex = file_get_contents('https://www.bitmex.com/api/v1/instrument?filter=%7B%22state%22%3A%20%22Open%22%7D&count=100&reverse=false');
                $grabmexarray = json_decode($grabmex, true);
               
		foreach ($grabmexarray as $x) {
			if ($x['symbol'] == "XBTM17") {
			$btcsymbol=$x['symbol'];
			$btcindex=$x['indicativeSettlePrice'];
			$btcquote=$x['midPrice'];
			$qtlyexpire=strtotime($x['expiry']);
			$bmtimestamp=strtotime($x['timestamp']);
			$btcbid=$x['bidPrice'];
			$btcask=$x['askPrice'];
			$btcvol=$x['turnover24h']/100000000;
			} elseif ($x['symbol'] == "XBTUSD") {
			$xbtsymbol=$x['symbol'];
			$xbtindex=$x['indicativeSettlePrice'];
			$xbtquote=$x['midPrice'];
			$xbtbid=$x['bidPrice'];
			$xbtask=$x['askPrice'];
			$xbtvol=$x['turnover24h']/100000000;
			} elseif ($x['symbol'] == "DASHM17") {
			$dashsymbol=$x['symbol'];
			$dashindex=$x['indicativeSettlePrice'];
			$dashquote=$x['midPrice'];
			$dashbid=$x['bidPrice'];
			$dashask=$x['askPrice'];
			$dashvol=$x['turnover24h']/100000000;
			} elseif ($x['symbol'] == "ETHM17") {
			$ethsymbol=$x['symbol'];
			$ethindex=$x['indicativeSettlePrice'];
			$ethquote=$x['midPrice'];
			$ethbid=$x['bidPrice'];
			$ethask=$x['askPrice'];
			$ethvol=$x['turnover24h']/100000000;
			} elseif ($x['symbol'] == "ETC7D") {
			$etcsymbol=$x['symbol'];
			$etcindex=$x['indicativeSettlePrice'];
			$etcquote=$x['midPrice'];
			$wklyexpire=strtotime($x['expiry']);
			$etcbid=$x['bidPrice'];
			$etcask=$x['askPrice'];
			$etcvol=$x['turnover24h']/100000000;
			} elseif ($x['symbol'] == "REP7D") {
			$repsymbol=$x['symbol'];
			$repindex=$x['indicativeSettlePrice'];
			$repquote=$x['midPrice'];
			$repbid=$x['bidPrice'];
			$repask=$x['askPrice'];
			$repvol=$x['turnover24h']/100000000;
			} elseif ($x['symbol'] == "FCTM17") {
			$fctsymbol=$x['symbol'];
			$fctindex=$x['indicativeSettlePrice'];
			$fctquote=$x['midPrice'];
			$fctbid=$x['bidPrice'];
			$fctask=$x['askPrice'];
			$fctvol=$x['turnover24h']/100000000;
			} elseif ($x['symbol'] == "LTCM17") {
			$ltcsymbol=$x['symbol'];
			$ltcindex=$x['indicativeSettlePrice'];
			$ltcquote=$x['midPrice'];
			$ltcbid=$x['bidPrice'];
			$ltcask=$x['askPrice'];
			$ltcvol=$x['turnover24h']/100000000;
			} elseif ($x['symbol'] == "XMRM17") {
			$xmrsymbol=$x['symbol'];
			$xmrindex=$x['indicativeSettlePrice'];
			$xmrquote=$x['midPrice'];
			$xmrbid=$x['bidPrice'];
			$xmrask=$x['askPrice'];
			$xmrvol=$x['turnover24h']/100000000;
			} elseif ($x['symbol'] == "ZECM17") {
			$zecsymbol=$x['symbol'];
			$zecindex=$x['indicativeSettlePrice'];
			$zecquote=$x['midPrice'];
			$zecbid=$x['bidPrice'];
			$zecask=$x['askPrice'];
			$zecvol=$x['turnover24h']/100000000;
			} elseif ($x['symbol'] == "XRPM17") {
			$xrpsymbol=$x['symbol'];
			$xrpindex=$x['indicativeSettlePrice'];
			$xrpquote=$x['midPrice'];
			$xrpbid=$x['bidPrice'];
			$xrpask=$x['askPrice'];
			$xrpvol=$x['turnover24h']/100000000;
			}
		}


                $timetoqtr = $qtlyexpire-$bmtimestamp;
                $timetowk = $wklyexpire-$bmtimestamp;
		$daystoqtr=$timetoqtr/60/60/24;
		$daystowk=$timetowk/60/60/24;

		$btcprem=$btcquote-$btcindex;
		$btcpremp=100*(($btcquote/$btcindex)-1);
                $btcprempa =(pow(($btcquote/$btcindex),(365/$daystoqtr))-1)*100;
		$btcspread=$btcask-$btcbid;
		$btcspreadpct=100*$btcspread/$btcquote;

		$xbtprem=$xbtquote-$xbtindex;
		$xbtpremp=100*(($xbtquote/$xbtindex)-1);
                $xbtprempa =(pow(($xbtquote/$xbtindex),(365/$daystoqtr))-1)*100;
		$xbtspread=$xbtask-$xbtbid;
		$xbtspreadpct=100*$xbtspread/$xbtquote;




		$dashprem=$dashquote-$dashindex;
		$dashpremp=100*(($dashquote/$dashindex)-1);
                $dashprempa =(pow(($dashquote/$dashindex),(365/$daystoqtr))-1)*100;

		$dashspread=$dashask-$dashbid;
		$dashspreadpct=100*$dashspread/$dashquote;

	$ethprem=$ethquote-$ethindex;
		$ethpremp=100*(($ethquote/$ethindex)-1);
                $ethprempa =(pow(($ethquote/$ethindex),(365/$daystoqtr))-1)*100;
		$ethspread=$ethask-$ethbid;
		$ethspreadpct=100*$ethspread/$ethquote;

	$etcprem=$etcquote-$etcindex;
		$etcpremp=100*(($etcquote/$etcindex)-1);
                $etcprempa =(pow(($etcquote/$etcindex),(365/$daystowk))-1)*100;
		$etcspread=$etcask-$etcbid;
		$etcspreadpct=100*$etcspread/$etcquote;

	$fctprem=$fctquote-$fctindex;
		$fctpremp=100*(($fctquote/$fctindex)-1);
                $fctprempa =(pow(($fctquote/$fctindex),(365/$daystoqtr))-1)*100;
		$fctspread=$fctask-$fctbid;
		$fctspreadpct=100*$fctspread/$fctquote;

	$ltcprem=$ltcquote-$ltcindex;
		$ltcpremp=100*(($ltcquote/$ltcindex)-1);
                $ltcprempa =(pow(($ltcquote/$ltcindex),(365/$daystoqtr))-1)*100;
		$ltcspread=$ltcask-$ltcbid;
		$ltcspreadpct=100*$ltcspread/$ltcquote;

	$xmrprem=$xmrquote-$xmrindex;
		$xmrpremp=100*(($xmrquote/$xmrindex)-1);
                $xmrprempa =(pow(($xmrquote/$xmrindex),(365/$daystoqtr))-1)*100;
		$xmrspread=$xmrask-$xmrbid;
		$xmrspreadpct=100*$xmrspread/$xmrquote;

	$zecprem=$zecquote-$zecindex;
		$zecpremp=100*(($zecquote/$zecindex)-1);
                $zecprempa =(pow(($zecquote/$zecindex),(365/$daystoqtr))-1)*100;
		$zecspread=$zecask-$zecbid;
		$zecspreadpct=100*$zecspread/$zecquote;

	$xrpprem=$xrpquote-$xrpindex;
		$xrppremp=100*(($xrpquote/$xrpindex)-1);
                $xrpprempa =(pow(($xrpquote/$xrpindex),(365/$daystoqtr))-1)*100;
		$xrpspread=$xrpask-$xrpbid;
		$xrpspreadpct=100*$xrpspread/$xrpquote;

	$repprem=$repquote-$repindex;
		$reppremp=100*(($repquote/$repindex)-1);
                $repprempa =(pow(($repquote/$repindex),(365/$daystoqtr))-1)*100;
		$repspread=$repask-$repbid;
		$repspreadpct=100*$repspread/$repquote;
 





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

if ($cfweeklyleft<1) { $cfweeklyleft=1; }
$cfweeklyjson = file_get_contents('https://www.cryptofacilities.com/derivatives/api/ticker?tradeable='.$cfweekly.'&unit=USD');
$cfweeklyarray = json_decode($cfweeklyjson, true);

if (isset($cfweeklyarray)) {
if ($cfweeklyarray['result'] == "success") {
// $cfweeklyprice = $cfweeklyarray['last'];


$cfweeklybid = $cfweeklyarray['bid'];
$cfweeklyask = $cfweeklyarray['ask'];
$cfweeklyprice = ($cfweeklyask+$cfweeklybid)/2;

$cfweeklyspread = round(($cfweeklyprice - $cfbpi),2);
$cfweeklyspreadperc = round(($cfweeklyspread / $cfbpi)*100, 2);

$cfweeklyspreadpa2=(pow(($cfweeklyprice/$cfbpi), (365/$cfweeklyleft))-1)*100;
$cfweeklyspreadpa=round($cfweeklyspreadpa2,2);


$cfweeklybid = $cfweeklyarray['bid'];
$cfweeklyask = $cfweeklyarray['ask'];

$cfweeklybidaskspread = round(($cfweeklyask - $cfweeklybid),2);
$cfweeklybidaskspreadperc = round(($cfweeklybidaskspread / $cfweeklyprice)*100,2); 


$cfweeklymidprice=round(($cfweeklyask + $cfweeklybid)/2,2);
$cfweeklymidspreadpa2=(pow(($cfweeklymidprice/$cfbpi), (365/$cfweeklyleft))-1)*100;
$cfweeklymidspreadpa=round($cfweeklymidspreadpa2,2);

if ($cfweeklymidspreadpa > 1000) { $cfweeklymidspreadpa=1000; }

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
// $cfbiweeklyprice = $cfbiweeklyarray['last'];


$cfbiweeklybid = $cfbiweeklyarray['bid'];
$cfbiweeklyask = $cfbiweeklyarray['ask'];

$cfbiweeklyprice =($cfbiweeklyask+$cfbiweeklybid)/2;

$cfbiweeklyspread = round(($cfbiweeklyprice - $cfbpi),2);
$cfbiweeklyspreadperc = round(($cfbiweeklyspread / $cfbpi)*100, 2);

$cfbiweeklyspreadpa2=(pow(($cfbiweeklyprice/$cfbpi), (365/($cfbiweeklyleft)))-1)*100;
$cfbiweeklyspreadpa=round($cfbiweeklyspreadpa2,2);




$cfbiweeklybidaskspread = round(($cfbiweeklyask - $cfbiweeklybid),2);
$cfbiweeklybidaskspreadperc = round(($cfbiweeklybidaskspread / $cfbiweeklyprice)*100,2); 

$cfbiweeklymidprice = round(($cfbiweeklyask + $cfbiweeklybid)/2,2);
$cfbiweeklymidspreadpa2=(pow(($cfbiweeklymidprice/$cfbpi), (365/($cfbiweeklyleft)))-1)*100;
$cfbiweeklymidspreadpa=round($cfbiweeklymidspreadpa2,2);


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

// $cfdec16price = $cfdec16array['last'];

$cfdec16bid = $cfdec16array['bid'];
$cfdec16ask = $cfdec16array['ask'];

$cfdec16price = ($cfdec16bid+$cfdec16ask)/2;

$cfdec16spread = round(($cfdec16price - $cfbpi),2);
$cfdec16spreadperc = round(($cfdec16spread / $cfbpi)*100, 2);

$cfdec16spreadpa2=(pow(($cfdec16price/$cfbpi), (365/$cfquarterlyleft))-1)*100;
$cfdec16spreadpa=round($cfdec16spreadpa2,2);




$cfdec16bidaskspread = round(($cfdec16ask - $cfdec16bid),2);
$cfdec16bidaskspreadperc = round(($cfdec16bidaskspread / $cfdec16price)*100,2); 


$cfdec16midprice =round(($cfdec16ask + $cfdec16bid)/2,2);

$cfdec16midspreadpa2=(pow(($cfdec16midprice/$cfbpi), (365/$cfquarterlyleft))-1)*100;
$cfdec16midspreadpa=round($cfdec16midspreadpa2,2);

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
// $cfsemiannualprice = $cfsemiannualarray['last'];

$cfsemiannualbid = $cfsemiannualarray['bid'];
$cfsemiannualask = $cfsemiannualarray['ask'];

$cfsemiannualprice = ($cfsemiannualbid+$cfsemiannualask)/2;

$cfsemiannualspread = round(($cfsemiannualprice - $cfbpi),2);
$cfsemiannualspreadperc = round(($cfsemiannualspread / $cfbpi)*100, 2);

$cfsemiannualspreadpa2=(pow(($cfsemiannualprice/$cfbpi), (365/($cfsemiannualleft)))-1)*100;
$cfsemiannualspreadpa=round($cfsemiannualspreadpa2,2);



$cfsemiannualbidaskspread = round(($cfsemiannualask - $cfsemiannualbid),2);
$cfsemiannualbidaskspreadperc = round(($cfsemiannualbidaskspread / $cfsemiannualprice)*100,2); 

$cfsemiannualmidprice = round(($cfsemiannualask + $cfsemiannualbid)/2,2);
$cfsemiannualmidspreadpa2=(pow(($cfsemiannualmidprice/$cfbpi), (365/($cfsemiannualleft)))-1)*100;
$cfsemiannualmidspreadpa=round($cfsemiannualmidspreadpa2,2);

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
// $cftriquarterlyprice = $cftriquarterlyarray['last'];

$cftriquarterlybid = $cftriquarterlyarray['bid'];
$cftriquarterlyask = $cftriquarterlyarray['ask'];

$cftriquarterlyprice = ($cftriquarterlybid+$cftriquarterlyask)/2;

$cftriquarterlyspread = round(($cftriquarterlyprice - $cfbpi),2);
$cftriquarterlyspreadperc = round(($cftriquarterlyspread / $cfbpi)*100, 2);

$cftriquarterlyspreadpa2=(pow(($cftriquarterlyprice/$cfbpi), (365/($cftriquarterlyleft)))-1)*100;
$cftriquarterlyspreadpa=round($cftriquarterlyspreadpa2,2);



$cftriquarterlybidaskspread = round(($cftriquarterlyask - $cftriquarterlybid),2);
$cftriquarterlybidaskspreadperc = round(($cftriquarterlybidaskspread / $cftriquarterlyprice)*100,2); 

$cftriquarterlymidprice =round(($cftriquarterlyask + $cftriquarterlybid)/2,2);
$cftriquarterlymidspreadpa2=(pow(($cftriquarterlymidprice/$cfbpi), (365/($cftriquarterlyleft)))-1)*100;
$cftriquarterlymidspreadpa=round($cftriquarterlymidspreadpa2,2);

} else {
$cftriquarterlyprice ="0";
return;
} 
} else {
$cftriquarterlyprice ="0";
return;
}


// Deribit 
/*
$nextfriday = strtotime("next friday");
$currentts = strtotime("now");
$month=strtoupper(date('M', $nextfriday));
$day=date('d', $nextfriday);
$year=date('y', $nextfriday);
$weeklycont="BTC-".$day.$month.$year;
*/

$deribitinstrumentjson = file_get_contents('https://www.deribit.com/api/v1/public/getinstruments');
$deribitinstrumentarray = json_decode($deribitinstrumentjson, true);
$deribitinstruments=$deribitinstrumentarray['result'];
$deribitinstrumentcount=count($deribitinstruments)-1;

for( $x = 0; $x <= $deribitinstrumentcount; $x++ ) {
	$deribitname=$deribitinstruments[$x]['instrumentName'];
	if ($deribitinstruments[$x]['kind'] == "future" and $deribitinstruments[$x]['isActive']) {
if ($deribitinstruments[$x]['settlement'] == 'week') {
		$dericont=$deribitname;
$deriwk=$deribitinstruments[$x]['expiration'];
		}

else {
		$dericontq=$deribitname;
$deriq=$deribitinstruments[$x]['expiration'];
 }
}
}

$derindexjson = file_get_contents('https://www.deribit.com/api/v1/public/index');
$derindexarray = json_decode($derindexjson, true);
$deriindex = $derindexarray['result']['btc'];


// Weekly


$derijson = file_get_contents('https://www.deribit.com/api/v1/public/getlasttrades?instrument='.$dericont);
$deriarray = json_decode($derijson, true);
// $deriprice = $deriarray['result'][0]['price'];
#$deriindex = $deriarray['result'][0]['indexPrice'];

$derijson2 = file_get_contents('https://www.deribit.com/api/v1/public/getorderbook?instrument='.$dericont);
$deriarray2 = json_decode($derijson2, true);

$deribid = $deriarray2['result']['bids'][0]['price'];
$deriask = $deriarray2['result']['asks'][0]['price'];

$deriprice = ($deribid+$deriask)/2;


$deribidaskspread = round(($deriask - $deribid),2);
$deribidaskspreadperc = round(($deribidaskspread / $deriprice)*100,2); 

$derispread = round(($deriprice - $deriindex),2);
$derispreadperc = round(($derispread / $deriindex)*100, 2);

$now=strtotime("now GMT");
$deriwkexpire=strtotime($deriwk);
$deriwkmins=($deriwkexpire-$now)/60;

$derispreadpa2 = round((pow(($deriprice/$deriindex), (525600/$deriwkmins))-1),2)*100;
$derispreadpa = round($derispreadpa2,2);
if ($derispreadpa > 1000)  { $derispreadpa=1000; }

//grab vol
$derijson3 = file_get_contents('https://www.deribit.com/api/v1/public/getsummary?instrument='.$dericont);
$deriarray3 = json_decode($derijson3, true);
$deriwvol = $deriarray3['result']['volume'];

//Quarterly

$derijsonq = file_get_contents('https://www.deribit.com/api/v1/public/getlasttrades?instrument='.$dericontq);
$deriarrayq = json_decode($derijsonq, true);
// $deripriceq = $deriarrayq['result'][0]['price'];

$derijsonq2 = file_get_contents('https://www.deribit.com/api/v1/public/getorderbook?instrument='.$dericontq);
$deriarrayq2 = json_decode($derijsonq2, true);

$deribidq = $deriarrayq2['result']['bids'][0]['price'];
$deriaskq = $deriarrayq2['result']['asks'][0]['price'];
// $deriqmid = ($deribidq-$deriaskq)/2;

$deripriceq = ($deribidq+$deriaskq)/2;

$deribidaskspreadq = round(($deriaskq - $deribidq),2);
$deribidaskspreadqperc = round(($deribidaskspreadq / $deripriceq)*100,2); 

$derispreadq = round(($deripriceq - $deriindex),2);
$derispreadqperc = round(($derispreadq / $deriindex)*100, 2);

$deriqexpire=strtotime($deriq);
$deriqmins=($deriqexpire-$now)/60;

$derispreadqpa2 = round((pow(($deripriceq/$deriindex), (525600/$deriqmins))-1),2)*100;
$derispreadqpa = round($derispreadqpa2,2);
if ($derispreadqpa > 1000)  { $derispreadqpa=1000; }

$derijsonq3 = file_get_contents('https://www.deribit.com/api/v1/public/getsummary?instrument='.$dericontq);
$deriarrayq3 = json_decode($derijsonq3, true);
$deriqvol = $deriarrayq3['result']['volume'];


// Coinpit

//weekly and biweekly



$coinpitjson = file_get_contents('https://live.coinpit.io/api/v1/all/info');
$coinpitarray = json_decode($coinpitjson, true);


$coinpitkeys=array_keys($coinpitarray);

$cpweeklysymbol=$coinpitkeys[0];
$cpbiweeklysymbol=$coinpitkeys[2];

$coinpitindex = $coinpitarray[$cpweeklysymbol]['indexPrice'];

//w


$coinpitbidw = $coinpitarray[$cpweeklysymbol]['bid'];
$coinpitaskw = $coinpitarray[$cpweeklysymbol]['ask'];
$coinpitpricew = ($coinpitbidw+$coinpitaskw)/2;

$coinpitbidaskspreadw = round(($coinpitaskw - $coinpitbidw),2);
$coinpitbidaskspreadwperc = round(($coinpitbidaskspreadw / $coinpitpricew)*100,2); 

$coinpitspreadw = round(($coinpitpricew - $coinpitindex),2);
$coinpitspreadwperc = round(($coinpitspreadw / $coinpitindex)*100, 2);

$coinpitwspreadpa2 = round((pow(($coinpitpricew/$coinpitindex), (525600/$deriwkmins))-1),2)*100;
$coinpitwspreadpa = round($coinpitwspreadpa2,2);
$coinpitwvol = $coinpitarray[$cpweeklysymbol]['vol24H']['qty']*100;

//bw

$coinpitbidbw = $coinpitarray[$cpbiweeklysymbol]['bid'];
$coinpitaskbw = $coinpitarray[$cpbiweeklysymbol]['ask'];
$coinpitpricebw = ($coinpitbidbw+$coinpitaskbw)/2;

$coinpitbidaskspreadbw = round(($coinpitaskbw - $coinpitbidbw),2);
$coinpitbidaskspreadbwperc = round(($coinpitbidaskspreadbw / $coinpitpricebw)*100,2); 

$coinpitspreadbw = round(($coinpitpricebw - $coinpitindex),2);
$coinpitspreadbwperc = round(($coinpitspreadbw / $coinpitindex)*100, 2);

$coinpitbwspreadpa2 = round((pow(($coinpitpricebw/$coinpitindex), (525600/($deriwkmins+(60*24*7))))-1),2)*100;
$coinpitbwspreadpa = round($coinpitbwspreadpa2,2);
$coinpitbwvol = $coinpitarray[$cpbiweeklysymbol]['vol24H']['qty']*100;



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
echo "            categories: ['Index', '$cfweeklydate', '$cfbiweeklydate', '$cfquarterlydateexp', '$cfsemiannualdateexp', '$cftriquarterlydateexp']\n";
echo "        },\n";
echo "        yAxis: [{\n";
echo "            gridLineWidth: 0,\n";
echo "            title: {\n";
echo "                text: 'USD ($)'\n";
echo "            },\n";
echo "            plotLines: [{\n";
echo "                value: 0,\n";
echo "                width: 1,\n";
echo "                color: '#808080'\n";
echo "            }],\n";
echo "   }, {\n";
echo "            gridLineWidth: 0,\n";
echo "            title: {\n";
echo "                text: 'APY(%)',\n";
echo "            },\n";
echo "           opposite: true\n";
echo "        }],\n";
echo "        tooltip: {\n";
echo "            shared: true\n";
echo "        },\n";
echo "        legend: {\n";
echo "            layout: 'vertical',\n";
echo "            align: 'right',\n";
echo "            verticalAlign: 'middle',\n";
echo "            borderWidth: 0\n";
echo "        },\n";
echo "        chart: {\n";
echo "            alignTicks: false,\n";
echo "        },\n";
echo "        series: [{\n";
echo "            connectNulls: true,\n";
echo "            name: 'OKCoin(%)',\n";
echo "            visible: false,\n";
echo "             color: '#66ccff',\n";
echo "            type: 'spline',\n";
echo "            yAxis: 1,\n";
echo "        tooltip: {\n";
echo "            valueSuffix: '%'\n";
echo "        },\n";
echo "            data: [null, ".$weeklyspreadpa.", ".$biweeklyspreadpa.", ".$quarterlyspreadpa.", null, null]\n";
echo "        },{\n";
echo "            name: 'OKCoin($)',\n";
echo "            visible: false,\n";
echo "            type: 'line',\n";
echo "             color: '#0066ff',\n";
echo "            data: [".$theokcindex.", ".$theokcweekly.", ".$theokcbiweekly.", ".$theokcquarterly.", null, null],\n";
echo "        tooltip: {\n";
echo "            valueSuffix: ' USD'\n";
echo "        }\n";
echo "        },{\n";
echo "            connectNulls: true,\n";
echo "            name: 'CryptoFacilities(%)',\n";
echo "            visible: false,\n";
echo "             color: '#66ff66',\n";
echo "            type: 'spline',\n";
echo "            yAxis: 1,\n";
echo "        tooltip: {\n";
echo "            valueSuffix: '%'\n";
echo "        },\n";
echo "            data: [null, ".$cfweeklymidspreadpa.", ".$cfbiweeklymidspreadpa.", ".$cfdec16midspreadpa.", ".$cfsemiannualmidspreadpa.", ".$cftriquarterlymidspreadpa."]\n";
echo "        }, {\n";
echo "            name: 'CryptoFacilities($)',\n";
echo "             color: '#009933',\n";
echo "            type: 'line',\n";
echo "        tooltip: {\n";
echo "            valueSuffix: ' USD'\n";
echo "        },\n";
echo "            data: [".$cfbpi.", ".$cfweeklymidprice.", ".$cfbiweeklymidprice.", ".$cfdec16midprice.", ".$cfsemiannualmidprice.", ".$cftriquarterlymidprice."]\n";
echo "        }, {\n";
echo "            connectNulls: true,\n";
echo "            name: 'Coinpit ($)',\n";;
echo "        tooltip: {\n";
echo "            valueSuffix: ' USD'\n";
echo "        },\n";
echo "            data: [".$coinpitindex.", ".$coinpitpricew.", ".$coinpitpricebw.", null, null, null]\n";
echo "        }, {\n";
echo "            connectNulls: true,\n";
echo "            name: 'Deribit ($)',\n";;
echo "        tooltip: {\n";
echo "            valueSuffix: ' USD'\n";
echo "        },\n";
echo "            data: [".$deriindex.", ".$deriprice.", null, ".$deripriceq.", null, null]\n";
echo "        },{\n";
echo "            connectNulls: true,\n";
echo "            name: 'BitMEX ($)',\n";
echo "        tooltip: {\n";
echo "            valueSuffix: ' USD'\n";
echo "        },\n";
echo "            data: [".$btcindex.", null , null, ".$btcquote.", null, null]\n";
echo "        }]\n";
echo "    });\n";
echo "});\n";
echo "</script>\n";
echo "    <div id=\"container\" style=\"min-width: 400px; height: 400px; max-width: 800px; margin: 0 auto\"></div>\n";
echo "<a href='https://www.bitmex.com/register/RrmvSe'><h1>BitMEX</h1></a>\n";
#echo "<span class=\"hotspot\" onmouseover=\"tooltip.show('BitMEX index is based on an undisclosed index from Kaiko.');\" onmouseout=\"tooltip.hide();\">Index (?):</span> $".number_format($bitmexindicative,2)." <a href=\"https://bitmex.kaiko.com/\">[Components]</a>\n";
#echo "<span class=\"hotspot\" onmouseover=\"tooltip.show('For XBC series, BitMEX index is based on the Big 3 (Huobi, OKCoin, BTCC).');\" onmouseout=\"tooltip.hide();\">Index (?):</span> ".number_format($bitmexindicativec)." CNY <a href=\"https://www.bitmex.com/app/index/.XBTCNY\">[Components]</a>\n";
#echo "<span class=\"hotspot\" onmouseover=\"tooltip.show('For XBJ series, BitMEX just settles to Quoine.');\" onmouseout=\"tooltip.hide();\">Index (?):</span> ".number_format($bitmexindicativej)." JPY <a href=\"https://www.bitmex.com/app/index/.XBTJPY\">[Components]</a>\n";
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
echo "    <th class=\"tg-yw4l\">Price (Mark)</th>\n";
echo "    <th class=\"tg-yw4l\">Price (Index)</th>\n";
echo "    <th class=\"tg-c9cr\">Premium (nom)</th>\n";
echo "    <th class=\"tg-c9cr\">Premium (%)</th>\n";
echo "    <th class=\"tg-c9cr\">Annualized Premium (%)</th>\n";
echo "    <th class=\"tg-c9cr\">Bid-Ask Spread (nom)</th>\n";
echo "    <th class=\"tg-c9cr\">Bid-Ask (%)</th>\n";
echo "    <th class=\"tg-c9cr\">Volume</th>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">$".$btcsymbol."</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($btcquote)."</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($btcindex)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($btcprem,2)."</td>\n";
echo "    <td class=\"tg-031e\">".number_format($btcpremp,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($btcprempa,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($btcspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".number_format($btcspreadpct,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($btcvol*$btcindex)."</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">$".$xbtsymbol."</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($xbtquote)."</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($xbtindex)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($xbtprem,2)."</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xbtpremp,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xbtprempa,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($xbtspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xbtspreadpct,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($xbtvol*$xbtindex)."</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">".$ltcsymbol."</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($ltcquote,"5")." BTC</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($ltcindex,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ltcprem,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ltcpremp,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ltcprempa,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ltcspread,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ltcspreadpct,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ltcvol)." BTC</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">".$xmrsymbol."</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($xmrquote,"5")." BTC</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($xmrindex,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xmrprem,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xmrpremp,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xmrprempa,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xmrspread,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xmrspreadpct,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xmrvol)." BTC</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">".$ethsymbol."</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($ethquote,"5")." BTC</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($ethindex,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ethprem,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ethpremp,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ethprempa,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ethspread,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ethspreadpct,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($ethvol)." BTC</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">".$etcsymbol."</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($etcquote,"5")." BTC</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($etcindex,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($etcprem,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($etcpremp,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($etcprempa,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($etcspread,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($etcspreadpct,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($etcvol)." BTC</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">".$zecsymbol."</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($zecquote,"5")." BTC</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($zecindex,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($zecprem,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($zecpremp,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($zecprempa,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($zecspread,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($zecspreadpct,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($zecvol)." BTC</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">".$repsymbol."</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($repquote,"5")." BTC</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($repindex,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($repprem,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($reppremp,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($repprempa,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($repspread,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($repspreadpct,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($repvol)." BTC</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">".$dashsymbol."</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($dashquote,"5")." BTC</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($dashindex,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($dashprem,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($dashpremp,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($dashprempa,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($dashspread,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($dashspreadpct,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($dashvol)." BTC</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">".$xrpsymbol."</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($xrpquote,"5")." BTC</td>\n";
echo "    <td class=\"tg-yw4l\">".number_format($xrpindex,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xrpprem,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xrppremp,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xrpprempa,"1")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xrpspread,"5")." BTC</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xrpspreadpct,"2")."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($xrpvol)." BTC</td>\n";
echo "  </tr>\n";
echo "</table>";
echo "<br>";
echo "<a href='https://live.coinpit.io'><h1>Coinpit</h1></a>\n";
echo "<span class=\"hotspot\" onmouseover=\"tooltip.show('Coinpit index is based on multiple BTC/USD exchanges.');\" onmouseout=\"tooltip.hide();\">Index (?):</span> $".$coinpitindex." <a href=\"https://coinpit.io\">[Components]</a>";
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
echo "    <th class=\"tg-c9cr\">Volume ($)</th>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Weekly (".$cfweeklydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($coinpitpricew)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($coinpitspreadw,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$coinpitspreadwperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$coinpitwspreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($coinpitbidaskspreadw,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$coinpitbidaskspreadwperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($coinpitwvol)."</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Biweekly (".$cfbiweeklydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($coinpitpricebw)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($coinpitspreadbw,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$coinpitspreadbwperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$coinpitbwspreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($coinpitbidaskspreadbw,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$coinpitbidaskspreadbwperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($coinpitbwvol)."</td>\n";
echo "  </tr>\n";
echo "</table>";
echo "<br>";
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
echo "    <th class=\"tg-c9cr\">Volume ($)</th>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Weekly (".$cfweeklydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($cfweeklyprice)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfweeklyspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$cfweeklyspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($cfweeklymidspreadpa)."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfweeklybidaskspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$cfweeklybidaskspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfwvol*$cfbpi)."</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">BiWeekly (".$cfbiweeklydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($cfbiweeklyprice)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfbiweeklyspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$cfbiweeklyspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($cfbiweeklyspreadpa)."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfbiweeklybidaskspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$cfbiweeklybidaskspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfbwvol*$cfbpi)."</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Quarterly (".$cfquarterlydateexp.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($cfdec16price)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfdec16spread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$cfdec16spreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($cfdec16spreadpa)."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfdec16bidaskspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$cfdec16bidaskspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfqvol*$cfbpi)."</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Semianually (".$cfsemiannualdateexp.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($cfsemiannualprice)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfsemiannualspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$cfsemiannualspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($cfsemiannualspreadpa)."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfsemiannualbidaskspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$cfsemiannualbidaskspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cfsavol*$cfbpi)."</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">TriQuarterly (".$cftriquarterlydateexp.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($cftriquarterlyprice)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cftriquarterlyspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$cftriquarterlyspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($cftriquarterlyspreadpa)."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cftriquarterlybidaskspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$cftriquarterlybidaskspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($cftqvol*$cfbpi)."</td>\n";
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
echo "    <th class=\"tg-c9cr\">Volume ($)</th>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Weekly (".$cfweeklydate.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($deriprice)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($derispread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$derispreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$derispreadpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($deribidaskspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$deribidaskspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($deriwvol*10)."</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\">Quarterly (".$cfquarterlydateexp.")</td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($deripriceq)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($derispreadq,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$derispreadqperc."%</td>\n";
echo "    <td class=\"tg-031e\">".$derispreadqpa."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($deribidaskspreadq,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$deribidaskspreadqperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($deriqvol*10)."</td>\n";
echo "  </tr>\n";
echo "</table>";
echo "<br>";
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
echo "    <th class=\"tg-c9cr\">Daily Premium (%)</th>\n";
echo "    <th class=\"tg-c9cr\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This shows the spread between the bid and ask on the given contract, in nominal dollar terms.');\" onmouseout=\"tooltip.hide();\">Bid-Ask Spread ($) (?)</span></th>\n";
echo "    <th class=\"tg-c9cr\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This shows the spread between the bid and ask on the given contract, in percentage terms.');\" onmouseout=\"tooltip.hide();\">Bid-Ask (%) (?)</span></th>\n";
echo "    <th class=\"tg-c9cr\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This is USD-equivalent notional volume over the past 24-hours, rolling');\" onmouseout=\"tooltip.hide();\">Volume ($) (?)</span></th>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This contract expires every Friday (like all weekly contracts) at 8:00AM UTC');\" onmouseout=\"tooltip.hide();\">Weekly (".$cfweeklydate.") (?)</span></td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($theokcweekly)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($weeklyspread, 2)."</td>\n";
echo "    <td class=\"tg-031e\">".$weeklyspreadperc2."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($weeklyspreadpa)."%</td>\n";
echo "    <td class=\"tg-031e\">".$weeklyspreadpd."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($theokcweeklyspread, 2)."</td>\n";
echo "    <td class=\"tg-031e\">".$theokcweeklyspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($okcwvol*100)."</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\"><span class=\"hotspot\" onmouseover=\"tooltip.show('This contract expires next Friday at 8:00AM UTC');\" onmouseout=\"tooltip.hide();\">BiWeekly (".$cfbiweeklydate.") (?)</span></td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($theokcbiweekly)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($biweeklyspread, 2)."</td>\n";
echo "    <td class=\"tg-031e\">".$biweeklyspreadperc2."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($biweeklyspreadpa)."%</td>\n";
echo "    <td class=\"tg-031e\">".$biweeklyspreadpd."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($theokcbiweeklyspread, 2)."</td>\n";
echo "    <td class=\"tg-031e\">".$theokcbiweeklyspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($okcbwvol*100)."</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td class=\"tg-e3zv\"><span class=\"hotspot\" onmouseover=\"tooltip.show('The quarterly contract will expire on December 30, 2016');\" onmouseout=\"tooltip.hide();\">Quarterly (".$cfquarterlydateexp.") (?)</span></td>\n";
echo "    <td class=\"tg-yw4l\">$".number_format($theokcquarterly)."</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($quarterlyspread, 2)."</td>\n";
echo "    <td class=\"tg-031e\">".$quarterlyspreadperc2."%</td>\n";
echo "    <td class=\"tg-031e\">".number_format($quarterlyspreadpa)."%</td>\n";
echo "    <td class=\"tg-031e\">".$quarterlyspreadpd."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($theokcquarterlyspread,2)."</td>\n";
echo "    <td class=\"tg-031e\">".$theokcquarterlyspreadperc."%</td>\n";
echo "    <td class=\"tg-031e\">$".number_format($okcqvol*100)."</td>\n";
echo "  </tr>\n";
echo "<br>";
echo "</table>";


#print_r("Want to show appreciation or support efforts to make this to look prettier? Send BTC to author: 3CnxCCrkfJGrjg6XCdVxGEbbcDgQCYGLr6");
#echo "<img src='https://chart.googleapis.com/chart?cht=qr&chs=50x50&chl=3CnxCCrkfJGrjg6XCdVxGEbbcDgQCYGLr6'>";
echo "</body>";
echo "</html>";


?>

