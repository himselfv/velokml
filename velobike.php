<?php
/*
https://developers.google.com/kml/documentation/kml_tut
Адреса стандартных иконок можно смотреть в интерфейсе, выбирая их.
*/
header('Content-type: application/vnd.google-earth.kml+xml');
header("Content-disposition: attachment; filename=velobike.kml");
?>
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document>
<Style id="parking">
	<IconStyle>
		<color>ff00ffff</color>
		<scale>0.6</scale>
		<Icon>
			<href>http://maps.google.com/mapfiles/kml/shapes/square.png</href>
		</Icon>
	</IconStyle>
	<LabelStyle>
		<color>ffffffff</color>
		<scale>0.5</scale>
	</LabelStyle>
</Style>
<?php

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
	print "<styleUrl>#parking</styleUrl>\n";
	
	$pos = $pt->Position;
	print "<Point>";
	print "<coordinates>$pos->Lon,$pos->Lat,0</coordinates>";
	print "</Point>\n";
	
	print "</Placemark>\n";
	/*
	Address
	AvailableElectricBikes
	AvailableOrdinaryBikes
	FreeElectricPlaces
	FreeOrdinaryPlaces
	FreePlaces
	HasTerminal
	IconsSet
	Id
	IsFavourite
	IsLocked
	Name
	Position -> Lat, Lon
	StationTypes -> array ["ordinary", "electric"]
	TemplateId
	TotalElectricPlaces
	TotalOrdinaryPlaces
	TotalPlaces
	*/
}


?>
</Document>
</kml>