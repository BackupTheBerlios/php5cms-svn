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


/**
 * This class checks the availability of a domain and gets the whois data
 * You can check domains of the following tld's
 *
 * - ac
 * - ac.cn
 * - ac.jp
 * - ac.uk
 * - ad.jp
 * - adm.br
 * - adv.br
 * - aero
 * - ag
 * - agr.br
 * - ah.cn
 * - al
 * - am.br
 * - arq.br
 * - at
 * - au
 * - art.br
 * - as
 * - asn.au
 * - ato.br
 * - be
 * - bg
 * - bio.br
 * - biz
 * - bj.cn
 * - bmd.br
 * - br
 * - ca
 * - cc
 * - cd
 * - ch
 * - cim.br
 * - ck
 * - cl
 * - cn
 * - cng.br
 * - cnt.br
 * - com
 * - com.au
 * - com.br
 * - com.cn
 * - com.eg
 * - com.hk
 * - com.mx
 * - com.ru
 * - com.tw
 * - conf.au
 * - co.jp
 * - co.uk
 * - cq.cn
 * - csiro.au
 * - cx
 * - cz
 * - de
 * - dk
 * - ecn.br
 * - ee
 * - edu
 * - edu.au
 * - edu.br
 * - eg
 * - es
 * - esp.br
 * - etc.br
 * - eti.br
 * - eun.eg
 * - emu.id.au
 * - eng.br
 * - far.br
 * - fi
 * - fj
 * - fj.cn
 * - fm.br
 * - fnd.br
 * - fo
 * - fot.br
 * - fst.br
 * - fr
 * - g12.br
 * - gd.cn
 * - ge
 * - ggf.br
 * - gl
 * - gr
 * - gr.jp
 * - gs
 * - gs.cn
 * - gov.au
 * - gov.br
 * - gov.cn
 * - gov.hk
 * - gob.mx
 * - gs
 * - gz.cn
 * - gx.cn
 * - he.cn
 * - ha.cn
 * - hb.cn
 * - hi.cn
 * - hl.cn
 * - hn.cn
 * - hm
 * - hk
 * - hk.cn
 * - hu
 * - id.au
 * - ie
 * - ind.br
 * - imb.br
 * - inf.br
 * - info
 * - info.au
 * - it
 * - idv.tw
 * - int
 * - is
 * - il
 * - jl.cn
 * - jor.br
 * - jp
 * - js.cn
 * - jx.cn
 * - kr
 * - la
 * - lel.br
 * - li
 * - lk
 * - ln.cn
 * - lt
 * - lu
 * - lv
 * - ltd.uk
 * - mat.br
 * - mc
 * - med.br
 * - mil
 * - mil.br
 * - mn
 * - mo.cn
 * - ms
 * - mus.br
 * - mx
 * - name
 * - ne.jp
 * - net
 * - net.au
 * - net.br
 * - net.cn
 * - net.eg
 * - net.hk
 * - net.lu
 * - net.mx
 * - net.uk
 * - net.ru
 * - net.tw
 * - nl
 * - nm.cn
 * - no
 * - nom.br
 * - not.br
 * - ntr.br
 * - nx.cn
 * - nz
 * - plc.uk
 * - odo.br
 * - oop.br
 * - or.jp
 * - org
 * - org.au
 * - org.br
 * - org.cn
 * - org.hk
 * - org.lu
 * - org.ru
 * - org.tw
 * - org.uk
 * - pl
 * - pp.ru
 * - ppg.br
 * - pro.br
 * - psi.br
 * - psc.br
 * - pt
 * - qh.cn
 * - qsl.br
 * - rec.br
 * - ro
 * - ru
 * - sc.cn
 * - sd.cn
 * - se
 * - sg
 * - sh
 * - sh.cn
 * - si
 * - sk
 * - slg.br
 * - sm
 * - sn.cn
 * - srv.br
 * - st
 * - sx.cn
 * - tc
 * - th
 * - tj.cn
 * - tmp.br
 * - to
 * - tr
 * - trd.br
 * - tur.br
 * - tv // ! .tv domains are limited in requests of WHOIS information at the server whois.tv up to 20 requests
 * - tv.br
 * - tw
 * - tw.cn
 * - uk
 * - va
 * - vet.br
 * - vg
 * - wattle.id.au
 * - ws
 * - xj.cn
 * - xz.cn
 * - yn.cn
 * - zlg.br
 * - zj.cn
 *
 * @package peer
 */

