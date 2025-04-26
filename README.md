# jadco-vue-full

## Queue Workers for Email Processing

To improve performance when sending emails, the application uses Laravel's queue system. After deploying or updating the application, you should make sure a queue worker is running:

### Start Queue Worker

```bash
# Run this on the server
php artisan queue:work --tries=3
```

For production, consider setting up a supervisor configuration to ensure the queue worker runs continuously:

### Example Supervisor Configuration

```
[program:jadco-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/queue.log
stopwaitsecs=3600
```

Without a running queue worker, emails will still be queued but won't be sent until a worker processes them.
 
