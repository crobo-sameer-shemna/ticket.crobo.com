<?php

require_once __DIR__ . '/../main.inc.php';

$allCroboUsersQuery = db_query("select * from ost_user_email join ost_user on ost_user.id = ost_user_email.user_id
                                where ost_user_email.address LIKE '%crobo.com' and ost_user_email.address NOT IN 
                                (select email FROM ost_staff)
                              ");

while ($croboUser = db_fetch_array($allCroboUsersQuery)) {

    $username = explode('@', $croboUser['address'])[0];
    $firstName = explode('.', $username)[0];
    $lastName = isset(explode('.', $username)[1]) ? explode('.', $username)[1] : '';

    db_query("INSERT INTO ost_staff (
                    group_id, dept_id, timezone_id, username, firstname, lastname, passwd, email, isactive, isadmin,
                    isvisible ,onvacation, assigned_only, show_assigned_tickets, daylight_saving, max_page_size, 
                    auto_refresh_rate, default_signature_type, default_paper_size, created
                ) SELECT
                    group_id, dept_id, timezone_id, '{$username}', '{$firstName}', '{$lastName}', md5('{$username}'), '{$croboUser['address']}', 
                    isactive, isadmin, isvisible ,onvacation, assigned_only, show_assigned_tickets, daylight_saving, 
                    max_page_size, auto_refresh_rate, default_signature_type, default_paper_size, NOW()                    
                FROM ost_staff where staff_id = 4 
            ");

    echo "<br/> user {$croboUser['address']} inserted to staff <br/>";
}
