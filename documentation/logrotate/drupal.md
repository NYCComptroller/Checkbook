/etc/logrotate.d/drupal

```
/var/log/drupal/drupal.log {
  daily
  copytruncate
  notifempty
  missingok
  compress
  minsize 1M
  size 10M
  postrotate
    /sbin/service filebeat restart > /dev/null 2>/dev/null || true
  endscript
}
```
