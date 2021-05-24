
$(document).ready(function ()
{
    $table_body= $("#products_table > tbody > tr");
    $table_body.each(function ()
    {
        // allow changes to number of producst ordered
       $valNumber = $(this).find('input[type=number]');
       $valNumber.change(function ()
       {
           const max = parseInt($(this).attr("max"));
           if($(this).val() > max)
               $(this).val(max);
           $parent = $(this).parent().parent();
           $valForOne = $parent.find('td:eq(1)');
           $valForAll = $parent.find('td:eq(4)');
           const value =  parseFloat($valForOne.text()) * $(this).val();

           $valForAll.text(value.toFixed(2));
       })

    });

    $("#order_form").submit(function (e)
    {
        $total_order = {};
        $total_order.title = $("#title").text();
        $total_order.userName = $("#user_name").text();
        $total_order.userId = $("#user_id").val();
        e.preventDefault();
        $modal_box = $("<div>", {"class": "modal"});
        $("#mod").empty();
        $modal = $("<div>", {"class": "modal-content", "id": "order_confirm"});
        $modal.append($("<h1>", {"text": "Sumár objednávky"}));
        $sum_table = $("<table> <thead><tr><th>Názov produktu</th><th>Cena za kus</th> <th>Počet objednaných kusov</th>" +
            "<th>Celková cena</th></tr></thead>");
        $sum_table_body = $("<tbody>");
        $ordered_items = [];
        $total_val = 0.0;
        $table_body.each(function ()
        {
            $valNumber = $(this).find('input[type=number]');
            if ($valNumber.val() > 0) {
                $ordered_item = {};
                $ordered_item.id = $(this).find('td:first-child').find("input").val();
                $ordered_item.product_name = $(this).find('td:first-child').text();
                $ordered_item.qty = $valNumber.val();
                $ordered_item.single_price = $(this).find('td:eq(1)').text();
                $ordered_item.total_price = $(this).find('td:eq(4)').text();
                $ordered_items.push($ordered_item);
                $row = $("<tr>");

                $row.append(add_table_body($ordered_item.product_name));
                $row.append(add_table_body($ordered_item.single_price));
                $row.append(add_table_body($ordered_item.qty));
                $row.append(add_table_body($ordered_item.total_price));
                $sum_table_body.append($row);
                $total_val = $total_val + parseFloat($ordered_item.total_price);
            }
        })
        $total_order.products = $ordered_items;
        $total_order.total_price = $total_val;
        //console.log($total_order);

        $sum_table.append($sum_table_body);
        $sum_table.append($("<tfoot> <tr> <td colspan='3'> Celková suma</td> <td>" + $total_val.toFixed(2) +"</td></tr>"));
        $modal.append($sum_table);
        //console.log($ordered_items);

        $sum_button = $("<button>", { "text": "Potvrdiť objednávku"});
        $cancel_button = $("<button>", {"text": "Stornovať objednávku"});
        $cancel_button.click(function (e)
        {
            e.preventDefault();
            $("#mod").empty();
        })
        $sum_button.click(function (e)
        {
            e.preventDefault();
            $.ajax(
                {
                    url: "scripts/order.php",
                    type: "post",
                    contentType: "application/json",
                    dataType: "json",
                    data: JSON.stringify($total_order),
                    success: function (data)
                    {

                        $("#mod").empty();

                        $modal_box = $("<div>", {"class": "modal"});
                        $modal = $("<div>", {"class":"modal-content", "text" : "Objednávka potvrdená"});
                        $ok_button = $("<button>", {"text": "OK"});
                        $ok_button.click(function (e)
                        {
                            e.preventDefault();
                            location.reload();
                        })
                        $modal.append($ok_button);
                        $modal_box.append($modal);
                        $("#mod").append($modal_box);

                        //console.log(data);
                        //location.reload();
                    },
                    error: function (request,status,error)
                    {
                        console.log(request.responseText);
                    }
                }
            )
        });

        $modal.append($sum_button);
        $modal.append($cancel_button);
        $modal_box.append($modal);
        $("#mod").append($modal_box);
    })
})

function add_table_body(text)
{
    $tmp = $("<td>", {"text": text});
    return $tmp;
}