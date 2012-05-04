<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://www.gnu.org/licenses/lgpl.html
* @version    $Id$
*/

/**
 * Defined constants used for GeoIp database reading
 */
if (!defined("GEOIP_COUNTRY_BEGIN")) define("GEOIP_COUNTRY_BEGIN", 16776960);
if (!defined("GEOIP_STATE_BEGIN_REV0")) define("GEOIP_STATE_BEGIN_REV0", 16700000);
if (!defined("GEOIP_STATE_BEGIN_REV1")) define("GEOIP_STATE_BEGIN_REV1", 16000000);
if (!defined("GEOIP_STANDARD")) define("GEOIP_STANDARD", 0);
if (!defined("GEOIP_MEMORY_CACHE")) define("GEOIP_MEMORY_CACHE", 1);
if (!defined("GEOIP_SHARED_MEMORY")) define("GEOIP_SHARED_MEMORY", 2);
if (!defined("STRUCTURE_INFO_MAX_SIZE")) define("STRUCTURE_INFO_MAX_SIZE", 20);
if (!defined("DATABASE_INFO_MAX_SIZE")) define("DATABASE_INFO_MAX_SIZE", 100);
if (!defined("GEOIP_COUNTRY_EDITION")) define("GEOIP_COUNTRY_EDITION", 106);
if (!defined("GEOIP_PROXY_EDITION")) define("GEOIP_PROXY_EDITION", 8);
if (!defined("GEOIP_ASNUM_EDITION")) define("GEOIP_ASNUM_EDITION", 9);
if (!defined("GEOIP_NETSPEED_EDITION")) define("GEOIP_NETSPEED_EDITION", 10);
if (!defined("GEOIP_REGION_EDITION_REV0")) define("GEOIP_REGION_EDITION_REV0", 112);
if (!defined("GEOIP_REGION_EDITION_REV1")) define("GEOIP_REGION_EDITION_REV1", 3);
if (!defined("GEOIP_CITY_EDITION_REV0")) define("GEOIP_CITY_EDITION_REV0", 111);
if (!defined("GEOIP_CITY_EDITION_REV1")) define("GEOIP_CITY_EDITION_REV1", 2);
if (!defined("GEOIP_ORG_EDITION")) define("GEOIP_ORG_EDITION", 110);
if (!defined("GEOIP_ISP_EDITION")) define("GEOIP_ISP_EDITION", 4);
if (!defined("SEGMENT_RECORD_LENGTH")) define("SEGMENT_RECORD_LENGTH", 3);
if (!defined("STANDARD_RECORD_LENGTH")) define("STANDARD_RECORD_LENGTH", 3);
if (!defined("ORG_RECORD_LENGTH")) define("ORG_RECORD_LENGTH", 4);
if (!defined("MAX_RECORD_LENGTH")) define("MAX_RECORD_LENGTH", 4);
if (!defined("MAX_ORG_RECORD_LENGTH")) define("MAX_ORG_RECORD_LENGTH", 300);
if (!defined("GEOIP_SHM_KEY")) define("GEOIP_SHM_KEY", 0x4f415401);
if (!defined("US_OFFSET")) define("US_OFFSET", 1);
if (!defined("CANADA_OFFSET")) define("CANADA_OFFSET", 677);
if (!defined("WORLD_OFFSET")) define("WORLD_OFFSET", 1353);
if (!defined("FIPS_RANGE")) define("FIPS_RANGE", 360);
if (!defined("GEOIP_UNKNOWN_SPEED")) define("GEOIP_UNKNOWN_SPEED", 0);
if (!defined("GEOIP_DIALUP_SPEED")) define("GEOIP_DIALUP_SPEED", 1);
if (!defined("GEOIP_CABLEDSL_SPEED")) define("GEOIP_CABLEDSL_SPEED", 2);
if (!defined("GEOIP_CORPORATE_SPEED")) define("GEOIP_CORPORATE_SPEED", 3);
if (!defined("GEOIP_DOMAIN_EDITION")) define("GEOIP_DOMAIN_EDITION", 11);
if (!defined("GEOIP_LOCATIONA_EDITION")) define("GEOIP_LOCATIONA_EDITION", 13);
if (!defined("GEOIP_ACCURACYRADIUS_EDITION")) define("GEOIP_ACCURACYRADIUS_EDITION", 14);
if (!defined("GEOIP_CITYCOMBINED_EDITION")) define("GEOIP_CITYCOMBINED_EDITION", 15);
if (!defined("CITYCOMBINED_FIXED_RECORD")) define("CITYCOMBINED_FIXED_RECORD", 7 );


