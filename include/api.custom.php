<?php

include_once INCLUDE_DIR.'class.api.php';
include_once INCLUDE_DIR.'class.ticket.php';
include_once INCLUDE_DIR.'class.dynamic_forms.php';
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

        $lists_max = 10;
        $lists = [];


        for($list_id = 1; $list_id <= $lists_max; $list_id++){
            $list = DynamicList::lookup($list_id);
            if($list === null){
                break;
            }
            $items_count = $list->getItemCount();
            $allitems = $list->getAllItems();
            if(isset($list->_list)){
                $list = $list->_list;
            }
            $list = $list->ht;
            $list['items_count'] = $items_count;
            $items = [];
            foreach($allitems as $item){
                $items[] = [
                    'id' => $item->getId(),
                    'value' => $item->getValue(),
                    'abbrev' => $item->getAbbrev(),
                    'sortOrder' => $item->getSortOrder()
                ];
            }

            //sort modes: Alpha, -Alpha (reverse alpha), SortCol
            $sort_mode = str_replace('-', '_', $list['sort_mode']);
            usort($items, array($this, 'itemSort_'.$sort_mode));
            $list['items'] = $items;
            array_push($lists, $list);

            //array_push($fields, DynamicListItem::lookup($field_id));
            //$list['items'] = DynamicListItem::objects()->filter(array('list_id'=>$list['id']));

        }

        echo '{"status":"200", "data":'.json_encode($lists).'}';
    }

    function getForms() {

        //if(!($key=$this->requireApiKey()))
        //    return $this->exerr(401, __('API key not authorized'));

        $forms_max = 10;
        $forms = [];


        for($form_id = 1; $form_id <= $forms_max; $form_id++){

            $form = DynamicForm::lookup($form_id);
            if($form === null){
                break;
            }
            $allfields = $form->getFields();
            $form = $form->ht;
            $fields = [];
            foreach($allfields as $field){
                $fields[] = $field->ht;
            }
            $form['fields_count'] = count($fields);
            $form['fields'] = $fields;
            array_push($forms, $form);
        }
        echo '{"status":"200", "data":'.json_encode($forms).'}';
    }

    /* private helper functions */

    function itemSort_SortCol( $a, $b ) {//sort by sortOrder
        return $a['sortOrder'] == $b['sortOrder'] ? 0 : ( $a['sortOrder'] > $b['sortOrder'] ) ? 1 : -1;
    }

    function itemSort_Alpha($a, $b) {//sort alphabetically
        return strcmp($a["value"], $b["value"]);
    }

    function itemSort__Alpha($a, $b) {//sort reverse alphabetically
        return strcmp($b["value"], $a["value"]);
    }

}

?>
