<?php

/**************************************************

Feb 6, 2015

Wade Guidry, University of Puget Sound, wguidry@pugetsound.edu


Script for generating HTML markup containing the past 7 days of circulation.

A copy of the analytics report this script runs against can be found in the Alma analytics community folder at:

/Shared Folders/Community/Reports/University of Puget Sound/circ_7_day_summary

*/

$context = stream_context_create(
    array(
        'http' => array(
            'follow_location' => false
        )
    )
);



/* START - Read in the analytics report  */

$ResumptionToken = "";

while (($IsFinished != 'true') Or ($rowcount == 0)) {
	
	$report = "https://api-na.hosted.exlibrisgroup.com/almaws/v1/analytics/reports?path=/shared/University%20of%20Puget%20Sound/Reports/Wade/circ_7_day_summary&limit=1000&apikey=[your api key here]";
	$xml = simplexml_load_file($report); 
	$ResumptionToken = (string) $xml->QueryResult->ResumptionToken;
	$IsFinished = (string) $xml->QueryResult->IsFinished;
		
	/* register the "rowset" namespace */
	
	$xml->registerXPathNamespace('rowset', 'urn:schemas-microsoft-com:xml-analysis:rowset');
	
	/* use xpath to get rows of interest */
	
	$result = $xml->xpath('/report/QueryResult/ResultXml/rowset:rowset/rowset:Row');
	
	/* using rowcount of 0 to repeat call until data is obtained is a low-tech way of addressing the issue that calls to the analytics API sometimes just fail;
	   Since I know for certain that there should be data in the report */
	
	$rowcount = count($result);
	}

$output = '7dayscirc_new.html';

/* echo $output; */

$oldfile = '7dayscirc.html';


/* write the first part of the html file */

file_put_contents($output,'
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>7 days of circulation at Collins Library</title>
	<link rel="stylesheet" href="//www.pugetsound.edu/css/site_library.css" type="text/css" media="all" />
	<link rel="stylesheet" href="//www.pugetsound.edu/css/print.css" type="text/css" media="print" />
	<!--[if lte IE 9]>
			<script>
				var OLDIE = true;
			</script>
			<script src="http://www.pugetsound.edu/js/html5.js"></script>
			<link rel="stylesheet" href="http://www.pugetsound.edu/css/ie.css" type="text/css" media="all" />
	<![endif]-->
	<style>
	td, th {font-family:UniversRegular,Univers,Sans-serif;}
	table { color: #747578; font-size: 14px; line-height: 1.4;}
	table thead { background-color: #222; font-size: 14px; line-height: 1.2em; text-align: left; text-transform: uppercase; -webkit-transform: uppercase; -moz-transform: uppercase; -ms-transform: uppercase; -o-transform: uppercase;  }
	table tfoot {}
	table tr { border: none; }
	table th { color: #fff; margin-bottom: 20px; padding: 10px 20px; text-shadow: none; vertical-align: top; }
	table td { background: white; border: 1px solid #CCCCCC; font-size: 14px; padding: 20px; vertical-align: top; }
	table.borderless td { border: 0; }
	table td.sub_title { background: #444; color: #fff; padding: 10px 20px; }
	</style>

 </head>
  <body style="background-image: none !important;background-color:white;">
');

/* parse the analytics report data into the HTML */

foreach ($result as $row) {

	$yesterday = (string) $row->Column1;
	$twodaysago = (string) $row->Column2;
	$threedaysago = (string) $row->Column3;
	$fourdaysago = (string) $row->Column4;
	$fivedaysago = (string) $row->Column5;
	$sixdaysago = (string) $row->Column6;
	$sevendaysago = (string) $row->Column7;
	}

$total = $yesterday + $twodaysago + $threedaysago + $fourdaysago + $fivedaysago + $sixdaysago + $sevendaysago;
	
file_put_contents($output, '<div><table><thead>
<tr style="color: white; background-color: #5f9797; font-weight: bold; text-align: center;">
<th>'.date("l", strtotime( "-1 days" ) ).'</th>
<th>'.date("l", strtotime( "-2 days" ) ).'</th>
<th>'.date("l", strtotime( "-3 days" ) ).'</th>
<th>'.date("l", strtotime( "-4 days" ) ).'</th>
<th>'.date("l", strtotime( "-5 days" ) ).'</th>
<th>'.date("l", strtotime( "-6 days" ) ).'</th>
<th>'.date("l", strtotime( "-7 days" ) ).'</th>
<th>7 Day Total</th>
</tr></thead>
<tbody>
<tr>
<td style="text-align: right;">'.$yesterday.'</td>
<td style="text-align: right;">'.$twodaysago.'</td>
<td style="text-align: right;">'.$threedaysago.'</td>
<td style="text-align: right;">'.$fourdaysago.'</td>
<td style="text-align: right;">'.$fivedaysago.'</td>
<td style="text-align: right;">'.$sixdaysago.'</td>
<td style="text-align: right;">'.$sevendaysago.'</td>
<td style="text-align: right;">'.$total.'</td>

</tr></tbody></table></div></body></html>', FILE_APPEND);

	


/* copy the new output to the file actually in use */

copy($output, $oldfile);

?>
