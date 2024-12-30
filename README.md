# Attendize-TeamProject

## Overview
Attendize-TeamProject is an open-source ticket selling and event management platform. Below are the steps to get started with Attendize and configure it for your development or production environment.

<p align="center">
  <img src="/public/assets/images/logo-dark.png" alt="Attendize"/>
  <img style='border: 1px solid #444;' src="https://www.attendize.com/images/screenshots/screen1.PNG" alt="Attendize"/>
</p>

## Table of Contents
- [Getting Started](#getting-started)
  - [Minimum Requirements](#minimum-requirements)
  - [Manual Installation Steps](#manual-installation-steps)
  - [Running Attendize-TeamProject in Docker for Development](#running-attendize-teamproject-in-docker-for-development)
  - [Branch Renaming](#branch-renaming)
  - [Which Version of Attendize Should I Use?](#which-version-of-attendize-do-i-use)
- [Licensing](#licensing)
- [Payment Gateways](#payment-gateways)
- [Release Notes](#release-notes)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)

## Getting Started

These instructions will help you get started quickly with Attendize-TeamProject.

### Minimum Requirements
Attendize runs on most LAMP or LEMP environments as long as certain requirements are met. It is based on the Laravel framework.

**Note**: There are two main versions of Attendize:
- Version 2.0+ requires PHP 7.1.3 or higher.
- Older Laravel 5.2 branch works with PHP 5.6 to PHP 7.1.

You can use the stable main branch for the latest updates.

#### PHP Requirements:
- PHP >= 7.1.3 (for the main branch)
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- Fileinfo PHP Extension
- GD PHP Extension

#### MySQL Requirements:
- MySQL version 5.7 or higher

### Manual Installation Steps
1. **Clone the repository:**
   ```bash
   git clone https://github.com/rodrichie/Attendize-TeamProject.git
   cd Attendize-TeamProject
   git checkout main
   ```

2. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

3. **Set file permissions:**
   Ensure the following files/folders are writable by the webserver user (e.g., `www-data`):
   ```bash
   chown -R www-data storage/app
   chown -R www-data storage/framework
   chown -R www-data storage/logs
   chown -R www-data storage/cache
   chown -R www-data bootstrap/cache
   chown -R www-data .env
   chmod -R a+w storage/app
   chmod -R a+w storage/framework
   chmod -R a+w storage/logs
   chmod -R a+w storage/cache
   chmod -R a+w bootstrap/cache
   chmod -R a+w .env
   ```

4. **Install dependencies:**
   You need [Composer](https://getcomposer.org/) installed.
   ```bash
   composer install
   ```

5. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

6. **Create MySQL database:**
   Use this database for your Attendize installation and enter the details on the installer page.

7. **Navigate to the installer:**
   Open your browser and go to `http://your-ticket-site.com/install`.

   If the page shows an error, check the logs in `./storage/logs` and ensure proper permissions are set.

### Running Attendize-TeamProject in Docker for Development
**Note**: Docker must be installed on your machine.

1. **Clone the project:**
   ```bash
   git clone https://github.com/rodrichie/Attendize-TeamProject.git
   cd Attendize-TeamProject
   git checkout main
   ```

2. **Build Docker images and run containers:**
   ```bash
   make setup
   ```

3. **Access the installer:**
   Browse to [https://localhost:8081/install](https://localhost:8081/install).

4. **Run database migrations:**
   ```bash
   docker-compose run php php artisan attendize:install
   ```

Attendize-TeamProject should now be running at [https://localhost:8081](https://localhost:8081), with maildev available at [http://localhost:1080](http://localhost:1080) for email testing.

### Branch Renaming

The default branch has been renamed!  
`develop` is now named `main`.

If you have a local clone, you can update it by running the following commands:

```bash
git branch -m develop main
git fetch origin
git branch -u origin/main main
git remote set-head origin -a
```

### Which Version of Attendize Should I Use?
Choose the version based on your payment provider. Attendize uses the Omnipay framework for payment processing. The Laravel 5.2 branch supports Omnipay v2, while version 1.1+ supports Omnipay v3.

Check the list of [supported payment gateways](https://github.com/thephpleague/omnipay#payment-gateways) to decide which version of Attendize to use:
- Omnipay v2: Use Laravel 5.2 version.
- Omnipay v3: Use the most current version of Attendize.

## Licensing
Attendize-TeamProject is open-source and follows an open license. Please see the [LICENSE](LICENSE) file for details.

## Payment Gateways
Attendize supports various payment gateways through Omnipay, including:
- Stripe
- PayPal
- Authorize.Net

Check [Omnipay's payment gateways](https://github.com/thephpleague/omnipay#payment-gateways) for more details.

## Release Notes
See the [release notes](https://github.com/rodrichie/Attendize-TeamProject/releases) for detailed information on new features and bug fixes.

## Troubleshooting
Visit the [troubleshooting guide](https://attendize.com/docs/troubleshooting.html) for tips and solutions to common issues.

## Contributing
If you wish to contribute to Attendize-TeamProject, please see our [Contributing Guide](CONTRIBUTING.md) for more information.
