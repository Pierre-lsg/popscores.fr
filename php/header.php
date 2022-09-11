<?php
/* Variables communes */
$l_racineSite =  "/";
?>
<head>
	<!-- Matomo -->
	<script type="text/javascript">
	  var _paq = _paq || [];
	  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
	  _paq.push(['trackPageView']);
	  _paq.push(['enableLinkTracking']);
	  (function() {
		var u="//www.popscores.fr/analytics/";
		_paq.push(['setTrackerUrl', u+'piwik.php']);
		_paq.push(['setSiteId', '1']);
		var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
		g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
	  })();
	</script>
	<!-- End Matomo Code -->
</head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:image" content="https://www.popscores.fr/logo.jpg" />

<link rel="stylesheet" href="<?php echo $l_racineSite ?>/css/w3.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#ps_footer").click(function(){
	var profil     = $_GET('profil'),
		id_comp    = $_GET('id_comp'),
		id_arbitre = $_GET('id_arbitre'),
		id_joueur  = $_GET('id_joueur'),
		idinfos	   = profil + "/" + id_comp + "/" + id_arbitre + "/" + id_joueur
		
	if ($("#ps_ident").text() != '') {
			$("#ps_ident").text('');
		} else {
			$("#ps_ident").text(idinfos);
		}
    });
});
</script>

<style>
p, li {text-align:left;}
.w3-half img{margin-bottom:-6px;margin-top:16px;opacity:0.8;cursor:pointer}
.w3-half img:hover{opacity:1}
</style>

