$(document).ready(function () {

    (function myLoop(i) {
        setTimeout(function () {

                //alert (document.location.hostname+'/getUsersInSectorsQty');
            $.ajax({
                method: 'POST',
                url: '/getUsersInSectorsQty'
            }).done(function (data) {

                $.each(data, function (index, value) {



                    // console.log(index);
                    // console.log(value);

                    for (company in value) {
                        //console.log(index+company, value[company])
                        $('#'+index+company).html(value[company]);
                    }
                    for (company in value) {
                        //console.log(index+company, value[company])
                        $('.'+index+company).html(value[company]);
                    }

                    // console.log(value['gs']);
                    // console.log(data['PACK_IS']['lamoda']);
                    // console.log(data['PACK_IS']['total']);
                    // console.log(data['PACK_IS']['gs']);

                });
            });

            if (i) myLoop(i); // decrement i and call myLoop again if i > 0
        }, 5000)
    })(10); // pass the number of iterations as an argument

});