# Qphp
A PHP class to control the Belleds Q Station and networked light bulbs.

Currently this works only to control bulb(s) given their serial numbers.  When the Q API properly supports querying the bulbs and their states from the Q Station, this will be added to Qphp.

When '$bulbs' is specified in a method, it can be passed as either a serial number string representing a single bulb or an array of serial number strings to control multiple bulbs at once.

Methods:
--------
* set_on($bulbs, $bright=255) - Turn $bulbs on to the 'white' state (as a normal light bulb).  Brightness can be optionally specified.
* set_off($bulbs) - Turn $bulbs off.
* set_color($bulbs, $red=255, $green=255, $blue=255, $bright=255) - Set the color of $bulbs.  Any unspecified color or brightness will be set to full.

Examples:
---------
        <?php
        include 'QAPI.php';
        
        // Create a new Q object and specify its IP address:
        $station = new Q('10.0.0.100');
        
        // Turn on a bulb:
        $station->set_on('MD1AC44200000001');
        
        // Set two bulbs to solid red (NB: no need to separately turn them on):
        $station->set_color(array('MD1AC44200000001', 'MD1AC44200000002'), 255, 0, 0);
        ?>
