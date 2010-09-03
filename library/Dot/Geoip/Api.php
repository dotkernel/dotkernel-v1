<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2010 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
 * Defined constants used for GeoIp database reading
 */
define("GEOIP_COUNTRY_BEGIN", 16776960);
define("GEOIP_STATE_BEGIN_REV0", 16700000);
define("GEOIP_STATE_BEGIN_REV1", 16000000);
define("GEOIP_STANDARD", 0);
define("GEOIP_MEMORY_CACHE", 1);
define("GEOIP_SHARED_MEMORY", 2);
define("STRUCTURE_INFO_MAX_SIZE", 20);
define("DATABASE_INFO_MAX_SIZE", 100);
define("GEOIP_COUNTRY_EDITION", 106);
define("GEOIP_PROXY_EDITION", 8);
define("GEOIP_ASNUM_EDITION", 9);
define("GEOIP_NETSPEED_EDITION", 10);
define("GEOIP_REGION_EDITION_REV0", 112);
define("GEOIP_REGION_EDITION_REV1", 3);
define("GEOIP_CITY_EDITION_REV0", 111);
define("GEOIP_CITY_EDITION_REV1", 2);
define("GEOIP_ORG_EDITION", 110);
define("GEOIP_ISP_EDITION", 4);
define("SEGMENT_RECORD_LENGTH", 3);
define("STANDARD_RECORD_LENGTH", 3);
define("ORG_RECORD_LENGTH", 4);
define("MAX_RECORD_LENGTH", 4);
define("MAX_ORG_RECORD_LENGTH", 300);
define("GEOIP_SHM_KEY", 0x4f415401);
define("US_OFFSET", 1);
define("CANADA_OFFSET", 677);
define("WORLD_OFFSET", 1353);
define("FIPS_RANGE", 360);
define("GEOIP_UNKNOWN_SPEED", 0);
define("GEOIP_DIALUP_SPEED", 1);
define("GEOIP_CABLEDSL_SPEED", 2);
define("GEOIP_CORPORATE_SPEED", 3);

