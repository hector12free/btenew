{% extends "layouts/base.volt" %}

{% block main %}
<header class="well clearfix" id="searchbox">
  <form role="form" method="post">

      <div class="col-sm-12">
        <h3 style="margin-top: 0;">Inventory Location Search</h3>
      </div>

      <div class="col-sm-6">
        <input autofocus required type="text" class="form-control" name="keyword" autofocus placeholder="Enter PartNumber/UPC/Location/Note">
      </div>

      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search </button>
      </div>

      <div class="col-sm-12">
        <label class="radio-inline">
          <input type="radio" name="searchby" value="partnum" {% if searchby == 'partnum' %}checked{% endif %}>Part number
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="upc" {% if searchby == 'upc' %}checked{% endif %}>UPC
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="location" {% if searchby == 'location' %}checked{% endif %}>Location
        </label>
        <label class="radio-inline">
          <input type="radio" name="searchby" value="note" {% if searchby == 'note' %}checked{% endif %}>Note
        </label>
      </div>

  </form>
</header>

{% if data is not empty %}
  <p>Search result for <b>{{ keyword }}</b> in <b>{{ searchby }}</b>:(only first 20 rows)</p>
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>#</th>
        <th>Part Number</th>
        <th>UPC</th>
        <th>Location</th>
        <th>Qty</th>
        <th>SN #</th>
        <th>Note</th>
        <!-- <th>Action</th> -->
      </tr>
    </thead>
    <tbody>

    {% for item in data %}
      <tr data-id="{{ item['id'] }}">
        <td><b>{{ loop.index }}</b></td>
        <td class="partnum">{{ item['partnum'] }}</td>
        <td class="upc">{{ item['upc'] }}</td>
        <td class="location">{{ item['location'] }}</td>
        <td class="qty">{{ item['qty'] }}</td>
        <td class="sn">{{ item['sn'] }}</td>
        <td class="note">{{ item['note'] }}</td>
        <!--
        <td>
          <a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-edit"></span> Edit </a>
          <a href="#" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-remove"></span> Delete </a>
        </td>
        -->
      </tr>
    {% endfor  %}

    </tbody>
  </table>

{% else %}
  {% if keyword is not empty %}
    No inventory information found for <b>{{ keyword }}</b> as <b>{{ searchby }}</b>.
  {% endif %}
{% endif %}

{% endblock %}

{% block csscode %}
{% endblock %}

{% block jscode %}
function editNoteHtml(data) {
  var note = data.note;
  return `<div style="padding: 20px;">
     <label for="note">Note</label> (Max 80 chars)<br />
     <textarea id="note" maxlength="80" style="width: 440px; height: 80px; resize: none;">${note}</textarea>
   </div>`;
}

function editNote(data, success, fail, done) {
  layer.open({
    title: 'Edit Note',
    area: ['480px', 'auto'],
    btn: ['Save', 'Cancel'],
    yes: function(index, layero) {
      var note = layero.find('#note').val();

      data.note = note;

      ajaxCall('/inventory/update', data, success, fail);
      layer.close(index);
    },
    end: function(index, layero) {
      done();
    },
    content: editNoteHtml(data)
  })
}
{% endblock %}

{% block docready %}
  layer.config({
    type: 1,
    moveType: 1,
    skin: 'layui-layer-molv',
  });

  $('tr').click(function() {
    $('tr').removeClass('info');

    var self = $(this);

    var id = self.data('id');
    var note = self.find('.note').text();

    self.addClass('info');

    editNote({ id: id, note: note },
      function(data) {
        showToast('Your change has benn saved', 1000);
        self.find('.note').text(data.note);
      },
      function(message) {
        showError(message);
        self.addClass('danger');
      },
      function() {}
    );
  });
{% endblock %}
