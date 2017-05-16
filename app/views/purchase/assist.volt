{% extends "layouts/base.volt" %}

{% block main %}
  <h2 style="margin-top:0;">Purchase assistant</h2>
  <div class="well">
    <form class="form-inline" role="form" method="POST">
      <div class="form-group col-xs">
        <input class="form-control" name="orderId" placeholder="Order number" type="text">
      </div>
      <div class="form-group col-xs">
        <label for="sel1" class="control-label">Date:</label>
        <select class="form-control" id="sel1" name="date">
          <option value="all">All</option>
          {% for d in orderDates %}
          <option value="{{ d }}"{% if d == date %} selected{% endif %}>{{ d }}</option>
          {% endfor %}
        </select>
      </div>
      <div class="form-group col-xs">
        <label for="sel2" class="control-label">Status:</label>
        <select class="form-control" id="sel2" name="status">
          <option value="all">All</option>
          <option value="pending"{% if status == 'pending' %} selected{% endif %}>Pending</option>
          <option value="purchased"{% if status == 'purchased' %} selected{% endif %}>Purchased</option>
        </select>
      </div>
      <div class="checkbox col-xs">
        <label><input type="checkbox" name="overstock" value="1"{% if overstock == 1 %} checked{% endif %}> Overstock </label>
      </div>
      <div class="checkbox col-xs">
        <label><input type="checkbox" name="express" value="1"{% if express == 1 %} checked{% endif %}> Express </label>
      </div>
      <div class="checkbox col-xs">
        <label><input type="checkbox" name="multitem" value="1"{% if multitem == 1 %} checked{% endif %}> Multi Items</label>
      </div>
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-filter"></span> Filter </button>
      <!--
      <button type="button" class="btn btn-success" onclick="checkout()"><span class="glyphicon glyphicon-check"></span> Checkout </button>
      -->
    </form>
  </div>

  {% if orders is not empty %}
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Date</th>
        <th>Order ID</th>
        <th>Note</th>
        <th>Related SKU</th>
        <!--
        <th>Cart</th>
        -->
        <th>Dimension</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>

    {% for order in orders %}
      <tr data-order-id="{{ order['order_id'] }}" data-qty="{{ order['qty'] }}">
        <td{% if order['express'] %} class="text-danger"{% endif %}>{{ order['date'] }}</td>
        <td class="order-id">
          {% if order['multi_items'] %}<b>{% endif %}
          <a href="javascript:void(0)">{{ order['order_id'] }}</a>
          {% if order['multi_items'] %}</b>{% endif %}
        </td>
        <td>{{ order['notes'] }}</td>
        <td class="sku" nowrap style="white-space:nowrap">
          {% if order['status'] == 'purchased' %}
            {{ order['actual_sku'] }}
          {% else %}
            {% if order['related_sku'] is not empty %}
              <select style="min-width: 85%; max-width: 85%;">
                {% for sku in order['related_sku'] %}
                  <option value="{{ sku }}"{% if sku == order['supplier_sku'] %} selected{% endif %}>{{ sku }}</option>
                {% endfor %}
              </select>
              <button class="btn btn-xs btn-warning">{{ order['related_sku'] | length }}</button>
            {% else %}
              &nbsp;
            {% endif %}
          {% endif %}
        </td>
        <!--
        <td class="add">
          {% if not order['multi_items'] %}
          {% if order['related_sku'] is not empty and order['status'] != 'purchased' %}
            <button class="btn btn-xs {% if order['in_cart'] %}btn-success{% else %}btn-default{% endif %}"><span class="glyphicon glyphicon-plus"></span> Add </button>
          {% endif %}
          {% endif %}
        </td>
        -->
        <td>{{ order['dimension'] }}</td>
        <td class="action">
          {% if not order['multi_items'] %}
          {% if order['related_sku'] is not empty and order['status'] != 'purchased' %}
            <button class="btn btn-xs btn-info"><span class="glyphicon glyphicon-shopping-cart"></span> Go </button>
          {% endif %}
          {% endif %}
        </td>
      </tr>
    {% endfor %}

    </tbody>
  </table>
  {{ orders | length }} orders found.
  {% else %}
    No order information found.
  {% endif %}
{% endblock %}

{% block csscode %}
  .form-group, .checkbox { margin-right: 20px; }
  .main-container { width: 1250px; }
{% endblock %}

