# Tenant Usage Calculator Command

## Overview
The `tenants:calculate-usage` command is a production-ready artisan command that:
1. Cleans up temporary Livewire files from S3 storage
2. Calculates database and S3 storage usage for each tenant
3. Updates subscription metadata with current usage statistics

## Command Signature
```bash
php artisan tenants:calculate-usage [options]
```

## Options

| Option | Description |
|--------|-------------|
| `--force` | Force cleanup even if not in production environment |
| `--skip-cleanup` | Skip the livewire-tmp file cleanup step |
| `--tenant=<ID>` | Process only a specific tenant by ID |

## Usage Examples

### Basic Usage (All Tenants)
```bash
php artisan tenants:calculate-usage
```

### Skip Cleanup (Calculate Only)
```bash
php artisan tenants:calculate-usage --skip-cleanup
```

### Process Specific Tenant
```bash
php artisan tenants:calculate-usage --tenant=4cfc153c-ab76-47b7-b051-84f86dd5d0f3
```

### Force Cleanup in Non-Production
```bash
php artisan tenants:calculate-usage --force
```

## What It Does

### Step 1: Cleanup Temporary Files
- Scans the `livewire-tmp` folder in S3 for each tenant
- Deletes files in batches of 100 to avoid memory issues
- Logs the number of files cleaned per tenant
- Gracefully handles errors without stopping the process

### Step 2: Calculate Storage Usage
For each active subscription:
- **Database Usage**: Calculates total size of tenant database
- **S3 Usage**: Calculates total size of S3 files
- **Total Usage**: Sum of database + S3
- **Percentage**: Usage vs plan limit

### Step 3: Save Metadata
Updates the subscription's metadata with:
```json
{
  "usage_stats": {
    "bytes": 1234567,
    "formatted": "1.18 MB",
    "percentage": 23.7,
    "last_updated": "2025-12-31 13:35:00"
  }
}
```

## Improvements Over Original

### 🐛 Bug Fixes
1. **Batch Processing**: Files deleted in chunks of 100 to prevent memory exhaustion
2. **Transaction Safety**: Database updates wrapped in transactions
3. **Null Safety**: Checks for tenant existence before processing
4. **Error Isolation**: One tenant's failure doesn't stop others

### ✨ New Features
1. **CLI Options**: Flexible command-line options for different scenarios
2. **Progress Bar**: Real-time progress with current tenant being processed
3. **Better Logging**: Structured logs with context for debugging
4. **Statistics Tracking**: Counts successes, failures, and files cleaned
5. **Return Codes**: Proper exit codes (0 = success, 1 = failure)
6. **Emoji Indicators**: Visual feedback in output

### 🔒 Production Readiness
1. **Error Handling**: Try-catch blocks with detailed logging
2. **Resource Management**: Batch processing to avoid memory issues
3. **Monitoring Hooks**: Ready for notification integration
4. **Environment Awareness**: Different behavior in production
5. **Graceful Degradation**: Continues processing even if one tenant fails

## Scheduling

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Run daily at 2 AM
    $schedule->command('tenants:calculate-usage')
        ->dailyAt('02:00')
        ->onOneServer()
        ->withoutOverlapping()
        ->runInBackground();
}
```

### Recommended Schedule
- **Daily**: For most applications
- **Weekly**: For low-activity tenants
- **Hourly**: For high-activity or storage-critical applications

## Output Example

```
🚀 Starting tenant usage calculation...

📊 Processing 5 tenant(s)...

 5/5 [============================] 100% - Complete!

✨ Cleanup and Calculation Complete!

Total Processed ............... 5
Successful .................... 5
Failed ........................ 0
Files Cleaned ................. 127

+--------------------------------------+----------+---------+---------+--------+
| Tenant ID                            | Plan     | Usage   | Percent | Status |
+--------------------------------------+----------+---------+---------+--------+
| 4cfc153c-ab76-47b7-b051-84f86dd5d0f3 | standard | 1.18 MB | 23.7%   | ✅ OK  |
| 7a2b9c4d-1e3f-4b5a-8c6d-9e0f1a2b3c4d | premium  | 5.42 GB | 27.1%   | ✅ OK  |
| ...                                  | ...      | ...     | ...     | ...    |
+--------------------------------------+----------+---------+---------+--------+
```

## Error Handling

### Logged Errors
All errors are logged to `storage/logs/laravel.log` with context:
```json
{
  "subscription_id": 123,
  "tenant_id": "uuid",
  "plan": "standard",
  "error": "Error message",
  "trace": "Stack trace..."
}
```

### Failed Tenants
- Marked as "FAILED" in output table
- Error logged but processing continues
- Exit code 1 returned if any failures

## Monitoring

### Success Metrics
- Number of tenants processed
- Files cleaned
- Total storage calculated

### Failure Alerts
In production, consider adding notifications:
```php
if (app()->environment('production')) {
    Notification::route('mail', config('app.admin_email'))
        ->notify(new TenantUsageCalculationFailed($context));
}
```

## Performance

### Optimization Tips
1. **Run during off-peak hours** (e.g., 2 AM)
2. **Use `--skip-cleanup`** if cleanup isn't needed
3. **Process specific tenants** during business hours
4. **Monitor execution time** and adjust batch sizes if needed

### Expected Performance
- **Small tenant** (< 100 MB): ~2-5 seconds
- **Medium tenant** (100 MB - 1 GB): ~10-30 seconds
- **Large tenant** (> 1 GB): ~1-3 minutes

## Troubleshooting

### Command Hangs
- Check S3 connectivity
- Verify AWS credentials
- Look for large file counts in livewire-tmp

### High Memory Usage
- Reduce batch size in `cleanupTempFiles()` (currently 100)
- Process tenants individually with `--tenant` option

### Inaccurate Usage
- Verify S3 bucket permissions
- Check database connection
- Review `getUsedStorageInBytes()` method in Subscription model

---

**Last Updated**: 2025-12-31  
**Version**: 2.0 (Production Ready)
