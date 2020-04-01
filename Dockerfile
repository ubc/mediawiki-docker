FROM ubcctlt/mediawiki:REL1_31_B11
COPY User.php /var/www/html/includes/user/
RUN chown 1000:1000 /var/www/html/includes/user/User.php
RUN chmod 0664 /var/www/html/includes/user/User.php
