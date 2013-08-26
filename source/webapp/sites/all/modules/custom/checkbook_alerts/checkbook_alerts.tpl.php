<div id='dialog'>
  <div id='errorMessages'></div>
  <table>
    <tr>
      <th><span class="bold">Alert Settings</span></th>
    </tr>
    <tr>
      <td>Label:</td><td><input type='text' name='alert_label'/></td>
    </tr>
    <tr>
      <td>Email:</td><td><input type='text' name='alert_email' /></td>
    </tr>
    <tr>
      <td>Minimum Results:</td><td><input type='text' name='alert_minimum_results' value='10' /></td>
    </tr>
    <tr>
      <td>Alert Frequency:</td>
      <td>
        <select name='alert_minimum_days'>
          <option value="1">Daily</option>
          <option value="7">Weekly</option>
          <option value="30">Monthy</option>
          <option value="92" default>Quarterly</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>Expiration Date:</td><td><input type='text' name='alert_end[date]' class="form-text hasDatepicker date-popup-init"> E.g., 2013-08-09</td>
    </tr>
  </table>
</div>
