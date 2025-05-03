<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../node_modules/jquery/dist/jquery.min.js"></script>
    <title>Rozvrh</title>
</head>

<body>
    <?php
    include_once "header.php";
    ?>
    <div class="container impContainer text-center">
        <h1 class="impFontH">Rozvrh</h1>
        <div class="table-responsive">
            <table class="table table-dark table-striped responsive-table" id="timetableMain">
                <thead>
                    <tr>
                        <?php
                        echo "<th scope=\"col\">Deň</th>";
                        for ($hour = 8; $hour <= 19; $hour++) {
                            for ($minute = 0; $minute <= 50; $minute += 100) {
                                $time = sprintf("%02d:%02d-%02d:%02d", $hour, $minute, $hour, $minute + 50);
                                echo "<th scope=\"col\">$time</th>";
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <tbody id="timetableBody">
                    <?php
                    $days = ['Po', 'Ut', 'St', 'Stv', 'Pi'];
                    for ($day = 0; $day < 5; $day++) {
                        echo "<tr>";
                        if ($days[$day] == 'Stv')
                            echo "<th scope=\"row\">Št</th>";
                        else
                            echo "<th scope=\"row\">$days[$day]</th>";
                        for ($hour = 8; $hour <= 19; $hour++) {
                            for ($minute = 0; $minute <= 50; $minute += 100) {
                                $time = sprintf("%02d:%02d-%02d:%02d", $hour, $minute, $hour, $minute + 50);
                                echo "<td id=\"$days[$day]-$time\"></td>";
                            }
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <h5 id="RESTresponse" class="my-3 impBold text-danger"></h5>
        <button id="addButton" class="impGreenButton my-3 btn btn-primary">Pridať akciu</button>

        <form id="addForm" class="d-none">
            <div class="form-group my-2 col-9 mx-auto d-none">
                <label class="font-weight-bold impFontW fs-5" for="courseId">ID</label>
                <input type="number" class="form-control text-light impSelect" id="courseId" name="courseId">
            </div>
            <div class="form-group my-2 col-9 mx-auto">
                <label class="font-weight-bold impFontW fs-5" for="day">Deň</label>
                <select class="form-control text-light impSelect form-select" id="day" name="day">
                    <option value="Po" selected>Pondelok</option>
                    <option value="Ut">Utorok</option>
                    <option value="St">Streda</option>
                    <option value="Stv">Štvrtok</option>
                    <option value="Pi">Piatok</option>
                </select>
            </div>
            <div class="form-group my-2 col-9 mx-auto">
                <label class="font-weight-bold impFontW fs-5" for="timeFrom">Čas od</label>
                <input type="time" class="impTimeControl form-control text-light impSelect" id="timeFrom"
                    name="timeFrom" maxlength="5">
            </div>
            <div class="form-group
        my-2 col-9 mx-auto">
                <label class="font-weight-bold impFontW fs-5" for="timeTo">Dĺžka výučby</label>
                <select class="form-control text-light impSelect form-select" id="timeTo" name="timeTo">
                    <option value="2h" selected>2 Hodiny</option>
                    <option value="1h">1 Hodina</option>
                </select>
            </div>
            <div class="form-group my-2 col-9 mx-auto">
                <label class="font-weight-bold impFontW fs-5" for="type">Typ akcie</label>
                <select class="form-control text-light impSelect form-select" id="type" name="type">
                    <option value="Prednáška" selected>Prednáška</option>
                    <option value="Cvičenie">Cvičenie</option>
                </select>
            </div>
            <div class="form-group my-2 col-9 mx-auto">
                <label class="font-weight-bold impFontW fs-5" for="name">Názov akcie</label>
                <input type="text" class="form-control text-light impSelect" id="name" name="name">
            </div>
            <div class="form-group my-2 col-9 mx-auto">
                <label class="font-weight-bold impFontW fs-5" for="room">Miestnosť</label>
                <input type="text" class="form-control text-light impSelect" id="room" name="room">
            </div>
            <div class="form-group my-2 col-9 mx-auto">
                <label class="font-weight-bold impFontW fs-5" for="teacher">Vyučujúci</label>
                <input type="text" class="form-control text-light impSelect" id="teacher" name="teacher">
            </div>
            <div class="form-group my-2 col-9 mx-auto">
                <button id="submitAddButton" type="submit" class="impButton my-3 btn btn-primary">Potvrdiť</button>
                <button id="closeAddButton" type="button" class="impGreenButton my-3 btn btn-primary">Zavrieť</button>
            </div>
        </form>

    </div>

    <?php
    include_once "footer.php";
    ?>
    <script src="../js/timetable.js"></script>
</body>

</html>