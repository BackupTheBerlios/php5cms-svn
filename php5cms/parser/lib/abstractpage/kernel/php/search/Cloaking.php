<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'util.array.ArrayUtil' );


define( 'CLOAKING_MATCH_TYPE_NONE',       "none"       );
define( 'CLOAKING_MATCH_TYPE_EXACT',      "exact"      );
define( 'CLOAKING_MATCH_TYPE_ASSUMED',    "assumed"    );

define( 'CLOAKING_MATCH_SOURCE_NONE',     "none"       );
define( 'CLOAKING_MATCH_SOURCE_AGENT',    "http_agent" );
define( 'CLOAKING_MATCH_SOURCE_IP',       "ip"         );
define( 'CLOAKING_MATCH_SOURCE_HOST',     "host"       );
define( 'CLOAKING_MATCH_SOURCE_FRAGMENT', "fragment"   );

define( 'CLOAKING_LEVEL_NONE',                       0 );
define( 'CLOAKING_LEVEL_RANDOM_META_KEYWORDS',       1 );
define( 'CLOAKING_LEVEL_RANDOM_META_DESCRIPTION',    2 );
define( 'CLOAKING_LEVEL_TITLE_SUFFIX',               4 );
define( 'CLOAKING_LEVEL_HIDDEN_PARAGRAPHS',          8 );
define( 'CLOAKING_LEVEL_HIDDEN_PAGELINKS',          16 );
define( 'CLOAKING_LEVEL_ALT_TAGS',                  32 );

define( 'CLOAKING_LEVEL_FULL', ( 
	  CLOAKING_LEVEL_RANDOM_META_KEYWORDS 
	| CLOAKING_LEVEL_RANDOM_META_DESCRIPTION 
	| CLOAKING_LEVEL_TITLE_SUFFIX 
	| CLOAKING_LEVEL_HIDDEN_PARAGRAPHS 
	| CLOAKING_LEVEL_HIDDEN_PAGELINKS 
	| CLOAKING_LEVEL_ALT_TAGS 
) );


/**
 * Cloaking Class
 *
 * Usage:
 *
 * $cl = new Cloaking();
 * $res = $cl->check();
 *
 * if ( $res == true )
 *		echo "Hello crawler."
 *
 * Note:
 *
 * ---------------------------------
 * | Search engine | Database      |
 * |---------------|---------------|
 * | Abacho        | Abacho        |
 * | Abadoor       | Inktomi       |
 * | Acoon         | Inktomi       |
 * | Alexa         | Google        |
 * | Alltheweb     | Fast          |
 * | Altavista     | Altavista     |
 * | AOL           | Google        |
 * | Ask Jeeves    | Ask Jeeves    |
 * | Evreka        | Fast          |
 * | Excite        | Fast          |
 * | Fireball      | Fireball      |
 * | Google        | Google        |
 * | Hotbot        | Inktomi       |
 * | Lycos         | Fast          |
 * | MSN           | Inktomi       |
 * | T-Online      | Fast          |
 * | Web.de        | Google        |
 * | Yahoo!        | Google        |
 * ---------------------------------
 *
 * Add your url to Abacho, Hotbot, Google,
 * Lycos, Firball and Altavista. That would fit 99%!
 *
 * See http://www.php-tools.de/site.php?file=patSpiderizer/documentation.xml
 * for some useful hints on search engine optimization
 *
 * @package search
 */

