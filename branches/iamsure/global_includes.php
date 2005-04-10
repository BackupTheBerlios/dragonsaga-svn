<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: global_includes.php

// Adodb handles database abstraction
if (!@include_once ("./backends/adodb/adodb.inc.php"))
{
    echo "adodb.inc.php ";
    echo "cannot be found, and it is required for TKI/BNT to run.";
    die();
}

// Adodb handles database abstraction
if (!@include_once ("./backends/adodb/adodb-perf.inc.php"))
{
    echo "adodb-perf.inc.php ";
    echo "cannot be found, and it is required for TKI/BNT to run.";
    die();
}

// XML Schema handler
if (!@include_once ("./backends/adodb/adodb-xmlschema.inc.php"))
{
    echo "adodb-xmlschema.inc.php ";
    echo "cannot be found, and it is required for TKI/BNT to run.";
    die();
}

/*
// encrypted session handler
if (!@include_once ("./backends/adodb/session/adodb-cryptsession.php"))
{
    echo "adodb-cryptsession.php ";
    echo "cannot be found, and it is required for TKI/BNT to run.";
    die();
}

// compressed session handler
if (!@include_once ("./backends/adodb/session/adodb-compress-gzip.php"))
{
    echo "adodb-compress-gzip.php ";
    echo "cannot be found, and it is required for TKI/BNT to run.";
    die();
}
*/

?>
