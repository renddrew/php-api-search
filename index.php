<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>JSON Data Search</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>

    <h1>Personnel Search</h1>
    <p>Search by job title (mock data)</p>

    <form method="GET">
        <input type="search" name="term">
        <input type="submit" value="Search">
    </form>

    <?php
        require_once('search_api_data.php');
        $search = new dataSearch;
        echo $search->data_search(); 
    ?>

    </body>
</html>
