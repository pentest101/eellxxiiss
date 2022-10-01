<?php 
/**
* @version		4.4
* @package		Elxis
* @subpackage	Elxis Defender
* @copyright	Copyright (c) 2006-2016 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*
* Last update: 2016-02-01 17:41:00 GMT
* Source 1: https://github.com/wesbos/burner-email-providers
* Source 2: https://gist.github.com/michenriksen/8710649
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');

//Disposable/temporary email domains

$rules = array(
	'0815.ru', '0wnd.net', '0wnd.org', '10minutemail.co.za', '10minutemail.com', '123-m.com', '1fsdfdsfsdf.tk', '1pad.de', '20mail.eu', '20mail.it', 
	'20minutemail.com', '21cn.com', '2fdgdfgdfgdf.tk', '2prong.com', '30minutemail.com', '33mail.com', '3trtretgfrfe.tk', '4gfdsgfdgfd.tk', '4warding.com', '5ghgfhfghfgh.tk', 
	'6hjgjhgkilkj.tk', '6paq.com', '7tags.com', '9ox.net', 'a-bc.net', 'abyssmail.com', 'agedmail.com', 'ama-trade.de', 'amilegit.com', 'amiri.net', 
	'amiriindustries.com', 'anappthat.com', 'anonmails.de', 'anonymbox.com', 'antichef.com', 'antichef.net', 'antireg.ru', 'antispam.de', 'antispammail.de', 'armyspy.com', 
	'artman-conception.com', 'azmeil.tk', 'baxomale.ht.cx', 'beefmilk.com', 'bigstring.com', 'binkmail.com', 'bio-muesli.net', 'bobmail.info', 'bodhi.lawlita.com', 'bofthew.com', 
	'bootybay.de', 'boun.cr', 'bouncr.com', 'breakthru.com', 'brefmail.com', 'bsnow.net', 'bspamfree.org', 'bugmenot.com', 'bund.us', 'burstmail.info', 
	'buymoreplays.com', 'byom.de', 'c2.hu', 'card.zp.ua', 'casualdx.com', 'cek.pm', 'centermail.com', 'centermail.net', 'chammy.info', 'childsavetrust.org', 
	'chogmail.com', 'choicemail1.com', 'clixser.com', 'cmail.net', 'cmail.org', 'coldemail.info', 'cool.fr.nf', 'courriel.fr.nf', 'courrieltemporaire.com', 'crapmail.org', 
	'cust.in', 'cuvox.de', 'd3p.dk', 'dacoolest.com', 'dandikmail.com', 'dayrep.com', 'dcemail.com', 'deadaddress.com', 'deadspam.com', 'delikkt.de', 
	'despam.it', 'despammed.com', 'devnullmail.com', 'dfgh.net', 'digitalsanctuary.com', 'dingbone.com', 'disposableaddress.com', 'disposableemailaddresses.com', 'disposableinbox.com', 'dispose.it', 
	'dispostable.com', 'dodgeit.com', 'dodgit.com', 'donemail.ru', 'dontreg.com', 'dontsendmespam.de', 'drdrb.net', 'dropmail.me', 'dump-email.info', 'dumpandjunk.com', 
	'dumpyemail.com', 'e-mail.com', 'e-mail.org', 'e4ward.com', 'easytrashmail.com', 'einmalmail.de', 'einrot.com', 'eintagsmail.de', 'emailgo.de', 'emailias.com', 
	'emaillime.com', 'emailsensei.com', 'emailtemporanea.com', 'emailtemporanea.net', 'emailtemporar.ro', 'emailtemporario.com.br', 'emailthe.net', 'emailtmp.com', 'emailwarden.com', 'emailx.at.hm', 
	'emailxfer.com', 'emeil.in', 'emeil.ir', 'emz.net', 'ero-tube.org', 'evopo.com', 'explodemail.com', 'express.net.ua', 'eyepaste.com', 'fakeinbox.com', 
	'fakeinformation.com', 'fansworldwide.de', 'fantasymail.de', 'fightallspam.com', 'filzmail.com', 'fivemail.de', 'fleckens.hu', 'frapmail.com', 'friendlymail.co.uk', 'fuckingduh.com', 
	'fudgerub.com', 'fyii.de', 'garliclife.com', 'gehensiemirnichtaufdensack.de', 'get2mail.fr', 'getairmail.com', 'getmails.eu', 'getonemail.com', 'giantmail.de', 'girlsundertheinfluence.com', 
	'gishpuppy.com', 'gmial.com', 'goemailgo.com', 'gotmail.net', 'gotmail.org', 'gotti.otherinbox.com', 'grandmamail.com', 'great-host.in', 'greensloth.com', 'grr.la', 
	'gsrv.co.uk', 'guerillamail.biz', 'guerillamail.com', 'guerrillamail.biz', 'guerrillamail.com', 'guerrillamail.de', 'guerrillamail.info', 'guerrillamail.net', 'guerrillamail.org', 'guerrillamailblock.com', 
	'gustr.com', 'harakirimail.com', 'hat-geld.de', 'hatespam.org', 'herp.in', 'hidemail.de', 'hidzz.com', 'hmamail.com', 'hopemail.biz', 'iaoss.com', 
	'ieh-mail.de', 'ikbenspamvrij.nl', 'imails.info', 'inbax.tk', 'inbox.si', 'inboxalias.com', 'inboxclean.com', 'inboxclean.org', 'infocom.zp.ua', 'instant-mail.de', 
	'ip6.li', 'irish2me.com', 'iwi.net', 'jetable.com', 'jetable.fr.nf', 'jetable.net', 'jetable.org', 'jnxjn.com', 'jourrapide.com', 'jsrsolutions.com', 
	'kasmail.com', 'kaspop.com', 'killmail.com', 'killmail.net', 'klassmaster.com', 'klzlk.com', 'koszmail.pl', 'kurzepost.de', 'lawlita.com', 'letthemeatspam.com', 
	'lhsdv.com', 'lifebyfood.com', 'link2mail.net', 'litedrop.com', 'lol.ovpn.to', 'lolfreak.net', 'lookugly.com', 'lortemail.dk', 'lr78.com', 'lroid.com', 
	'lukop.dk', 'm21.cc', 'mail-filter.com', 'mail-temporaire.fr', 'mail.by', 'mail.mezimages.net', 'mail.zp.ua', 'mail1a.de', 'mail21.cc', 'mail2rss.org', 
	'mail333.com', 'mailbidon.com', 'mailbiz.biz', 'mailblocks.com', 'mailbucket.org', 'mailcat.biz', 'mailcatch.com', 'mailde.de', 'mailde.info', 'maildrop.cc', 
	'maildx.com', 'mailed.ro', 'maileimer.de', 'mailexpire.com', 'mailfa.tk', 'mailforspam.com', 'mailfreeonline.com', 'mailguard.me', 'mailin8r.com', 'mailinater.com', 
	'mailinator.com', 'mailinator.net', 'mailinator.org', 'mailinator2.com', 'mailincubator.com', 'mailismagic.com', 'mailme.lv', 'mailme24.com', 'mailmetrash.com', 'mailmoat.com', 
	'mailms.com', 'mailnesia.com', 'mailnull.com', 'mailorg.org', 'mailpick.biz', 'mailrock.biz', 'mailscrap.com', 'mailshell.com', 'mailsiphon.com', 'mailtemp.info', 
	'mailtome.de', 'mailtothis.com', 'mailtrash.net', 'mailtv.net', 'mailtv.tv', 'mailzilla.com', 'makemetheking.com', 'manybrain.com', 'mbx.cc', 'mega.zik.dj', 
	'meinspamschutz.de', 'meltmail.com', 'messagebeamer.de', 'mezimages.net', 'ministry-of-silly-walks.de', 'mintemail.com', 'misterpinball.de', 'mohmal.com', 'moncourrier.fr.nf', 'monemail.fr.nf', 
	'monmail.fr.nf', 'monumentmail.com', 'mt2009.com', 'mt2014.com', 'mycard.net.ua', 'mycleaninbox.net', 'mymail-in.net', 'mypacks.net', 'mypartyclip.de', 'myphantomemail.com', 
	'mysamp.de', 'mytempemail.com', 'mytempmail.com', 'mytrashmail.com', 'nabuma.com', 'neomailbox.com', 'nepwk.com', 'nervmich.net', 'nervtmich.net', 'netmails.com', 
	'netmails.net', 'neverbox.com', 'nice-4u.com', 'nincsmail.com', 'nincsmail.hu', 'nnh.com', 'no-spam.ws', 'noblepioneer.com', 'nomail.pw', 'nomail.xl.cx', 
	'nomail2me.com', 'nomorespamemails.com', 'nospam.ze.tc', 'nospam4.us', 'nospamfor.us', 'nospammail.net', 'notmailinator.com', 'nowhere.org', 'nowmymail.com', 'nurfuerspam.de', 
	'nus.edu.sg', 'objectmail.com', 'obobbo.com', 'odnorazovoe.ru', 'oneoffemail.com', 'onewaymail.com', 'onlatedotcom.info', 'online.ms', 'opayq.com', 'ordinaryamerican.net', 
	'otherinbox.com', 'ovpn.to', 'owlpic.com', 'pancakemail.com', 'pcusers.otherinbox.com', 'pjjkp.com', 'plexolan.de', 'politikerclub.de', 'poofy.org', 'pookmail.com', 
	'privacy.net', 'privatdemail.net', 'proxymail.eu', 'prtnx.com', 'putthisinyourspamdatabase.com', 'pwrby.com', 'qq.com', 'quickinbox.com', 'rcpt.at', 'reallymymail.com', 
	'realtyalerts.ca', 'recode.me', 'recursor.net', 'reliable-mail.com', 'rhyta.com', 'rmqkr.net', 'royal.net', 'rtrtr.com', 's0ny.net', 'safe-mail.net', 
	'safersignup.de', 'safetymail.info', 'safetypost.de', 'saynotospams.com', 'schafmail.de', 'schrott-email.de', 'secretemail.de', 'secure-mail.biz', 'senseless-entertainment.com', 'services391.com', 
	'sharklasers.com', 'shieldemail.com', 'shiftmail.com', 'shitmail.me', 'shitware.nl', 'shmeriously.com', 'shortmail.net', 'sibmail.com', 'sinnlos-mail.de', 'slapsfromlastnight.com', 
	'slaskpost.se', 'slipry.net', 'smashmail.de', 'smellfear.com', 'snakemail.com', 'sneakemail.com', 'sneakmail.de', 'snkmail.com', 'sofimail.com', 'sogetthis.com', 
	'solvemail.info', 'soodonims.com', 'spam4.me', 'spamail.de', 'spamarrest.com', 'spambob.net', 'spambog.ru', 'spambox.us', 'spamcannon.com', 'spamcannon.net', 
	'spamcon.org', 'spamcorptastic.com', 'spamcowboy.com', 'spamcowboy.net', 'spamcowboy.org', 'spamday.com', 'spamex.com', 'spamfree.eu', 'spamfree24.com', 'spamfree24.de', 
	'spamfree24.org', 'spamgoes.in', 'spamgourmet.com', 'spamgourmet.net', 'spamgourmet.org', 'spamherelots.com', 'spamhereplease.com', 'spamhole.com', 'spamify.com', 'spaml.de', 
	'spammotel.com', 'spamobox.com', 'spamslicer.com', 'spamspot.com', 'spamthis.co.uk', 'spamtroll.net', 'speed.1s.fr', 'spoofmail.de', 'stuffmail.de', 'super-auswahl.de', 
	'supergreatmail.com', 'supermailer.jp', 'superrito.com', 'superstachel.de', 'suremail.info', 'talkinator.com', 'teewars.org', 'teleworm.com', 'teleworm.us', 'temp-mail.org', 
	'temp-mail.ru', 'tempe-mail.com', 'tempemail.co.za', 'tempemail.com', 'tempemail.net', 'tempinbox.co.uk', 'tempinbox.com', 'tempmail.eu', 'tempmaildemo.com', 'tempmailer.com', 
	'tempmailer.de', 'tempomail.fr', 'temporaryemail.net', 'temporaryforwarding.com', 'temporaryinbox.com', 'temporarymailaddress.com', 'tempthe.net', 'thankyou2010.com', 'thc.st', 'thelimestones.com', 
	'thisisnotmyrealemail.com', 'thismail.net', 'throwawayemailaddress.com', 'tilien.com', 'tittbit.in', 'tizi.com', 'tmailinator.com', 'toomail.biz', 'topranklist.de', 'tradermail.info', 
	'trash-mail.at', 'trash-mail.com', 'trash-mail.de', 'trash2009.com', 'trashdevil.com', 'trashemail.de', 'trashmail.at', 'trashmail.com', 'trashmail.de', 'trashmail.me', 
	'trashmail.net', 'trashmail.org', 'trashymail.com', 'trbvm.com', 'trialmail.de', 'trillianpro.com', 'tryalert.com', 'twinmail.de', 'tyldd.com', 'uggsrock.com', 
	'umail.net', 'uroid.com', 'us.af', 'venompen.com', 'veryrealemail.com', 'viditag.com', 'viralplays.com', 'vkcode.ru', 'vpn.st', 'vsimcard.com', 
	'vubby.com', 'wasteland.rfc822.org', 'webemail.me', 'weg-werf-email.de', 'wegwerf-emails.de', 'wegwerfadresse.de', 'wegwerfemail.com', 'wegwerfemail.de', 'wegwerfmail.de', 'wegwerfmail.info', 
	'wegwerfmail.net', 'wegwerfmail.org', 'wh4f.org', 'whyspam.me', 'willhackforfood.biz', 'willselfdestruct.com', 'winemaven.info', 'wronghead.com', 'www.e4ward.com', 'www.mailinator.com', 
	'wwwnew.eu', 'x.ip6.li', 'xagloo.com', 'xemaps.com', 'xents.com', 'xmaily.com', 'xoxy.net', 'yep.it', 'yogamaven.com', 'yopmail.com', 
	'yopmail.fr', 'yopmail.net', 'yourdomain.com', 'yuurok.com', 'z1p.biz', 'za.com', 'zehnminuten.de', 'zehnminutenmail.de', 'zippymail.info', 'zoemail.net', 
	'zomg.info', 'poczta.onet.pl', 'zomg.info'
);

?>