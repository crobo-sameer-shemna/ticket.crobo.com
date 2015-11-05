<?php

include_once INCLUDE_DIR.'class.api.php';
include_once INCLUDE_DIR.'class.ticket.php';
include_once INCLUDE_DIR.'class.list.php';

class CustomApiController extends ApiController {




    function getTickets($format) {

        if(!($key=$this->requireApiKey()))
            return $this->exerr(401, __('API key not authorized'));

        $request = $this->getRequest($format);

        $tickets = [];

        if(isset($request['ticket_numbers']) && (count($request['ticket_numbers']) > 0)) {
            $ticket_ids = $request['ticket_numbers'];

            foreach ($ticket_ids as $ticket_id) {
                //$tick_id = Ticket::getIdByNumber($ticket_id);
                $ticket = Ticket::lookupByNumber($ticket_id);
                $ticket = $ticket->ht;
                array_push($tickets, $ticket);
            }
        }else if(isset($request['ticket_ids']) && (count($request['ticket_ids']) > 0)) {
            $ticket_ids = $request['ticket_ids'];

            foreach ($ticket_ids as $ticket_id) {
                $ticket = new Ticket($ticket_id);
                $ticket = $ticket->ht;
                array_push($tickets, $ticket);
            }
        }

        echo '{"status":"200", "data":'.json_encode($tickets).'}';
    }


    function getLists() {

        //if(!($key=$this->requireApiKey()))
        //    return $this->exerr(401, __('API key not authorized'));

        $field_ids = [1,2];
        $fields = [];


        foreach($field_ids as $field_id){
            //array_push($fields, DynamicForm::lookup($field_id));
            $list = DynamicList::lookup($field_id);
            $items_count = $list->getItemCount();
            $items = $list->getAllItems();
            if(isset($list->_list)){
                $list = $list->_list;
            }
            $list = $list->ht;
            $list['items_count'] = $items_count;
            $list['items'] = [];
            foreach($items as $item){
                $list['items'][] = [
                    'id' => $item->getId(),
                    'value' => $item->getValue(),
                    'abbrev' => $item->getAbbrev()
                ];
            }
            array_push($fields, $list);
            //array_push($fields, DynamicListItem::lookup($field_id));

            //$list['items'] = DynamicListItem::objects()->filter(array('list_id'=>$list['id']));

        }

        echo '{"status":"200", "data":'.json_encode($fields).'}';
    }


}

?>
