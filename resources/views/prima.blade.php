<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<?php
function apakahPrima($angka)
{
    $pembagi = 0;
    for ($i = 1; $i <= $angka; $i++) {
        if ($angka % $i == 0) {
            $pembagi++;
        }
    }

    if ($pembagi == 2) {
        echo "<td class='bg-warning'>$angka</td>";
    } else {
        echo "<td>$angka</td>";
    }
}
function generateTable($rows, $cols)
{
    $number = 1;
    for ($i = 0; $i < $rows; $i++) {
        echo "<tr class='text-center'>";
        for ($j = 0; $j < $cols; $j++) {
            apakahPrima($number);
            $number++;
        }
        echo "</tr>";
    }
}
?>

<body>
    <div class="container">
        <h1 class="text-center">Tabel Bilangan Prima</h1>
        <table class="table table-bordered border-dark">
            <?php generateTable(10, 10) ?>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>