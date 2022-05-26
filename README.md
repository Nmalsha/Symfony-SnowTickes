# SnowTricks

## Project 6 - Parcours développeur d'application PHP/Symfony

I build this project to learn symfony (using v 5.4).

### Installation

- 1 Downloard the repository
- 2 Modify the .env document with your Database and e mail parameters
- 3 Open your console in the repository and install the dependencies using composer install ->
- 4 Create your database using php bin/console doctrine:database:create
- 5 Then loard the datafixtures using php bin/console doctrine:fixtures:load ->
- 6 Start the server using php bin/console server:run ->
  \_7 Start mailhog server /.MailHog_linux_amd64
- 8 please add your gmail and password to .env in fields MAIL_USER= MAIL_PASS= and run ./MailHog_linux_amd64 to active mailhog (create your own mailhog account)
