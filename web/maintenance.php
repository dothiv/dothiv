<?php

$retry = 15;

// Send 503 error code.
header('HTTP/1.0 503 Service Temporarily Unavailable', 503);
header('Retry-After: ' . $retry);
header('Expires: -1');
header('Cache-Control: private, max-age=0');

// Build refresh URL based on server variables.
$proto = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https' : 'http';
$redirect = $proto . '://' . $_SERVER['HTTP_HOST'];

?>
<!DOCTYPE html>
<!--


            _|_|          _|_|
            _|_|          _|_|
            _|_|
            _|_|
            _|_|_|_|      _|_|  _|_|    _|_|
            _|_|_|_|_|    _|_|  _|_|    _|_|
            _|_|    _|_|  _|_|  _|_|    _|_|
            _|_|    _|_|  _|_|  _|_|    _|_|
            _|_|    _|_|  _|_|  _|_|    _|_|
      _|_|  _|_|    _|_|  _|_|    _|_|_|_|
      _|_|  _|_|    _|_|  _|_|      _|_|

 .hiv domains – The Red Ribbon of the digital age

                    dotHIV.org

-->
<html lang="en">
<head>
    <title>Service Temporarily Unavailable</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="<?php echo $retry; ?>; url=<?php echo $redirect; ?>">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <style type="text/css">
        /* Space out content a bit */
        body {
            padding-top: 60px;
            padding-bottom: 20px;
        }

        header,
        footer {
            padding-right: 15px;
            padding-left: 15px;
        }

        /* Custom page header */
        header {
            margin-bottom: 30px;
        }

        /* Customize container */
        @media (min-width: 768px) {
            .container {
                max-width: 730px;
            }
        }

        .jumbotron {
            text-align: center;
            border-bottom: 1px solid #e5e5e5;
        }

        /* Responsive: Portrait tablets and up */
        @media screen and (min-width: 768px) {
            /* Remove the padding we set earlier */
            header {
                padding-right: 0;
                padding-left: 0;
            }

            /* Remove the bottom border on the jumbotron for visual effect */
            .jumbotron {
                border-bottom: 0;
            }
        }

    </style>
</head>
<body>
<div class="container">
    <header>
        <h1 class="text-muted">
            Service Temporarily Unavailable </h1>
    </header>

    <main class="jumbotron">
        <p>Sorry! We are upgrading our site.</p>

        <p>
            <small>This page will reload after <?php echo $retry; ?> seconds.</small>
        </p>

        <p>In the meantime check out this beautiful
            pony:<br><img src="//24.media.tumblr.com/63a0f448c5857ca8429d0e89519b412c/tumblr_mkbtnuqWwn1rl22k3o1_500.gif" alt="Pony">
        </p>


    </main>
</div>
<script type="text/javascript" src="//code.jquery.com/jquery-2.1.0.min.js"></script>
<script type="text/javascript">
    $(function () {
        if (!window.location.hash.length > 0) return;
        // Append hashBang if present.
        var refreshMeta = $('head meta[http-equiv="refresh"]');
        var refreshUrl = refreshMeta.prop('content').split(';')[1].trim().split('=')[1];
        refreshMeta.prop('content', refreshMeta.prop('content').replace(refreshUrl, refreshUrl + "/" + window.location.hash));
    });
</script>
</body>
</html>
