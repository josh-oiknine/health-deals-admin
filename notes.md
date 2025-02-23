Notes:
sudo -u postgres psql -d health_deals -c "SELECT COUNT(*) FROM scraping_jobs WHERE status = 'stopped'"