var base_url = $('input[name=base_url]').val();

$(document).ready(function () {
    //updated by Polaris
    $("#search_doctor").autocomplete({
        minLength: 1,
        source: function( request, response ) {
            $.ajax( {
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                data:{
                    search_doctor: request.term
                },
                url: base_url + '/autocomplete-doctors',
                dataType: "json",
                success: function( data ) {
                    response( data.data );
                }
            });
        },
        focus: function( event, ui ) {
          $( "#search_doctor" ).val( ui.item.name );
          return false;
        },
        select: function( event, ui ) {
          $( "#search_doctor" ).val( ui.item.name );
          $( "#search_type" ).val( ui.item.type );
          $('#gender_all').trigger('click');
          $('input[name="select_specialist"]').prop('checked', false);
          if (ui.item.type=='category') {
            $('input[name="select_specialist"]').each(function(i)
            {
                if ($(this).next().html() == ui.item.name )
                    $(this).prop('checked', true);
            });
          }
          searchDoctor();
          return false;
        }
      })
      .autocomplete( "instance" )._renderItem = function( ul, item ) {
        var searchInput = $( "#search_doctor" ).val();
        var emphasizedText = item.name.replace(new RegExp(searchInput, 'gi'), function(match) {
            return '<b>' + match + '</b>';
        });
        if (item.type=='doctor') {
            return $( "<li>" )
            // .append( "<a class='atc-box' href='"+base_url+item.href+"'><div class='atc-img'><img src='"+base_url+'/images/upload/'+item.img+"'/></div><div class='atc-desc'><div class='atc-title'>" + emphasizedText + "</div><div class='atc-categ'>" + item.category + "</div></div></a>" )
            .append( "<div class='atc-box'><div class='atc-img'><img src='"+base_url+'/images/upload/'+item.img+"'/></div><div class='atc-desc'><div class='atc-title'>" + emphasizedText + "</div><div class='atc-categ'>" + item.category + "</div></div></div>" )
            .appendTo( ul );
        } else {
            return $( "<li>" )
            // .append( "<div class='atc-box'><div class='atc-title'>" + emphasizedText + "</div><div class='atc-categ'>" + item.category + "</div></div>" )
            .append( "<div class='atc-box'><div class='atc-title'>" + emphasizedText + "</div><div class='atc-categ'>[Expertise]</div></div>" )
            .appendTo( ul );
        }
        
      };


    var page = 1;
    $("#more-doctor").click(function ()
    {
        page++;
        $.ajax({
            headers:
            {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '?page=' + page,
            type: 'post',
        })
        .done(function(data){
            if(data.meta.current_page == data.meta.last_page){
                $('#more-doctor').hide();
            } else{
                $('#more-doctor').show();
            }
            $('.dispDoctor').append(data.html);
        })
        .fail(function(jqXHR, ajaxOptions, throwError){
            alert('Server error');
        })
    });
    $('input[name="select_specialist"]').change(function () {
        $('#search_doctor').val('');
    });
    $('#filter_form').change(function () {
        searchDoctor();
    });
});

function geolocate()
{
    var autocomplete = new google.maps.places.Autocomplete(
        /** @type {HTMLInputElement} */(document.getElementById('autocomplete')),
        { types: ['geocode'] });
    google.maps.event.addListener(autocomplete, 'place_changed', function()
    {
        var lat = autocomplete.getPlace().geometry.location.lat();
        var lang = autocomplete.getPlace().geometry.location.lng();
        $('input[name=doc_lat]').val(lat);
        $('input[name=doc_lang]').val(lang);
    });
}

function searchDoctor()
{
    $('.dispDoctor').html('<div class="ajax-loading"><svg version="1.2" height="300" width="600" xmlns="http://www.w3.org/2000/svg" viewport="0 0 60 60" xmlns:xlink="http://www.w3.org/1999/xlink"><path id="pulsar" stroke="rgb(57 108 240)" fill="none" stroke-width="2" stroke-linejoin="round" d="M0,90L250,90Q257,60 262,87T267,95 270,88 273,92t6,35 7,-60T290,127 297,107s2,-11 10,-10 1,1 8,-10T319,95c6,4 8,-6 10,-17s2,10 9,11h210"></path></svg></div>');
    var categories = [];
    var gender = $('input[name="gender_type"]:checked').val();
    var sort_by = $('select[name=sort_by]').val();

    var search_doctor = $('#search_doctor').val();
    var search_type = $('#search_type').val();
    var doc_lat = $('input[name="doc_lat"]').val();
    var doc_lang = $('input[name="doc_lang"]').val();
    $('input[name="select_specialist"]:checked').each(function(i)
    {
        if(categories.indexOf(this.value) === -1) {
            categories.push(this.value);
        }
    });

    $.ajax({
        headers:
        {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        data:{
        search_doctor: search_doctor,
        search_type: search_type,
        doc_lat: doc_lat,
        doc_lang:  doc_lang,
        category:categories,
        gender_type:gender,
        sort_by:sort_by,
        from: 'js'
        },
        url: base_url + '/show-doctors',
        success: function (result)
        {
            $('.dispDoctor').html('');
            $('.dispDoctor').append(result.html);
            $(".myDrop").toggleClass("show");
            $("#more-doctor").hide();
            categories.length = 0;

            $(document).ready(function () {
                $(".add-favourite").click(function () {
                    $(this).toggleClass("active");
                    if ($(this).find("i").hasClass("fa-regular fa-bookmark") && $(this).hasClass("active")) {
                        $(this).find("i").removeClass("fa-regular fa-bookmark");
                        $(this).find("i").addClass("fa fa-bookmark");
                    }
                    else if ($(this).find("i").hasClass("fa fa-bookmark")) {
                        $(this).find("i").removeClass("fa fa-bookmark");
                        $(this).find("i").addClass("fa-regular fa-bookmark");
                    }
                    var doctor_id = $(this).attr('data-id');
                    $.ajax({
                        headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "GET",
                        url: base_url + '/addBookmark/' + doctor_id,
                        success: function (result) {
                            if (result.success == false)
                                window.location.href = base_url + '/patient-login';
                            else {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                })
                                Toast.fire({
                                    icon: 'success',
                                    title: result.msg
                                })
                            }
                        },
                        error: function (err) {
                
                        }
                    });
                });
            });
        },
        error: function (err) {
            $('.dispDoctor').html('<h3 class="text-center">Network Error</h3>');
        }
    });
}
