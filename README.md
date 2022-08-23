# GitHub Utils

GitHub utilities for team management.

## Setup

```php
git clone <this-repo> github-utils
cd github-utils
cp .env.example .env
composer install
```

Now, put your GitHub token in `.env` file to make the REST calls work.

### Unified Labels

To create labels, run:

```bash
php labels.php <user or org> <repo>

# example
php labels.php weDevsOfficial dokan
```