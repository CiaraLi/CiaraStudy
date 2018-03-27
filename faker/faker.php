<?php

require_once './cassClass.php';
require_once './php-cassandra/php-cassandra.php';
require_once './vendor/autoload.php';


$faker = Faker\Factory::create();

$cass = new cassClass('books_search');

$data = [
    'bookid' => $cass->prseUuid($faker->uuid),
    'companyid' => $cass->prseInt($faker->numberBetween(1, 3)),
    'userid' => $cass->prseInt($faker->numberBetween(1, 3)),
    'isbn' => $faker->isbn13,
    'version' =>$cass->prseInt($faker->numberBetween(1, 1)),
    'status' => $cass->prseInt($faker->numberBetween(0, 3)),
];
$cass->insert($data);
var_dump($cass->fetchAll());
