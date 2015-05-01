jQuery(document).ready(function($){



    $('input.big-post').on('change', function(){

        var posts_per_page = $('#posts_per_page').val();

        var small = posts_per_page - $(this).val();
        $(this).closest('table').find('input.small-post[value="' + small + '"]').attr('checked', 'checked');
    })

    $('input.small-post').on('click', function(){

        var posts_per_page = $('#posts_per_page').val();

        var big = posts_per_page - $(this).val();
        $(this).closest('table').find('input.big-post[value="' + big + '"]').attr('checked', 'checked');

    })

});


