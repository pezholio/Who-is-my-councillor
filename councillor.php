<?php
include("xmlparse.php");

if($_GET){
$postcode = str_replace(" ", "", $_GET['postcode']);

$area = get_xml("http://neighbourhood.statistics.gov.uk/NDE2/Disco/SearchSByAByPostcode?LevelTypeId=14&Postcode=". $postcode);

$wardid = $area['ans1:SearchSByAByPostcodeResponseElement']['ns0:AreaFallsWithins']['ns0:AreaFallsWithin']['ns0:Area']['ns0:AreaId']['value'];

$wardname = $area['ans1:SearchSByAByPostcodeResponseElement']['ns0:AreaFallsWithins']['ns0:AreaFallsWithin']['ns0:Area']['ns0:Name']['value'];

$councilid = $area['ans1:SearchSByAByPostcodeResponseElement']['ns0:AreaFallsWithins']['ns0:AreaFallsWithin']['ns0:FallsWithin']['ns0:Area']['ns0:AreaId']['value'];

$getwardsnac = get_xml("http://neighbourhood.statistics.gov.uk/NDE2/Disco/GetAreaDetail?AreaId=". $wardid);

$getcouncilsnac = get_xml("http://neighbourhood.statistics.gov.uk/NDE2/Disco/GetAreaDetail?AreaId=". $councilid);

$wardsnac = $getwardsnac['ans1:GetAreaDetailResponseElement']['ns0:AreaDetail']['ns0:ExtCode']['value'];

$councilsnac = $getcouncilsnac['ans1:GetAreaDetailResponseElement']['ns0:AreaDetail']['ns0:ExtCode']['value'];

$warddetail = get_xml("http://openlylocal.com/wards/snac_id/". $wardsnac .".xml");

$councildetail = get_xml("http://openlylocal.com/councils/snac_id/". $councilsnac .".xml");

$members = $warddetail['ward']['members']['member'];
}
$location = $xml['rss']['channel']['item'][0]['title']['value'];
$details = explode(",", $location);
$postcode = trim(str_replace(strstr($details[2],"heading"), "", $details[2]));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Who is my councillor?</title>
<link type="text/css" rel="stylesheet" href="http://www.blueprintcss.org/blueprint/src/grid.css" />
<link type="text/css" rel="stylesheet" href="http://www.blueprintcss.org/blueprint/src/typography.css" />
<link type="text/css" rel="stylesheet" href="http://www.blueprintcss.org/blueprint/src/forms.css" />
<link type="text/css" rel="stylesheet" href="style.css" />
</head>
<body>
<div style="width: 90%; margin: 10px auto;">
<h1>Who is my councillor?</h1>
<div style="width: 800px; margin: 0 auto;">
<?php
if ($_GET) {
?>
<h2>Your council is <strong><?php echo $councildetail['council']['name']['value']; ?></strong>, and you are in <strong><?php echo $wardname; ?></strong> ward</h2>
<?php
if (sizeof($members) == 0) {
?>
<p>Your council isn't on Openly Local yet. Why not try visiting the <a href="<?php echo $councildetail['council']['url']['value']; ?>"><?php echo $councildetail['council']['name']['value']; ?> website</a> to get your councillor's details?</p>
<?php
} else {
if (!is_array($members[0])) {
$members = $warddetail['ward']['members'];
?>
<p>Your councillor is:</p>
<?php
} else {
?>
<p>Your councillors are:</p>
<?php } ?>
<ul>
<?php
	foreach ($members as $member) {
	if (strlen($member['first-name']['value']) > 0) {
?>
<li>
<h3><a href="<?php echo nl2br($member['url']['value']); ?>"><?php echo $member['first-name']['value']; ?> <?php echo $member['last-name']['value']; ?></a></h3>
<p><?php echo nl2br($member['address']['value']); ?></p>
<?php
if (strlen($member['telephone']['value']) > 0) {
?>
<p><strong>Tel:</strong> <?php echo $member['telephone']['value']; ?></p>
<?php } ?>
<?php
if (strlen($member['email']['value']) > 0) {
?>
<p><strong>Email:</strong> <a href="mailto:<?php echo $member['email']['value']; ?>"><?php echo $member['email']['value']; ?></a></p>
<?php 
}
} 
}
?>
</li>
<?php
	}
?>
</ul>
<?php
} else {
?>
<form action="" method="get" style="text-align: center;">
<p><input type="text" class="title" name="postcode" id="dummy0" onclick="this.value=''" value="Enter your postcode"></p>
<p><button type="submit" class="btn"><span><span>Submit</span></span></button></p>
</form>
<?php } ?>
<div id="powered">
<p>Powered by <a href="http://www.openlylocal.com">Openly Local</a> and the <a href="http://neighbourhood.statistics.gov.uk/dissemination/Info.do?page=NDE.htm">NeSS Data Exchange</a>.</p>
</div>
</div>
</div>
</body>
</html>