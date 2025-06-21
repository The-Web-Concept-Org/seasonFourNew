$(document).ready(function () {
  document.onkeyup = function (e) {
    if (e.altKey && e.which == 13) {
      //enter press
      $("#addProductPurchase").trigger("click");
      // subAmount();
    }
    if (e.altKey && e.which == 83) {
      //s press
      $("#sale_order_btn").click();
    }
    if (e.altKey && e.which == 80) {
      //s press
      Swal.clickConfirm();
    }
  };
  $("#formData").on("submit", function (e) {
    e.preventDefault();
    var form = $("#formData");
    $.ajax({
      type: "POST",
      url: form.attr("action"),
      data: form.serialize(),
      dataType: "json",
      beforeSend: function () {
        $("#formData_btn").prop("disabled", true);
      },
      success: function (response) {
        if (response.sts == "success") {
          $("#formData")[0].reset();
          $("#tableData").load(location.href + " #tableData > *");
          $(".modal").modal("hide");

          Swal.fire({
            icon: "success",
            title: response.msg,
            showConfirmButton: false,
            timer: 1500,
          });

          setTimeout(() => location.reload(), 1000);
        } else {
          Swal.fire({
            icon: "error",
            title: response.msg,
            showConfirmButton: false,
            timer: 1500,
          });
        }
        $("#formData_btn").prop("disabled", false);
      },
    });
  });

  $("#formData1").on("submit", function (e) {
    e.stopPropagation();
    e.preventDefault();
    var form = $("#formData1");
    $.ajax({
      type: "POST",
      url: form.attr("action"),
      data: form.serialize(),
      dataType: "json",
      beforeSend: function () {
        $("#formData1_btn").prop("disabled", true);
      },
      success: function (response) {
        if (response.sts == "success") {
          $("#formData1").each(function () {
            this.reset();
          });
          //$("#tableData").load(location.href+" #tableData");
          $(".modal").modal("hide");
        }
        $("#formData1_btn").prop("disabled", false);

        $("#tableData1").load(location.href + " #tableData1 > *");

        sweeetalert(response.msg, response.sts, 1500);
      },
    }); //ajax call
  }); //main
  $("#formData2").on("submit", function (e) {
    e.stopPropagation();
    e.preventDefault();
    var form = $("#formData2");
    $.ajax({
      type: "POST",
      url: form.attr("action"),
      data: form.serialize(),
      dataType: "json",
      beforeSend: function () {
        $("#formData2_btn").prop("disabled", true);
      },
      success: function (response) {
        if (response.sts == "success") {
          $("#formData2").each(function () {
            this.reset();
          });
          //$("#tableData").load(location.href+" #tableData");
          $(".modal").modal("hide");
        }
        $("#formData2_btn").prop("disabled", false);

        $("#tableData2").load(location.href + " #tableData2 > *");

        sweeetalert(response.msg, response.sts, 1500);
      },
    }); //ajax call
  }); //main
  $("#sale_order_fm").on("submit", function (e) {
    e.preventDefault();

    var form = $("#sale_order_fm")[0];
    var formData = new FormData(form);

    for (var pair of formData.entries()) {
      console.log(pair[0] + ': ' + pair[1]);
    }
    const $btn = $("#sale_order_btn");
    const $spinner = $btn.find(".spinner-border");
    const $text = $btn.find(".btn-text");

    $.ajax({
      type: "POST",
      url: $(form).attr("action"),
      data: formData,
      dataType: "json",
      processData: false,
      contentType: false,
      beforeSend: function () {
        // Show loader
        $spinner.removeClass("d-none");
        $text.addClass("d-none");
        $btn.prop("disabled", true);
      },
      success: function (response) {
        // Hide loader
        resetButton();

        if (response.sts == "success") {
          $("#sale_order_fm")[0].reset();
          $("#purchase_product_tb").html("");
          $("#product_grand_total_amount").html("");
          $("#product_total_amount").html("");

          Swal.fire({
            title: response.msg,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Print`,
            denyButtonText: `Add New`,
          }).then((result) => {
            if (result.isConfirmed) {
              window.open(
                "print_sale.php?id=" +
                response.order_id +
                "&type=" +
                response.type,
                "_blank"
              );
              location.reload();
            } else if (
              result.isDenied ||
              result.dismiss === Swal.DismissReason.cancel
            ) {
              location.reload();
            }
          });
        } else if (response.sts == "error") {
          sweeetalert(response.msg, response.sts, 1500);
        }
      },
      error: function (xhr) {
        console.log("AJAX error:", xhr.responseText);
      },
      complete: function () {
        // Re-enable and reset button UI regardless of outcome
        resetButton();
      },
    });

    function resetButton() {
      $spinner.addClass("d-none");
      $text.removeClass("d-none");
      $btn.prop("disabled", false);
    }
  });

  $("#credit_order_client_name").on("change", function () {
    var value = $("#credit_order_client_name :selected").data("id");
    var contact = $("#credit_order_client_name :selected").data("contact");
    $("#customer_account").val(value);
    $("#client_contact").val(contact);
  });

  $("#add_product_fm").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);
    var fd = new FormData(this);

    $.ajax({
      url: form.attr("action"),
      type: form.attr("method"),
      data: fd,
      dataType: "json",
      contentType: false,
      processData: false,
      beforeSend: function () {
        $("#add_product_btn").prop("disabled", true);
      },
      success: function (response) {
        console.log("click");
        sweeetalert(response.msg, response.sts, 1500);
        $("#add_product_btn").prop("disabled", false);
        var product_add_from = $("#product_add_from").val();
        if (product_add_from == "modal") {
          $("#get_product_name").load(location.href + " #get_product_name > *");
          $("#add_product_modal").modal("hide");
        }

        console.log(response.sts);
        if (response.sts == "success") {
          $("#add_product_fm").each(function () {
            this.reset();
            location.reload();
          });
        }
      },
    }); //ajax call
  }); //main

  $("#voucher_general_fm").on("submit", function (e) {
    e.preventDefault();
    var form = $("#voucher_general_fm");
    $.ajax({
      type: "POST",
      url: form.attr("action"),
      data: form.serialize(),
      dataType: "json",
      beforeSend: function () {
        $("#voucher_general_btn").prop("disabled", true);
      },
      success: function (response) {
        if (response.sts == "success") {
          $("#voucher_general_fm").each(function () {
            this.reset();
          });
          $("#tableData").load(location.href + " #tableData");
        }
        $("#voucher_general_btn").prop("disabled", false);

        Swal.fire({
          title: response.msg,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: `Print`,
          denyButtonText: `Add New`,
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            Swal.fire({
              title: "Which type of do you Print?",
              showDenyButton: true,
              showCancelButton: true,
              confirmButtonText: `Debit`,
              denyButtonText: `Credit`,
              cancelButtonText: "Both",
            }).then((result) => {
              if (result.isConfirmed) {
                window.open(
                  "print_voucher.php?type=debit&voucher_id=" +
                  response.voucher_id,
                  "_blank"
                );
              } else if (result.isDenied) {
                window.open(
                  "print_voucher.php?type=credit&voucher_id=" +
                  response.voucher_id,
                  "_blank"
                );
              } else {
                window.open(
                  "print_voucher.php?type=both&voucher_id=" +
                  response.voucher_id,
                  "_blank"
                );
              }
            });
            //
          } else if (result.isDenied) {
            location.reload();
          }
        });
      },
    }); //ajax call
  }); //main
  $("#voucher_expense_fm").on("submit", function (e) {
    e.preventDefault();
    var form = $("#voucher_expense_fm");
    $.ajax({
      type: "POST",
      url: form.attr("action"),
      data: form.serialize(),
      dataType: "json",
      beforeSend: function () {
        $("#voucher_expense_btn").prop("disabled", true);
      },
      success: function (response) {
        if (response.sts == "success") {
          $("#voucher_expense_fm").each(function () {
            this.reset();
          });
          $("#tableData").load(location.href + " #tableData");
        }
        $("#voucher_expense_btn").prop("disabled", false);
        //    sweeetalert(response.msg,response.sts,1500);
        Swal.fire({
          title: response.msg,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: `Print`,
          denyButtonText: `Add New`,
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            Swal.fire({
              title: "Which type of do you Print?",
              showDenyButton: true,
              showCancelButton: true,
              confirmButtonText: `Debit`,
              denyButtonText: `Credit`,
              cancelButtonText: "Both",
            }).then((result) => {
              if (result.isConfirmed) {
                window.open(
                  "print_voucher.php?type=debit&voucher_id=" +
                  response.voucher_id,
                  "_blank"
                );
              } else if (result.isDenied) {
                window.open(
                  "print_voucher.php?type=credit&voucher_id=" +
                  response.voucher_id,
                  "_blank"
                );
              } else {
                window.open(
                  "print_voucher.php?type=both&voucher_id=" +
                  response.voucher_id,
                  "_blank"
                );
              }
            });
          } else if (result.isDenied) {
            location.reload();
          }
        });
      },
    }); //ajax call
  }); //main
  $("#voucher_single_fm").on("submit", function (e) {
    e.preventDefault();
    var form = $("#voucher_single_fm");
    $.ajax({
      type: "POST",
      url: form.attr("action"),
      data: form.serialize(),
      dataType: "json",
      beforeSend: function () {
        $("#voucher_single_btn").prop("disabled", true);
      },
      success: function (response) {
        if (response.sts == "success") {
          $("#voucher_single_fm").each(function () {
            this.reset();
          });
          $("#tableData").load(location.href + " #tableData");
        }
        $("#voucher_single_btn").prop("disabled", false);
        sweeetalert(response.msg, response.sts, 1500);
        Swal.fire({
          title: response.msg,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: `Print`,
          denyButtonText: `Add New`,
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            window.open(
              "print_voucher.php?type=debit&voucher_id=" + response.voucher_id,
              "_blank"
            );
          } else if (result.isDenied) {
            location.reload();
          }
        });
      },
    }); //ajax call
  }); //main
  $("#get_product_code").on("keyup", function () {
    var code = $("#get_product_code").val();
    var credit_sale_type = $("#credit_sale_type").val();
    var payment_type = $("#payment_type").val();
    //   var podid=  $('#get_product_name :selected').val();
    var branch_id = $("#branch_id").val();
    var purchase_return = $("#purchase_return").val();
    var isSaleReturn = $("#order_return").val() === "order_return"; // Detect Sale Return form
  
  
    $.ajax({
      type: "POST",
      url: "php_action/custom_action.php",
      data: { get_products_list: code, type: "code" },
      dataType: "text",
      success: function (msg) {
        var res = msg.trim();
        $("#get_product_name").empty().html(res);
      },
    }); //ajax call }
    $.ajax({
      type: "POST",
      url: "php_action/custom_action.php",
      data: {
        getPrice: code,
        type: "code",
        credit_sale_type: credit_sale_type,
        payment_type: payment_type,
        branch_id: branch_id,
      },
      dataType: "json",
      success: function (response) {
        $("#get_product_price").val(response.price);
        $("#get_product_sale_price").val(response.price);
        $("#get_product_detail").val(response.description);
        $("#get_final_rate").val(response.final_rate);
        $("#instockQty").html("instock :" + response.qty);
        console.log(response);
        if (
          (!isSaleReturn &&
            (payment_type === "cash_in_hand" || payment_type === "credit_sale")) || (payment_type === "credit_purchase" &&
              purchase_return === "purchase_return") ||
          (payment_type === "cash_purchase" &&
            purchase_return === "purchase_return")
        ) {
          $("#get_product_quantity").attr("max", response.qty);
          $("#addProductPurchase").prop("disabled", response.qty <= 0);
        } else {
          // For Sale Return, allow adding regardless of stock and set a high max
          $("#get_product_quantity").attr("max", 99999999999);
          $("#addProductPurchase").prop("disabled", false);
        }
      },
    }); //ajax call }
  });
}); /*--------------end of-------------------------------------------------------*/
function pending_bills(value) {
  $.ajax({
    url: "php_action/custom_action.php",
    type: "post",
    data: { pending_bills_detils: value },
    dataType: "json",
    success: function (response) {
      $("#bill_customer_name").empty().val(response.client_name);
      $("#order_id").empty().val(response.order_id);
      $("#bill_grand_total").empty().val(response.grand_total);
      $("#bill_paid_ammount").empty().val(response.paid);
      $("#bill_remaining").empty().val(response.due);
      $("#bill_paid").attr("max", response.due);
      $("#bill_paid").empty().val(0);
    },
  });
}
function getCustomer_name(value) {
  $.ajax({
    url: "php_action/custom_action.php",
    type: "post",
    data: { getCustomer_name: value },
    dataType: "text",
    success: function (response) {
      var data = response.trim();
      $("#sale_order_client_name").empty().val(data);
    },
  });
}
function getRemaingAmount() {
  var paid_ammount = $("#paid_ammount").val();
  var product_grand_total_amount = $("#product_grand_total_amount").html();
  var total = product_grand_total_amount - paid_ammount;
  // alert(total)
  $("#remaining_ammount").val(total);
}

function loadProducts(id) {
  $.ajax({
    url: "php_action/custom_action.php",
    type: "post",
    data: { getProductPills: id },
    dataType: "text",
    success: function (response) {
      var data = response.trim();
      $("#products_list").empty().html(data);
    },
  });
}

$("#get_product_name").on("change", function () {
  //var code=  $('#get_product_code').val();
  var code = $("#get_product_name :selected").val();
  var payment_type = $("#payment_type").val();
  var credit_sale_type = $("#credit_sale_type").val();
  var price_type = $("#price_type").val();
  var branch_id = $("#branch_id").val();
  var purchase_return = $("#purchase_return").val();
  var isSaleReturn = $("#order_return").val() === "order_return"; // Detect Sale Return form

  $.ajax({
    type: "POST",
    url: "php_action/custom_action.php",
    data: { get_products_list: code, type: "product" },
    dataType: "text",
    success: function (msg) {
      var res = msg.trim();
      $("#get_product_code").val(res);
    },
  }); //ajax call }
  function fetchProductPrice(price_type) {
    $.ajax({
      type: "POST",
      url: "php_action/custom_action.php",
      data: {
        getPrice: code,
        type: "product",
        credit_sale_type: credit_sale_type,
        payment_type: payment_type,
        price_type: price_type,
        branch_id: branch_id, // Pass either "purchase" or "sale"
      },
      dataType: "json",
      success: function (response) {
        // alert(response.price);
        $("#get_product_price").val(response.price);
        $("#get_product_sale_price").val(response.price);
        $("#get_product_detail").val(response.description);
        $("#get_final_rate").val(response.final_rate);
        $("#instockQty").html("instock :" + response.qty);
        console.log(response);
        if (
          (!isSaleReturn &&
            (payment_type === "cash_in_hand" || payment_type === "credit_sale")) || (payment_type === "credit_purchase" &&
              purchase_return === "purchase_return") ||
          (payment_type === "cash_purchase" &&
            purchase_return === "purchase_return")
        ) {
          $("#get_product_quantity").attr("max", response.qty);
          $("#addProductPurchase").prop("disabled", response.qty <= 0);
        } else {
          // For Sale Return, allow adding regardless of stock and set a high max
          $("#get_product_quantity").attr("max", 99999999999);
          $("#addProductPurchase").prop("disabled", false);
        }
      },
    });
  }

  // Call the function for both purchase and sale prices
  fetchProductPrice(price_type);
  // fetchProductPrice(price_type);
});
$("#product_code").on("change", function () {
  //var code=  $('#get_product_code').val();
  var code = $("#product_code").val();
  if (/^[A-Za-z0-9]+$/.test(code)) {
    $.ajax({
      type: "POST",
      url: "php_action/custom_action.php",
      data: { get_products_code: code },
      dataType: "json",
      success: function (response) {
        if (response.sts == "error") {
          $("#add_product_btn").prop("disabled", true);
          $("#product_code").val("");
          Swal.fire({
            position: "center",
            icon: "error",
            title: response.msg,
            showConfirmButton: true,
          });
        } else {
          $("#add_product_btn").prop("disabled", false);
        }
      },
    });
  } else {
    Swal.fire({
      position: "center",
      icon: "error",
      title:
        "Please Enter Only Alphabets and Number Without Space and Characters",
      showConfirmButton: true,
    });
  }
});
$("#full_payment_check").on("click", function () {
  var checked = $("#full_payment_check").is(":checked");
  var grand = $("#product_grand_total_amount").html();

  if (checked) {
    $("#paid_ammount").val(grand);
  } else {
    $("#paid_ammount").val(0);
  }
  $("#paid_ammount").trigger("keyup");
});
//function addProductPurchase() {

function getRandomInt(max) {
  return Math.floor(Math.random() * max);
}
$("#addProductPurchase").on("click", function () {
  const $btn = $(this);
  const originalHtml = $btn.html();

  // Add loader and disable button
  $btn.html('<i class="fa fa-spinner fa-spin"></i>').prop("disabled", true);

  setTimeout(function () {
    try {
      var currentForm = $btn.closest("form"); // USE $btn INSTEAD OF this
      var get_final_rate =
        currentForm.data("get-final-rate")?.toString() === "true";

      var payment_type = $("#payment_type").val();
      var isSaleReturn = $("#order_return").val() === "order_return";
var isPurchaseReturn = $("#purchase_return").val() === "purchase_return";
      var name = $("#get_product_name :selected").text();
      var price = parseFloat($("#get_product_price").val());
      var sale_price = parseFloat($("#get_product_sale_price").val());
      var final_rate = $("#get_final_rate").val();
      var id = $("#get_product_name").val();
      var code = $("#get_product_code").val();
      var product_quantity = parseInt($("#get_product_quantity").val());
      var max_qty = parseInt($("#get_product_quantity").attr("max")) || 0;
      var pro_type = $("#add_pro_type").val();

      if (
       (payment_type === "cash_purchase" && !isPurchaseReturn ) ||
        (payment_type === "credit_purchase" && !isPurchaseReturn) ||
        isSaleReturn
      ) {
        max_qty = 99999999999;
      }

      if (!isSaleReturn && product_quantity > max_qty) {
        Swal.fire({
          icon: "error",
          title: "Out of Stock",
          text: "Cannot add quantity more than available stock.",
          timer: 1500,
          showConfirmButton: false,
        });
        return;
      }

      var total_price = price * product_quantity;

      if (!id || !product_quantity || !code) {
        Swal.fire({
          icon: "error",
          title: "Missing Information",
          text: "Please make sure to select product and quantity.",
          timer: 1500,
          showConfirmButton: false,
        });
        return;
      }

      // Reset inputs before append (optional timing choice)
      $("#get_product_name").val(null).trigger("change");
      $("#add_pro_type").val("add");
      $("#get_product_code").val("");
      $("#get_product_price").val("");
      $("#get_product_sale_price").val("");
      $("#get_final_rate").val("");
      $("#instockQty").text("instock :0");
      $("#get_product_quantity").val("");
      $("#get_product_code").focus();

      if ($("#product_idN_" + id).length) {
        $(".product_ids").each(function () {
          var quantity = $(this).data("quantity");
          var val = $(this).val();

          if (val === id) {
            var Currentquantity =
              pro_type === "add"
                ? parseInt(quantity) + parseInt(product_quantity)
                : parseInt(product_quantity);

            if (!isSaleReturn && Currentquantity > max_qty) {
              Swal.fire({
                icon: "error",
                title: "Stock Limit Exceeded",
                text: "Cannot add quantity more than available stock.",
                timer: 1500,
                showConfirmButton: false,
              });
              return false;
            }

            var updated_total = price * Currentquantity;

            $("#product_idN_" + id).replaceWith(`
              <tr id="product_idN_${id}">
                <input type="hidden" data-price="${price}" data-quantity="${Currentquantity}" 
                       id="product_ids_${id}" class="product_ids" name="product_ids[]" value="${id}">
                <input type="hidden" id="product_quantites_${id}" name="product_quantites[]" value="${Currentquantity}">
                <input type="hidden" id="product_rate_${id}" name="product_rates[]" value="${price}">
                <input type="hidden" id="product_totalrate_${id}" name="product_totalrates[]" value="${updated_total}">
                <input type="hidden" id="product_salerate_${id}" name="product_salerates[]" value="${updated_total}">
                <td>${code}</td>
                <td>${name}</td>
                <td>${price}</td>
                ${get_final_rate
                ? `<input type="hidden" id="product_final_rate_${id}" name="product_final_rates[]" value="${final_rate}">
                       <td>${final_rate}</td>`
                : ""
              }
                <td>${Currentquantity}</td>
                <td>${updated_total}</td>
                <td>
                  <button type="button" onclick="removeByid('#product_idN_${id}')" class="fa fa-trash text-danger"></button>
                  <button type="button" onclick="editByid(${id}, '${code}', '${price}', '${Currentquantity}', '${final_rate}')" class="fa fa-edit text-success"></button>
                </td>
              </tr>
            `);
          }
        });
      } else {
        $("#purchase_product_tb").append(`
          <tr id="product_idN_${id}">
            <input type="hidden" data-price="${price}" data-quantity="${product_quantity}" 
                   id="product_ids_${id}" class="product_ids" name="product_ids[]" value="${id}">
            <input type="hidden" id="product_quantites_${id}" name="product_quantites[]" value="${product_quantity}">
            <input type="hidden" id="product_rate_${id}" name="product_rates[]" value="${price}">
            <input type="hidden" id="product_totalrate_${id}" name="product_totalrates[]" value="${total_price}">
            <input type="hidden" id="product_salerate_${id}" name="product_salerates[]" value="${sale_price}">
            <td>${code}</td>
            <td>${name}</td>
            <td>${price}</td>
            ${get_final_rate
            ? `<input type="hidden" id="product_final_rate_${id}" name="product_final_rates[]" value="${final_rate}">
                   <td>${final_rate}</td>`
            : ""
          }
            <td>${product_quantity}</td>
            <td>${total_price}</td>
            <td>
              <button type="button" onclick="removeByid('#product_idN_${id}')" class="fa fa-trash text-danger"></button>
              <button type="button" onclick="editByid(${id}, '${code}', '${price}', '${product_quantity}', '${final_rate}')" class="fa fa-edit text-success"></button>
            </td>
          </tr>
        `);
      }

      getOrderTotal();
      $("#purchase_type").change();
      $("#sale_type").change();
    } finally {
      // Re-enable button and remove loader
      $btn.html(originalHtml).prop("disabled", false);
    }
  }, 100);
});

function removeByid(id) {
  $(id).remove();
  getOrderTotal();
}
function getOrderTotal() {
  var payment_type = $("#payment_type").val();
  var total_bill = (grand_total = 0);
  $(".product_ids").each(function () {
    var quantity = $(this).data("quantity");
    var rates = $(this).data("price");
    //console.log(rates);
    total_bill += parseFloat(rates) * parseFloat(quantity);
  });
  var discount = $("#ordered_discount").val();
  var freight = $("#freight").val();
  discount = discount == "" ? (discount = 0) : parseFloat(discount);
  if (payment_type == "cash_in_hand" || payment_type == "credit_sale") {
    freight = freight == "" ? (freight = 0) : parseFloat(freight);
  } else {
    freight = 0;
  }
  //console.log(discount);
  //grand_total=freight+total_bill-total_bill*(discount/100);
  grand_total = freight + total_bill - discount;
  $("#product_total_amount").html(total_bill);
  $("#product_grand_total_amount").html(grand_total);

  if (payment_type == "cash_in_hand" || payment_type == "cash_purchase") {
    $("#paid_ammount").val(grand_total);
    $("#paid_ammount").attr("max", grand_total);
    $("#paid_ammount").prop("required", true);
    if (payment_type == "cash_in_hand") {
      $("#full_payment_check").prop("checked", true);
    }
  } else {
    $("#paid_ammount").val("0");
    $("#paid_ammount").prop("required", false);
  }
  if (grand_total > 0) {
    $("input[name='payment_account'] ").prop("required", true);
  } else {
    $("input[name='payment_account'] ").prop("required", false);
  }

  getRemaingAmount();
  $("#purchase_type").change();
  $("#sale_type").change();
}

function editByid(id, code, price, qty, final_rate, pro_details) {
  // alert(pro_details);
  $("#get_product_name").val(id);

  $("#get_product_code").val(code);
  $("#get_product_quantity").val(qty);
  $("#get_final_rate").val(final_rate);
  let total = price * qty;
  $("#get_product_sale_price").val(total);
  $("#get_product_price").val(price);
  $("#get_product_detail").val(pro_details);
  $("#add_pro_type").val("update");
  var effect = function () {
    return $(".searchableSelect").select2().trigger("change");
  };
  $.when(effect()).done(function () {
    setTimeout(function () {
      $("#get_product_sale_price").val(total);
      $("#get_product_price").val(price);
    }, 500);
  });
}
function getBalance(val, id) {
  setTimeout(function () {
    if (id == "customer_account_exp") {
      var value = $("#customer_account").val();
    } else {
      var value = val;
    }
    $.ajax({
      type: "POST",
      url: "php_action/custom_action.php",
      data: { getBalance: value },
      dataType: "json",
      success: function (response) {
        //alert(response.blnc);
        //var res=msg.trim();

        $("#" + id).html(response.blnc);
        $("#customer_Limit").html(response.custLimit);
        var RLIMIT = Math.abs(response.custLimit - response.blnc);
        //alert(RLIMIT);
        $("#R_Limit").html(RLIMIT);
        $("#R_LimitInput").val(RLIMIT);
      },
    }); //ajax call }
  }, 1000);
}
// ---------------------------order gui---------------------------------------

// var input = document.getElementById("barcode_product");
$("#barcode_product").on("keyup", function (event) {
  // input.addEventListener("keyup", function(event) {
  if (event.keyCode === 13) {
    var value = input.value;
    event.preventDefault();
    addbarcode_product(value, "plus");
    //   console.log(value);
    input.value = "";
  }
});
function addbarcode_product(code, action_value) {
  //$("#ordered_products").append(dbarcode_productata);

  $.ajax({
    url: "php_action/custom_action.php",
    type: "post",
    data: { getProductDetailsBycode: code },
    dataType: "json",
    success: function (res) {
      console.log(action_value);

      if ($("#product_idN_" + res.product_id).length) {
        var jsonObj = [];
        $(".product_ids").each(function (index) {
          var quantity = $(this).data("quantity");
          var val = $(this).val();

          if (val == res.product_id) {
            //$("#product_idN_"+id).remove();

            if (action_value == "plus") {
              var Currentquantity = 1 + parseInt(quantity);
            }
            if (action_value == "minus") {
              var Currentquantity = parseInt(quantity) - 1;
            }

            $("#product_idN_" + res.product_id).replaceWith(
              '<tr id="product_idN_' +
              res.product_id +
              '">\
					<input type="hidden" data-price="' +
              res.current_rate +
              '" data-quantity="' +
              Currentquantity +
              '" id="product_ids_' +
              res.product_id +
              '" class="product_ids" name="product_ids[]" value="' +
              res.product_id +
              '">\
					<input type="hidden" id="product_quantites_' +
              res.product_id +
              '" name="product_quantites[]" value="' +
              Currentquantity +
              '">\
					<input type="hidden" id="product_rates_' +
              res.product_id +
              '" name="product_rates[]" value="' +
              res.current_rate +
              '">\
					<td>' +
              res.product_code +
              "  </td>\
          <td>" +
              res.product_name +
              ' (<span class="text-success">' +
              res.brand_name +
              "</span>) </td>\
					<td>" +
              res.current_rate +
              " </td>\
					<td>" +
              Currentquantity +
              " </td>\
					<td>" +
              res.current_rate * Currentquantity +
              ' </td>\
					<td> <button type="button" onclick="addbarcode_product(`' +
              res.product_code +
              '`,`plus`)" class="fa fa-plus text-success" href="#" ></button>\
						<button type="button" onclick="addbarcode_product(`' +
              res.product_code +
              '`,`minus`)" class="fa fa-minus text-warning" href="#" ></button>\
						<button type="button" onclick="removeByid(`#product_idN_' +
              res.product_id +
              '`)" class="fa fa-trash text-danger" href="#" ></button>\
						</td>\
					</tr>'
            );
          }
          getOrderTotal();
        });
      } else {
        $("#purchase_product_tb").append(
          '<tr id="product_idN_' +
          res.product_id +
          '">\
			          <input type="hidden" data-price="' +
          res.current_rate +
          '"  data-quantity="1" id="product_ids_' +
          res.product_id +
          '" class="product_ids" name="product_ids[]" value="' +
          res.product_id +
          '">\
			          <input type="hidden" id="product_quantites_' +
          res.product_id +
          '" name="product_quantites[]" value="1">\
			          <input type="hidden" id="product_rate_' +
          res.product_id +
          '" name="product_rates[]" value="' +
          res.current_rate +
          '">\
			          <input type="hidden" id="product_totalrate_' +
          res.product_id +
          '" name="product_totalrates[]" value="' +
          res.current_rate +
          '">\
			          <td>' +
          res.product_code +
          "  </td>\
                <td>" +
          res.product_name +
          ' (<span class="text-success">' +
          res.brand_name +
          "</span>)</td>\
			           <td>" +
          res.current_rate +
          "</td>\
			           <td>1</td>\
			          <td>" +
          res.current_rate +
          '</td>\
			          <td>\
			            <button type="button" onclick="addbarcode_product(`' +
          res.product_code +
          '`,`plus`)" class="fa fa-plus text-success" href="#" ></button>\
						<button type="button" onclick="addbarcode_product(`' +
          res.product_code +
          '`,`minus`)" class="fa fa-minus text-warning" href="#" ></button>\
						<button type="button" onclick="removeByid(`#product_idN_' +
          res.product_id +
          '`)" class="fa fa-trash text-danger" href="#" ></button>\
						</td>\
			          </tr>'
        );

        getOrderTotal();
      }
      //console.log(jsonObj);
    },
  });
}

// ---------------------------order gui---------------------------------------
function addProductOrder(id, max = 100, action_value) {
  //$("#ordered_products").append(data);

  $.ajax({
    url: "php_action/custom_action.php",
    type: "post",
    data: { getProductDetails: id },
    dataType: "json",
    success: function (res) {
      console.log(action_value);

      if ($("#product_idN_" + id).length) {
        var jsonObj = [];
        $(".product_ids").each(function (index) {
          var quantity = $(this).data("quantity");
          var val = $(this).val();

          if (val == id) {
            //$("#product_idN_"+id).remove();

            if (action_value == "plus") {
              var Currentquantity = 1 + parseInt(quantity);
            }
            if (action_value == "minus") {
              var Currentquantity = parseInt(quantity) - 1;
            }

            $("#product_idN_" + id).replaceWith(
              '<tr id="product_idN_' +
              id +
              '">\
          <input type="hidden" data-price="' +
              res.current_rate +
              '" data-quantity="' +
              Currentquantity +
              '" id="product_ids_' +
              id +
              '" class="product_ids" name="product_ids[]" value="' +
              res.product_id +
              '">\
          <input type="hidden" id="product_quantites_' +
              id +
              '" name="product_quantites[]" value="' +
              Currentquantity +
              '">\
          <input type="hidden" id="product_rates_' +
              id +
              '" name="product_rates[]" value="' +
              res.current_rate +
              '">\
          <td>' +
              res.product_name +
              ' (<span class="text-success">' +
              res.brand_name +
              "</span>) </td>\
          <td>" +
              res.current_rate +
              " </td>\
          <td>" +
              Currentquantity +
              " </td>\
          <td>" +
              res.current_rate * Currentquantity +
              ' </td>\
          <td> <button type="button" onclick="addProductOrder(' +
              id +
              "," +
              res.quantity +
              ',`plus`)" class="fa fa-plus text-success" href="#" ></button>\
            <button type="button" onclick="addProductOrder(' +
              id +
              "," +
              res.quantity +
              ',`minus`)" class="fa fa-minus text-warning" href="#" ></button>\
            <button type="button" onclick="removeByid(`#product_idN_' +
              id +
              '`)" class="fa fa-trash text-danger" href="#" ></button>\
            </td>\
          </tr>'
            );
          }
          getOrderTotal();
        });
      } else {
        $("#purchase_product_tb").append(
          '<tr id="product_idN_' +
          id +
          '">\
                <input type="hidden" data-price="' +
          res.current_rate +
          '"  data-quantity="1" id="product_ids_' +
          id +
          '" class="product_ids" name="product_ids[]" value="' +
          id +
          '">\
                <input type="hidden" id="product_quantites_' +
          id +
          '" name="product_quantites[]" value="1">\
                <input type="hidden" id="product_rate_' +
          id +
          '" name="product_rates[]" value="' +
          res.current_rate +
          '">\
                <input type="hidden" id="product_totalrate_' +
          id +
          '" name="product_totalrates[]" value="' +
          res.current_rate +
          '">\
                <td>' +
          res.product_name +
          ' (<span class="text-success">' +
          res.brand_name +
          "</span>)</td>\
                 <td>" +
          res.current_rate +
          "</td>\
                 <td>1</td>\
                <td>" +
          res.current_rate +
          '</td>\
                <td>\
                  <button type="button" onclick="addProductOrder(' +
          id +
          "," +
          res.quantity +
          ',`plus`)" class="fa fa-plus text-success" href="#" ></button>\
            <button type="button" onclick="addProductOrder(' +
          id +
          "," +
          res.quantity +
          ',`minus`)" class="fa fa-minus text-warning" href="#" ></button>\
            <button type="button" onclick="removeByid(`#product_idN_' +
          id +
          '`)" class="fa fa-trash text-danger" href="#" ></button>\
            </td>\
                </tr>'
        );

        getOrderTotal();
      }
      //console.log(jsonObj);
    },
  });
}
function readonlyIt(value, read_it_id) {
  if (value == "") {
    $("#" + read_it_id).prop("readonly", false);
  } else {
    $("#" + read_it_id).prop("readonly", true);
  }
}

$("#product_mm,#product_inch,#product_meter").on("keyup", function () {
  getTotal_price();
});

$("#tableData1").on("change", function () {
  getTotal_price();
});
function getTotal_price() {
  var total = (total1 = total2 = fif_rate = current_cat = thir_rate = 0);
  var cat = $("#tableData1 :selected").data("price");
  var product_mm = $("#product_mm").val();
  var product_inch = $("#product_inch").val();
  var product_meter = $("#product_meter").val();
  var product_mm = product_mm == "" ? (product_mm = 0) : parseFloat(product_mm);
  var product_inch =
    product_inch == "" ? (product_inch = 0) : parseFloat(product_inch);
  var product_meter =
    product_meter == "" ? (product_meter = 0) : parseFloat(product_meter);
  total = product_mm * product_inch * product_meter;
  total1 = (total * parseFloat(cat)) / 54;
  total2 = Math.round(total1);
  $("#current_rate").val(total2);

  current_cat = parseFloat(cat) + 0.05;
  fif_rate = (total * current_cat) / 54;
  fif_rate = Math.round(fif_rate);
  $("#f_days").val(fif_rate);

  current_cat = parseFloat(cat) + 0.1;
  thir_rate = (total * current_cat) / 54;
  thir_rate = Math.round(thir_rate);
  $("#t_days").val(thir_rate);

  console.log(total);
}

function getVoucherPrint(voucher_id) {
  Swal.fire({
    title: "Which type of do you Print?",
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: `Debit`,
    denyButtonText: `Credit`,
    cancelButtonText: "Both",
  }).then((result) => {
    if (result.isConfirmed) {
      window.open(
        "print_voucher.php?type=debit&voucher_id=" + voucher_id,
        "_blank"
      );
    } else if (result.isDenied) {
      window.open(
        "print_voucher.php?type=credit&voucher_id=" + voucher_id,
        "_blank"
      );
    } else {
      window.open(
        "print_voucher.php?type=both&voucher_id=" + voucher_id,
        "_blank"
      );
    }
  });
}

function setAmountPaid(id, paid) {
  Swal.fire({
    title: "Did the Customer Paid All Amount?",
    showCancelButton: true,
    confirmButtonText: `Yes`,
    cancelButtonText: "No",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "php_action/custom_action.php",
        type: "post",
        data: { setAmountPaid: id, paid: paid },
        dataType: "json",
        success: function (res) {
          sweeetalert(res.msg, res.sts, 1500);
          if (res.sts == "success") {
            //$("#view_orders_tb").load(location.href+" #view_orders_tb > *");
            location.reload();
          }
        },
      });
    } else {
    }
  });
}

