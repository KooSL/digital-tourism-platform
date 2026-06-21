<?php
function getTrips($conn)
{

    $result = mysqli_query($conn, "
SELECT *
FROM tours
WHERE status=1
");

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

function getFlights($conn)
{

    $result = mysqli_query($conn, "
SELECT *
FROM flights
WHERE status=1
");

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

function getBuses($conn)
{

    $result = mysqli_query($conn, "
SELECT *
FROM buses
WHERE status=1
");

    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

$websiteData = [

    "tours" => getTrips($conn),

    "flights" => getFlights($conn),

    "buses" => getBuses($conn)

];


$context = json_encode($websiteData);
