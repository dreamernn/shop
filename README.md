# Project Information
> Simple e-commerce system for a coding challenge.

---

## Table of Contents
1. [Project Overview](#project-overview)
2. [Framework Introduction](#framework-introduction)
3. [API Documentation Location](#api-documentation-location)
4. [Database File Reference Location](#database-file-reference-location)
5. [Unit Testing Approach and Location](#unit-testing-approach-and-location)
6. [Nginx Configuration File Location](#nginx-configuration-file-location)
7. [Static Files Location](#static-files-location)
8. [Deployment Steps](#deployment-steps)
9. [Other Notes](#other notes)

---

## Project Overview
> The goal of the challenge is to create a simple user interface for a shop.

---

## Framework Introduction
### Framework
- **Framework Name:** [Xly Framework]
- **Version:** v1.1.0
- **Developer:** [Damon.Meng]
- **Contact Email:** [xiangchen0814@gmail.com](mailto:xiangchen0814@gmail.com)
- **Date:** 29/11/2023
- **Additional Information:** 

> 
 - This framework was developed by Xiangchen.Meng (Damon.Meng) and internally incorporates some basic libraries from third parties.
 - Due to time constraints, it is still needs improvements, such as console, Redis cluster, MySQL cluster, gRPC communication, etc.
 - Feel free to contact me for any suggestions or issues.

### Framework Directory Structure
- Xly Framework
  - 📁 app                # Program
    - 🛠️ Helpers        # Utility functions
    - 📁 Html           # Application
      - 📁 Controllers		# Controller layer (input, output, parameter validation)
         - 📁 Modules1		# Module 1
         - 📁 Modules2		# Module 2
      - 📁 Middleware		# Middleware
      - 📁 Models         # Model layer
      - 📁 Services       # Logic layer
    - 📁 bootstrap        # Startup (load environment variables, configure data, etc.)
    - 📁 config           # Configuration files
    - 📁 doc           	# Development documentation
    - 📁 environment      # Environment configuration
    - 📁 library      		# Libraries
		- 📁 Cache			# Cache base libraries
		- 📁 Common			# Common base libraries (Jwt, Logger, etc.)
		- 📁 Log				# Log extension libraries
		- 📁 Xly				# Framework base libraries (Autoload, Routing, Request, Response, Database, etc.)
    - 📁 logs      			# Log storage
    - 🎨 public           # Resource files
      - 📁 assets         # Resource categories (html, js, style will be moved here later)
      - 📁 html           # html
      - 📁 images         # images
      - 📁 js             # js
      - 📁 styles         # css files
    - 📁 routers      		# Route configuration
    - 🧪 tests            # Unit testing
    	- 📁 mock			   # Mock data configuration
    - 📁 logs      			# Log storage
    - 📁 vendor  			# composer
    - 📜 README.md        # Project description

---

## API Documentation
The API documentation is located at `/docs/interface_guide.pdf`, providing detailed descriptions of the system's interfaces, request methods, parameters, and return results.

---

## Database File
The database file is located at `/docs/Databases/shop.sql`, containing the required database structure and table definitions for the system.

---

## Unit Testing
### Unit Testing Approach
- Perform unit testing using PHPUnit, which can be installed via Composer.

```
composer require --dev phpunit/phpunit

```
- Unit test files are located in the `/tests` directory, extensively testing the functionality and logic of various modules.

```
[YourProjectPath]/vendor/bin/phpunit —testdox tests/indexTest.php
[YourProjectPath]/vendor/bin/phpunit --testdox tests/productTest.php
.[YourProjectPath]/vendor/bin/phpunit --testdox tests/cartTest.php
[YourProjectPath]/vendor/bin/phpunit --testdox tests/orderTest.php
```

- Sample Output

```
PHPUnit 8.5.34 by Sebastian Bergmann and contributors.

Product
 ✔ Customer product list
 ✔ Admin product list
 ✔ Admin edit product

Time: 239 ms, Memory: 4.00 MB

OK (3 tests, 15 assertions)
```

---

## Nginx Configuration File
The Nginx configuration file is located at `/docs/Nginx_conf/local.shop_api.com.conf`, containing configuration information for the Nginx server.

---

## Static Files
- HTML files are located in `/public/html/`.
- CSS files are located in `/public/styles/`.
- JS files are located in `/public/js/`.

## Deployment Steps
### Steps
1. Clone project code: `git clone [git@github.com:dreamernn/shop.git]`
2. Configure the Nginx server (requires reload) and modify the local hosts file.
3. Import the database file.
4. Modify the framework's logs directory permissions to 755 `chmod -R 755 ./log`.
5. Perform a `composer update`.
6. Customize the configuration files in the framework's config folder and environment variables under environment based on your needs.
7. Access the website via the browser (can be defined based on the Nginx configuration): http://local.shop_api.com/html

---

## Other Notes (Optional)
[Any other notes, such as specific dependencies, important notes, etc.]