/**
* Geo IP Country - connect to GeoIP.dat database to return the country based on IP address
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotGeoip
* @author     DotKernel Team <team@dotkernel.com>
*/
class Dot_Geoip_Country extends Dot_Geoip
{
	/**
	 * Flags country code
	 * @access private
	 * @var string
	 */
    private $_flags;
	/**
	 * File handle identifier
	 * @access private
	 * @var resource
	 */
	private $_filehandle;
	/**
	 * Memmory buffered - the read string
	 * @access private
	 * @var string
	 */
    private $_memoryBuffer;
	/**
	 * Database type
	 * @access private
	 * @var int
	 */
    private $_databaseType;
	/**
	 * Database segments
	 * @access private
	 * @var int
	 */
    private $_databaseSegments;
	/**
	 * Record length
	 * @access private
	 * @var int
	 */
    private $_recordLength;
	/**
	 * ID returned by shmop_open() function to access the shared memory segment
	 * @access private
	 * @var int
	 */
    private $_shmid;
	/**
	 * Array with country code to number corespondence
	 * @access private
	 * @var array
	 */
    private $_geoipCountryCodeToNumber = array(
		"" => 0, "AP" => 1, "EU" => 2, "AD" => 3, "AE" => 4, "AF" => 5,
		"AG" => 6, "AI" => 7, "AL" => 8, "AM" => 9, "AN" => 10, "AO" => 11,
		"AQ" => 12, "AR" => 13, "AS" => 14, "AT" => 15, "AU" => 16, "AW" => 17,
		"AZ" => 18, "BA" => 19, "BB" => 20, "BD" => 21, "BE" => 22, "BF" => 23,
		"BG" => 24, "BH" => 25, "BI" => 26, "BJ" => 27, "BM" => 28, "BN" => 29,
		"BO" => 30, "BR" => 31, "BS" => 32, "BT" => 33, "BV" => 34, "BW" => 35,
		"BY" => 36, "BZ" => 37, "CA" => 38, "CC" => 39, "CD" => 40, "CF" => 41,
		"CG" => 42, "CH" => 43, "CI" => 44, "CK" => 45, "CL" => 46, "CM" => 47,
		"CN" => 48, "CO" => 49, "CR" => 50, "CU" => 51, "CV" => 52, "CX" => 53,
		"CY" => 54, "CZ" => 55, "DE" => 56, "DJ" => 57, "DK" => 58, "DM" => 59,
		"DO" => 60, "DZ" => 61, "EC" => 62, "EE" => 63, "EG" => 64, "EH" => 65,
		"ER" => 66, "ES" => 67, "ET" => 68, "FI" => 69, "FJ" => 70, "FK" => 71,
		"FM" => 72, "FO" => 73, "FR" => 74, "FX" => 75, "GA" => 76, "GB" => 77,
		"GD" => 78, "GE" => 79, "GF" => 80, "GH" => 81, "GI" => 82, "GL" => 83,
		"GM" => 84, "GN" => 85, "GP" => 86, "GQ" => 87, "GR" => 88, "GS" => 89,
		"GT" => 90, "GU" => 91, "GW" => 92, "GY" => 93, "HK" => 94, "HM" => 95,
		"HN" => 96, "HR" => 97, "HT" => 98, "HU" => 99, "ID" => 100, "IE" => 101,
		"IL" => 102, "IN" => 103, "IO" => 104, "IQ" => 105, "IR" => 106, "IS" => 107,
		"IT" => 108, "JM" => 109, "JO" => 110, "JP" => 111, "KE" => 112, "KG" => 113,
		"KH" => 114, "KI" => 115, "KM" => 116, "KN" => 117, "KP" => 118, "KR" => 119,
		"KW" => 120, "KY" => 121, "KZ" => 122, "LA" => 123, "LB" => 124, "LC" => 125,
		"LI" => 126, "LK" => 127, "LR" => 128, "LS" => 129, "LT" => 130, "LU" => 131,
		"LV" => 132, "LY" => 133, "MA" => 134, "MC" => 135, "MD" => 136, "MG" => 137,
		"MH" => 138, "MK" => 139, "ML" => 140, "MM" => 141, "MN" => 142, "MO" => 143,
		"MP" => 144, "MQ" => 145, "MR" => 146, "MS" => 147, "MT" => 148, "MU" => 149,
		"MV" => 150, "MW" => 151, "MX" => 152, "MY" => 153, "MZ" => 154, "NA" => 155,
		"NC" => 156, "NE" => 157, "NF" => 158, "NG" => 159, "NI" => 160, "NL" => 161,
		"NO" => 162, "NP" => 163, "NR" => 164, "NU" => 165, "NZ" => 166, "OM" => 167,
		"PA" => 168, "PE" => 169, "PF" => 170, "PG" => 171, "PH" => 172, "PK" => 173,
		"PL" => 174, "PM" => 175, "PN" => 176, "PR" => 177, "PS" => 178, "PT" => 179,
		"PW" => 180, "PY" => 181, "QA" => 182, "RE" => 183, "RO" => 184, "RU" => 185,
		"RW" => 186, "SA" => 187, "SB" => 188, "SC" => 189, "SD" => 190, "SE" => 191,
		"SG" => 192, "SH" => 193, "SI" => 194, "SJ" => 195, "SK" => 196, "SL" => 197,
		"SM" => 198, "SN" => 199, "SO" => 200, "SR" => 201, "ST" => 202, "SV" => 203,
		"SY" => 204, "SZ" => 205, "TC" => 206, "TD" => 207, "TF" => 208, "TG" => 209,
		"TH" => 210, "TJ" => 211, "TK" => 212, "TM" => 213, "TN" => 214, "TO" => 215,
		"TL" => 216, "TR" => 217, "TT" => 218, "TV" => 219, "TW" => 220, "TZ" => 221,
		"UA" => 222, "UG" => 223, "UM" => 224, "US" => 225, "UY" => 226, "UZ" => 227,
		"VA" => 228, "VC" => 229, "VE" => 230, "VG" => 231, "VI" => 232, "VN" => 233,
		"VU" => 234, "WF" => 235, "WS" => 236, "YE" => 237, "YT" => 238, "RS" => 239,
		"ZA" => 240, "ZM" => 241, "ME" => 242, "ZW" => 243, "A1" => 244, "A2" => 245,
		"O1" => 246, "AX" => 247, "GG" => 248, "IM" => 249, "JE" => 250, "BL" => 251,
		"MF" => 252);
	/**
	 * Array with country codes (2 chars)
	 * @access private
	 * @var array
	 */
    private $_geoipCountryCodes = array(
		"", "AP", "EU", "AD", "AE", "AF", "AG", "AI", "AL", "AM", "AN", "AO", "AQ",
		"AR", "AS", "AT", "AU", "AW", "AZ", "BA", "BB", "BD", "BE", "BF", "BG", "BH",
		"BI", "BJ", "BM", "BN", "BO", "BR", "BS", "BT", "BV", "BW", "BY", "BZ", "CA",
		"CC", "CD", "CF", "CG", "CH", "CI", "CK", "CL", "CM", "CN", "CO", "CR", "CU",
		"CV", "CX", "CY", "CZ", "DE", "DJ", "DK", "DM", "DO", "DZ", "EC", "EE", "EG",
		"EH", "ER", "ES", "ET", "FI", "FJ", "FK", "FM", "FO", "FR", "FX", "GA", "GB",
		"GD", "GE", "GF", "GH", "GI", "GL", "GM", "GN", "GP", "GQ", "GR", "GS", "GT",
		"GU", "GW", "GY", "HK", "HM", "HN", "HR", "HT", "HU", "ID", "IE", "IL", "IN",
		"IO", "IQ", "IR", "IS", "IT", "JM", "JO", "JP", "KE", "KG", "KH", "KI", "KM",
		"KN", "KP", "KR", "KW", "KY", "KZ", "LA", "LB", "LC", "LI", "LK", "LR", "LS",
		"LT", "LU", "LV", "LY", "MA", "MC", "MD", "MG", "MH", "MK", "ML", "MM", "MN",
		"MO", "MP", "MQ", "MR", "MS", "MT", "MU", "MV", "MW", "MX", "MY", "MZ", "NA",
		"NC", "NE", "NF", "NG", "NI", "NL", "NO", "NP", "NR", "NU", "NZ", "OM", "PA",
		"PE", "PF", "PG", "PH", "PK", "PL", "PM", "PN", "PR", "PS", "PT", "PW", "PY",
		"QA", "RE", "RO", "RU", "RW", "SA", "SB", "SC", "SD", "SE", "SG", "SH", "SI",
		"SJ", "SK", "SL", "SM", "SN", "SO", "SR", "ST", "SV", "SY", "SZ", "TC", "TD",
		"TF", "TG", "TH", "TJ", "TK", "TM", "TN", "TO", "TL", "TR", "TT", "TV", "TW",
		"TZ", "UA", "UG", "UM", "US", "UY", "UZ", "VA", "VC", "VE", "VG", "VI", "VN",
		"VU", "WF", "WS", "YE", "YT", "RS", "ZA", "ZM", "ME", "ZW", "A1", "A2", "O1",
		"AX", "GG", "IM", "JE", "BL", "MF");
	/**
	 * Array with country codes (3 chars)
	 * @access private
	 * @var array
	 */
    private $_geoipCountryCodes3 = array(
		"","AP","EU","AND","ARE","AFG","ATG","AIA","ALB","ARM","ANT","AGO","ATA","ARG",
		"ASM","AUT","AUS","ABW","AZE","BIH","BRB","BGD","BEL","BFA","BGR","BHR","BDI",
		"BEN","BMU","BRN","BOL","BRA","BHS","BTN","BVT","BWA","BLR","BLZ","CAN","CCK",
		"COD","CAF","COG","CHE","CIV","COK","CHL","CMR","CHN","COL","CRI","CUB","CPV",
		"CXR","CYP","CZE","DEU","DJI","DNK","DMA","DOM","DZA","ECU","EST","EGY","ESH",
		"ERI","ESP","ETH","FIN","FJI","FLK","FSM","FRO","FRA","FX","GAB","GBR","GRD",
		"GEO","GUF","GHA","GIB","GRL","GMB","GIN","GLP","GNQ","GRC","SGS","GTM","GUM",
		"GNB","GUY","HKG","HMD","HND","HRV","HTI","HUN","IDN","IRL","ISR","IND","IOT",
		"IRQ","IRN","ISL","ITA","JAM","JOR","JPN","KEN","KGZ","KHM","KIR","COM","KNA",
		"PRK","KOR","KWT","CYM","KAZ","LAO","LBN","LCA","LIE","LKA","LBR","LSO","LTU",
		"LUX","LVA","LBY","MAR","MCO","MDA","MDG","MHL","MKD","MLI","MMR","MNG","MAC",
		"MNP","MTQ","MRT","MSR","MLT","MUS","MDV","MWI","MEX","MYS","MOZ","NAM","NCL",
		"NER","NFK","NGA","NIC","NLD","NOR","NPL","NRU","NIU","NZL","OMN","PAN","PER",
		"PYF","PNG","PHL","PAK","POL","SPM","PCN","PRI","PSE","PRT","PLW","PRY","QAT",
		"REU","ROU","RUS","RWA","SAU","SLB","SYC","SDN","SWE","SGP","SHN","SVN","SJM",
		"SVK","SLE","SMR","SEN","SOM","SUR","STP","SLV","SYR","SWZ","TCA","TCD","ATF",
		"TGO","THA","TJK","TKL","TLS","TKM","TUN","TON","TUR","TTO","TUV","TWN","TZA",
		"UKR","UGA","UMI","USA","URY","UZB","VAT","VCT","VEN","VGB","VIR","VNM","VUT",
		"WLF","WSM","YEM","MYT","SRB","ZAF","ZMB","MNE","ZWE","A1","A2","O1",
		"ALA","GGY","IMN","JEY","BLM","MAF");
	/**
	 * Array with country names
	 * @access private
	 * @var array
	 */
    private $_geoipCountryNames = array(
		"", "Asia/Pacific Region", "Europe", "Andorra", "United Arab Emirates",
		"Afghanistan", "Antigua and Barbuda", "Anguilla", "Albania", "Armenia",
		"Netherlands Antilles", "Angola", "Antarctica", "Argentina", "American Samoa",
		"Austria", "Australia", "Aruba", "Azerbaijan", "Bosnia and Herzegovina",
		"Barbados", "Bangladesh", "Belgium", "Burkina Faso", "Bulgaria", "Bahrain",
		"Burundi", "Benin", "Bermuda", "Brunei Darussalam", "Bolivia", "Brazil",
		"Bahamas", "Bhutan", "Bouvet Island", "Botswana", "Belarus", "Belize",
		"Canada", "Cocos (Keeling) Islands", "Congo, The Democratic Republic of the",
		"Central African Republic", "Congo", "Switzerland", "Cote D'Ivoire", "Cook Islands",
		"Chile", "Cameroon", "China", "Colombia", "Costa Rica", "Cuba", "Cape Verde",
		"Christmas Island", "Cyprus", "Czech Republic", "Germany", "Djibouti",
		"Denmark", "Dominica", "Dominican Republic", "Algeria", "Ecuador", "Estonia",
		"Egypt", "Western Sahara", "Eritrea", "Spain", "Ethiopia", "Finland", "Fiji",
		"Falkland Islands (Malvinas)", "Micronesia, Federated States of", "Faroe Islands",
		"France", "France, Metropolitan", "Gabon", "United Kingdom",
		"Grenada", "Georgia", "French Guiana", "Ghana", "Gibraltar", "Greenland",
		"Gambia", "Guinea", "Guadeloupe", "Equatorial Guinea", "Greece", "South Georgia and the South Sandwich Islands",
		"Guatemala", "Guam", "Guinea-Bissau",
		"Guyana", "Hong Kong", "Heard Island and McDonald Islands", "Honduras",
		"Croatia", "Haiti", "Hungary", "Indonesia", "Ireland", "Israel", "India",
		"British Indian Ocean Territory", "Iraq", "Iran, Islamic Republic of",
		"Iceland", "Italy", "Jamaica", "Jordan", "Japan", "Kenya", "Kyrgyzstan",
		"Cambodia", "Kiribati", "Comoros", "Saint Kitts and Nevis", "Korea, Democratic People's Republic of",
		"Korea, Republic of", "Kuwait", "Cayman Islands",
		"Kazakhstan", "Lao People's Democratic Republic", "Lebanon", "Saint Lucia",
		"Liechtenstein", "Sri Lanka", "Liberia", "Lesotho", "Lithuania", "Luxembourg",
		"Latvia", "Libyan Arab Jamahiriya", "Morocco", "Monaco", "Moldova, Republic of",
		"Madagascar", "Marshall Islands", "Macedonia",
		"Mali", "Myanmar", "Mongolia", "Macau", "Northern Mariana Islands",
		"Martinique", "Mauritania", "Montserrat", "Malta", "Mauritius", "Maldives",
		"Malawi", "Mexico", "Malaysia", "Mozambique", "Namibia", "New Caledonia",
		"Niger", "Norfolk Island", "Nigeria", "Nicaragua", "Netherlands", "Norway",
		"Nepal", "Nauru", "Niue", "New Zealand", "Oman", "Panama", "Peru", "French Polynesia",
		"Papua New Guinea", "Philippines", "Pakistan", "Poland", "Saint Pierre and Miquelon",
		"Pitcairn Islands", "Puerto Rico", "Palestinian Territory",
		"Portugal", "Palau", "Paraguay", "Qatar", "Reunion", "Romania",
		"Russian Federation", "Rwanda", "Saudi Arabia", "Solomon Islands",
		"Seychelles", "Sudan", "Sweden", "Singapore", "Saint Helena", "Slovenia",
		"Svalbard and Jan Mayen", "Slovakia", "Sierra Leone", "San Marino", "Senegal",
		"Somalia", "Suriname", "Sao Tome and Principe", "El Salvador", "Syrian Arab Republic",
		"Swaziland", "Turks and Caicos Islands", "Chad", "French Southern Territories",
		"Togo", "Thailand", "Tajikistan", "Tokelau", "Turkmenistan",
		"Tunisia", "Tonga", "Timor-Leste", "Turkey", "Trinidad and Tobago", "Tuvalu",
		"Taiwan", "Tanzania, United Republic of", "Ukraine",
		"Uganda", "United States Minor Outlying Islands", "United States", "Uruguay",
		"Uzbekistan", "Holy See (Vatican City State)", "Saint Vincent and the Grenadines",
		"Venezuela", "Virgin Islands, British", "Virgin Islands, U.S.",
		"Vietnam", "Vanuatu", "Wallis and Futuna", "Samoa", "Yemen", "Mayotte",
		"Serbia", "South Africa", "Zambia", "Montenegro", "Zimbabwe",
		"Anonymous Proxy","Satellite Provider","Other",
		"Aland Islands","Guernsey","Isle of Man","Jersey","Saint Barthelemy","Saint Martin");
	/**
	 * Array with continent codes
	 * @access private
	 * @var array
	 */
    private $_geoipContinentCodes = array(
		  "--", "AS", "EU", "EU", "AS", "AS", "NA", "NA", "EU", "AS",
		  "NA", "AF", "AN", "SA", "OC", "EU", "OC", "NA", "AS", "EU",
		  "NA", "AS", "EU", "AF", "EU", "AS", "AF", "AF", "NA", "AS",
		  "SA", "SA", "NA", "AS", "AN", "AF", "EU", "NA", "NA", "AS",
		  "AF", "AF", "AF", "EU", "AF", "OC", "SA", "AF", "AS", "SA",
		  "NA", "NA", "AF", "AS", "AS", "EU", "EU", "AF", "EU", "NA",
		  "NA", "AF", "SA", "EU", "AF", "AF", "AF", "EU", "AF", "EU",
		  "OC", "SA", "OC", "EU", "EU", "EU", "AF", "EU", "NA", "AS",
		  "SA", "AF", "EU", "NA", "AF", "AF", "NA", "AF", "EU", "AN",
		  "NA", "OC", "AF", "SA", "AS", "AN", "NA", "EU", "NA", "EU",
		  "AS", "EU", "AS", "AS", "AS", "AS", "AS", "EU", "EU", "NA",
		  "AS", "AS", "AF", "AS", "AS", "OC", "AF", "NA", "AS", "AS",
		  "AS", "NA", "AS", "AS", "AS", "NA", "EU", "AS", "AF", "AF",
		  "EU", "EU", "EU", "AF", "AF", "EU", "EU", "AF", "OC", "EU",
		  "AF", "AS", "AS", "AS", "OC", "NA", "AF", "NA", "EU", "AF",
		  "AS", "AF", "NA", "AS", "AF", "AF", "OC", "AF", "OC", "AF",
		  "NA", "EU", "EU", "AS", "OC", "OC", "OC", "AS", "NA", "SA",
		  "OC", "OC", "AS", "AS", "EU", "NA", "OC", "NA", "AS", "EU",
		  "OC", "SA", "AS", "AF", "EU", "EU", "AF", "AS", "OC", "AF",
		  "AF", "EU", "AS", "AF", "EU", "EU", "EU", "AF", "EU", "AF",
		  "AF", "SA", "AF", "NA", "AS", "AF", "NA", "AF", "AN", "AF",
		  "AS", "AS", "OC", "AS", "AF", "OC", "AS", "EU", "NA", "OC",
		  "AS", "AF", "EU", "AF", "OC", "NA", "SA", "AS", "EU", "NA",
		  "SA", "NA", "NA", "AS", "OC", "OC", "OC", "AS", "AF", "EU",
		  "AF", "AF", "EU", "AF", "--", "--", "--", "EU", "EU", "EU",
		  "EU", "NA", "NA");
	/**
	 * Constructor
	 * @access public
	 * @return Dot_Geoip_Country
	 */
	public function __construct()
	{
	}
	 /** Get file version
	 * Return database name and version
	 * @access public
	 * @param string $path - path to GeoIP.dat  database file
	 * @return string
	 */
	public static function geoipDatabaseInfo($path)
	{
		$message =  "Missing File";
		$content = @file_get_contents($path, FILE_BINARY);
		// if we find  the file
		if($content)
	 	{
	 		$parts = explode("\n", $content);
			$lastLine = end($parts);
			$pos1 = stripos($lastLine, 'GEO');
			$pos2 = stripos($lastLine, 'Build');
			$message = trim(substr($lastLine, $pos1, $pos2-$pos1));
	 	}
		return $message;
	}
	/**
	 * Get the country by IP
	 * Return an array with : short name, like 'us' and long name, like 'United States'
	 * @access public
	 * @param string $path - path to GeoIP.dat  database file
	 * @param string $ip
	 * @return array
	 */
	public function getCountryByAddr($path, $ip)
	{
		$country = array(0 => 'unknown',1 => 'NA', 'response' => 'OK');
		$this->_geoipOpen($path, GEOIP_STANDARD);
		$countryCode = $this->_geoipCountryCodeByAddr($ip);
		$this->_geoipClose();
		if($countryCode)
		{
			$countryIndex = array_search($countryCode, $this->_geoipCountryCodes);
			$country[0] = strtolower($countryCode);
			$country[1] = $this->_geoipCountryNames[$countryIndex];
		}
		return $country;
	}
	/**
	 * Open Geoip database
	 * @access private
	 * @param string $filename
	 * @param int $flags
	 * @return void
	 */
	private function _geoipOpen($filename, $flags)
	{
		$this->_flags = $flags;
		if ($this->_flags & GEOIP_SHARED_MEMORY)
		{
			$this->_shmid = @shmop_open (GEOIP_SHM_KEY, "a", 0, 0);
		}
		else
		{
			$this->_filehandle = fopen($filename,"rb") or die( "Can not open $filename\n" );
			if ($this->_flags & GEOIP_MEMORY_CACHE)
			{
				$sArray = fstat($this->_filehandle);
			    $this->_memoryBuffer = fread($this->_filehandle, $sArray['size']);
			}
		}
		$this->_setupSegments();
	}
	/**
	 * Close opened file
	 * @access private
	 * @return bool
	 */
	private function _geoipClose()
	{
		if ($this->_flags & GEOIP_SHARED_MEMORY)
		{
			return true;
		}
		return fclose($this->_filehandle);
	}
	/**
	 * Get the country ID by name.
	 * Return the ID of the country or false if no country found
	 * @access public
	 * @param string $name
	 * @return int
	 */
	public function geoipCountryIdByName($name)
	{
		$addr = gethostbyname($name);
		if (!$addr || $addr == $name)
		{
			return false;
  		}
  		return $this->_geoipCountryIdByAddr($addr);
	}
	/**
	 * Get country code by name
	 * @access public
	 * @param string $name
	 * @return string
	 */
	public function geoipCountryCodeByName($name)
	{
		$countryId = $this->geoipCountryIdByName($name);
		if ($countryId !== false)
		{
			return $this->_geoipCountryCodes[$countryId];
		}
		return false;
	}
	/**
	 * Get country name by name
	 * @access public
	 * @param string $name
	 * @return string
	 */
	public function geoipCountryNameByName($name)
	{
		$countryId = $this->geoipCountryIdByName($name);
		if ($countryId !== false)
		{
		    return $this->_geoipCountryNames[$countryId];
		}
		return false;
	}
	/**
	 * Get country id based on IP address
	 * @access private
	 * @param string $addr
	 * @return int
	 */
	private function _geoipCountryIdByAddr($addr)
	{
	  $ipNum = ip2long($addr);
	  return $this->_geoipSeekCountry($ipNum) - GEOIP_COUNTRY_BEGIN;
	}
	/**
	 * Get country code based on IP address
	 * @access private
	 * @param string $addr
	 * @return string
	 */
	private function _geoipCountryCodeByAddr($addr)
	{
	  if ($this->_databaseType == GEOIP_CITY_EDITION_REV1)
	  {
	    $record = $this->geoip_record_by_addr($addr);
	    if ( $record !== false )
		{
	      return $record->country_code;
	    }
	  }
	  else
	  {
	    $countryId = $this->_geoipCountryIdByAddr($addr);
	    if ($countryId !== false)
		{
	      return $this->_geoipCountryCodes[$countryId];
	    }
	  }
	  return false;
	}
	/**
	 * Get country name based on IP address
	 * @access public
	 * @param string $addr
	 * @return string
	 */
	public function geoipCountryNameByAddr($addr)
	{
		if ($this->_databaseType == GEOIP_CITY_EDITION_REV1)
		{
			$record = geoip_record_by_addr($gi,$addr);
			return $record->country_name;
		}
		else {
			$countryId = $this->_geoipCountryIdByAddr($addr);
			if ($countryId !== false)
			{
		  		return $this->_geoipCountryNames[$countryId];
			}
		}
		return false;
	}
	/**
	 * Search the country based on IP address
	 * @access private
	 * @param int $ipNum
	 * @return int
	 */
	private function _geoipSeekCountry($ipNum)
	{
	  $offset = 0;
	  for ($depth = 31; $depth >= 0; --$depth)
	  {
	    if ($this->_flags & GEOIP_MEMORY_CACHE)
		{
	      // workaround php's broken substr, strpos, etc handling with
	      // mbstring.func_overload and mbstring.internal_encoding
	      $enc = mb_internal_encoding();
	      mb_internal_encoding('ISO-8859-1');
	      $buf = substr($this->_memoryBuffer,
                        2 * $this->_recordLength * $offset,
                        2 * $this->_recordLength);
	      mb_internal_encoding($enc);
	    }
		elseif ($this->_flags & GEOIP_SHARED_MEMORY)
		{
	      $buf = @shmop_read($this->_shmid,
                             2 * $this->_recordLength * $offset,
                             2 * $this->_recordLength );
	    }
		else
		{
	      fseek($this->_filehandle, 2 * $this->_recordLength * $offset, SEEK_SET) == 0
		  	or die("fseek failed");
	      $buf = fread($this->_filehandle, 2 * $this->_recordLength);
	    }
	    $x = array(0,0);
	    for ($i = 0; $i < 2; ++$i)
		{
	      for ($j = 0; $j < $this->_recordLength; ++$j)
		  {
	        $x[$i] += ord($buf[$this->_recordLength * $i + $j]) << ($j * 8);
	      }
	    }
	    if ($ipNum & (1 << $depth))
		{
	      if ($x[1] >= $this->_databaseSegments)
		  {
	        return $x[1];
	      }
	      $offset = $x[1];
	    }
		else
		{
	      if ($x[0] >= $this->_databaseSegments)
		  {
	        return $x[0];
	      }
	      $offset = $x[0];
	    }
	  }
	  trigger_error("error traversing database - perhaps it is corrupt?", E_USER_ERROR);
	  return false;
	}
	/**
	 * Get GeoIP Organization by IP address
	 * Note: file GeoIPOrg.dat is required
	 * @param string $ipnum
	 * @return string
	 */
	private function _getOrg($ipnum)
	{
		$seekOrg = $this->_geoipSeekCountry($ipnum);
		if ($seekOrg == $this->_databaseSegments)
		{
			return NULL;
		}
		$recordPointer = $seekOrg + (2 * $this->_recordLength - 1) * $this->_databaseSegments;
		if ($this->_flags & GEOIP_SHARED_MEMORY) {
		$orgBuf = @shmop_read ($this->_shmid, $recordPointer, MAX_ORG_RECORD_LENGTH);
		} else {
		fseek($this->_filehandle, $recordPointer, SEEK_SET);
		$orgBuf = fread($this->_filehandle,MAX_ORG_RECORD_LENGTH);
		}
		// workaround php's broken substr, strpos, etc handling with
		// mbstring.func_overload and mbstring.internal_encoding
		$enc = mb_internal_encoding();
		mb_internal_encoding('ISO-8859-1');
		$orgBuf = substr($orgBuf, 0, strpos($orgBuf, "\0"));
		mb_internal_encoding($enc);
		return $orgBuf;
	}
	/**
	 * Get Orgasnization based on IP address
	 * @access private
	 * @param string $addr
	 * @return string
	 */
	public function geoipOrgByAddr ($addr)
	{
	  if ($addr == NULL)
	  {
	    return 0;
	  }
	  $ipnum = ip2long($addr);
	  return $this->_getOrg($ipnum);
	}
	/**
	 * Get GeoIp region based on IP address.
	 * Return an array with country code and region name
	 * @access private
	 * @param object $ipnum
	 * @return array
	 */
	private function _getRegion($ipnum)
	{
		if ($this->_databaseType == GEOIP_REGION_EDITION_REV0)
		{
			$seekRegion = $this->_geoipSeekCountry($ipnum) - GEOIP_STATE_BEGIN_REV0;
			if ($seekRegion >= 1000)
			{
			  $country_code = "US";
			  $region = chr(($seekRegion - 1000)/26 + 65) . chr(($seekRegion - 1000)%26 + 65);
			}
			else
			{
			  $country_code = $this->_geoipCountryCodes[$seekRegion];
			  $region = "";
			}
			return array ($country_code,$region);
		}
		else if ($this->_databaseType == GEOIP_REGION_EDITION_REV1)
		{
			$seekRegion = $this->_geoipSeekCountry($ipnum) - GEOIP_STATE_BEGIN_REV1;
			//print $seek_region;
			if ($seekRegion < US_OFFSET)
			{
			  $country_code = "";
			  $region = "";
			}
			elseif ($seekRegion < CANADA_OFFSET)
			{
			  $country_code = "US";
			  $region = chr(($seekRegion - US_OFFSET)/26 + 65) . chr(($seekRegion - US_OFFSET)%26 + 65);
			}
			elseif ($seekRegion < WORLD_OFFSET)
			{
			  $country_code = "CA";
			  $region = chr(($seekRegion - CANADA_OFFSET)/26 + 65) . chr(($seekRegion - CANADA_OFFSET)%26 + 65);
			}
			else
			{
			  $country_code = $this->_geoipCountryCodes[($seekRegion - WORLD_OFFSET) / FIPS_RANGE];
			  $region = "";
			}
			return array ($country_code,$region);
		}
	}
	/**
	 * Get GeoIp region based on IP address
	 * @access public
	 * @param string $addr
	 * @return array
	 */
	public function geoipRegionByAddr ($addr)
	{
		if ($addr == NULL)
		{
			return 0;
		}
		$ipnum = ip2long($addr);
		return $this->_getRegion($ipnum);
	}
	/**
	 * Setup segments from GeoIp
	 * @access private
	 * @return void
	 */
	private function _setupSegments()
	{
	  $this->_databaseType = GEOIP_COUNTRY_EDITION;
	  $this->_recordLength = STANDARD_RECORD_LENGTH;
	  if ($this->_flags & GEOIP_SHARED_MEMORY)
	  {
	    $offset = @shmop_size ($this->_shmid) - 3;
	    for ($i = 0; $i < STRUCTURE_INFO_MAX_SIZE; $i++)
		{
	        $delim = @shmop_read ($this->_shmid, $offset, 3);
	        $offset += 3;
	        if ($delim == (chr(255).chr(255).chr(255)))
			{
	            $this->_databaseType = ord(@shmop_read ($this->_shmid, $offset, 1));
	            $offset++;
	            if ($this->_databaseType == GEOIP_REGION_EDITION_REV0)
				{
	                $this->_databaseSegments = GEOIP_STATE_BEGIN_REV0;
	            }
				else if ($this->_databaseType == GEOIP_REGION_EDITION_REV1)
				{
	                $this->_databaseSegments = GEOIP_STATE_BEGIN_REV1;
		    	}
				else if (($this->_databaseType == GEOIP_CITY_EDITION_REV0)
						 || ($this->_databaseType == GEOIP_CITY_EDITION_REV1)
	                     || ($this->_databaseType == GEOIP_ORG_EDITION)
	                     || ($this->_databaseType == GEOIP_DOMAIN_EDITION)
			    		 || ($this->_databaseType == GEOIP_ISP_EDITION)
			    		 || ($this->_databaseType == GEOIP_LOCATIONA_EDITION)
			    		 || ($this->_databaseType == GEOIP_ACCURACYRADIUS_EDITION)
			    		 || ($this->_databaseType == GEOIP_ASNUM_EDITION)
						)
				{
	                $this->_databaseSegments = 0;
	                $buf = @shmop_read ($this->_shmid, $offset, SEGMENT_RECORD_LENGTH);
	                for ($j = 0;$j < SEGMENT_RECORD_LENGTH;$j++)
					{
	                    $this->_databaseSegments += (ord($buf[$j]) << ($j * 8));
	                }
		            if (($this->_databaseType == GEOIP_ORG_EDITION)
						|| ($this->_databaseType == GEOIP_DOMAIN_EDITION)
						|| ($this->_databaseType == GEOIP_ISP_EDITION)
					   )
					{
		                $this->_recordLength = ORG_RECORD_LENGTH;
	                }
	            }
	            break;
	        }
			else
			{
	            $offset -= 4;
	        }
	    }
	    if (($this->_databaseType == GEOIP_COUNTRY_EDITION)
			|| ($this->_databaseType == GEOIP_PROXY_EDITION)
			|| ($this->_databaseType == GEOIP_NETSPEED_EDITION)
		   )
		{
			$this->_databaseSegments = GEOIP_COUNTRY_BEGIN;
		}
	}
	else
	{
	    $filepos = ftell($this->_filehandle);
	    fseek($this->_filehandle, -3, SEEK_END);
	    for ($i = 0; $i < STRUCTURE_INFO_MAX_SIZE; $i++)
		{
	        $delim = fread($this->_filehandle,3);
	        if ($delim == (chr(255).chr(255).chr(255)))
			{
	        	$this->_databaseType = ord(fread($this->_filehandle,1));
				if ($this->_databaseType == GEOIP_REGION_EDITION_REV0)
				{
	            	$this->_databaseSegments = GEOIP_STATE_BEGIN_REV0;
	        	}
	        	else if ($this->_databaseType == GEOIP_REGION_EDITION_REV1)
				{
		    		$this->_databaseSegments = GEOIP_STATE_BEGIN_REV1;
	            }
				else if (($this->_databaseType == GEOIP_CITY_EDITION_REV0)
						 || ($this->_databaseType == GEOIP_CITY_EDITION_REV1)
						 || ($this->_databaseType == GEOIP_ORG_EDITION)
	                     || ($this->_databaseType == GEOIP_DOMAIN_EDITION)
			    		 || ($this->_databaseType == GEOIP_ISP_EDITION)
			    		 || ($this->_databaseType == GEOIP_LOCATIONA_EDITION)
			    		 || ($this->_databaseType == GEOIP_ACCURACYRADIUS_EDITION)
			    		 || ($this->_databaseType == GEOIP_ASNUM_EDITION)
						)
				{
	            	$this->_databaseSegments = 0;
	            	$buf = fread($this->_filehandle,SEGMENT_RECORD_LENGTH);
	            	for ($j = 0;$j < SEGMENT_RECORD_LENGTH;$j++)
					{
	            		$this->_databaseSegments += (ord($buf[$j]) << ($j * 8));
	            	}
		    		if ($this->_databaseType == GEOIP_ORG_EDITION
						|| $this->_databaseType == GEOIP_DOMAIN_EDITION
						|| $this->_databaseType == GEOIP_ISP_EDITION
						)
					{
		    			$this->_recordLength = ORG_RECORD_LENGTH;
	            	}
	        	}
	        	break;
	        }
			else
			{
	        	fseek($this->_filehandle, -4, SEEK_CUR);
	        }
	    }
	    if (($this->_databaseType == GEOIP_COUNTRY_EDITION)
			|| ($this->_databaseType == GEOIP_PROXY_EDITION)
			|| ($this->_databaseType == GEOIP_NETSPEED_EDITION))
		{
			$this->_databaseSegments = GEOIP_COUNTRY_BEGIN;
	    }
	    fseek($this->_filehandle,$filepos,SEEK_SET);
	 }
	}
}
