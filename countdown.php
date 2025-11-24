<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo "Countdown to METM" . date('y'); ?></title>
<link rel="stylesheet" href="https://www.metmeetings.org/css/flip/flip.min.css" />
</head>
<body>
<!--  
  <h1><?php echo "Countdown to METM" . date('y'); ?></h1> 
-->
<style>
    .tick {
        padding-bottom: 1em;
        font-size:1rem;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    }
    
    .tick-label {
        font-size:.375em;
        text-align:center;
    }

    .tick-group {
        margin:0 .25em;
        text-align:center;
    }
</style>

<div class="tick"
     data-did-init="handleTickInit">

    <div data-repeat="true"
         data-layout="horizontal center fit"
         data-transform="preset(d, h, m, s) -> delay">

        <div class="tick-group">

            <div data-key="value"
                 data-repeat="true"
                 data-transform="pad(00) -> split -> delay">

                <span data-view="flip"></span>

            </div>

            <span data-key="label"
                  data-view="text"
                  class="tick-label"></span>

        </div>

    </div>

</div>

<script>
	function handleTickInit(tick) {

        var METMDate = '2023-10-12';

        Tick.count.down(METMDate).onupdate = function(value) {
			tick.value = value;
        };

	}
</script>

<script src="https://www.metmeetings.org/scripts/js/flip.min.js"></script>
  
</body>
</html>
