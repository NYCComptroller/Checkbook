<div id='dialog'>
  <div id='errorMessages'></div>
  <div>Checkbook alerts will notify you by email when new results matching your current search criteria are available.  Emails will be sent based on the frequency selected and only after the minimum number of additional results entered has been reached since the last alert.</div>
  <table>
    <tr>
      <th>
        <span class="bold">Alert Settings</span>
      </th>
    </tr>
    <tr>
      <td>Description:</td>
      <td>
        <input type='text' name='alert_label' size="25" />
        <div class="description">This is how the alert will be described in the email text.</div>
      </td>
    </tr>
    <tr>
      <td>Email:</td><td><input type='text' name='alert_email' size="50" /></td>
    </tr>
    <tr>
      <td>Minimum Additional Results:</td><td><input type='text' name='alert_minimum_results' value='10' size="5" maxlength="5" /><div class="description">Checkbook will not notify you until this many new results are returned.</div></td>
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
      <td>Expiration Date:</td>
      <td>
        <input type='text' name='alert_end[date]' size="30" maxlength="30"><div class="description">E.g., 2013-08-09</div>
      </td>
    </tr>
  </table>
</div>
