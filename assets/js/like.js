import $ from 'jquery';


$('#saveChanges').click(function () {
    console.log('save Changes clicked');
    let path = $('#evalPath');
    let note = $('#note').val();
    let description = $('#description').val();
    $.ajax({
        url : path.attr('data-path'),
        method: 'POST',
        dataType: 'json',
        data: {note: note, description: description},
        success: function (receivedData) {
            $('#evaluateModal').modal('toggle');
            $('#evaluation').html('');

            $.each(receivedData, function (key, value) {
            let newRow = `
                    <div class="alert alert-info">
                            ${value.date} / ${value.username} :
                            ${value.description}
                    </div>
                `;
            path.prop("disabled",true);
            $('#evaluation').append(
                newRow
            );
            });
        },
        error: function (error) {
            alert(error.message);
        }
    })


})