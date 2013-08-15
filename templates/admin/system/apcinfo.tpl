<iframe src="{SITE_URL}/externals/{APC_FILE}.php" style="width:100%; height:100%;" id="apcFrame">
</iframe>

<script>
	var $apcFrame;
	$(document).ready(function(){
		$apcFrame = $("#apcFrame");	// cache the iframe object
		$apcFrame.load(function(){
			// get the height of the iframe contents
			var contentHeight = $("#apcFrame").contents().find(".content").height() + $("#apcFrame").contents().find(".menu").height() + 50;
			// set the iframe to the same height
			$apcFrame.height(contentHeight);
		})
	});
</script>