{% block jscode %}
function getShipMethods(data) {
  var shipMethod = '';

  if (data.sku.substr(0, 3) != 'SYN') {
    return shipMethod;
  }

  var loading = layer.load(1, { shade: false });

  $.ajax({
    type: 'POST',
    url: '/ajax/query/freightquote',
    data: data,
    async: false,
    success: function(res) {
      layer.close(loading);
      shipMethod = `
        <label>Ship Method</label>
        <select id="ship-method" style="float:right;width:320px;">
        ${res.data}
        </select><br><br>`;
    }
  });

  return shipMethod;
}

function getNotifyEmails(data) {
  var emails = '';

  if (data.sku.substr(0, 2) == 'TD') {
    emails = `<div style="margin-top:15px">
      <label>Email Notification</label>
      <select id="notify-email" style="float:right;width:300px;">
        <option>doris@btecanada.com</option>
      </select></div>`;
  }

  return emails;
}

function getMaxLength(data) {
  if (data.sku.substr(0, 2) == 'DH') {
      return '58';
  }
  if (data.sku.substr(0, 3) == 'ING') {
      return '35';
  }
  if (data.sku.substr(0, 3) == 'SYN') {
      return '60';
  }
  if (data.sku.substr(0, 2) == 'TD') {
      return '52';
  }
  return '60';
}

function getPurchaseNote(data) {
  if (data.sku.substr(0, 2) == 'DH') {
      return 'Drop ship';
  }
  return '';
}

function purchaseNoteHtml(data) {
  var shipMethod = getShipMethods(data);
  var notifyEmails = getNotifyEmails(data);
  var maxLength = getMaxLength(data);
  var purchaseNote = getPurchaseNote(data);

  return `<div style="padding: 20px;">
     <table class="table table-condensed">
       <tr><td><b>SKU: </b></td><td>${data.sku ? data.sku : '-'}</td></tr>
       <tr><td><b>Branch: </b></td><td>${data.branch ? data.branch: '-'}</td></tr>
       <tr><td><b>Qty: </b></td><td>${data.qty? data.qty: '-'}</td></tr>
     </table>
     ${shipMethod}
     <label for="comment">Purchase note</label> (Max ${maxLength} chars)<br />
     <textarea id="comment" maxlength="${maxLength}" style="width: 440px; height: 80px; resize: none;">${purchaseNote}</textarea>
     ${notifyEmails}
   </div>`;
}

function makePurchase(data, success, fail, done) {
  layer.open({
    title: 'Purchase',
    area: ['480px', 'auto'],
    btn: ['Purchase', 'Cancel'],
    yes: function(index, layero) {
      var comment = layero.find('#comment').val();
      data.comment = comment;

      var shipMethod = layero.find('#ship-method option:selected').val();
      var notifyEmail = layero.find('#notify-email option:selected').text();

      data.shipMethod = shipMethod;
      data.notifyEmail = notifyEmail;

      ajaxCall('/ajax/dropship/purchase', data, success, fail);
      layer.close(index);
    },
    end: function(index, layero) {
      done();
    },
    content: purchaseNoteHtml(data)
  })
}

function priceAvailHtml(items) {
  var content = '';

  for (var i=0; i<items.length; i++) {
    for (var a=0; a<items[i].avail.length; a++) {
      content += `<tr data-sku="${items[i].sku}" data-branch="${items[i].avail[a].branch}" data-branch-code="${items[i].avail[a].code}">
        <td><input type="radio" name="skubranch"></td>
        <td>${a==0 ? items[i].sku : '&nbsp;'}</td>
        <td>${a==0 ? items[i].price : '&nbsp;'}</td>
        <td>${items[i].avail[a].branch}</td>
        <td>${items[i].avail[a].qty}</td>
      </tr>`;
    }
  }

  return `<div style="padding: 20px;">
    <table class="table table-bordered table-condensed">
    <thead>
      <tr>
        <th>&nbsp;</th>
        <th>PartNum</th>
        <th>Price</th>
        <th>Branch</th>
        <th>Qty</th>
      </tr>
    </thead>
    <tbody>
      ${content}
    </tbody>
    </table>
    </div>`;
}

function getPriceAvail(data, selected, done) {
  ajaxCall('/ajax/query/priceavail', { sku: data },
    function(res) {
      layer.open({
        title: 'Price and Availability',
        area: ['600px', 'auto'],
        btn: ['OK', 'Cancel'],
        yes: function(index, layero) {
          var radio = layero.find('input[type=radio]:checked');
          if (radio.length) {
            var tr = radio.closest('tr');
            var sku = tr.data('sku');
            var branch = tr.data('branch');
            var code = tr.data('branch-code');
            selected({sku: sku, branch: branch, code: code});
          }
          layer.close(index);
        },
        success: function(layero, index){
          layero.find('table tr').click(function(){
            $(this).find('input[type=radio]').prop('checked', true);
          });
        },
        end: function(index, layero) { done(); },
        content: priceAvailHtml(res)
      })
      done();
    },
    function(message) {
      done();
      showError(message);
    }
  );
}

