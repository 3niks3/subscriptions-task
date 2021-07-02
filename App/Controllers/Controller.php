<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\MasterController;
use App\Core\Validator;
use App\Models\Subscription;
use phpDocumentor\Reflection\Types\Integer;

class Controller extends MasterController
{

    public function index()
    {
        $this->view('subscribe');

    }

    public function storeSubscription()
    {
        $email = trim($this->request->get('post', 'email'));
        $terms = $this->request->get('post', 'agree');

        $fields = ['email' => $email , 'terms' => $terms];
        $rules = [
            'email' => ['required', 'email', 'excludeColombia'],
            'terms' => ['required']
        ];
        $messages = [
            'email' => [
                'required' => 'Email address is required',
                'email' => 'Please provide a valid e-mail address',
                'excludeColombia' => 'We are not accepting subscriptions from Colombia emails',
            ],
            'terms' => [
                'required' => 'You must accept the terms and conditions',
            ]
        ];

        $validator = new Validator($fields,$rules,$messages);
        $validator->validate();

        //respond errors
        if($validator->failed() && $this->request->isAjax()) {

            $messages = $validator->getFirstMessages();
            echo json_encode(['status' => false, 'messages' => $messages]);
            die();

        } elseif($validator->failed() && !$this->request->isAjax()) {

            $messages = $validator->getFirstMessages();
            $this->request->set('flush', 'form_errors', $messages);
            header('Location: /');
            die();

        }

        //store data in database
        $connection = Database::getConnection();

        $query= $connection->prepare('INSERT INTO subscription (email) VALUES (?)');
        $query->execute([$email]);

        //respond success

        if($this->request->isAjax()) {

            echo json_encode(['status' => true, 'messages' => []]);
            die();

        } else {

            $this->request->set('flush', 'subscription_success', true);
            header('Location: /');
            die();

        }

    }
    
    public function members()
    {
        $this->view('subscribe_members');
    }

    public function membersGetData()
    {
        $records_per_page = 10;

        //get params form post
        $page = (int) $this->request->get('post', 'page')??1;
        $page = ($page <= 0)?1:$page;

        $filter = $this->request->get('post', 'filter')??false;

        $order_column = $this->request->get('post', 'order_column')??false;
        $order_column = in_array(strtolower($order_column), ['subscribed', 'email']) ? $order_column : 'subscribed';

        $order_direction = $this->request->get('post', 'order_direction')??false;
        $order_direction = in_array(strtoupper($order_direction), ['DESC', 'ASC']) ? $order_direction : 'DESC';

        $search_string = trim($this->request->get('post', 'search')??false);

        //set where conditions
        $where = [];
        $whereValues = [];

        $filter_options = Subscription::getSubscriptionEmailProviders();

        if(!empty($filter) && in_array($filter, $filter_options)) {
            $where[] = "(SUBSTRING_INDEX(SUBSTR(email, INSTR(email, '@') + 1),'.',1)) = ?";
            $whereValues[] = $filter;
        }

        if(!empty($search_string)) {
            $where[] = 'email like ?';
            $whereValues[] = '%'.$search_string.'%';
        }

        $total_records = Subscription::getTotalRecords($where, $whereValues);
        $total_pages = ceil($total_records/$records_per_page);
        $page = ($page > $total_pages) ? $total_pages : $page;

        $limit = $records_per_page;
        $offset = $page*$records_per_page-$records_per_page;

        //get subscription data
        $query_string = [];
        $query_string[] = 'Select * from subscription';

        if(!empty($where)) {
            $query_string[] = ' WHERE '.implode(' and ',$where);
        }

        $query_string[] = "Order by $order_column $order_direction";
        $query_string[] = "LIMIT $limit OFFSET $offset";
        $query_string = implode(' ',$query_string);

        $connection = Database::getConnection();
        $data = $connection->prepare($query_string);
        $data->execute($whereValues);
        $data = $data->fetchAll(\PDO::FETCH_ASSOC);


        echo json_encode([
            'data' => $data,
            'page' => $page,
            'totalPages' => $total_pages,
            'filters' => $filter_options,
            'active_filter' => $filter,
        ]);
        die();
    }

    public function deleteSubscription()
    {
        $items = json_decode($this->request->get('post','items'));
        $items_count = count($items);

        $where = array_fill(0, $items_count, '?');
        $where = 'id in ('.implode(', ',$where).')';
        $whereValues = $items;

        $query_string = [];
        $query_string[] = 'Delete from subscription';
        $query_string[] = 'WHERE '.$where;

        $query_string = implode(' ',$query_string);

        $connection = Database::getConnection();
        $data = $connection->prepare($query_string);
        $data->execute($whereValues);

        echo json_encode(['success' => true]);
        die();
    }

    public function exportSubscription()
    {
        $items = json_decode($this->request->get('post','items'));
        $items_count = count($items);

        $where = array_fill(0, $items_count, '?');
        $where = 'id in ('.implode(', ',$where).')';
        $whereValues = $items;

        $query_string = [];
        $query_string[] = 'Select * from subscription';
        $query_string[] = 'WHERE '.$where;

        $query_string = implode(' ',$query_string);

        $connection = Database::getConnection();
        $data = $connection->prepare($query_string);
        $data->execute($whereValues);
        $data = $data->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; Content-Type: "application/octet-stream"');
        header('Content-Disposition: attachment; filename="subscription.csv"');

        $delimiter = ';';

        $f = fopen('php://memory', 'w');

        foreach ($data as $index => $line) {

            if($index == 0) {
                fputcsv($f, array_keys($line), $delimiter);
            }
            fputcsv($f, $line, $delimiter);
        }

        fseek($f, 0);
        fpassthru($f);

        die();
    }
}