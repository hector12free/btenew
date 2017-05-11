<html>
<head>
<title>Chitchat</title>

<style>
  body { width: 960px; margin: 0 auto; }
  table { border-collapse: collapse; }
  table, td, th { border: 1px solid gray; padding: 5px 10px; }
</style>

<script type="text/javascript">
  function autoSubmit(){
    if (document.getElementById("autosubmit").checked) {
      setTimeout(function(){ submitForm(); }, 300);
    }
  }
  function submitForm(){
    var tracking = document.getElementById('tracking').value.trim();
    if (tracking.length > 9) {
      document.forms["form1"].submit();
    }
  }
</script>
</head>

<body OnLoad="document.form1.Tracking.focus();">
<h2>Chitchat</h2>

<b>Before scanning:</b>
<p>1. Generate a Complete Shipping Report from Shippingeasy and save to <b>/out/shipping as shippingeasy-shipping-report.csv</b>.</p>
<p>2. After file is saved, run <a href="http://192.168.0.12/job/importshippingeasy">this script</a>.</p>
<b>Tracking Number:</b><br>

<form name="form1" method="POST" action="/chitchat">
<input type="text" id="tracking" name="Tracking" value="" size="40" onkeypress="autoSubmit()" autocomplete="off" autofocus style="font-size:15px; padding: 5px 10px;">
<input type="submit" name="add" value="Add" />
<input type="checkbox" id="autosubmit" checked> Auto<br>
</form>

<p>Number of parcels: <b style="font-size: 24px;">{{ list | length }}</b></p>

<form name="form2" method="POST" action='/chitchat'>
  <p>
    <input type="submit" name="Delete" value="Delete Selected">
    <input type="submit" name="Export" value="Export CSV" style="margin-left: 50px;">
    <input type="submit" name="Delete_All" value="Delete All" style="margin-left: 100px;" onclick="return confirm('Are you sure?')">
  </p>

  <table border="1">
    <tr>
      <td>#</td>
      <td>Order ID</td>
      <td>Carrier</td>
      <td>Tracking Number</td>
      <td>Date</td>
      <td>Source</td>
      <td>Delete</td>
    </tr>

    {% for item in list %}
      <tr>
        <td>{{ loop.revindex }}</td>
        <td>{{ item['OrderNumber'] }}</td>
        <td>{{ item['Carrier'] }}</td>
        <td>{{ item['TrackingNum'] }}</td>
        <td>{{ item['ShipDate'] }}</td>
        <td>{{ item['OrderNumber'] ? item['Source'] : '' }}</td>
        <td><input type="checkbox"  name="Items[]" value="{{ item['TrackingNum'] }}"></td>
      </tr>
    {% endfor %}
  </table>
</form>

<script type="text/javascript">
  var saved = {{ saved }};
  if (saved) {
    var audio = new Audio('/assets/sound/sound1.mp3');
    audio.play();
  }
</script>

</body>
</html>