let purchaseType = (value) => {
  // console.log("Selected purchase type:", value);
  if (value == "cash_purchase") {
    let total_amount = $("#product_grand_total_amount").text();
    $("#paid_ammount").val(total_amount);
    $("#paid_ammount").attr("readonly", true);
    $("#remaining_ammount").val(0);
    $("#payment_type").val("cash_purchase");
    $("#payment_account").attr("required", true);
    $(".for_cash").show();
  } else {
    let total_amount = $("#product_grand_total_amount").text();
    $("#payment_account").attr("required", false);
    $("#paid_ammount").val(0);
    $("#remaining_ammount").val(total_amount);
    $("#paid_ammount").attr("readonly", false);
    $("#payment_type").val("credit_purchase");
    $("#payment_account").attr("required", false);
    $(".for_cash").hide();
  }
};

let saleType = (value) => {

  if (value == "cash") {
    let total_amount = $("#product_grand_total_amount").text();
    $("#paid_ammount").val(total_amount);
    $("#paid_ammount").attr("readonly", false);
    $("#payment_account").attr("required", true);
    $("#credit_order_client_name").attr("required", false);
    $("#remaining_ammount").val(0);
    $("#payment_type").val("cash_in_hand");
    $("#form_type").val("cash_in_hand");
    $(".return_days-div").hide();
    $(".cash-sale-div1").show();
    $(".cash-sale-div2").show();
    $(".input-group-prepend").hide();
    $("#credit_order_client_name").attr("name", "zksjf");
    $("#sale_order_client_name").attr("name", "sale_order_client_name");
    $("#client_contact").attr("name", "sdsa");
    $("#account_row").show();
    $("#split_payment_container").show(); // Show split payment checkbox
  } else {
    let total_amount = $("#product_grand_total_amount").text();
    $("#paid_ammount").val(0);
    $("#payment_type").val("credit_sale");
    $("#payment_account").attr("required", false);
    $("#credit_order_client_name").attr("required", true);
    $("#form_type").val("credit_sale");
    $("#credit_order_client_name").attr("name", "credit_order_client_name");
    $("#sale_order_client_name").attr("name", "skd");
    $("#client_contact").attr("name", "client_contact");
    $("#remaining_ammount").val(total_amount);
    $("#paid_ammount").attr("readonly", false);
    $(".return_days-div").show();
    $(".input-group-prepend").show();
    $(".cash-sale-div1").hide();
    $(".cash-sale-div2").hide();
    $("#account_row").hide();
    $("#split_payment_container").hide(); // Hide split payment checkbox
    toggleSplitPayment(false); // Reset split payment state
  console.log(total_amount);
  
  }
  toggleSplitPayment();
};
$(document).ready(function () {
  $("#sale_type").change();
  function calculateQuantity() {
    var price = $("#get_product_price").val() || 0;
    var quantity = $("#get_product_quantity").val() || 0;
    var total_price = price * quantity;
    $("#get_product_sale_price").val(total_price);
  }

  $("#get_product_price, #get_product_quantity").on("input", calculateQuantity);
});

