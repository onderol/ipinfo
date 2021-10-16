<?php

$ip = false;

if(isset($_GET['ip']) && filter_var($_GET['ip'], FILTER_VALIDATE_IP)){
  $ip = $_GET['ip'];
}

if(!$ip){
  $ip = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
  if (strstr($ip, ',')) {
      $tmp = explode (',', $ip);
      $ip = trim($tmp[0]);
  }
}

/* https://ipinfo.io/account/token */
$token = 'XXXXXXXXXXXX';
$data = curls('http://ipinfo.io/'.$ip.'/json?token='.$token);
$data = json_decode($data, true);
$coor = explode(',', $data['loc']);

?><!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8" />
<title>Ipinfo</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<style>
  body{
  margin:0;
  padding:0;
  font-family: Tahoma, sans-serif;
  font-size:14px;
  cursor: default;
  }
  #page{
  margin:0 auto;
  max-width: 640px;
  }
  #map{
  max-width: 640px;
  height: 320px;
  border:1px solid #ccc;
  }
  .sec1 table{
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin: 10px 0;
  }
  .sec1 table td{
  border:1px solid #ccc;
  padding: 4px;
  }
  .sec1 table tr:hover td{
  background: #f6f6f6;
  }
</style>
</head>
<body>
<div id="page">
  <div class="sec1">
    <table>
      <tr>
        <td>IP:</td>
        <td class="ip"><?php echo $data['ip'] ?></td>
      </tr>
      <tr>
        <td title="Ana Bilgisayar Adı">Ana B.: </td>
        <td class="hostname"><?php echo $data['hostname'] ?></td>
      </tr>
      <tr>
        <td>Şehir: </td>
        <td><?php echo $data['city'] ?></td>
      </tr>
      <tr>
        <td>Bölge: </td>
        <td><?php echo $data['region'] ?></td>
      </tr>
      <tr>
        <td>Ülke: </td>
        <td><?php echo code2CountryName($data['country']).' ('.$data['country'].')' ?></td>
      </tr>
      <tr>
        <td title="Organizasyon">Org.: </td>
        <td><?php echo $data['org'] ?> </td>
      </tr>
      <tr>
        <td title="Posta Kodu">Posta K.: </td>
        <td><?php echo $data['postal'] ?> </td>
      </tr>
      <tr>
        <td title="Saat Dilimi">Saat D.: </td>
        <td><?php echo $data['timezone'] ?> </td>
      </tr>
      <tr>
        <td title="Koordinat Enlem">Koor. Enlem: </td>
        <td class="lat"><?php echo $coor[0] ?> </td>
      </tr>
      <tr>
        <td title="Koordina Boylam">Koor. Boylam: </td>
        <td class="lon"><?php echo $coor[1] ?> </td>
      </tr>
    </table>
  </div>
  <div class="sec2">
    <div id="map"></div>
  </div>
</div>

<script>

