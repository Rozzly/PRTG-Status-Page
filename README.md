# PRTG-Status-Page
This project provides a front-end dashboard for monitoring many similar, but separate, items from PRTG Network Monitor. This status page is best suited for those who have many items nested in PRTG, but want a quick and clean, indicative, dashboard to quickly find any problems within an environment. 

* GetJSON.php is written to pull single and multi-dimensional array objects from PRTG's API, and re-write them to a standardized format for easy parsing and display.
* index.php provides a basic, but functional, interface to display the status of sensors with a 30 second automatic refresh.

![Dashboard Example](https://trucyan.com/dashboard/dashboard.png)

# Links
* [DEMO](https://trucyan.com/dashboard) - JSON templates replicate actual API output from PRTG. All data on this page is fictitious and randomly generated.
* [PRTG](https://www.paessler.com/) - Paessler PRTG Network Monitor // Free for up to 100 device sensors

# Setup
Ideally, one server runs PRTG, while another runs a web server with PHP to host the status-page files.

* Install PRTG and configure your primary device sensors
* Create a new READ ONLY system user
* Edit `/includes/getJSON.php` > fill out the FQDN for your PRTG web server, READ ONLY username, and password.
* Edit `index.php` > Adjust the sensor ID lines to match your sensor IDs in PRTG.

# Tested Compatability
* [PHP](https://www.php.net/) `7.4.x`
* [jQuery](https://jquery.com/) `3.5.x`
* [Bootstrap](https://getbootstrap.com/docs/4.5/getting-started/introduction/) `4.5.x`
* [Popper.js](https://github.com/popperjs/popper-core) `2.x`
* [Tippy.js](https://atomiks.github.io/tippyjs/) `6.x`

# License
This project is licensed using Apache 2.0 License (https://www.apache.org/licenses/LICENSE-2.0).