function shoppingCartAdd(data, added, deleted) {
  console.log(data);
  ajaxCall('/ajax/shoppingcart/add', data,
    function(data) {
      if (data == 0) {
        showToast('Deleted from shopping cart', 3000);
        deleted();
      }
      if (data == 1) {
        showToast('Added to shopping cart', 1000);
        added();
      }
    },
    function(message) {
      showError(message);
    }
  );
};

function checkout() {
  layer.confirm('Are you sure you want to checkout?', {
      title: 'Confirm',
      btn: ['Yes','No'],
      type: 0,
      icon: 3,
      skin: ''
    },
    function(index, layero) {
      layer.close(index);
      ajaxCall('/ajax/shoppingcart/checkout', {},
        function(data) {
          showToast(data, 2000);
        },
        function(message) {
          showError(message);
        }
      );
    },
    function() {
    }
  );
};

function markAsProcessed(order, yesfunc) {
  layer.confirm('Are you sure you want to mark the order as Processed?', {
      title: 'Confirm',
      btn: ['Yes','No'],
      type: 0,
      icon: 3,
      skin: ''
    },
    function(index, layero) {
      layer.close(index);
      ajaxCall('/ajax/dropship/markasprocessed', order,
        function(data) {
          yesfunc();
          showToast(data, 2000);
        },
        function(message) {
          showError(message);
        }
      );
    },
    function() {
    }
  );
};
{% endblock %}

{% block docready %}
  // click on action button
  $('.action button').click(function() {
    $('tr').removeClass('info');

    var tr = $(this).closest('tr');
    var orderId = tr.data('order-id');
    var sku = tr.data('sku');
    var qty = tr.data('qty');
    var branch = tr.data('branch');
    var code = tr.data('code');

    if (!sku) {
        sku = tr.find('select').val();
    }

    tr.addClass('info');

    makePurchase({ order_id: orderId, sku: sku, branch: branch, code: code, qty: qty },
      function(poNumber) {
        showToast([
            'Order ID: ' + orderId,
            'SKU: ' + sku,
            'PO Number: ' + poNumber
        ]);
        tr.remove();
      },
      function(message) {
        showError(message);
        tr.addClass('danger');
      },
      function() {
        /*tr.removeClass('info');*/
      }
    );
  });

  // click on sku button
  $('.sku button').click(function() {
    $('tr').removeClass('info');

    var tr = $(this).closest('tr');
    var td = $(this).parent();

    var sku = [];
    td.find("select option").each(function() {
        sku.push($(this).val());
    });

    tr.addClass('info');

    var loading = layer.load(1, { shade: false });

    getPriceAvail(sku,
      function(sel) {
        if (sel.sku) {
          tr.data(sel);
          tr.find('select').val(sel.sku);
        }
      },
      function() {
        layer.close(loading);
        /*tr.removeClass('info');*/
      }
    );
  });

  // click on order id
  $('.order-id a').click(function() {
    $('tr').removeClass('info');

    var tr = $(this).closest('tr');
    var orderId = tr.data('order-id');

    tr.addClass('info');

    var modal = new bte.OrderDetailModal(orderId);
    modal.show();
  });

  // click on add button
  $('.add button').click(function() {
    $('tr').removeClass('info');

    var tr = $(this).closest('tr');
    var td = $(this).closest('td');
    var orderId = tr.data('order-id');
    var sku = tr.data('sku');
    var qty = tr.data('qty');
    var branch = tr.data('branch');
    var code = tr.data('code');

    if (!sku) {
        sku = tr.find('select').val();
    }

    //tr.addClass('info');
    //tr.remove();
    //tr.addClass('danger');

    $btn = $(this);

    shoppingCartAdd({ order_id: orderId, sku: sku, branch: branch, code: code },
      function() { $btn.removeClass('btn-default').addClass('btn-success'); },
      function() { $btn.removeClass('btn-success').addClass('btn-default'); }
    );
  });

  // sku selection changed
  $('.sku select').change(function() {
    $('tr').removeClass('info');

    var tr = $(this).closest('tr');
    var orderId = tr.data('order-id');

    tr.addClass('info');

    if ($(this).val() == 'Mark as Processed') {
      markAsProcessed({orderId: orderId},
        function() { tr.remove(); }
      );
    }
  });

{% endblock %}