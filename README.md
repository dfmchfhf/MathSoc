MathSoc
=======

# Checkouts
## Setting Up
1.  To run a local PHP server, you'll need to get up Apache and MySQL running (if you're using Windows, this can be done easily through PHPMyAdmin).
2.  If using Windows, download and setup [phpMyAdmin](http://www.phpmyadmin.net/home_page/index.php), and skip to step 5, otherwise, go to step 3.
3.  Download `apache2` and `mysql` (e.g. `brew install apache2 mysql`).
4.  Follow the steps listed [here](http://coolestguidesontheplanet.com/set-virtual-hosts-apache-mac-osx-10-10-yosemite/) and point the DocumentRoot at your Checkouts project directory.
5.  Load `mathsoc.sql` into the MathSoc database.
6.  Go to `127.0.0.1`.
7.  Enjoy!
