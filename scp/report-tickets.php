<?php

require_once __DIR__ . '/staff.inc.php';
require_once(STAFFINC_DIR . 'header.inc.php');
require_once __DIR__ . '/../include/class.ticket.php';

$errors = [];
$generalReportDownloadFile = null;
$scrubbingReportDownloadFile = null;
$monthlyStatsReportDownloadFile = null;
$monthlyStatsByAgentReportDownloadFile = null;

generalReport($generalReportDownloadFile);
scrubbingReport($scrubbingReportDownloadFile);
monthlyStatsReport($monthlyStatsReportDownloadFile);
monthlyStatsByAgentReport($monthlyStatsByAgentReportDownloadFile);
?>

    <style>
        .downloadLink {
            display: block;
            margin: 2px 0px 10px 0px;
            padding: 0px;
            color: #004a80;
            font-weight: bold;
            text-decoration: underline;
            font-size: 12px;
        }

        h1 {
            margin: 2px 0px !important;
        }

        hr{
            margin:30px auto;
            display: block;
        }
    </style>

    <div>
        <form method="get">
            <h1>General Report</h1>
            <?php if (!empty($generalReportDownloadFile)) { ?>
                <a class="downloadLink" href="./../attachments/<?= basename($generalReportDownloadFile) ?>">
                    Download general report (right click and save link as)
                </a>
            <?php } ?>
            <label> From
                <input type="date"
                       name="general_from"
                       required
                       value="<?= (isset($_GET['general_from']) and strtotime($_GET['general_from'])) ?
                           (new DateTime($_GET['general_from']))->format('Y-m-d') : '' ?>"
                />
            </label>
            <label> To
                <input
                    type="date"
                    name="general_to"
                    required
                    value="<?= (isset($_GET['general_to']) and strtotime($_GET['general_to'])) ?
                        (new DateTime($_GET['general_to']))->format('Y-m-d') : '' ?>"
                />
            </label>

            <input type="submit" value="export"/>
            <input type="hidden" name="report_type" value="general"/>
        </form>
    </div>

    <hr/>

    <div>
        <form method="get">
            <h1>Scrubbing tickets Report</h1>
            <?php if (!empty($scrubbingReportDownloadFile)) { ?>
                <a class="downloadLink" href="./../attachments/<?= basename($scrubbingReportDownloadFile) ?>">
                    Download scrubbing report (right click and save link as)
                </a>
            <?php } ?>
            <label> From
                <input type="date"
                       name="scrubbing_from"
                       required
                       value="<?= (isset($_GET['scrubbing_from']) and strtotime($_GET['scrubbing_from'])) ?
                           (new DateTime($_GET['scrubbing_from']))->format('Y-m-d') : '' ?>"
                />
            </label>
            <label> To
                <input
                    type="date"
                    name="scrubbing_to"
                    required
                    value="<?= (isset($_GET['scrubbing_to']) and strtotime($_GET['scrubbing_to'])) ?
                        (new DateTime($_GET['scrubbing_to']))->format('Y-m-d') : '' ?>"
                />
            </label>

            <input type="submit" value="export"/>
            <input type="hidden" name="report_type" value="scrubbing"/>
        </form>
    </div>

    <hr/>

    <div>
        <form method="get">
            <h1>Monthly stats Report</h1>
            <?php if (!empty($monthlyStatsReportDownloadFile)) { ?>
                <a class="downloadLink" href="./../attachments/<?= basename($monthlyStatsReportDownloadFile) ?>">
                    Download monthly stats report (right click and save link as)
                </a>
            <?php } ?>
            <label> From
                <input type="date"
                       name="monthly_stats_from"
                       required
                       value="<?= (isset($_GET['monthly_stats_from']) and strtotime($_GET['monthly_stats_from'])) ?
                           (new DateTime($_GET['monthly_stats_from']))->format('Y-m-d') : '' ?>"
                />
            </label>
            <label> To
                <input
                    type="date"
                    name="monthly_stats_to"
                    required
                    value="<?= (isset($_GET['monthly_stats_to']) and strtotime($_GET['monthly_stats_to'])) ?
                        (new DateTime($_GET['monthly_stats_to']))->format('Y-m-d') : '' ?>"
                />
            </label>

            <input type="submit" value="export"/>
            <input type="hidden" name="report_type" value="monthly_stats"/>
        </form>
    </div>

    <hr/>

    <div>
        <form method="get">
            <h1>Monthly stats (by agent) Report</h1>
            <?php if (!empty($monthlyStatsByAgentReportDownloadFile)) { ?>
                <a class="downloadLink" href="./../attachments/<?= basename($monthlyStatsByAgentReportDownloadFile) ?>">
                    Download monthly stats (by agent) report (right click and save link as)
                </a>
            <?php } ?>
            <label> From
                <input type="date"
                       name="monthly_stats_by_agent_from"
                       required
                       value="<?= (isset($_GET['monthly_stats_by_agent_from']) and strtotime($_GET['monthly_stats_by_agent_from'])) ?
                           (new DateTime($_GET['monthly_stats_by_agent_from']))->format('Y-m-d') : '' ?>"
                />
            </label>
            <label> To
                <input
                    type="date"
                    name="monthly_stats_by_agent_to"
                    required
                    value="<?= (isset($_GET['monthly_stats_by_agent_to']) and strtotime($_GET['monthly_stats_by_agent_to'])) ?
                        (new DateTime($_GET['monthly_stats_by_agent_to']))->format('Y-m-d') : '' ?>"
                />
            </label>

            <input type="submit" value="export"/>
            <input type="hidden" name="report_type" value="monthly_stats_by_agent"/>
        </form>
    </div>

