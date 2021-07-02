<?php

?>
<html>
<head>
    <title>Test</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/css/styles.css">

    <style>
        .container{
            padding:100px
        }

        .gap-10{
            gap: 10px;
        }



        .filters *,
        .dynamicTable-operations *
        {
            display: inline-block;
            width: auto;
            padding: 10px;
        }

        .filters > button.filter-button.active{
            background-color: #4066A5;
            color: #fff;
            border-color: #4066A5;
            border-style: solid;
        }

        .dynamicTable-table{
            border: solid thin black;
            border-collapse: collapse;
            text-align: center;
        }

        .dynamicTable-table td{
            padding: 5px;
        }

        .pagination {
            display: inline-block;
        }

        .dynamicTable-pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
        }

        .dynamicTable-pagination a.active {
            background-color: #4066A5;
            color: white;
        }

        .pagination a:hover:not(.active) {background-color: #ddd;}

    </style>

</head>
<body>
<div class="container" style="padding:100px">

    <div class="row gap-10">

        <div class="row">
            <h1>Subscribed Memebers</h1>
        </div>

        <div class="row gap-10" id="dynamic-table-container">

            <div class="row filters gap-10 dynamicTable-filters">

            </div>

            <div class="row dynamicTable-operations">
                <div class="col">
                    <label>Order field</label>
                    <select id="order-column" class="filter-select">
                        <option value="subscribe">Date</option>
                        <option value="email" >Email</option>
                    </select>
                    <label>Order direction</label>
                    <select id="order-direction" class="filter-select">
                        <option value="DESC" >DESC</option>
                        <option value="ASC">ASC</option>
                    </select>
                </div>
                <div class="col" style="margin-left:auto">
                    <button type="button" id="export-button">Export (Selected records)</button>
                    <button type="button" id="delete-button">Delete (Selected records)</button>
                    <input type="text" id="search-input">
                    <button type="button" id="search-button">Search</button>
                </div>

            </div>

            <div class="row">
                <table class="dynamicTable-table" border="1">
                    <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Email</th>
                            <th>Subscribed</th>
                        </tr>
                    </thead>

                </table>
            </div>

            <div class="row">
                <div class="dynamicTable-pagination">

                </div>
            </div>

        </div>

    </div>
</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.1/underscore-min.js" integrity="sha512-ZuOjyqq409+q6uc49UiBF3fTeyRyP8Qs0Jf/7FxH5LfhqBMzrR5cwbpDA4BgzSo884w6q/+oNdIeHenOqhISGw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.js" integrity="sha512-otOZr2EcknK9a5aa3BbMR9XOjYKtxxscwyRHN6zmdXuRfJ5uApkHB7cz1laWk2g8RKLzV9qv/fl3RPwfCuoxHQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/js/dynamicTable.js"></script>
<script>
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
</script>
<script>

    var dynamicTable = new DynamicTable('#dynamic-table-container');

    function loadTable()
    {
        var data = {}

        //get current page
        let page = $('div.dynamicTable-pagination a.active').first();
        data.page = (page && page.length > 0)?page.attr('data-page'):1;

        //get filter value
        let filter = $('div.dynamicTable-filters button.filter-button.active').first();
        data.filter= (filter && filter.length > 0)?filter.attr('data-filter-name'):'';

        //get order Values
        data.order_column = $('#order-column').val();
        data.order_direction = $('#order-direction').val();

        //get search value
        data.search = $('#search-input').val();

        let formData = new FormData();
        formData.append('page',  data.page);
        formData.append('filter',  data.filter);
        formData.append('order_column',  data.order_column);
        formData.append('order_direction',  data.order_direction);
        formData.append('search',  data.search);

        axios.post('membersGetData',formData)
            .then(function(response){

                dynamicTable.drawTable(response.data.data, response.data.page,response.data.totalPages);
                dynamicTable.drawFilters(response.data.active_filter, response.data.filters);

            }).catch(function (error) {});

    }


    $('#search-button').click(function(e){
        loadTable();
    })

    $(document).on('click','div.dynamicTable-pagination a',function(e){
        e.preventDefault();

        $('div.dynamicTable-pagination a').removeClass('active');
        $(this).addClass('active');

        loadTable();

    })

    $(document).on('click','div.dynamicTable-filters button.filter-button',function(e){
        e.preventDefault();

        if($(this).hasClass('active')) {
            $('div.filters button').removeClass('active')
        }else {
            $('div.filters button').removeClass('active')
            $(this).addClass('active');
        }

        loadTable();
    })

    $('div.dynamicTable-operations select.filter-select').change(function(e){
        loadTable();
    })

    $('button#delete-button').click(function(e){

        let items = [];

        let selected_checkboxes = $('table.dynamicTable-table input.items-checbox:checked').each(function(index, element){
            items.push($(element).attr('data-item'));
        })

        if(items.length <= 0) {
            alert('No subscriptions has been selected');
            return false;
        }

        if(!confirm('Are you sure you want to remove selected subscriptions')) {
            return false
        }

        let formData = new FormData();
        formData.append('items',  JSON.stringify(items) )


        axios.post('/deleteSubscription', formData)
        .then(function(response){
            loadTable();
        })
        .catch(function(error){
            console.log(error)
        });

    });

    $('button#export-button').click(function(e){
        let items = [];

        let selected_checkboxes = $('table.dynamicTable-table input.items-checbox:checked').each(function(index, element){
            items.push($(element).attr('data-item'));
        })

        if(items.length <= 0) {
            alert('No subscriptions has been selected');
            return false;
        }

        let formData = new FormData();
        formData.append('items',  JSON.stringify(items) )

        axios({
            url:'/exportSubscription',
            method:'post',
            data:formData,
            responseType: 'blob',
            })
            .then(function(response){
                let url = window.URL.createObjectURL(new Blob([response.data]));
                let link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'subscription.csv');
                document.body.appendChild(link);
                link.click();
                link.remove()

            })
            .catch(function(error){
                console.log(error)
            });

        console.log(items);
    });


    $(document).ready(function(e){
        loadTable();
    });
</script>

</body>
</html>
