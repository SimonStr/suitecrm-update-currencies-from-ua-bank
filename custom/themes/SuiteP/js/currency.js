YAHOO.util.Event.onContentReady("currency_id_select", function () {

    if($("#currency_id_select").length) {
        var currency = $("#currency_id_select").val();
        if (currency != "-99") {
            $.post( "index.php?module=Currencies&action=getTime", { id: currency }, function( data ) {
                $("#last_up_date").remove();
                $("#currency_id_select").after(data);
            });
        }

    }

    $(document).on("change", "#currency_id_select", function(){
        var currency = $(this).val();
        if (currency != "-99") {
            $.post( "index.php?module=Currencies&action=getTime", { id: currency }, function( data ) {
                // console.log(data);
                $("#last_up_date").remove();
                $("#currency_id_select").after(data);
            });
        }
    });
});
YAHOO.util.Event.onContentReady("currency_select", function () {
    if($("#currency_select").length) {
        var currency = $("#currency_select").val();
        if (currency != "-99") {
            $.post( "index.php?module=Currencies&action=getTime", { id: currency }, function( data ) {
                $("#last_up_date").remove();
                $("#currency_select").after(data);
            });
        }

    }

    $(document).on("change", "#currency_select", function(){
        var currency = $(this).val();
        if (currency != "-99") {
            $.post( "index.php?module=Currencies&action=getTime", { id: currency }, function( data ) {
                $("#last_up_date").remove();
                $("#currency_select").after(data);
            });
        }
    });
});