class TLDInformation extends PEAR
{
    var $domain = "";

	// initializing server variables
	// array(top level domain,whois_Server,not_found_string or MAX number of CHARS: MAXCHARS:n)
	var $servers = array(
    	array( "ac",           "whois.nic.ac",             "No match"                   ),
		array( "ac.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "ac.jp",        "whois.nic.ad.jp",          "No match"                   ),
		array( "ac.uk",        "whois.ja.net",             "no entries"                 ),
		array( "ad.jp",        "whois.nic.ad.jp",          "No match"                   ),
		array( "adm.br",       "whois.nic.br",             "No match"                   ),
		array( "adv.br",       "whois.nic.br",             "No match"                   ),
		array( "aero",         "whois.information.aero",   "is available"               ),
		array( "ag",           "whois.nic.ag",             "does not exist"             ),
		array( "agr.br",       "whois.nic.br",             "No match"                   ),
		array( "ah.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "al",           "whois.ripe.net",           "No entries found"           ),
		array( "am.br",        "whois.nic.br",             "No match"                   ),
		array( "arq.br",       "whois.nic.br",             "No match"                   ),
		array( "at",           "whois.nic.at",             "nothing found"              ),
		array( "au",           "whois.aunic.net",          "No Data Found"              ),
		array( "art.br",       "whois.nic.br",             "No match"                   ),
		array( "as",           "whois.nic.as",             "Domain Not Found"           ),
		array( "asn.au",       "whois.aunic.net",          "No Data Found"              ),
		array( "ato.br",       "whois.nic.br",             "No match"                   ),
		array( "be",           "whois.geektools.com",      "No such domain"             ),
		array( "bg",           "whois.digsys.bg",          "does not exist"             ),
		array( "bio.br",       "whois.nic.br",             "No match"                   ),
		array( "biz",          "whois.biz",                "Not found"                  ),
		array( "bj.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "bmd.br",       "whois.nic.br",             "No match"                   ),
		array( "br",           "whois.registro.br",        "No match"                   ),
		array( "ca",           "whois.cira.ca",            "Status: AVAIL"              ),
		array( "cc",           "whois.nic.cc",             "No match"                   ),
		array( "cd",           "whois.cd",                 "No match"                   ),
		array( "ch",           "whois.nic.ch",             "We do not have an entry"    ),
		array( "cim.br",       "whois.nic.br",             "No match"                   ),
		array( "ck",           "whois.ck-nic.org.ck",      "No entries found"           ),
		array( "cl",           "whois.nic.cl",             "no existe"                  ),
		array( "cn",           "whois.cnnic.net.cn",       "No entries found"           ),
		array( "cng.br",       "whois.nic.br",             "No match"                   ),
		array( "cnt.br",       "whois.nic.br",             "No match"                   ),
		array( "com",          "whois.verisign-grs.net",   "No match"                   ),
		array( "com.au",       "whois.aunic.net",          "No Data Found"              ),
		array( "com.br",       "whois.nic.br",             "No match"                   ),
		array( "com.cn",       "whois.cnnic.net.cn",       "No entries found"           ),
		array( "com.eg",       "whois.ripe.net",           "No entries found"           ),
		array( "com.hk",       "whois.hknic.net.hk",       "No Match for"               ),
		array( "com.mx",       "whois.nic.mx",             "Nombre del Dominio"         ),
		array( "com.ru",       "whois.ripn.ru",            "No entries found"           ),
		array( "com.tw",       "whois.twnic.net",          "NO MATCH TIP"               ),
		array( "conf.au",      "whois.aunic.net",          "No entries found"           ),
		array( "co.jp",        "whois.nic.ad.jp",          "No match"                   ),
		array( "co.uk",        "whois.nic.uk",             "No match for"               ),
		array( "cq.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "csiro.au",     "whois.aunic.net",          "No Data Found"              ),
		array( "cx",           "whois.nic.cx",             "No match"                   ),
		array( "cz",           "whois.nic.cz",             "No data found"              ),
		array( "de",           "whois.denic.de",           "No entries found"           ),
		array( "dk",           "whois.dk-hostmaster.dk",   "No entries found"           ),
		array( "ecn.br",       "whois.nic.br",             "No match"                   ),
		array( "ee",           "whois.eenet.ee",           "NOT FOUND"                  ),
		array( "edu",          "whois.verisign-grs.net",   "No match"                   ),
		array( "edu.au",       "whois.aunic.net",          "No Data Found"              ),
		array( "edu.br",       "whois.nic.br",             "No match"                   ),
		array( "eg",           "whois.ripe.net",           "No entries found"           ),
		array( "es",           "whois.ripe.net",           "No entries found"           ),
		array( "esp.br",       "whois.nic.br",             "No match"                   ),
		array( "etc.br",       "whois.nic.br",             "No match"                   ),
		array( "eti.br",       "whois.nic.br",             "No match"                   ),
		array( "eun.eg",       "whois.ripe.net",           "No entries found"           ),
		array( "emu.id.au",    "whois.aunic.net",          "No Data Found"              ),
		array( "eng.br",       "whois.nic.br",             "No match"                   ),
		array( "far.br",       "whois.nic.br",             "No match"                   ),
		array( "fi",           "whois.ripe.net",           "No entries found"           ),
		array( "fj",           "whois.usp.ac.fj",          ""                           ),
		array( "fj.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "fm.br",        "whois.nic.br",             "No match"                   ),
		array( "fnd.br",       "whois.nic.br",             "No match"                   ),
		array( "fo",           "whois.ripe.net",           "no entries found"           ),
		array( "fot.br",       "whois.nic.br",             "No match"                   ),
		array( "fst.br",       "whois.nic.br",             "No match"                   ),
		array( "fr",           "whois.nic.fr",             "No entries found"           ),
		array( "g12.br",       "whois.nic.br",             "No match"                   ),
		array( "gd.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "ge",           "whois.ripe.net",           "no entries found"           ),
		array( "ggf.br",       "whois.nic.br",             "No match"                   ),
		array( "gl",           "whois.ripe.net",           "no entries found"           ),
		array( "gr",           "whois.ripe.net",           "no entries found"           ),
		array( "gr.jp",        "whois.nic.ad.jp",          "No match"                   ),
		array( "gs",           "whois.adamsnames.tc",      "is not registered"          ),
		array( "gs.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "gov.au",       "whois.aunic.net",          "No Data Found"              ),
		array( "gov.br",       "whois.nic.br",             "No match"                   ),
		array( "gov.cn",       "whois.cnnic.net.cn",       "No entries found"           ),
		array( "gov.hk",       "whois.hknic.net.hk",       "No Match for"               ),
		array( "gob.mx",       "whois.nic.mx",             "Nombre del Dominio"         ),
		array( "gs",           "whois.adamsnames.tc",      "is not registered"          ),
		array( "gz.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "gx.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "he.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "ha.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "hb.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "hi.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "hl.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "hn.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "hm",           "whois.registry.hm",        "(null)"                     ),
		array( "hk",           "whois.hknic.net.hk",       "No Match for"               ),
		array( "hk.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "hu",           "whois.ripe.net",           "MAXCHARS:500"               ),
		array( "id.au",        "whois.aunic.net",          "No Data Found"              ),
		array( "ie",           "whois.domainregistry.ie",  "no match"                   ),
		array( "ind.br",       "whois.nic.br",             "No match"                   ),
		array( "imb.br",       "whois.nic.br",             "No match"                   ),
		array( "inf.br",       "whois.nic.br",             "No match"                   ),
		array( "info",         "whois.afilias.info",       "Not found"                  ),
		array( "info.au",      "whois.aunic.net",          "No Data Found"              ),
		array( "it",           "whois.nic.it",             "No entries found"           ),
		array( "idv.tw",       "whois.twnic.net",          "NO MATCH TIP"               ),
		array( "int",          "whois.iana.org",           "not found"                  ),
		array( "is",           "whois.isnic.is",           "No entries found"           ),
		array( "il",           "whois.isoc.org.il",        "No data was found"          ),
		array( "jl.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "jor.br",       "whois.nic.br",             "No match"                   ),
		array( "jp",           "whois.nic.ad.jp",          "No match"                   ),
		array( "js.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "jx.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "kr",           "whois.krnic.net",          "is not registered"          ),
		array( "la",           "whois.nic.la",             "NO MATCH"                   ),
		array( "lel.br",       "whois.nic.br",             "No match"                   ),
		array( "li",           "whois.nic.ch",             "We do not have an entry"    ),
		array( "lk",           "whois.nic.lk",             "No domain registered"       ),
		array( "ln.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "lt",           "ns.litnet.lt",             "No matches found"           ),
		array( "lu",           "whois.dns.lu",             "No entries found"           ),
		array( "lv",           "whois.ripe.net",           "no entries found"           ),
		array( "ltd.uk",       "whois.nic.uk",             "No match for"               ),
		array( "mat.br",       "whois.nic.br",             "No match"                   ),
		array( "mc",           "whois.ripe.net",           "No entries found"           ),
		array( "med.br",       "whois.nic.br",             "No match"                   ),
		array( "mil",          "whois.nic.mil",            "No match"                   ),
		array( "mil.br",       "whois.nic.br",             "No match"                   ),
		array( "mn",           "whois.nic.mn",             "Domain not found"           ),
		array( "mo.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "ms",           "whois.adamsnames.tc",      "is not registered"          ),
		array( "mus.br",       "whois.nic.br",             "No match"                   ),
		array( "mx",           "whois.nic.mx",             "Nombre del Dominio"         ),
		array( "name",         "whois.nic.name",           "No match"                   ),
		array( "ne.jp",        "whois.nic.ad.jp",          "No match"                   ),
		array( "net",          "whois.verisign-grs.net",   "No match"                   ),
		array( "net.au",       "whois.aunic.net",          "No Data Found"              ),
		array( "net.br",       "whois.nic.br",             "No match"                   ),
		array( "net.cn",       "whois.cnnic.net.cn",       "No entries found"           ),
		array( "net.eg",       "whois.ripe.net",           "No entries found"           ),
		array( "net.hk",       "whois.hknic.net.hk",       "No Match for"               ),
		array( "net.lu",       "whois.dns.lu",             "No entries found"           ),
		array( "net.mx",       "whois.nic.mx",             "Nombre del Dominio"         ),
		array( "net.uk",       "whois.nic.uk",             "No match for "              ),
		array( "net.ru",       "whois.ripn.ru",            "No entries found"           ),
		array( "net.tw",       "whois.twnic.net",          "NO MATCH TIP"               ),
		array( "nl",           "whois.domain-registry.nl", "is not a registered domain" ),
		array( "nm.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "no",           "whois.norid.no",           "no matches"                 ),
		array( "nom.br",       "whois.nic.br",             "No match"                   ),
		array( "not.br",       "whois.nic.br",             "No match"                   ),
		array( "ntr.br",       "whois.nic.br",             "No match"                   ),
		array( "nx.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "nz",           "whois.domainz.net.nz",     "Not Listed"                 ),
		array( "plc.uk",       "whois.nic.uk",             "No match for"               ),
		array( "odo.br",       "whois.nic.br",             "No match"                   ),
		array( "oop.br",       "whois.nic.br",             "No match"                   ),
		array( "or.jp",        "whois.nic.ad.jp",          "No match"                   ),
		array( "org",          "whois.verisign-grs.net",   "No match"                   ),
		array( "org.au",       "whois.aunic.net",          "No Data Found"              ),
		array( "org.br",       "whois.nic.br",             "No match"                   ),
		array( "org.cn",       "whois.cnnic.net.cn",       "No entries found"           ),
		array( "org.hk",       "whois.hknic.net.hk",       "No Match for"               ),
		array( "org.lu",       "whois.dns.lu",             "No entries found"           ),
		array( "org.ru",       "whois.ripn.ru",            "No entries found"           ),
		array( "org.tw",       "whois.twnic.net",          "NO MATCH TIP"               ),
		array( "org.uk",       "whois.nic.uk",             "No match for"               ),
		array( "pl",           "nazgul.nask.waw.pl",       "does not exists"            ),
		array( "pp.ru",        "whois.ripn.ru",            "No entries found"           ),
		array( "ppg.br",       "whois.nic.br",             "No match"                   ),
		array( "pro.br",       "whois.nic.br",             "No match"                   ),
		array( "psi.br",       "whois.nic.br",             "No match"                   ),
		array( "psc.br",       "whois.nic.br",             "No match"                   ),
		array( "pt",           "whois.ripe.net",           "No entries found"           ),
		array( "qh.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "qsl.br",       "whois.nic.br",             "No match"                   ),
		array( "rec.br",       "whois.nic.br",             "No match"                   ),
		array( "ro",           "whois.rotld.ro",           "No entries found"           ),
		array( "ru",           "whois.ripn.ru",            "No entries found"           ),
		array( "sc.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "sd.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "se",           "whois.nic-se.se",          "No data found"              ),
		array( "sg",           "whois.nic.net.sg",         "NO entry found"             ),
		array( "sh",           "whois.nic.sh",             "No match for"               ),
		array( "sh.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "si",           "whois.arnes.si",           "No entries found"           ),
		array( "sk",           "whois.ripe.net",           "no entries found"           ),
		array( "slg.br",       "whois.nic.br",             "No match"                   ),
		array( "sm",           "whois.ripe.net",           "no entries found"           ),
		array( "sn.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "srv.br",       "whois.nic.br",             "No match"                   ),
		array( "st",           "whois.nic.st",             "No entries found"           ),
		array( "sx.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "tc",           "whois.adamsnames.tc",      "is not registered"          ),
		array( "th",           "whois.nic.uk",             "No entries found"           ),
		array( "tj.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "tmp.br",       "whois.nic.br",             "No match"                   ),
		array( "to",           "whois.tonic.to",           "No match"                   ),
		array( "tr",           "whois.ripe.net",           "Not found in database"      ),
		array( "trd.br",       "whois.nic.br",             "No match"                   ),
		array( "tur.br",       "whois.nic.br",             "No match"                   ),
		array( "tv",           "whois.tv",                 "MAXCHARS:75"                ),
		array( "tv.br",        "whois.nic.br",             "No match"                   ),
		array( "tw",           "whois.twnic.net",          "NO MATCH TIP"               ),
		array( "tw.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "uk",           "whois.thnic.net",          "No match for"               ),
		array( "va",           "whois.ripe.net",           "No entries found"           ),
		array( "vet.br",       "whois.nic.br",             "No match"                   ),
		array( "vg",           "whois.adamsnames.tc",      "is not registered"          ),
		array( "wattle.id.au", "whois.aunic.net",          "No Data Found"              ),
		array( "ws",           "whois.worldsite.ws",       "No match for"               ),
		array( "xj.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "xz.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "yn.cn",        "whois.cnnic.net.cn",       "No entries found"           ),
		array( "zlg.br",       "whois.nic.br",             "No match"                   ),
		array( "zj.cn",        "whois.cnnic.net.cn",       "No entries found"           )  
	);
	
	
    /**
     * Constructor
	 *
     * @param string	$str_domainame    the full name of the domain
     */
    function TLDInformation( $str_domainname )
	{
        $this->domain = $str_domainname;
    }

	
    /**
     * Returns the whois data of the domain.
	 *
     * @return string $whoisdata Whois data as string
     */
    function info()
	{
		if ( $this->is_valid() )
		{
			$tldname      = $this->get_tld();
			$domainname   = $this->get_domain();
			$whois_server = $this->get_whois_server();
            
            // If tldname have been found.
            if ( $whois_server != "" )
			{
                // Getting whois information.
                $fp  = fsockopen( $whois_server, 43 );
                $dom = $domainname . "." . $tldname;
                fputs( $fp, "$dom\r\n" );

                // Getting string.
                $string = "";
				
                while ( !feof( $fp ) )
                    $string .= fgets( $fp, 128 );
                
                fclose( $fp );
                return $string;
            }
			else
			{
                return "No whois server for this tld in list!";
            }
        }
		else
		{
            return "Domainname isn't valid!";
        }
    }

    /**
     * Returns the whois data of the domain in HTML format.
	 *
     * @return string $whoisdata Whois data as string in HTML
     */
    function html_info()
	{
        return nl2br( $this->info() );
    }

    /**
     * Returns name of the whois server of the tld.
	 *
     * @return string $server the whois servers hostname
     */
    function get_whois_server()
	{
		$found   = false;
		$tldname = $this->get_tld();
		
		for ( $i = 0; $i < count( $this->servers ); $i++ )
		{
			if ( $this->servers[$i][0] == $tldname )
			{
				$server   = $this->servers[$i][1];
				$full_dom = $this->servers[$i][3];
				$found    = true;
			}
		}
			
		return $server;
    }

    /**
     * Returns the tld of the domain without domain name.
	 *
     * @return string $tldname the tlds name without domain name
     */
    function get_tld()
	{
		// Splitting domainname.
		$domain = split( "\.", $this->domain );
		
       	if ( count( $domain ) > 2 )
		{
			$domainname = $domain[0];
			
			for ( $i = 1; $i < count( $domain ); $i++ )
			{
				if ( $i == 1 )
					$tldname = $domain[$i];
				else
                  $tldname .= "." . $domain[$i];
            }
		}
		else
		{
			$domainname = $domain[0];
			$tldname    = $domain[1];
		}
		
		return $tldname;
    }

    /**
     * Returns all tlds which are supported by the class.
	 *
     * @return array $tlds all tlds as array
     */    
    function get_tlds()
	{
    	$tlds = "";
    	
		for ( $i = 0; $i < count( $this->servers ); $i++ )
    		$tlds[$i] = $this->servers[$i][0];
    	
    	return $tlds;
    }

    /**
     * Returns the name of the domain without tld.
	 *
     * @return string $domain the domains name without tld name
     */
    function get_domain()
	{
       // Splitting domainname.
       $domain = split( "\.", $this->domain );
       return $domain[0];
    }

    /**
     * Returns the string which will be returned by the whois server of the tld if a domain is avalable.
	 *
     * @return string $notfound  the string which will be returned by the whois server of the tld if a domain is avalable
     */
    function get_notfound_string()
	{
       $found   = false;
       $tldname = $this->get_tld();
       
	   for ( $i = 0; $i < count( $this->servers ); $i++ )
	   {
           if ( $this->servers[$i][0] == $tldname )
               $notfound = $this->servers[$i][2];
       }
	   
       return $notfound;
    }
    
    /**
     * Returns if the domain is available for registering.
	 *
     * @return boolean $is_available Returns 1 if domain is available and 0 if domain isn't available
     */
    function is_available()
	{
        $whois_string = $this->info();
        $not_found_string = $this->get_notfound_string();
        $domain = $this->domain;
        $whois_string2 = ereg_replace( "$domain", "", $whois_string );
        $array = split( ":", $not_found_string );
                
        if ( $array[0] == "MAXCHARS" )
		{
        	if ( strlen( $whois_string2 ) <= $array[1] )
        		return true;
        	else
        		return false; 	
        }
		else
		{
        	if ( preg_match( "/" . $not_found_string . "/i", $whois_string ) )
            	return true;
        	else
            	return false;
        }
    }

    /**
     * Returns if the domain name is valid.
	 *
     * @return boolean $is_valid Returns 1 if domain is valid and 0 if domain isn't valid
     */
    function is_valid()
	{
        if ( ereg( "^[a-zA-Z0-9\-]{3,}$", $this->get_domain() ) && !preg_match( "/--/", $this->get_domain() ) )
            return true;
        else
            return false;
    }
} // END OF TLDInformation

?>
