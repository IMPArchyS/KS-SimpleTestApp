$(function () {
    $('#RESTresponse').addClass('text-danger');
    $('#RESTresponse').removeClass('impGoodResponse');

    let courses = [];
    let addForm = $('#addForm');
    let addButton = $('#addButton');
    let closeButton = $('#closeAddButton');

    $.ajax({
        url: 'apiCourses.php/courses',
        type: 'GET',
        dataType: 'json',
        beforeSend: function (xhr) {
            xhr.overrideMimeType('text/plain; charset=utf-8');
        },
        success: function (data) {
            courses = data;
            if (courses.length === 0) return;
            // loop over the courses using forEach
            courses[0].forEach(function (course) {
                let td1 = document.getElementById(course.den + '-' + course.cas_od.substring(0, 5) + '-' + course.cas_od.substring(0, 2) + ':50');
                let td2 = document.getElementById(course.den + '-' + course.cas_do.substring(0, 2) + ':00' + '-' + course.cas_do.substring(0, 5));
                td1.innerHTML = '<div class="impInfo">' + course.miestnost + '<br>' + course.nazov_akcie + '<br>' + course.vyucujuci + '</div>';
                td1.innerHTML +=
                    '<button id="' +
                    course.id +
                    'editID" class="impEditButton btn btn-primary mx-1 px-2 py-1">EDIT</button> <button id="' +
                    course.id +
                    'DelID" class="impDeleteButton btn btn-primary mx-1 px-2 py-1">X</button>';
                if (course.typ_akcie === 'Prednáška') td1.className = 'impCourse';
                else td1.className = 'impLecture';
                if (td2 !== null && td1 !== td2) {
                    td1.colSpan = 2;
                    td2.parentNode.removeChild(td2);
                }

                $('#' + course.id + 'DelID').on('click', function (event) {
                    event.preventDefault();
                    deleteCourse(course.id);
                });
                $('#' + course.id + 'editID').on('click', function (event) {
                    event.preventDefault();
                    addForm.removeClass('d-none');
                    addButton.addClass('d-none');
                    $('#courseId').val(course.id);
                    $('#day').val(course.den);
                    $('#timeFrom').val(course.cas_od);
                    $('#timeTo').val(convertTime(course.cas_od, course.cas_do));
                    $('#type').val(course.typ_akcie);
                    $('#name').val(course.nazov_akcie);
                    $('#room').val(course.miestnost);
                    $('#teacher').val(course.vyucujuci);
                });
            });
        },
        error: function (error) {
            console.log(error);
        },
    });

    addButton.click(function () {
        if (addForm.hasClass('d-none')) {
            addForm.removeClass('d-none');
            addButton.addClass('d-none');
            $('#courseId').val('');
            $('#day').val('Po');
            $('#timeFrom').val('');
            $('#timeTo').val('2h');
            $('#type').val('Prednáška');
            $('#name').val('');
            $('#room').val('');
            $('#teacher').val('');
        }
    });

    closeButton.click(function () {
        addForm.addClass('d-none');
        addButton.removeClass('d-none');
        $('#courseId').val('');
        $('#day').val('Po');
        $('#timeFrom').val('');
        $('#timeTo').val('2h');
        $('#type').val('Prednáška');
        $('#name').val('');
        $('#room').val('');
        $('#teacher').val('');
    });

    function deleteCourse(courseId) {
        $.ajax({
            url: 'apiCourses.php/courses/' + courseId,
            method: 'DELETE',
            success: function (data) {
                $('#RESTresponse').removeClass('text-danger');
                $('#RESTresponse').addClass('impGoodResponse');
                $('#RESTresponse').text('Úspešne odstránené');
                setTimeout(function () {
                    location.reload();
                }, 500);
            },
            error: function (error) {
                $('#RESTresponse').text('Nepodarilo sa odstrániť akciu');
            },
        });
    }

    addForm.submit(function (event) {
        let timeS = $('#timeFrom').val();
        let hoursS = timeS.split(':')[0];
        let newTimeS = hoursS + ':00';
        let hours = $('#timeTo').val();
        let newTimeE = convertTimeToDb(newTimeS, hours);

        const postData = {
            den: $('#day').val(),
            cas_od: newTimeS,
            cas_do: newTimeE,
            typ_akcie: $('#type').val(),
            nazov_akcie: $('#name').val(),
            miestnost: $('#room').val(),
            vyucujuci: $('#teacher').val(),
        };

        event.preventDefault();
        let itemId = $('#courseId').val();
        if (isNaN(parseInt(itemId))) {
            $.ajax({
                url: 'apiCourses.php/courses',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(postData),
                success: function (data) {
                    $('#RESTresponse').removeClass('text-danger');
                    $('#RESTresponse').addClass('impGoodResponse');
                    $('#RESTresponse').text('Úspešne pridané');
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                },
                error: function (error) {
                    // Handle error
                    $('#RESTresponse').text('Nepodarilo sa pridať akciu');
                    console.log('ERROR FROM POST');
                },
            });
        } else {
            console.log('ID: ' + itemId);
            $.ajax({
                url: 'apiCourses.php/courses/' + itemId,
                method: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(postData),
                success: function (data) {
                    $('#RESTresponse').removeClass('text-danger');
                    $('#RESTresponse').addClass('impGoodResponse');
                    $('#RESTresponse').text('Úspešne upravené');
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                },
                error: function (error) {
                    console.log('ERROR FROM PUT');
                    $('#RESTresponse').text('Nepodarilo sa upraviť akciu');
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                },
            });
        }
    });

    function convertTime(startTime, endTime) {
        let startHour = parseInt(startTime.split(':')[0]);
        let endHour = parseInt(endTime.split(':')[0]);
        let duration = endHour - startHour;

        if (duration === 0) {
            return '1h';
        }
        return duration + 1 + 'h';
    }

    function convertTimeToDb(startTime, hours) {
        let [startHour, startMinute] = startTime.split(':').map(Number);
        let duration = parseInt(hours);
        let endHour = startHour + duration;
        let endMinute = 50;

        if (endHour > 23) {
            endHour -= 24;
        }
        endHour -= 1;

        let endHourStr = endHour.toString().padStart(2, '0');
        let endMinuteStr = endMinute.toString().padStart(2, '0');
        return endHourStr + ':' + endMinuteStr;
    }
});