class Cloaking extends PEAR
{
	/**
	 * @access private
	 */
	var $_search_engines = array(
		array(
			"full_name" => "Abacho",
			"description" => "Abacho is a german index that has gotten bigger lately through the ovetake of several other, smaller german search indexes. It is fast, and also offer navigation by categories. If your webpage is german-speaking, you should submit your site here too.",
			"homepage" => "http://www.abacho.de/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
			),
			"fragments" => array( 
				"AbachoBOT"
			),
			"ip" => array(
				"193.110.40.87"
			),
			"hosts" => array(
				"*.tricus.com"
			)
		),
		array(
			"full_name" => "Above.net - IP 3000",
			"description" => "Above.net is no search engine, ist is just a spider that indexes webpages and offers its index to other companies.",
			"homepage" => "http://www.above.net/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
			),
			"fragments" => array(
				"ip3000",
				"Above"
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "Altavista",
			"description" => "Altavista was the leading search engine before Google came in - its database is quite extensive, and its spiders are still active. Many users are still faithful to this search engine, so submitting your url to it is definitely no bad idea. Scooter is AltaVista's prime index agent.",
			"homepage" => "http://www.altavista.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"Mercator-1.0",
				"Mercator-1.1",
				"Mercator-1.2",
				"Mercator-2.0",
				"Mercator-Scrub-1.1",
				"Mercator-v1.2jg",
				"Scooter/1.0",
				"Scooter/1.0 scooter@pa.dec.com",
				"Scooter-1.0",
				"Scooter/1.1 (custom)",
				"Scooter/2.0 G.R.A.B. X2.0",
				"Scooter/2.0 G.R.A.B. V1.1.0",
				"Scooter-3.0.3_JT",
				"Scooter-3.0.au",
				"scooter-3.0.DY",
				"Scooter-3.0.EU",
				"Scooter-3.0.FS",
				"Scooter-3.0.gmk",
				"Scooter-3.0.gmkd*",
				"Scooter-3.0.jgsaint",
				"scooter-3.0.vns",
				"Scooter-3.0.VNS",
				"Scooter-3.0QI",
				"Scooter-3.2",
				"Scooter-3.2.DIL",
				"Scooter-3.2.EX",
				"Scooter-3.2.JT",
				"Scooter-3.2.NIV",
				"Scooter-3.2.QA",
				"Scooter-3.2.snippet",
				"Scooter-3.3dev",
				"Scooter-fgrab",
				"Scooter-Jellyfish1",
				"Scooter-tv34_Mc_kalyan_10",
				"scooter-v1.2ih",
				"scooter-venus-3.0.vns",
				"Scooter-W3-1.0",
				"Scooter-W3.1.2",
				"Scooter/3.2.SF0",
				"Scooter/3.3",
				"Scooter/3.3.QA",
				"Scooter/3.3.QA.pczukor",
				"Scooter/3.3.vscooter",
				"Scooter_trk15-3.0.3"
			),
			"fragments" => array( 
				"AltaVista", 
				"Alta Vista",
				"scooter",
				"g.r.a.b.",
				"mercator",
				"jellyfish"
			),
			"ip" => array(
				"204.213.9.*",
				"204.123.28.*",
				"212.187.226.*",
				"212.187.213.175",
				"209.73.164.*",
				"209.73.162.44",
				"64.152.75.*",
				"216.39.50.*",
				"204.123.9.*",
				"204.74.103.39",
				"204.123.2.67"
			),
			"hosts" => array(
				"*.webresearch.pa-x.dec.com",
				"*.aveurope.co.uk",
				"*.pa-x.dec.com",
				"*.sv.av.com",
				"*.altavista.digital.com",
				"*.av.pa-x.dec.com"
			)
		),
		array(
			"full_name" => "Ask Jeeves",
			"description" => "Ask Jeeves / Teoma spider",
			"homepage" => "http://www.ask.co.uk/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"Mozilla/2.0 (compatible; Ask Jeeves/Teoma)"
			),
			"fragments" => array(
				"jeeves"
			),
			"ip" => array(
			),
			"hosts" => array(
				"*.directhit.com"
			)
		),
		array(
			"full_name" => "Brain Bot",
			"description" => "",
			"homepage" => "",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"gigabaz/3.14",
				"gigabaz23.00_pia baz@gigabaz.com",
				"gigaBazV11.3"
			),
			"fragments" => array(
				"gigabaz"
			),
			"ip" => array(
				"151.189.96.99"
			),
			"hosts" => array(
				"*.brainbot.com"
			)
		),
		array(
			"full_name" => "Crawler.de",
			"description" => "Crawler.de has been replaced by Abacho.de, a german search engine and website index. However, the spider is still active and indexs pages.",
			"homepage" => "http://www.crawler.de/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
			),
			"fragments" => array(
				"crawler.de"
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "Digital Integrity",
			"description" => "Digital integrity is no search engine, it is just a spider that indexes webpages and offers its index to other companies.",
			"homepage" => "http://www.digital-integrity.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"DIIbot/1.2",
				"Digital Integrity"
			),
			"fragments" => array(
				"DIIbot",
				"findsame.com"
			),
			"ip" => array(
			),
			"hosts" => array(
				"*.digital-integrity.com"
			)
		),
		array(
			"full_name" => "Esis smart spider",
			"description" => "The Esis smart spider is no search engine, it is just a spider that indexes webpages and offers its index to other companies.",
			"homepage" => "",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
			),
			"fragments" => array(
				"esis"
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "Excite",
			"description" => "Its purpose is to generate a Resource Discovery database, and to generate statistics. The ArchitextSpider collects information for the Excite and WebCrawler search engines.",
			"homepage" => "http://www.excite.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"ArchitextSpider"
			),
			"fragments" => array(
				"architext",
				"libwww",
				"architextspider"
			),
			"ip" => array(
				"198.3.103.*",
				"199.172.149.*",
				"198.3.97.17",
				"204.62.245.*"
			),
			"hosts" => array(
				"*.excite.com",
				"*.atext.com"
			)
		),
		array(
			"full_name" => "Fast.no",
			"description" => "",
			"homepage" => "http://www.fast.no/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"FAST-WebCrawler/2.2-pre20 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2-pre27 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2-pre34 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2-pre41 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2-pre44 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2-pre45 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2.4 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2.5 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2.6 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2.7 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2.8 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2.10 (Multimedia Search) (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/2.2.11 (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)",
				"FAST-WebCrawler/3.3",
				"FAST-WebCrawler/3.3 (crawler@fast.no; http://fast.no/support.php?c=faqs/crawler)",
				"libwww-perl/5.52 FP/2.1",
				"libwww-perl/5.52 FP/4.0",
				"libwww-perl/5.53 FP/2.1",
				"Mozilla/4.0 (compatible; FastCrawler3, support-fastcrawler3@fast.no)"
			),
			"fragments" => array( 
				"FAST-WebCrawler",
				"fast.no",
				"FastCrawler"
			),
			"ip" => array( 
				"209.202.148.*",
				"66.77.74.*",
				"66.77.73.*",
				"209.202.148.250"
			),
			"hosts" => array(
				"*.bos2.fastsearch.net",
				"*.bos2.fast-search.net",
				"*.sac2.fastsearch.net",
				"*.sac2.fast-search.net"
			)
		),
		array(
			"full_name" => "KIT-Fireball",
			"description" => "The Fireball robots gather web documents in German language for the database of the Fireball search service. The robot was developed by Benhui Chen in a research project at the Technical University of Berlin in 1996 and was re-implemented by its developer in 1997 for the present owner.",
			"homepage" => "http://www.fireball.de/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"Firefly/1.0",
				"KIT-Fireball/2.0",
				"KIT_Fireball/2.0",
				"Firefly/1.0 (compatible; Mozilla 4.0; MSIE 5.5)",
				"KIT-Fireball/2.0 libwww/5.0a"
			),
			"fragments" => array( 
				"Fireball",
				"Firefly"
			),
			"ip" => array(
				"193.7.255.*",
				"193.189.227.*"
			),
			"hosts" => array(
				"*.fireball.de"
			)
		),
		array(
			"full_name" => "GigaBot",
			"description" => "Gigabot is the name of Gigablast's indexing agent, also known as a spider.",
			"homepage" => "http://www.gigablast.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array( 
				"Gigabot/1.0" 
			),
			"fragments" => array( 
				"Gigabot" 
			),
			"ip" => array( 
				"63.236.66.119",
				"216.243.113.1"
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "Google",
			"description" => "Google has become the most widely used search engine in just about a year. It is fast, offers good results, and charmed its visitors through its apparent simpleness.",
			"homepage" => "http://www.google.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"Googlebot/1.0 (googlebot@googlebot.com http://googlebot.com/)",
				"Googlebot/1.0",
				"Googlebot/2.0",
				"Googlebot/2.1",
				"Googlebot/2.1d"
			),
			"fragments" => array( 
				"Google",
				"Googlebot"
			),
			"ip" => array(
				"64.208.37.28",
				"209.185.253.*"
			),
			"hosts" => array(
				"*.googlebot.com",
				"*.stanford.edu"
			)
		),
		array(
			"full_name" => "Infoseek",
			"description" => "Its purpose is to generate a Resource Discovery database. Collects WWW pages for both InfoSeek's free WWW search and commercial search. Uses a unique proprietary algorithm to identify the most popular and interesting WWW pages. Very fast, but never has more than one request per site outstanding at any given time. Has been refined for more than a year. Infoseek as some of you may still know it has been gulped by go.com, but its spider is still active. Marvin replaced Sidewinder.",
			"homepage" => "http://www.infoseek.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"InfoSeek Robot 1.0",
				"Ultraseek",
				"Mozilla/3.01 (Win95; I)",
				"Infoseek Sidewinder",
				"Infoseek Sidewinder/0.9",
				"Marvin",
				"marvin/infoseek (marvin-team@webseek.de)",
				"Mozilla/3.01 (Win95; I) backdraft-bbn.infoseek.com",
				"marvin/infoseek (marvin-team@webseek.de)"
			),
			"fragments" => array(
				"Ultraseek",
				"Infoseek",
				"Sidewinder",
				"Marvin"
			),
			"ip" => array(
				"195.145.119.*",
				"204.162.96.*",
				"204.162.98.*",
				"205.226.201.*"
			),
			"hosts" => array(
				"*.sda.t-online.de",
				"*.infoseek.com"
			)
		),
		array(
			"full_name" => "Inktomi",
			"description" => "Indexing documents for the HotBot search engine (www.hotbot.com), collecting Web statistics. Inktomi is a spider, not a search engine. It sells its index to other companies. It also is a major pain in the ass - its behavior changes very often, and the frequency with which it comes to have a look at your pages is outright annoying. It also has some features like the multiple visit - several spiders at once that index your page.",
			"homepage" => "http://www.inktomi.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"Slurp/2.0",
				"Mozilla/3.0 (Slurp/si; slurp@inktomi.com; http://www.inktomi.com/slurp.html)",
				"Slurp/2.0-Adhoc (slurp@inktomi.com; http://www.inktomi.com/slurp.html)",
				"Slurp/2.0-Redtail (slurp@inktomi.com; http://www.inktomi.com/slurp.html)",
				"Slurp/si-emb (slurp@inktomi.com; http://www.inktomi.com/slurp.html)",
				"Slurp/2.0 (slurp@inktomi.com; http://www.inktomi.com/slurp.html)",
				"Slurp.so/1.0 (slurp@inktomi.com; http://www.inktomi.com/slurp.html)"
			),
			"fragments" => array( 
				"Inktomi", 
				"Slurp",
				"greatwhitecrawl",
				"Redtail",
				"Adhoc"
			),
			"ip" => array(
				"213.216.143.37",
				"66.196.73.39",
				"209.1.12.*",
				"216.32.237.*"
			),
			"hosts" => array(
				"*.inktomi.com",
				"*.inktomisearch.com"
			)
		),
		array(
			"full_name" => "Lycos",
			"description" => "Lycos is quite a big search engine, if not very widely known. The advantage of Lycos is that they have some page sin the index that are quite hard to find, and with the partner search engine HotBot you've got two very powerful search tools.",
			"homepage" => "http://www.lycos.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"", // yes, an empty string
				"Lycos_Spider_(T-Rex)/3.0"
			),
			"fragments" => array( 
				"Lycos",
				"T-Rex"
			),
			"ip" => array(
				"207.77.91.184",
				"207.77.90.*"
			),
			"hosts" => array(
				"*.bos.lycos.com",
				"*.lycos.com",
				"*.pgh.lycos.com",
				"*.srv.pgh.lycos.com",
				"*.sjc.lycos.com",
				"fuzine.mt.cs.cmu.edu",
				"*.lycos.com"
			)
		),
		array(
			"full_name" => "MaxBot",
			"description" => "Maxbot is not a real search engine, it is just a spider that indexes your webpages and sells its index to other companies.",
			"homepage" => "http://www.maxbot.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
			),
			"fragments" => array(
				"maxbot"
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "MSN",
			"description" => "MSN Search Crawler",
			"homepage" => "http://search.msn.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"MSNBOT/0.1 (http://search.msn.com/msnbot.htm)"
			),
			"fragments" => array( 
				"MSNBOT" 
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "Northern Light",
			"description" => "Gulliver is a robot to be used to collect web pages for indexing and subsequent searching of the index.",
			"homepage" => "http://www.northernlight.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"Gulliver/1.1",
				"Gulliver/1.2",
				"Gulliver/1.3"
			),
			"fragments" => array(
				"Gulliver"
			),
			"ip" => array(
				"216.34.109.190",
				"208.219.77.9"
			),
			"hosts" => array(
				"*.northernlight.com"
			)
		),
		array(
			"full_name" => "Netscape Compass",
			"description" => "",
			"homepage" => "http://home.netscape.com/compass/v3.0/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
			),
			"fragments" => array(
				"compass"
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "Wise Nutbot",
			"description" => "",
			"homepage" => "http://www.wisenutbot.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
			),
			"fragments" => array(
				"nutbot"
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "Openfind",
			"description" => "",
			"homepage" => "http://www.openfind.com.tw/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"Openbot/3.0+(robot-response@openfind.com.tw;+http://www.openfind.com.tw/robot.html)",
				"Openfind data gatherer, Openbot/3.0+(robot-response@openfind.com.tw;+http://www.openfind.com.tw/robot.html)"
			),
			"fragments" => array( 
				"Openbot",
				"Openfind"
			),
			"ip" => array(
				"66.237.60.21",
				"66.7.131.132"
			),
			"hosts" => array(
				"*.openfind.com"
			)
		),
		array(
			"full_name" => "Speed Find",
			"description" => "",
			"homepage" => "http://www.speedfind.de/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"speedfind ramBot xtreme 8.1"
			),
			"fragments" => array(
				"Speedfind",
				"Rambot"
			),
			"ip" => array(
			),
			"hosts" => array(
				"*.speedfind.de"
			)
		),
		array(
			"full_name" => "Suchnase",
			"description" => "",
			"homepage" => "http://www.suchnase.de/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
			),
			"fragments" => array(
				"Suchnase"
			),
			"ip" => array(
				"80.237.200.189"
			),
			"hosts" => array(
				"ds80-237-200-189.dedicated.hosteurope.de"
			)
		),
		array(
			"full_name" => "SureSeeker",
			"description" => "",
			"homepage" => "http://www.sureseeker.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array( 
				"Mozilla/4.0 (compatible; MSIE 5.0; Windows 98; DigExt; searchengine2000.com; sureseeker.com)" 
			),
			"fragments" => array(
				"Sureseeker"
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "Teoma",
			"description" => "",
			"homepage" => "http://www.teoma.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"teomaagent [crawler-admin@teoma.com]",
				"teomaagent1 [crawler-admin@teoma.com]",
				"teoma_agent3 [crawler-admin@teoma.com]"
			),
			"fragments" => array(
				"teomaagent",
				"teoma_agent",
				"Teoma"
			),
			"ip" => array(
				"63.236.92.151",
				"65.192.195.15"
			),
			"hosts" => array(
				"*.teo.ewr.qwest.net"
			)
		),
		array(
			"full_name" => "Thunderstone",
			"description" => "",
			"homepage" => "http://www.thunderstone.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"Mozilla/2.0 (compatible; T-H-U-N-D-E-R-S-T-O-N-E)"
			),
			"fragments" => array(
				"T-H-U-N-D-E-R-S-T-O-N-E"
			),
			"ip" => array(
				"198.49.220.62"
			),
			"hosts" => array(
				"*.thunderstone.com"
			)
		),
		array(
			"full_name" => "UDM Search",
			"description" => "UdmSearch is a free web search engine software for intranet/small domain internet servers.",
			"homepage" => "http://mysearch.udm.net/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"UdmSearch",
				"UdmSearch/2.1.1"
			),
			"fragments" => array(
				"udmsearch",
				"udm search"
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "Web Top",
			"description" => "",
			"homepage" => "http://www.webtop.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
			),
			"fragments" => array(
				"muscat",
				"webtop"
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		),
		array(
			"full_name" => "WiseWire",
			"description" => "",
			"homepage" => "http://www.wisewire.com/",
			"cloaking_level" => CLOAKING_LEVEL_FULL,
			"agents" => array(
				"WiseWire",
				"WiseWire-Alpha-1.0",
				"WiseWire-Alpha-Spider",
				"WiseWire-Alpha12-Spider971219a",
				"WiseWire-Alpha12-Spider(971223a)",
				"WiseWire-HotSpider-1.0",
				"WiseWire-Spider",
				"WiseWire-Spider-1.0",
				"WiseWire-Spider2",
				"WiseWire-Widow-1.0",
				"WiseWire-Widow-1.0r",
				"WiseWire-Widow-1.0-ALPHA12"
			),
			"fragments" => array(
				"WiseWire",
				"Widow"
			),
			"ip" => array(
			),
			"hosts" => array(
			)
		)
	);

	var $_agent   = "";
	var $_ip      = "";
	var $_host    = "";

	var $_options = array();
	
	var $_match_type;
	var $_match_source;
	var $_current;

	
	/**
	 * Constructor
	 *
	 * Possible values for options:
	 * $options["mail_on_visit"] = true;
	 * $options["mail_to"]       = "mnix@docuverse.de";
	 *
	 * @access public
	 */
	function Cloaking( $options = array() )
	{
		if ( is_array( $options ) )
			$this->_options = $options;
			
		$this->_agent = $_SERVER['HTTP_USER_AGENT'];
		
		if ( getenv( 'HTTP_CLIENT_IP' ) )
        	$this->_ip = getenv( 'HTTP_CLIENT_IP' );
        else
        	$this->_ip = $_SERVER["REMOTE_ADDR"];

		// if result is an ip we're not interested
		$host = gethostbyaddr( $this->_ip );
		$this->_host = ( !preg_match( "/^([01]?\d\d?|2[0-4]\d|25[0-4])\.([01]?\d\d?|2[0-4]\d|25[0-4])\.([01]?\d\d?|2[0-4]\d|25[0-4])\.([01]?\d\d?|2[0-4]\d|25[0-4])$/", $host ) )? $host : null;
	}
	
	
	/**
	 * @access public
	 */
	function check()
	{
		$this->reset();
		
		$this->_agent = $_SERVER['HTTP_USER_AGENT'];
		
		if ( getenv( 'HTTP_CLIENT_IP' ) )
        	$this->_ip = getenv( 'HTTP_CLIENT_IP' );
        else
        	$this->_ip = $_SERVER["REMOTE_ADDR"];
			
		$this->_host = @gethostbyaddr( $this->_ip );
		
		$result = $this->_runCheck();
		
		if ( $result == true )
		{
			if ( $this->_options["mail_on_visit"] && $this->_options["mail_to"] )
				mail( $this->_options["mail_to"], "Search engine visit notification", $this->infoString( "\n" ) );
		}
		else
		{
			;
		}
		
		return $result;
	}
	
	/**
	 * @access public
	 */
	function isSearchEngine()
	{
		return ( $this->_current )? true : false;
	}
	
	/**
	 * @access public
	 */
	function getEngineName()
	{
		return $this->_current["full_name"]? $this->_current["full_name"] : "[No Name given]";
	}
	
	/**
	 * @access public
	 */
	function getEngineDescription()
	{
		return $this->_current["description"]? $this->_current["description"] : "[No Description given]";
	}
	
	/**
	 * @access public
	 */
	function getEngineHomepage()
	{
		return $this->_current["homepage"]? $this->_current["homepage"] : "[No Homepage given]";
	}
	
	/**
	 * @access public
	 */
	function getCloakingLevel()
	{
		return $this->_current["cloaking_level"]? $this->_current["cloaking_level"] : CLOAKING_LEVEL_NONE;
	}
	
	/**
	 * @access public
	 */
	function getMatchType()
	{
		return $this->_match_type? $this->_match_type : CLOAKING_MATCH_TYPE_NONE;
	}
	
	/**
	 * @access public
	 */
	function getMatchSource()
	{
		return $this->_match_source? $this->_match_source : CLOAKING_MATCH_SOURCE_NONE;
	}
	
	/**
	 * @access public
	 */
	function infoString( $linebreak = "<br>\n" )
	{
		if ( $this->isSearchEngine() )
		{
			$str = sprintf( "%s has come to visit you!" . $linebreak . $linebreak
				. "Description: %s"     . $linebreak
				. "Homepage: %s"        . $linebreak
				. "Cloaking Level: %s"  . $linebreak
				. "Match Type: %s"      . $linebreak
				. "Match Source: %s"    . $linebreak
				. "User agent: %s"      . $linebreak
				. "IP Adress: %s"       . $linebreak
				. "Host: %s"            . $linebreak
				. "Date: %s",
				$this->getEngineName(),
				$this->getEngineDescription(),
				$this->getEngineHomepage(),
				$this->getCloakingLevel(),
				$this->getMatchType(),
				$this->getMatchSource(),
				$this->_agent,
				$this->_ip,
				$this->_host,
				date( "M d Y H:i:s" )
			);
		}
		else
		{
			$str = sprintf( "Agent seems not be a spider." . $linebreak . $linebreak
				. "Match Type: %s"      . $linebreak
				. "Match Source: %s"    . $linebreak
				. "User agent: %s"      . $linebreak
				. "IP Adress: %s"       . $linebreak
				. "Host: %s"            . $linebreak
				. "Date: %s",
				$this->getMatchType(),
				$this->getMatchSource(),
				$this->_agent,
				$this->_ip,
				$this->_host,
				date( "M d Y H:i:s" )
			);
		}
		
		return $str;
	}
	
	/**
	 * @access public
	 */
	function reset()
	{
		$this->_agent = null;
		$this->_ip    = null;
		$this->_host  = null;
		
		$this->_current      = null;
		$this->_match_type   = CLOAKING_MATCH_TYPE_NONE;
		$this->_match_source = CLOAKING_MATCH_SOURCE_NONE;
		
		return true;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _runCheck()
	{
		// #1: check user agent
		if ( !empty( $this->_agent ) )
		{
			for ( $i = 0; $i < count( $this->_search_engines ); $i++ )
			{
				for ( $j = 0; $j < count( $this->_search_engines[$i]["agents"] ); $j++ )
				{
					$agent = $this->_search_engines[$i]["agents"][$j];
					
					// strlen: Lycos may come along with an empty string
					if ( ( strlen( $agent ) > 1 ) && ( stristr( $this->_agent, $agent ) ) )
					{
						$this->_current      = $this->_search_engines[$i];
						$this->_match_type   = CLOAKING_MATCH_TYPE_EXACT;
						$this->_match_source = CLOAKING_MATCH_SOURCE_AGENT;
						
						return true;
					}
				}
			}
		}
		
		// #2: check ip adress
		if ( !empty( $this->_ip ) )
		{
			for ( $i = 0; $i < count( $this->_search_engines ); $i++ )
			{
				for ( $j = 0; $j < count( $this->_search_engines[$i]["ip"] ); $j++ )
				{
					$ip = $this->_search_engines[$i]["ip"][$j];
					
					if ( strpos( $ip, "*" ) === false )
					{
						if ( stristr( $this->_ip, $ip ) )
						{
							$this->_current      = $this->_search_engines[$i];
							$this->_match_type   = CLOAKING_MATCH_TYPE_EXACT;
							$this->_match_source = CLOAKING_MATCH_SOURCE_IP;
						
							return true;
						}
					}
					// handle wildcards
					else
					{
						$arr_ip_dataset = preg_split( "/[.]+/", $ip );
						$arr_ip_visitor = preg_split( "/[.]+/", $this->_ip );
						
						$comp = ArrayUtil::arrayCompare( $arr_ip_dataset, $arr_ip_visitor );
						
						// exactly one item is different, so...
						if ( ( count( $comp[0] ) == 1 ) && ( count( $comp[1] ) == 1 ) )
						{
							$this->_current      = $this->_search_engines[$i];
							$this->_match_type   = CLOAKING_MATCH_TYPE_ASSUMED;
							$this->_match_source = CLOAKING_MATCH_SOURCE_IP;
						
							// maybe we can get a better result - proceed
							break 2;
						}
					}
				}
			}
		}
		
		// #3: check host
		if ( !empty( $this->_host ) )
		{
			for ( $i = 0; $i < count( $this->_search_engines ); $i++ )
			{
				for ( $j = 0; $j < count( $this->_search_engines[$i]["hosts"] ); $j++ )
				{
					$host = $this->_search_engines[$i]["hosts"][$j];
					
					if ( strpos( $host, "*" ) === false )
					{
						if ( stristr( $this->_host, $host ) )
						{
							$this->_current      = $this->_search_engines[$i];
							$this->_match_type   = CLOAKING_MATCH_TYPE_EXACT;
							$this->_match_source = CLOAKING_MATCH_SOURCE_HOST;

							return true;
						}
					}
					// handle wildcards
					else
					{
						$arr_host_dataset = preg_split( "/[.]+/", $host );
						$arr_host_visitor = preg_split( "/[.]+/", $this->_host );
						
						$comp = ArrayUtil::arrayCompare( $arr_host_dataset, $arr_host_visitor );
						
						// exactly one item is different, so...
						if ( ( count( $comp[0] ) == 1 ) && ( count( $comp[1] ) == 1 ) )
						{
							$this->_current      = $this->_search_engines[$i];
							$this->_match_type   = CLOAKING_MATCH_TYPE_EXACT; // wildcarded hosts are regarded as exact matches
							$this->_match_source = CLOAKING_MATCH_SOURCE_HOST;

							// we cannot get a better result than 'assumed' if we proceed - so skip procedure
							return true;
						}
					}
				}
			}
		}
		
		// #4: check fragments against agent and host
		if ( !empty( $this->_agent ) )
		{
			for ( $i = 0; $i < count( $this->_search_engines ); $i++ )
			{
				for ( $j = 0; $j < count( $this->_search_engines[$i]["fragments"] ); $j++ )
				{
					$fragment = $this->_search_engines[$i]["fragments"][$j];
					
					if ( eregi( $fragment, $this->_agent ) || eregi( $fragment, $this->_host ) )
					{
						$this->_current      = $this->_search_engines[$i];
						$this->_match_type   = CLOAKING_MATCH_TYPE_ASSUMED;
						$this->_match_source = CLOAKING_MATCH_SOURCE_FRAGMENT;

						return true;
					}
				}
			}
		}
		
		return ( $this->_match_type == CLOAKING_MATCH_TYPE_ASSUMED )? true : false;
	}
} // END OF Cloaking

?>
