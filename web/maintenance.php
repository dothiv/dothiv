<?php

  $retry = 60;

  // Send 503 error code.
  header('HTTP/1.0 503 Service Temporarily Unavailable', 503);
  header('Retry-After: ' . $retry);
  
  // Build refresh URL based on server variables.
  $proto = (strtolower($_SERVER['HTTPS']) == 'on') ? 'https' : 'http';
  $redirect = $proto . '://' . $_SERVER['HTTP_HOST'];
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="refresh" content="<?php echo $retry; ?>; url=<?php echo $redirect; ?>">
  <title>Service Temporarily Unavailable</title>
</head>
<body>
  <h1>Service Temporarily Unavailable</h1>
  <p>Sorry! We are upgrading our site.</p>
  <p>In the meantime check out this beautiful pony:<br><img src="http://24.media.tumblr.com/63a0f448c5857ca8429d0e89519b412c/tumblr_mkbtnuqWwn1rl22k3o1_500.gif"></p>
  
  <p><small>This page will reload after <?php echo $retry; ?> seconds.</small></p>
  <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
  <script type="text/javascript">
    if (!window.location.hash.length > 0) return; 
    $(function() {
	// Append hashBang if present.
	var refreshMeta = $('head meta[http-equiv="refresh"]');
  	var refreshUrl = refreshMeta.prop('content').split(';')[1].trim().split('=')[1];
  	refreshMeta.prop('content', refreshMeta.prop('content').replace(refreshUrl, refreshUrl + "/" +  window.location.hash));
    });
  </script>
</body>
</html>