/**
* Geo IP API - connect to GeoIP.dat database to return the country based on IP address
* @category   DotKernel
* @package    DotLibrary
* @subpackage DotGeoip
* @author     DotKernel Team <team@dotkernel.com>
*/
class Dot_Geoip_Api extends Dot_Geoip
{
	/**
	 * Flags country code
	 * @access private
	 * @var string
	 */
    private $flags;
	/**
	 * File handle identifier
	 * @access private
	 * @var resource 
	 */
	private $filehandle;
	/**
	 * Memmory buffered - the read string
	 * @access private
	 * @var string
	 */
    private $memoryBuffer;
	/**
	 * Database type
	 * @access private
	 * @var int
	 */
    private $databaseType;
	/**
	 * Database segments
	 * @access private
	 * @var int
	 */
    private $databaseSegments;	
	/**
	 * Record length
	 * @access private
	 * @var int
	 */
    private $recordLength;	
	/**
	 * ID returned by shmop_open() function to access the shared memory segment 
	 * @access private
	 * @var int
	 */
    private $shmid;	
	/**
	 * Array with country code to number corespondence
	 * @access private
	 * @var array
	 */
    private $GEOIP_COUNTRY_CODE_TO_NUMBER = array(
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
    private $GEOIP_COUNTRY_CODES = array(
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
    private $GEOIP_COUNTRY_CODES3 = array(
		"","AP","EU","AND","ARE","AFG","ATG","AIA","ALB","ARM","ANT","AGO","AQ","ARG",
		"ASM","AUT","AUS","ABW","AZE","BIH","BRB","BGD","BEL","BFA","BGR","BHR","BDI",
		"BEN","BMU","BRN","BOL","BRA","BHS","BTN","BV","BWA","BLR","BLZ","CAN","CC",
		"COD","CAF","COG","CHE","CIV","COK","CHL","CMR","CHN","COL","CRI","CUB","CPV",
		"CX","CYP","CZE","DEU","DJI","DNK","DMA","DOM","DZA","ECU","EST","EGY","ESH",
		"ERI","ESP","ETH","FIN","FJI","FLK","FSM","FRO","FRA","FX","GAB","GBR","GRD",
		"GEO","GUF","GHA","GIB","GRL","GMB","GIN","GLP","GNQ","GRC","GS","GTM","GUM",
		"GNB","GUY","HKG","HM","HND","HRV","HTI","HUN","IDN","IRL","ISR","IND","IO",
		"IRQ","IRN","ISL","ITA","JAM","JOR","JPN","KEN","KGZ","KHM","KIR","COM","KNA",
		"PRK","KOR","KWT","CYM","KAZ","LAO","LBN","LCA","LIE","LKA","LBR","LSO","LTU",
		"LUX","LVA","LBY","MAR","MCO","MDA","MDG","MHL","MKD","MLI","MMR","MNG","MAC",
		"MNP","MTQ","MRT","MSR","MLT","MUS","MDV","MWI","MEX","MYS","MOZ","NAM","NCL",
		"NER","NFK","NGA","NIC","NLD","NOR","NPL","NRU","NIU","NZL","OMN","PAN","PER",
		"PYF","PNG","PHL","PAK","POL","SPM","PCN","PRI","PSE","PRT","PLW","PRY","QAT",
		"REU","ROU","RUS","RWA","SAU","SLB","SYC","SDN","SWE","SGP","SHN","SVN","SJM",
		"SVK","SLE","SMR","SEN","SOM","SUR","STP","SLV","SYR","SWZ","TCA","TCD","TF",
		"TGO","THA","TJK","TKL","TLS","TKM","TUN","TON","TUR","TTO","TUV","TWN","TZA",
		"UKR","UGA","UM","USA","URY","UZB","VAT","VCT","VEN","VGB","VIR","VNM","VUT",
		"WLF","WSM","YEM","YT","SRB","ZAF","ZMB","MNE","ZWE","A1","A2","O1",
		"ALA","GGY","IMN","JEY","BLM","MAF");	
	/**
	 * Array with country names
	 * @access private
	 * @var array
	 */
    private $GEOIP_COUNTRY_NAMES = array(
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
    private $GEOIP_CONTINENT_CODES = array(
		"--", "AS", "EU", "EU", "AS", "AS", "SA", "SA", "EU", "AS",
		"SA", "AF", "AN", "SA", "OC", "EU", "OC", "SA", "AS", "EU",
		"SA", "AS", "EU", "AF", "EU", "AS", "AF", "AF", "SA", "AS",
		"SA", "SA", "SA", "AS", "AF", "AF", "EU", "SA", "NA", "AS",
		"AF", "AF", "AF", "EU", "AF", "OC", "SA", "AF", "AS", "SA",
		"SA", "SA", "AF", "AS", "AS", "EU", "EU", "AF", "EU", "SA",
		"SA", "AF", "SA", "EU", "AF", "AF", "AF", "EU", "AF", "EU",
		"OC", "SA", "OC", "EU", "EU", "EU", "AF", "EU", "SA", "AS",
		"SA", "AF", "EU", "SA", "AF", "AF", "SA", "AF", "EU", "SA",
		"SA", "OC", "AF", "SA", "AS", "AF", "SA", "EU", "SA", "EU",
		"AS", "EU", "AS", "AS", "AS", "AS", "AS", "EU", "EU", "SA",
		"AS", "AS", "AF", "AS", "AS", "OC", "AF", "SA", "AS", "AS",
		"AS", "SA", "AS", "AS", "AS", "SA", "EU", "AS", "AF", "AF",
		"EU", "EU", "EU", "AF", "AF", "EU", "EU", "AF", "OC", "EU",
		"AF", "AS", "AS", "AS", "OC", "SA", "AF", "SA", "EU", "AF",
		"AS", "AF", "NA", "AS", "AF", "AF", "OC", "AF", "OC", "AF",
		"SA", "EU", "EU", "AS", "OC", "OC", "OC", "AS", "SA", "SA",
		"OC", "OC", "AS", "AS", "EU", "SA", "OC", "SA", "AS", "EU",
		"OC", "SA", "AS", "AF", "EU", "AS", "AF", "AS", "OC", "AF",
		"AF", "EU", "AS", "AF", "EU", "EU", "EU", "AF", "EU", "AF",
		"AF", "SA", "AF", "SA", "AS", "AF", "SA", "AF", "AF", "AF",
		"AS", "AS", "OC", "AS", "AF", "OC", "AS", "EU", "SA", "OC",
		"AS", "AF", "EU", "AF", "OC", "NA", "SA", "AS", "EU", "SA",
		"SA", "SA", "SA", "AS", "OC", "OC", "OC", "AS", "AF", "EU",
		"AF", "AF", "EU", "AF", "--", "--", "--", "EU", "EU", "EU",
		"EU", "SA", "SA" );
	/**
	 * Constructor
	 * @access public
	 * @return Dot_Geoip_Api
	 */
	public function __construct()
	{	
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
		$country = array('unknown','unknown');
		$filePath = $path.'GeoIP.dat';
		$this->geoipOpen($filePath, GEOIP_STANDARD);
		$countryCode = $this->geoipCountryCodeByAddr($ip);
		$this->geoipClose();
		if($countryCode)
		{
			$countryIndex = array_search($countryCode, $this->GEOIP_COUNTRY_CODES);
			$country[0] = strtolower($countryCode);
			$country[1] = $this->GEOIP_COUNTRY_NAMES[$countryIndex];
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
	private function geoipOpen($filename, $flags) 
	{
	  $this->flags = $flags;
	  if ($this->flags & GEOIP_SHARED_MEMORY) 
	  {
		$this->shmid = @shmop_open (GEOIP_SHM_KEY, "a", 0, 0);
	  }
	  else 
	  {
	    $this->filehandle = fopen($filename,"rb") or die( "Can not open $filename\n" );
	    if ($this->flags & GEOIP_MEMORY_CACHE) 
		{
			$sArray = fstat($this->filehandle);
	        $this->memoryBuffer = fread($this->filehandle, $sArray['size']);
	    }
	  }	
	  $this->setupSegments();
	}
	/**
	 * Close opened file
	 * @access private
	 * @return bool
	 */
	private function geoipClose() 
	{
	  if ($this->flags & GEOIP_SHARED_MEMORY) 
	  {
	    return true;
	  }	
	  return fclose($this->filehandle);
	}
	/**
	 * Get country code based on IP address
	 * @access private
	 * @param string $addr
	 * @return string
	 */
	private function geoipCountryCodeByAddr($addr) 
	{
	  if ($this->databaseType == GEOIP_CITY_EDITION_REV1) 
	  {
	    $record = $this->geoip_record_by_addr($addr);
	    if ( $record !== false ) 
		{
	      return $record->country_code;
	    }
	  } 
	  else 
	  {
	    $countryId = $this->geoipCountryIdByAddr($addr);
	    if ($countryId !== false) 
		{
	      return $this->GEOIP_COUNTRY_CODES[$countryId];
	    }
	  }
	  return false;
	}
	/**
	 * Get country id based on IP address
	 * @access private 
	 * @param string $addr
	 * @return int
	 */
	private function geoipCountryIdByAddr($addr) 
	{
	  $ipNum = ip2long($addr);
	  return $this->geoipSeekCountry($ipNum) - GEOIP_COUNTRY_BEGIN;
	}
	/**
	 * Search the country based on IP address
	 * @access private
	 * @param int $ipnum
	 * @return int
	 */
	private function geoipSeekCountry($ipNum) 
	{
	  $offset = 0;
	  for ($depth = 31; $depth >= 0; --$depth) 
	  {
	    if ($this->flags & GEOIP_MEMORY_CACHE) 
		{
	      // workaround php's broken substr, strpos, etc handling with
	      // mbstring.func_overload and mbstring.internal_encoding
	      $enc = mb_internal_encoding();
	      mb_internal_encoding('ISO-8859-1');
	      $buf = substr($this->memoryBuffer,
                        2 * $this->recordLength * $offset,
                        2 * $this->recordLength);
	      mb_internal_encoding($enc);
	    } 
		elseif ($this->flags & GEOIP_SHARED_MEMORY) 
		{
	      $buf = @shmop_read($this->shmid, 
                             2 * $this->recordLength * $offset,
                             2 * $this->recordLength );
	    } 
		else 
		{
	      fseek($this->filehandle, 2 * $this->recordLength * $offset, SEEK_SET) == 0 or die("fseek failed");
	      $buf = fread($this->filehandle, 2 * $this->recordLength);
	    }
	    $x = array(0,0);
	    for ($i = 0; $i < 2; ++$i) 
		{
	      for ($j = 0; $j < $this->recordLength; ++$j) 
		  {
	        $x[$i] += ord($buf[$this->recordLength * $i + $j]) << ($j * 8);
	      }
	    }
	    if ($ipNum & (1 << $depth)) 
		{
	      if ($x[1] >= $this->databaseSegments) 
		  {
	        return $x[1];
	      }
	      $offset = $x[1];
	    } 
		else 
		{
	      if ($x[0] >= $this->databaseSegments) 
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
	 * Setup segments from GeoIp
	 * @access private
	 * @return void
	 */	
	private function setupSegments()
	{
	  $this->databaseType = GEOIP_COUNTRY_EDITION;
	  $this->recordLength = STANDARD_RECORD_LENGTH;
	  if ($this->flags & GEOIP_SHARED_MEMORY) 
	  {
	    $offset = @shmop_size ($this->shmid) - 3;
	    for ($i = 0; $i < STRUCTURE_INFO_MAX_SIZE; $i++) 
		{
	        $delim = @shmop_read ($this->shmid, $offset, 3);
	        $offset += 3;
	        if ($delim == (chr(255).chr(255).chr(255))) 
			{
	            $this->databaseType = ord(@shmop_read ($this->shmid, $offset, 1));
	            $offset++;
	            if ($this->databaseType == GEOIP_REGION_EDITION_REV0)
				{
	                $this->databaseSegments = GEOIP_STATE_BEGIN_REV0;
	            } 
				else if ($this->databaseType == GEOIP_REGION_EDITION_REV1)
				{
	                $this->databaseSegments = GEOIP_STATE_BEGIN_REV1;
		    	} 
				else if (($this->databaseType == GEOIP_CITY_EDITION_REV0)
						 || ($this->databaseType == GEOIP_CITY_EDITION_REV1) 
	                     || ($this->databaseType == GEOIP_ORG_EDITION)
			    		 || ($this->databaseType == GEOIP_ISP_EDITION)
			    		 || ($this->databaseType == GEOIP_ASNUM_EDITION)
						)
				{
	                $this->databaseSegments = 0;
	                $buf = @shmop_read ($this->shmid, $offset, SEGMENT_RECORD_LENGTH);
	                for ($j = 0;$j < SEGMENT_RECORD_LENGTH;$j++)
					{
	                    $this->databaseSegments += (ord($buf[$j]) << ($j * 8));
	                }
		            if (($this->databaseType == GEOIP_ORG_EDITION) 
						|| ($this->databaseType == GEOIP_ISP_EDITION)
					   ) 
					{
		                $this->recordLength = ORG_RECORD_LENGTH;
	                }
	            }
	            break;
	        }
			else 
			{
	            $offset -= 4;
	        }
	    }
	    if (($this->databaseType == GEOIP_COUNTRY_EDITION)
			|| ($this->databaseType == GEOIP_PROXY_EDITION)
			|| ($this->databaseType == GEOIP_NETSPEED_EDITION)
		   )
		{
			$this->databaseSegments = GEOIP_COUNTRY_BEGIN;
		}
	}
	else 
	{
	    $filepos = ftell($this->filehandle);
	    fseek($this->filehandle, -3, SEEK_END);
	    for ($i = 0; $i < STRUCTURE_INFO_MAX_SIZE; $i++) 
		{
	        $delim = fread($this->filehandle,3);
	        if ($delim == (chr(255).chr(255).chr(255)))
			{
	        	$this->databaseType = ord(fread($this->filehandle,1));
				if ($this->databaseType == GEOIP_REGION_EDITION_REV0)
				{
	            	$this->databaseSegments = GEOIP_STATE_BEGIN_REV0;
	        	}
	        	else if ($this->databaseType == GEOIP_REGION_EDITION_REV1)
				{
		    		$this->databaseSegments = GEOIP_STATE_BEGIN_REV1;
	            }  
				else if (($this->databaseType == GEOIP_CITY_EDITION_REV0)
						 || ($this->databaseType == GEOIP_CITY_EDITION_REV1)
						 || ($this->databaseType == GEOIP_ORG_EDITION) 
						 || ($this->databaseType == GEOIP_ISP_EDITION) 
						 || ($this->databaseType == GEOIP_ASNUM_EDITION))
				{
	            	$this->databaseSegments = 0;
	            	$buf = fread($this->filehandle,SEGMENT_RECORD_LENGTH);
	            	for ($j = 0;$j < SEGMENT_RECORD_LENGTH;$j++)
					{
	            		$this->databaseSegments += (ord($buf[$j]) << ($j * 8));
	            	}
		    		if ($this->databaseType == GEOIP_ORG_EDITION || $this->databaseType == GEOIP_ISP_EDITION) 
					{
		    			$this->recordLength = ORG_RECORD_LENGTH;
	            	}
	        	}
	        	break;
	        }
			else 
			{
	        	fseek($this->filehandle, -4, SEEK_CUR);
	        }
	    }
	    if (($this->databaseType == GEOIP_COUNTRY_EDITION)
			|| ($this->databaseType == GEOIP_PROXY_EDITION)
			|| ($this->databaseType == GEOIP_NETSPEED_EDITION))
		{
			$this->databaseSegments = GEOIP_COUNTRY_BEGIN;
	    }
	    fseek($this->filehandle,$filepos,SEEK_SET);
	 }
	}
}