function yandexMapsInit(){
    ymap = new ymaps.Map("map",{
        center:[$('.lat').text(), $('.lon').text()],
        zoom:15,
        controls: ["zoomControl", "fullscreenControl", "typeSelector"]
    }),
    marker = new ymaps.Placemark([$('.lat').text(), $('.lon').text()],{
        hintContent:"<i style='color:blue'>" + $('.ip').text() + "</i> Olası Konumu",
        balloonContent:"<p>Ip Adresi: <strong style='color:blue'>" + $('.lat').text() + "</strong><br>Hostname: <strong style='color:blue'>" + $('.hostname').text() + "</strong></p>"
    }),
    ymap.behaviors.disable("scrollZoom");
    ymap.geoObjects.add(marker)
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.slim.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://api-maps.yandex.com/2.1/?lang=tr-TR&onload=yandexMapsInit"></script>
</body>
</html><?php

function curls($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    $c = curl_exec($ch);
    curl_close($ch);
    return $c;
}

function code2CountryName($code){
  /* https://gist.github.com/halillusion/69316aff297b8e4f8cd16d321238adad */
    $countries = array(
    	'TR' => 'Türkiye',
    	'VI' => 'ABD Virgin Adaları',
    	'AF' => 'Afganistan',
    	'AX' => 'Aland Adaları',
    	'DE' => 'Almanya',
    	'US' => 'Amerika Birleşik Devletleri',
    	'UM' => 'Amerika Birleşik Devletleri Küçük Dış Adaları',
    	'AS' => 'Amerikan Samoası',
    	'AD' => 'Andora',
    	'AO' => 'Angola',
    	'AI' => 'Anguilla',
    	'AQ' => 'Antarktika',
    	'AG' => 'Antigua ve Barbuda',
    	'AR' => 'Arjantin',
    	'AL' => 'Arnavutluk',
    	'AW' => 'Aruba',
    	'QU' => 'Avrupa Birliği',
    	'AU' => 'Avustralya',
    	'AT' => 'Avusturya',
    	'AZ' => 'Azerbaycan',
    	'BS' => 'Bahamalar',
    	'BH' => 'Bahreyn',
    	'BD' => 'Bangladeş',
    	'BB' => 'Barbados',
    	'EH' => 'Batı Sahara',
    	'BZ' => 'Belize',
    	'BE' => 'Belçika',
    	'BJ' => 'Benin',
    	'BM' => 'Bermuda',
    	'BY' => 'Beyaz Rusya',
    	'BT' => 'Bhutan',
    	'ZZ' => 'Bilinmeyen veya Geçersiz Bölge',
    	'AE' => 'Birleşik Arap Emirlikleri',
    	'GB' => 'Birleşik Krallık',
    	'BO' => 'Bolivya',
    	'BA' => 'Bosna Hersek',
    	'BW' => 'Botsvana',
    	'BV' => 'Bouvet Adası',
    	'BR' => 'Brezilya',
    	'BN' => 'Brunei',
    	'BG' => 'Bulgaristan',
    	'BF' => 'Burkina Faso',
    	'BI' => 'Burundi',
    	'CV' => 'Cape Verde',
    	'GI' => 'Cebelitarık',
    	'DZ' => 'Cezayir',
    	'CX' => 'Christmas Adası',
    	'DJ' => 'Cibuti',
    	'CC' => 'Cocos Adaları',
    	'CK' => 'Cook Adaları',
    	'TD' => 'Çad',
    	'CZ' => 'Çek Cumhuriyeti',
    	'CN' => 'Çin',
    	'DK' => 'Danimarka',
    	'DM' => 'Dominik',
    	'DO' => 'Dominik Cumhuriyeti',
    	'TL' => 'Doğu Timor',
    	'EC' => 'Ekvator',
    	'GQ' => 'Ekvator Ginesi',
    	'SV' => 'El Salvador',
    	'ID' => 'Endonezya',
    	'ER' => 'Eritre',
    	'AM' => 'Ermenistan',
    	'EE' => 'Estonya',
    	'ET' => 'Etiyopya',
    	'FK' => 'Falkland Adaları (Malvinalar)',
    	'FO' => 'Faroe Adaları',
    	'MA' => 'Fas',
    	'FJ' => 'Fiji',
    	'CI' => 'Fildişi Sahilleri',
    	'PH' => 'Filipinler',
    	'PS' => 'Filistin Bölgesi',
    	'FI' => 'Finlandiya',
    	'FR' => 'Fransa',
    	'GF' => 'Fransız Guyanası',
    	'TF' => 'Fransız Güney Bölgeleri',
    	'PF' => 'Fransız Polinezyası',
    	'GA' => 'Gabon',
    	'GM' => 'Gambia',
    	'GH' => 'Gana',
    	'GN' => 'Gine',
    	'GW' => 'Gine-Bissau',
    	'GD' => 'Granada',
    	'GL' => 'Grönland',
    	'GP' => 'Guadeloupe',
    	'GU' => 'Guam',
    	'GT' => 'Guatemala',
    	'GG' => 'Guernsey',
    	'GY' => 'Guyana',
    	'ZA' => 'Güney Afrika',
    	'GS' => 'Güney Georgia ve Güney Sandwich Adaları',
    	'KR' => 'Güney Kore',
    	'CY' => 'Güney Kıbrıs Rum Kesimi',
    	'GE' => 'Gürcistan',
    	'HT' => 'Haiti',
    	'HM' => 'Heard Adası ve McDonald Adaları',
    	'IN' => 'Hindistan',
    	'IO' => 'Hint Okyanusu İngiliz Bölgesi',
    	'NL' => 'Hollanda',
    	'AN' => 'Hollanda Antilleri',
    	'HN' => 'Honduras',
    	'HK' => 'Hong Kong SAR - Çin',
    	'HR' => 'Hırvatistan',
    	'IQ' => 'Irak',
    	'VG' => 'İngiliz Virgin Adaları',
    	'IR' => 'İran',
    	'IE' => 'İrlanda',
    	'ES' => 'İspanya',
    	'IL' => 'İsrail',
    	'SE' => 'İsveç',
    	'CH' => 'İsviçre',
    	'IT' => 'İtalya',
    	'IS' => 'İzlanda',
    	'JM' => 'Jamaika',
    	'JP' => 'Japonya',
    	'JE' => 'Jersey',
    	'KH' => 'Kamboçya',
    	'CM' => 'Kamerun',
    	'CA' => 'Kanada',
    	'ME' => 'Karadağ',
    	'QA' => 'Katar',
    	'KY' => 'Kayman Adaları',
    	'KZ' => 'Kazakistan',
    	'KE' => 'Kenya',
    	'KI' => 'Kiribati',
    	'CO' => 'Kolombiya',
    	'KM' => 'Komorlar',
    	'CG' => 'Kongo',
    	'CD' => 'Kongo Demokratik Cumhuriyeti',
    	'CR' => 'Kosta Rika',
    	'KW' => 'Kuveyt',
    	'KP' => 'Kuzey Kore',
    	'MP' => 'Kuzey Mariana Adaları',
    	'CU' => 'Küba',
    	'KG' => 'Kırgızistan',
    	'LA' => 'Laos',
    	'LS' => 'Lesotho',
    	'LV' => 'Letonya',
    	'LR' => 'Liberya',
    	'LY' => 'Libya',
    	'LI' => 'Liechtenstein',
    	'LT' => 'Litvanya',
    	'LB' => 'Lübnan',
    	'LU' => 'Lüksemburg',
    	'HU' => 'Macaristan',
    	'MG' => 'Madagaskar',
    	'MO' => 'Makao S.A.R. Çin',
    	'MK' => 'Makedonya',
    	'MW' => 'Malavi',
    	'MV' => 'Maldivler',
    	'MY' => 'Malezya',
    	'ML' => 'Mali',
    	'MT' => 'Malta',
    	'IM' => 'Man Adası',
    	'MH' => 'Marshall Adaları',
    	'MQ' => 'Martinik',
    	'MU' => 'Mauritius',
    	'YT' => 'Mayotte',
    	'MX' => 'Meksika',
    	'FM' => 'Mikronezya Federal Eyaletleri',
    	'MD' => 'Moldovya Cumhuriyeti',
    	'MC' => 'Monako',
    	'MS' => 'Montserrat',
    	'MR' => 'Moritanya',
    	'MZ' => 'Mozambik',
    	'MN' => 'Moğolistan',
    	'MM' => 'Myanmar',
    	'EG' => 'Mısır',
    	'NA' => 'Namibya',
    	'NR' => 'Nauru',
    	'NP' => 'Nepal',
    	'NE' => 'Nijer',
    	'NG' => 'Nijerya',
    	'NI' => 'Nikaragua',
    	'NU' => 'Niue',
    	'NF' => 'Norfolk Adası',
    	'NO' => 'Norveç',
    	'CF' => 'Orta Afrika Cumhuriyeti',
    	'UZ' => 'Özbekistan',
    	'PK' => 'Pakistan',
    	'PW' => 'Palau',
    	'PA' => 'Panama',
    	'PG' => 'Papua Yeni Gine',
    	'PY' => 'Paraguay',
    	'PE' => 'Peru',
    	'PN' => 'Pitcairn',
    	'PL' => 'Polonya',
    	'PT' => 'Portekiz',
    	'PR' => 'Porto Riko',
    	'RE' => 'Reunion',
    	'RO' => 'Romanya',
    	'RW' => 'Ruanda',
    	'RU' => 'Rusya Federasyonu',
    	'SH' => 'Saint Helena',
    	'KN' => 'Saint Kitts ve Nevis',
    	'LC' => 'Saint Lucia',
    	'PM' => 'Saint Pierre ve Miquelon',
    	'VC' => 'Saint Vincent ve Grenadinler',
    	'WS' => 'Samoa',
    	'SM' => 'San Marino',
    	'ST' => 'Sao Tome ve Principe',
    	'SN' => 'Senegal',
    	'SC' => 'Seyşeller',
    	'SL' => 'Sierra Leone',
    	'SG' => 'Singapur',
    	'SK' => 'Slovakya',
    	'SI' => 'Slovenya',
    	'SB' => 'Solomon Adaları',
    	'SO' => 'Somali',
    	'LK' => 'Sri Lanka',
    	'SD' => 'Sudan',
    	'SR' => 'Surinam',
    	'SY' => 'Suriye',
    	'SA' => 'Suudi Arabistan',
    	'SJ' => 'Svalbard ve Jan Mayen',
    	'SZ' => 'Svaziland',
    	'RS' => 'Sırbistan',
    	'CS' => 'Sırbistan-Karadağ',
    	'CL' => 'Şili',
    	'TJ' => 'Tacikistan',
    	'TZ' => 'Tanzanya',
    	'TH' => 'Tayland',
    	'TW' => 'Tayvan',
    	'TG' => 'Togo',
    	'TK' => 'Tokelau',
    	'TO' => 'Tonga',
    	'TT' => 'Trinidad ve Tobago',
    	'TN' => 'Tunus',
    	'TC' => 'Turks ve Caicos Adaları',
    	'TV' => 'Tuvalu',
    	'TM' => 'Türkmenistan',
    	'UG' => 'Uganda',
    	'UA' => 'Ukrayna',
    	'OM' => 'Umman',
    	'UY' => 'Uruguay',
    	'QO' => 'Uzak Okyanusya',
    	'JO' => 'Ürdün',
    	'VU' => 'Vanuatu',
    	'VA' => 'Vatikan',
    	'VE' => 'Venezuela',
    	'VN' => 'Vietnam',
    	'WF' => 'Wallis ve Futuna',
    	'YE' => 'Yemen',
    	'NC' => 'Yeni Kaledonya',
    	'NZ' => 'Yeni Zelanda',
    	'GR' => 'Yunanistan',
    	'ZM' => 'Zambiya',
    	'ZW' => 'Zimbabve'
  );
  return $countries[$code];
}



?>
