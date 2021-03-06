<?php

// help.php
// displays the help menu (only in Dutch)
if ((!isset($inIndex)) || (!$inIndex)) {
    include '../../redirect.php';
} else {
    help();
}
function help()
{
    echo '<ul class="nav navbar-nav">
           <li class="dropdown">
	        <a href="http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '#" class="dropdown-toggle" data-toggle="dropdown">Help<b class="caret"></b></a>';
    echo '  <ul class="dropdown-menu">';
    if ($_SESSION['lang'] == 'nl') {
        echo '  <li><a href="https://github.com/DeepskyLog/DeepskyLog/wiki/Handleidinghttps://github.com/DeepskyLog/DeepskyLog/wiki/Handleiding" rel="external">Handleiding</a></li>';
    }
    echo '   <li><a href="mailto:deepskylog@groups.io">' . _('Ask question') . '</a></li>';
    echo '   <li><a href="https://groups.io/g/deepskylog">' . _('Subscribe to mailing list') . '</a></li>';
    echo '   <li><a href="https://github.com/DeepskyLog/DeepskyLog/issues">' . _('Report issue') . '</a></li>';
    echo "   <li><a href=\"https://github.com/DeepskyLog/DeepskyLog/wiki/What's-New-in-DeepskyLog\">" . _("What's new in DeepskyLog") . '</a></li>';
    echo '  </ul>';
    echo ' </li>
          </ul>';
}
