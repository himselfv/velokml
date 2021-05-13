<?php
/*
https://developers.google.com/kml/documentation/kml_tut
Адреса стандартных иконок можно смотреть в интерфейсе, выбирая их.
*/
header('Content-type: application/vnd.google-earth.kml+xml');
header("Content-disposition: attachment; filename=velobike.kml");

$baseuri = dirname("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
?>
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document>
<?php

function print_style($name, $uri) {
	global $baseuri; ?>
<Style id="<?php print "$name"; ?>">
	<IconStyle>
		<!--color>ff00ffff</color-->
		<scale>1.0</scale>
		<Icon>
			<href><?php print "$baseuri/res/$uri"; ?></href>
		</Icon>
	</IconStyle>
	<LabelStyle>
		<color>ffffffff</color>
		<scale>0.0</scale>
	</LabelStyle>
</Style>
<?php }
print_style("parking_no", "marker_no_32.png");
print_style("parking0", "marker0_32.png");
print_style("parking1", "marker1_32.png");
print_style("parking2", "marker2_32.png");
print_style("parking3", "marker3_32.png");
print_style("parking4", "marker4_32.png");
print_style("parking5", "marker5_32.png");
print_style("parking6", "marker6_32.png");
print_style("parking7", "marker7_32.png");
print_style("parking8", "marker8_32.png");

//Пока вызываю без проверки, тк промежуточного сертификата в дефолтных нет, а к сайту не приложен.
//Это не первый раз, когда с velobike такая ерунда - из Андроида по той же причине отказывало.
$contextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);  

$resp = file_get_contents("https://velobike.ru/ajax/parkings/", false, stream_context_create($contextOptions));
$data = json_decode($resp);



foreach ($data->Items as $pt) {
	$name = $pt->Address;
	$desc = $pt->Name;
	
	print "<Placemark>\n";
	print "<name>$name</name>\n";
	print "<description>$desc</description>\n";
	
	$pos = $pt->Position;
	print "<Point>";
	print "<coordinates>$pos->Lon,$pos->Lat,0</coordinates>";
	print "</Point>\n";
	
	/*
	Determine style
	*/
	$free = $pt->FreePlaces;
	$total = $pt->TotalPlaces;
	
	$st = "";
	if ($total == 0)
		$st = "parking_no";
	elseif ($free == 0)
		$st = "parking8";
	elseif ($free >= $total)
		$st = "parking0";
	else {
		$share = $free / $total;
		if ($share >= 6.0/7.0)
			$st = "parking7";
		elseif ($share >= 5.0/7.0)
			$st = "parking6";
		elseif ($share >= 4.0/7.0)
			$st = "parking5";
		elseif ($share >= 3.0/7.0)
			$st = "parking4";
		elseif ($share >= 2.0/7.0)
			$st = "parking3";
		elseif ($share >= 1.0/7.0)
			$st = "parking2";
		else
			$st = "parking1";
	}
	print "<styleUrl>#$st</styleUrl>\n";
	
	print "</Placemark>\n";
	/*
	Id
	Name
	Address

	AvailableElectricBikes
	FreeElectricPlaces
	TotalElectricPlaces
	AvailableOrdinaryBikes
	FreeOrdinaryPlaces
	TotalOrdinaryPlaces
	FreePlaces
	TotalPlaces
	
	Position -> Lat, Lon
	HasTerminal
	IconsSet
	IsFavourite
	IsLocked
	StationTypes -> array ["ordinary", "electric"]
	TemplateId

	*/
}


?>
</Document>
</kml>