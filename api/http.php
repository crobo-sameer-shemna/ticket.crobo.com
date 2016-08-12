<?php
/*********************************************************************
    http.php

    HTTP controller for the osTicket API

    Jared Hancock
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
// Use sessions — it's important for SSO authentication, which uses
// /api/auth/ext
define('DISABLE_SESSION', false);

require 'api.inc.php';

# Include the main api urls
require_once INCLUDE_DIR."class.dispatcher.php";

$dispatcher = patterns('',
        url_post("^/tickets\.(?P<format>xml|json|email)$", array('api.tickets.php:TicketApiController','create')),
        url('^/tasks/', patterns('',
                url_post("^cron$", array('api.cron.php:CronApiController', 'execute'))
         )),
        url_post("^/custom_gettickets\.(?P<format>xml|json)$", array('api.custom.php:CustomApiController','getTickets')),
        url_post("^/custom_getlists\.(?P<format>xml|json)$", array('api.custom.php:CustomApiController','getLists')),
        url_post("^/custom_getforms\.(?P<format>xml|json)$", array('api.custom.php:CustomApiController','getForms')),
        url_post("^/custom_getagents\.(?P<format>xml|json)$", array('api.custom.php:CustomApiController','getAgents'))
    );

Signal::send('api', $dispatcher);

# Call the respective function
print $dispatcher->resolve($ost->get_path_info());
?>