$(document).ready(function () {
  // Check if the dropdown exists before attaching the event
  $("#branch_id").on("change", function () {
    let branch_id = $(this).val();

    $.ajax({
      url: "php_action/custom_action.php",
      type: "POST",
      data: { get_branch_data: branch_id },
      dataType: "json",
      success: function (response) {
  // Always reset these before population
  $("#customer_list, #bank_list, #supplier_list").empty();

  // === Customer Select ===
  var $customerSelect = $(".customer_name");
  $customerSelect.empty().append('<option value="">Customer Account</option>');

  if (response.customers?.length) {
    response.customers.forEach(function (cust) {
      $customerSelect.append(
        `<option data-id="${cust.customer_id}" data-contact="${cust.customer_phone}" value="${cust.customer_name}">
          ${cust.customer_name} | ${cust.customer_phone}
        </option>`
      );
    });
  }

  // === Payment Accounts (Banks) ===
  var $paymentSelect = $("#payment_account, #bank_account, #cash_account");
  $paymentSelect.empty().append('<option value="">Select Account</option>');

  if (response.banks?.length) {
    response.banks.forEach(function (bank) {
      $paymentSelect.append(
        `<option value="${bank.customer_id}">${bank.customer_name}</option>`
      );
    });
  }

  // === Supplier Select ===
  const $supplierSelect = $(".supplier_name");
  $supplierSelect.empty().append('<option value="">Select Supplier</option>');

  if (response.suppliers?.length) {
    response.suppliers.forEach(function (sup) {
      $supplierSelect.append(
        `<option data-id="${sup.customer_id}" data-contact="${sup.customer_phone}" value="${sup.customer_name}">
          ${sup.customer_name} | ${sup.customer_phone}
        </option>`
      );
    });
  }
}
,
    });
  });
});