<?php
/**
 * @param $generalReportDownloadFile
 */
function generalReport(&$generalReportDownloadFile)
{
    if (isset($_GET['report_type']) and $_GET['report_type'] == 'general') {
        if (strtotime($_GET['general_from']) and strtotime($_GET['general_to'])) {
            $from = (new DateTime($_GET['general_from']))->format('Y-m-d H:i:s');
            $to = (new DateTime($_GET['general_to']))->format('Y-m-d H:i:s');

            $ticketIds = db_query(" select ost_ticket.ticket_id from ost_ticket
                                    where ost_ticket.created >= '{$from}' and ost_ticket.created <= '{$to}' 
                                    order by ticket_id desc
                                  ");

            $ticketsExportArray = prepareTicketsExport($ticketIds);
            $phpExcelObject = new PHPExcel();
            $phpExcelObject->setActiveSheetIndex(0);
            $activeSheet = $phpExcelObject->getActiveSheet();
            $activeSheet->fromArray($ticketsExportArray, null, 'A1');
            $writer = PHPExcel_IOFactory::createWriter($phpExcelObject, 'Excel2007');
            $writer->save($generalReportDownloadFile = __DIR__ . '/../attachments/general_report_' . time() . '.xlsx');
        }
    }
}

/**
 * @param $scrubbingReportDownloadFile
 */
function scrubbingReport(&$scrubbingReportDownloadFile)
{
    if (isset($_GET['report_type']) and $_GET['report_type'] == 'scrubbing') {
        if (strtotime($_GET['scrubbing_from']) and strtotime($_GET['scrubbing_to'])) {
            $from = (new DateTime($_GET['scrubbing_from']))->format('Y-m-d H:i:s');
            $to = (new DateTime($_GET['scrubbing_to']))->format('Y-m-d H:i:s');

            $ticketIds = db_query(" select ost_ticket.ticket_id from ost_ticket
                                    join ost_ticket__cdata on ost_ticket.ticket_id = ost_ticket__cdata.ticket_id
                                    where ost_ticket.created >= '{$from}' and ost_ticket.created <= '{$to}' 
                                    and ost_ticket__cdata.subject like '%scrub%'
                                    order by ticket_id desc
                                  ");

            $ticketsExportArray = prepareTicketsExport($ticketIds);
            $phpExcelObject = new PHPExcel();
            $phpExcelObject->setActiveSheetIndex(0);
            $activeSheet = $phpExcelObject->getActiveSheet();
            $activeSheet->fromArray($ticketsExportArray, null, 'A1');
            $writer = PHPExcel_IOFactory::createWriter($phpExcelObject, 'Excel2007');
            $writer->save($scrubbingReportDownloadFile = __DIR__ . '/../attachments/scrubbing_report_' . time() . '.xlsx');
        }
    }
}

/**
 * @param $monthlyStatsReportDownloadFile
 */
function monthlyStatsReport(&$monthlyStatsReportDownloadFile)
{
    if (isset($_GET['report_type']) and $_GET['report_type'] == 'monthly_stats') {
        if (strtotime($_GET['monthly_stats_from']) and strtotime($_GET['monthly_stats_to'])) {
            $from = (new DateTime($_GET['monthly_stats_from']))->format('Y-m-d H:i:s');
            $to = (new DateTime($_GET['monthly_stats_to']))->format('Y-m-d H:i:s');

            $ticketIds = db_query(" select ost_ticket.ticket_id from ost_ticket
                                    join ost_ticket__cdata on ost_ticket.ticket_id = ost_ticket__cdata.ticket_id
                                    where ost_ticket.created >= '{$from}' and ost_ticket.created <= '{$to}' 
                                    order by ticket_id desc
                                  ");

            $ticketsExportArray = [['Month', 'All', 'Open', 'Closed']];

            while ($ticketRow = mysqli_fetch_assoc($ticketIds)) {
                $ticket = new Ticket($ticketRow['ticket_id']);

                $ticketMonth = (new DateTime($ticket->getCreateDate()))->format('Y-m');

                if (!isset($ticketsExportArray[$ticketMonth])) {
                    $ticketsExportArray[$ticketMonth] = [
                        'month' => $ticketMonth,
                        'all' => "0",
                        'open' => "0",
                        'closed' => "0"
                    ];
                }

                $ticketsExportArray[$ticketMonth]['all']++;
                if ($ticket->isClosed()) {
                    $ticketsExportArray[$ticketMonth]['closed']++;
                } else {
                    $ticketsExportArray[$ticketMonth]['open']++;
                }
            }

            $phpExcelObject = new PHPExcel();
            $phpExcelObject->setActiveSheetIndex(0);
            $activeSheet = $phpExcelObject->getActiveSheet();
            $activeSheet->fromArray($ticketsExportArray, null, 'A1');
            $writer = PHPExcel_IOFactory::createWriter($phpExcelObject, 'Excel2007');
            $writer->save($monthlyStatsReportDownloadFile = __DIR__ . '/../attachments/monthly_stats_' . time() . '.xlsx');
        }
    }
}

/**
 * @param $monthlyStatsByAgentReportDownloadFile
 */
function monthlyStatsByAgentReport(&$monthlyStatsByAgentReportDownloadFile)
{
    if (isset($_GET['report_type']) and $_GET['report_type'] == 'monthly_stats_by_agent') {
        if (strtotime($_GET['monthly_stats_by_agent_from']) and strtotime($_GET['monthly_stats_by_agent_to'])) {
            $from = (new DateTime($_GET['monthly_stats_by_agent_from']))->format('Y-m-d H:i:s');
            $to = (new DateTime($_GET['monthly_stats_by_agent_to']))->format('Y-m-d H:i:s');

            $ticketIds = db_query(" select ost_ticket.ticket_id from ost_ticket
                                    join ost_ticket__cdata on ost_ticket.ticket_id = ost_ticket__cdata.ticket_id
                                    where ost_ticket.created >= '{$from}' and ost_ticket.created <= '{$to}' 
                                    order by ticket_id desc
                                  ");

            $ticketsExportArray = [['Month', 'Agent', 'All', 'Open', 'Closed']];
            $ticketsExportArrayTempHolder = [];

            while ($ticketRow = mysqli_fetch_assoc($ticketIds)) {
                $ticket = new Ticket($ticketRow['ticket_id']);

                if (!$ticket->getAssignee())
                    continue;

                $ticketMonth = (new DateTime($ticket->getCreateDate()))->format('Y-m');
                $ticketAgent = ($ticket->getAssignee()) ? $ticket->getAssignee()->getName()->name : null;

                if (!isset($ticketsExportArrayTempHolder[$ticketMonth], $ticketsExportArrayTempHolder[$ticketMonth][$ticketAgent])) {
                    $ticketsExportArrayTempHolder[$ticketMonth][$ticketAgent] = [
                        'month' => $ticketMonth,
                        'agent' => $ticketAgent,
                        'all' => "0",
                        'open' => "0",
                        'closed' => "0"
                    ];
                }

                $ticketsExportArrayTempHolder[$ticketMonth][$ticketAgent]['all']++;
                if ($ticket->isClosed()) {
                    $ticketsExportArrayTempHolder[$ticketMonth][$ticketAgent]['closed']++;
                } else {
                    $ticketsExportArrayTempHolder[$ticketMonth][$ticketAgent]['open']++;
                }
            }

            foreach ($ticketsExportArrayTempHolder as $key => $value) {
                foreach ($value as $finalShit) {
                    $ticketsExportArray[] = $finalShit;
                }
            }

            $phpExcelObject = new PHPExcel();
            $phpExcelObject->setActiveSheetIndex(0);
            $activeSheet = $phpExcelObject->getActiveSheet();
            $activeSheet->fromArray($ticketsExportArray, null, 'A1');
            $writer = PHPExcel_IOFactory::createWriter($phpExcelObject, 'Excel2007');
            $writer->save($monthlyStatsByAgentReportDownloadFile = __DIR__ . '/../attachments/monthly_stats_by_agents_' . time() . '.xlsx');
        }
    }
}

/**
 * @param $mysqlQuery
 * @return array
 */
function prepareTicketsExport($mysqlQuery)
{
    $ticketsExportArray = [
        /* Excel Header*/
        ['ID', 'Subject', 'User', 'Department', 'Assignees', 'Status', 'Created', 'Closed', 'Priority', 'Platform', 'Advertiser', 'Publisher']
    ];

    while ($ticketRow = mysqli_fetch_assoc($mysqlQuery)) {
        $ticket = new Ticket($ticketRow['ticket_id']);
        $answers = array_map(function ($item) {
            $value = (is_object($item->getValue())) ? (string)$item->getValue() : $item->getValue();
            return (is_array($value)) ? array_shift($value) : $value;
        }, $ticket->_answers);

        $ticketsExportArray[] = [
            $ticket->getId(),
            $ticket->getSubject(),
            $ticket->getUser()->getUserName(),
            $ticket->getDept()->getName(),
            implode(', ', $ticket->getAssignees()),
            $ticket->getStatus()->getName(),
            $ticket->getCreateDate(),
            $ticket->getCloseDate(),
            $answers['priority'],
            $answers['platform'],
            $answers['advertiser'],
            $answers['publisher'],
        ];
    }
    return $ticketsExportArray;
}

require_once(STAFFINC_DIR . 'footer.inc.php');