<?php

require_once __DIR__ . '/staff.inc.php';
require_once(STAFFINC_DIR . 'header.inc.php');
require_once __DIR__ . '/../include/class.ticket.php';

$errors = [];
$generalReportDownloadFile = null;
$scrubbingReportDownloadFile = null;

generalReport($generalReportDownloadFile);
scrubbingReport($scrubbingReportDownloadFile);
?>

    <div>
        <?php if (!empty($generalReportDownloadFile)) { ?>
            <a href="./../attachments/<?= basename($generalReportDownloadFile) ?>">
                Download general report (right click and save link as)
            </a>
        <?php } ?>
        <form method="get">
            <h1>General Report</h1>
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

    <div>
        <?php if (!empty($scrubbingReportDownloadFile)) { ?>
            <a href="./../attachments/<?= basename($scrubbingReportDownloadFile) ?>">
                Download scrubbing report (right click and save link as)
            </a>
        <?php } ?>
        <form method="get">
            <h1>Scrubbing tickets Report</h1>
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