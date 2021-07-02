<?php
namespace App\Models;

use App\Core\Database;

class Subscription
{
    public static function getSubscriptionEmailProviders()
    {
        $connection = Database::getConnection();

        //get available filters
        $query_string = "SELECT DISTINCT (SUBSTRING_INDEX(SUBSTR(email, INSTR(email, '@') + 1),'.',1)) as email_providers FROM subscription";
        $filter_options = $connection->prepare($query_string);
        $filter_options->execute();
        $filter_options = $filter_options->fetchAll(\PDO::FETCH_ASSOC);

        $filter_options= array_column($filter_options,'email_providers');

        return $filter_options;
    }

    public static function getTotalRecords($where, $whereValues)
    {
        $connection = Database::getConnection();

        $query_string = [];
        $query_string[] = 'Select count(*) as cnt from subscription';

        if(!empty($where)) {
            $query_string[] = ' WHERE '.implode(' and ',$where);
        }

        $query_string = implode(' ',$query_string);

        $total_records = $connection->prepare($query_string);
        $total_records->execute($whereValues);
        $total_records = $total_records->fetch(\PDO::FETCH_ASSOC);

        $total_records = $total_records['cnt'];

        return $total_records;
    }
}